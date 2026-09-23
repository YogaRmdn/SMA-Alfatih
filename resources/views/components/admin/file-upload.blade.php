@props(['name', 'label', 'value' => null, 'path' => null, 'accept' => '*', 'hint' => null, 'required' => false])

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }} @if ($required)<span class="text-red-500">*</span>@endif</label>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        @if ($value || $path)
            <a href="{{ $path ? asset('storage/'.$path) : (is_string($value) ? asset('storage/'.$value) : '#') }}" target="_blank"
               class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-600 hover:border-emerald-300">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                File saat ini
            </a>
        @endif
        <input type="file" name="{{ $name }}" id="{{ $name }}" accept="{{ $accept }}"
            class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
    </div>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
