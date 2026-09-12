@props(['label', 'name' => null, 'required' => false, 'hint' => null])

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">
        {{ $label }} @if ($required)<span class="text-red-500">*</span>@endif
    </label>
    {{ $slot }}
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
