<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Session Expired</title>
    <link href="https://unpkg.com/primeicons/primeicons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div>
                <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-orange-100">
                    <i class="pi pi-clock text-4xl text-orange-600"></i>
                </div>
                <h1 class="mt-6 text-6xl font-bold text-gray-900">419</h1>
                <h2 class="mt-2 text-2xl font-semibold text-gray-700">Session Expired</h2>
                <p class="mt-4 text-gray-600">
                    Your session has expired. Please refresh the page and try again.
                </p>
            </div>

            <div class="space-y-4">
                <button @click="window.location.reload()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Refresh Page
                </button>
                <div>
                    <a href="{{ route('login') }}"
                        class="text-blue-600 hover:text-blue-500 font-medium transition-colors duration-200">
                        Sign In Again
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>