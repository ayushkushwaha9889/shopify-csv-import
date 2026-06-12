<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Product;
use App\Models\Upload;
use League\Csv\Reader;
use Log;

class ProcessCsvImport implements ShouldQueue
{
    use Queueable;

    public Upload $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }


    public function handle(): void
    {
        try {

            $this->upload->update([
                'status' => 'processing'
            ]);

            $filePath = storage_path('app/private/' . $this->upload->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception('CSV file not found.');
            }

            $stream = fopen($filePath, 'r');

            if (!$stream) {
                throw new \Exception('Unable to open CSV file.');
            }

            $csv = Reader::from($stream);

            $csv->setHeaderOffset(0);

            $records = iterator_to_array(
                $csv->getRecords()
            );

            $this->upload->update([
                'total_records' => count($records)
            ]);

            foreach ($records as $record) {

                $product = Product::updateOrCreate(
                    [
                        'upload_id' => $this->upload->id,
                        'sku' => $record['Variant SKU'] ?? null,
                    ],
                    [
                        'handle' => $record['Handle'] ?? null,
                        'title' => $record['Title'] ?? '',
                        'vendor' => $record['Vendor'] ?? null,
                        'product_type' => $record['Product Type'] ?? null,
                        'description' => $record['Body HTML'] ?? null,
                        'tags' => $record['Tags'] ?? null,
                        'status' => 'pending',
                    ]
                );

                ImportProductToShopify::dispatch($product);

            }

            fclose($stream);

            $this->upload->update([
                'status' => 'completed',
                'processed_records' => count($records)
            ]);

            Log::info('CSV Import Completed', [
                'upload_id' => $this->upload->id,
                'records' => count($records)
            ]);

        } catch (\Throwable $e) {

            Log::error('CSV Import Failed', [
                'upload_id' => $this->upload->id,
                'message' => $e->getMessage()
            ]);

            $this->upload->update([
                'status' => 'failed'
            ]);

            throw $e;
        }

    }

}
