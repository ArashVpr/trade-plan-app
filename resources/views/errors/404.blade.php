<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://unpkg.com/primeicons/primeicons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div>
                <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="pi pi-search text-4xl text-blue-600"></i>
                </div>
                <h1 class="mt-6 text-6xl font-bold text-gray-900">404</h1>
                <h2 class="mt-2 text-2xl font-semibold text-gray-700">Page Not Found</h2>
                <p class="mt-4 text-gray-600">
                    The page you're looking for doesn't exist or has been moved.
                </p>
            </div>

            <div class="space-y-4">
                <a href="{{ route('dashboard') }}"
                    class="inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-center">
                    Go to Dashboard
                </a>
                <div>
                    <button @click="window.history.back()"
                        class="text-blue-600 hover:text-blue-500 font-medium transition-colors duration-200">
                        Go Back
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>