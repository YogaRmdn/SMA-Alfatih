@props(['name', 'options' => [], 'value' => null, 'placeholder' => null])

<select name="{{ $name }}" id="{{ $name }}"
    {{ $attributes->merge(['class' => 'w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500']) }}>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach ($options as $key => $label)
        <option value="{{ $key }}" {{ (string) old($name, $value) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>
