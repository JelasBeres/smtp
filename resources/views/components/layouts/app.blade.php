@props(['title' => 'Dashboard'])

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MailFlow') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="border-b border-slate-800 bg-slate-900/80 p-4 lg:w-72 lg:border-b-0 lg:border-r">
            <div class="mb-6">
                <div class="text-xl font-semibold">MailFlow</div>
                <div class="text-sm text-slate-400">Permission-based email campaigns</div>
            </div>
            <nav class="grid gap-1 text-sm">
                @foreach ([
                    'Dashboard' => route('dashboard'),
                    'Contacts' => route('contacts.index'),
                    'Contact Lists' => route('contact-lists.index'),
                    'Segments' => route('segments.index'),
                    'Templates' => route('templates.index'),
                    'Campaigns' => route('campaigns.index'),
                    'Suppression List' => route('suppressions.index'),
                    'Sending Domains' => route('sending-domains.index'),
                    'Email Providers' => route('email-providers.index'),
                    'Webhook Logs' => route('webhook-logs.index'),
                    'Import History' => route('import-history.index'),
                    'Audit Logs' => route('audit-logs.index'),
                    'Users' => route('users.index'),
                    'Settings' => route('settings.index'),
                ] as $item => $url)
                    <a class="rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white" href="{{ $url }}">{{ $item }}</a>
                @endforeach
            </nav>
        </aside>
        <main class="flex-1 p-6">
            <header class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">{{ $title }}</h1>
                    <p class="text-sm text-slate-400">Logged in as {{ auth()->user()->email ?? '' }}</p>
                </div>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">Logout</button>
                    </form>
                @endauth
            </header>
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-700 bg-emerald-950 px-4 py-3 text-emerald-100">{{ session('status') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</body>
</html>
