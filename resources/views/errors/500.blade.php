<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link href="https://unpkg.com/primeicons/primeicons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div>
                <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-red-100">
                    <i class="pi pi-exclamation-triangle text-4xl text-red-600"></i>
                </div>
                <h1 class="mt-6 text-6xl font-bold text-gray-900">500</h1>
                <h2 class="mt-2 text-2xl font-semibold text-gray-700">Server Error</h2>
                <p class="mt-4 text-gray-600">
                    Something went wrong on our end. We're working to fix this issue.
                </p>
            </div>

            <div class="space-y-4">
                <button @click="window.location.reload()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Try Again
                </button>
                <div>
                    <a href="{{ route('dashboard') }}"
                        class="text-blue-600 hover:text-blue-500 font-medium transition-colors duration-200">
                        Return to Dashboard
                    </a>
                </div>
            </div>

            @if(!app()->environment('production'))
            <div class="mt-8 p-4 bg-gray-100 rounded-lg text-left border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2">Debug Information</h3>
                <p class="text-sm text-gray-600 font-mono">
                    Error: {{ $exception->getMessage() }}<br>
                    File: {{ $exception->getFile() }}:{{ $exception->getLine() }}
                </p>
            </div>
            @endif
        </div>
    </div>
</body>

</html>