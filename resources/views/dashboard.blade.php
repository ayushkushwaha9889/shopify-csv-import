<!DOCTYPE html>
<html>

<head>
    <title>Import Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <h2>CSV Import Data</h2>

        <div class="text-end"><a href="{{ route('uploadcsv') }}" class="btn btn-primary btn-sm">Upload CSV</a></div>

        @foreach($uploads as $upload)

            <div class="card mt-4">

                <div class="card-header">

                    <strong>
                        {{ $upload->file_name }}
                    </strong>

                </div>

                <div class="card-body">

                    <p>
                        Status:
                        @if($upload->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($upload->status == 'failed')
                            <span class="badge bg-danger">Failed</span>
                        @elseif($upload->status == 'processing')
                            <span class="badge bg-warning">Processing</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </p>

                    <p>
                        Records:
                        {{ $upload->processed_records }}
                        /
                        {{ $upload->total_records }}
                    </p>

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>SKU</th>
                                <th>Status</th>
                                <th>Shopify ID</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($upload->products as $product)
                                <tr>
                                    <td>{{ $product->title }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>
                                        @if($product->status == 'success')
                                            <span class="badge bg-success">Success</span>
                                        @elseif($product->status == 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif($product->status == 'processing')
                                            <span class="badge bg-warning">Processing</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->shopify_product_id }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        No products found
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                    <h5>Logs</h5>

                    <table class="table table-sm">

                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Message</th>
                                <th>Time</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($upload->logs as $log)
                                <tr>
                                    <td>{{ ucfirst($log->level) }}</td>
                                    <td>{{ $log->message }}</td>
                                    <td>{{ $log->created_at->format('Y M d H:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        No logs found
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        @endforeach

    </div>

</body>

</html>