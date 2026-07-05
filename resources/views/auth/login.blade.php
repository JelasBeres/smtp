<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MailFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="grid min-h-screen place-items-center p-6">
        <form method="POST" action="{{ route('login') }}" class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-8 shadow-xl">
            @csrf
            <h1 class="text-2xl font-semibold">Sign in to MailFlow</h1>
            <p class="mt-2 text-sm text-slate-400">Use only permission-based contact data.</p>
            <label class="mt-6 block text-sm">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2">
            @error('email')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
            <label class="mt-4 block text-sm">Password</label>
            <input name="password" type="password" required class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2">
            <label class="mt-4 flex items-center gap-2 text-sm text-slate-300">
                <input name="remember" type="checkbox" class="rounded border-slate-700 bg-slate-950">
                Remember me
            </label>
            <button class="mt-6 w-full rounded-lg bg-cyan-500 px-4 py-2 font-medium text-slate-950 hover:bg-cyan-400">Login</button>
        </form>
    </main>
</body>
</html>
