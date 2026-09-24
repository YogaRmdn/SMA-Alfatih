@props(['name', 'label', 'checked' => true])

<label class="inline-flex cursor-pointer items-center gap-2">
    <input type="checkbox" name="{{ $name }}" value="1"
        {{ $errors->any() ? old($name) : ($checked ? 'checked' : '') }}
        {{ $attributes->merge(['class' => 'rounded border-slate-300 text-emerald-600 focus:ring-emerald-500']) }}>
    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
</label>
