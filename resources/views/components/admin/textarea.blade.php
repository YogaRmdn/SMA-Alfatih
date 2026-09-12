@props(['name', 'value' => '', 'rows' => 4, 'placeholder' => null])

<textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500']) }}>{{ old($name, $value) }}</textarea>
