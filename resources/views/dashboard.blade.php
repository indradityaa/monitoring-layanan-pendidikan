<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100">

    <div class="max-w-4xl mx-auto py-10 px-6">

        <div class="bg-white rounded-lg shadow-md p-6">

            <h1 class="text-2xl font-bold text-gray-800">
                Dashboard
            </h1>

            <p class="mt-2 text-gray-600">
                Selamat datang,
                <strong>{{ auth()->user()->name }}</strong>
            </p>

            <p class="mt-1 text-sm text-gray-500">
                Role:
                <strong>{{ auth()->user()->role }}</strong>
            </p>

            <form action="{{ route('logout') }}" method="POST" class="mt-6">
                @csrf

                <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Logout
                </button>
            </form>

        </div>

    </div>

</body>

</html>