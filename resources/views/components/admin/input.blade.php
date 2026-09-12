@props(['name', 'type' => 'text', 'value' => '', 'placeholder' => null])

<input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500']) }}>
