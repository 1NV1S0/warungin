<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center text-orange-600 mb-6">Staff Login</h2>
        
        @if ($errors->any())
            <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" class="w-full border rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full border rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
            </div>
            <button type="submit" class="w-full bg-orange-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-orange-700 transition">
                Masuk Dashboard
            </button>
        </form>
        <div class="mt-4 text-center text-xs text-gray-400">
            Email: kasir@warungin.com <br> Pass: password123
        </div>
    </div>
</body>
</html>