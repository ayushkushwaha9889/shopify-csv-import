<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Upload;
use App\Jobs\ProcessCsvImport;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:5120'
        ]);

        $file = $request->file('csv_file');

        $path = $file->store('uploads');

        $upload = Upload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending',
        ]);

        ProcessCsvImport::dispatch($upload);

        return back()->with(
            'success',
            'CSV uploaded successfully.'
        );
    }

}
