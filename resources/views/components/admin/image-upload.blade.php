@props(['name', 'label', 'value' => null, 'path' => null, 'hint' => null])

<div>
    <label class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    <div class="flex items-start gap-4">
        <div class="flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-slate-300 bg-slate-50" id="preview-{{ $name }}">
            @if ($value || $path)
                <img src="{{ $path ? asset('storage/'.$path) : (is_string($value) && $value ? asset('storage/'.$value) : '') }}" class="h-full w-full object-cover" alt="Preview">
            @else
                <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            @endif
        </div>
        <div class="flex-1">
            <input type="file" name="{{ $name }}" id="{{ $name }}" accept="image/*"
                onchange="previewImage(this, 'preview-{{ $name }}')"
                class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
            @if ($hint)
                <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
            @endif
            @error($name)
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
