<!DOCTYPE html>
<html>

<head>
    <title>Shopify CSV Import</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">

        <div class="card">

            <div class="card-header">
                <h3>Upload CSV</h3>

                <div class="text-end"><a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Back</a></div>

            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('upload.csv') }}" enctype="multipart/form-data">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            CSV File
                        </label>

                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>

                    </div>

                    <button class="btn btn-primary">
                        Upload
                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>