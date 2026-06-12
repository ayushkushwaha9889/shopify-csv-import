<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Product;
use App\Models\ImportLog;
use App\Services\ShopifyService;

class ImportProductToShopify implements ShouldQueue
{
    use Queueable;
    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $this->product->update([
                'status' => 'processing'
            ]);

            $shopify = new ShopifyService();

            if ($this->product->shopify_product_id) {

                $response = $shopify->updateProduct(
                    $this->product->shopify_product_id,
                    [
                        'title' => $this->product->title,
                        'description' => $this->product->description,
                        'vendor' => $this->product->vendor,
                        'product_type' => $this->product->product_type,
                    ]
                );

            } else {

                $response = $shopify->createProduct(
                    [
                        'title' => $this->product->title,
                        'description' => $this->product->description,
                        'vendor' => $this->product->vendor,
                        'product_type' => $this->product->product_type,
                        'tags' => $this->product->tags,
                    ]
                );
            }

            $data = $response->json();

            $errors = data_get(
                $data,
                'data.productCreate.userErrors',
                []
            );

            if (!empty($errors)) {

                $message = collect($errors)
                    ->pluck('message')
                    ->implode(', ');

                $this->product->update([
                    'status' => 'failed',
                    'error_message' => $message
                ]);

                ImportLog::create([
                    'upload_id' => $this->product->upload_id,
                    'product_id' => $this->product->id,
                    'level' => 'error',
                    'message' => $message,
                ]);

                return;
            }

            $shopifyProductId = data_get(
                $data,
                'data.productCreate.product.id'
            );

            $shopify->addProductToCollection(
                $shopifyProductId,
                'gid://shopify/Collection/' . config('shopify.collection_id')
            );

            $this->product->update([
                'status' => 'success',
                'shopify_product_id' => $shopifyProductId,
            ]);

            ImportLog::create([
                'upload_id' => $this->product->upload_id,
                'product_id' => $this->product->id,
                'level' => 'info',
                'message' => 'Product imported successfully',
            ]);

        } catch (\Throwable $e) {

            $this->product->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            ImportLog::create([
                'upload_id' => $this->product->upload_id,
                'product_id' => $this->product->id,
                'level' => 'error',
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

    }
}
