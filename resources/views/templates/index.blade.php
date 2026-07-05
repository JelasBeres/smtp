<x-layouts.app title="Templates">
    <form method="post" action="{{ route('templates.store') }}" class="grid gap-3 rounded-xl border border-slate-800 bg-slate-900 p-5">
        @csrf
        <input name="name" required placeholder="Template name" class="rounded bg-slate-950 p-2">
        <input name="subject" required placeholder="Subject" class="rounded bg-slate-950 p-2">
        <input name="preview_text" placeholder="Preview text" class="rounded bg-slate-950 p-2">
        <textarea name="html_content" required rows="8" placeholder="HTML with @{{first_name}} and @{{unsubscribe_url}}" class="rounded bg-slate-950 p-2"></textarea>
        <textarea name="text_content" rows="4" placeholder="Plain text fallback" class="rounded bg-slate-950 p-2"></textarea>
        <button class="rounded bg-blue-600 px-4 py-2">Save template</button>
    </form>
    <div class="mt-4 grid gap-3">
        @forelse($templates as $template)<div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><strong>{{ $template->name }}</strong><div class="text-slate-400">{{ $template->subject }}</div></div>@empty<div class="text-slate-400">No templates yet.</div>@endforelse
    </div>
</x-layouts.app>
