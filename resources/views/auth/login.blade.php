<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Monitoring Layanan Pendidikan</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Monitoring Layanan Pendidikan
            </h1>

            <p class="text-sm text-gray-500 mt-2">
                Silakan login untuk melanjutkan
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-100 border border-red-300 p-3">
                @foreach ($errors->all() as $error)
                    <p class="text-sm text-red-700">
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">

            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>

                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>

                <input type="password" id="password" name="password" required
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <button type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700">
                Login
            </button>

        </form>

    </div>

</body>

</html>