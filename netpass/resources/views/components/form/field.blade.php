@props(['name', 'label', 'required' => false, 'hint' => null])

@php
    $hasError = $errors->has($name);
    $fieldId = $attributes->get('id') ?? $name;
@endphp

<div class="form-field">
    <label for="{{ $fieldId }}">{{ $label }} @if ($required)<span class="required-mark">*</span>@endif</label>
    {{ $slot }}
    @if ($hint && ! $hasError)
        <small class="field-hint">{{ $hint }}</small>
    @endif
    @error($name)
        <span class="field-error" id="{{ $fieldId }}-error">{{ $message }}</span>
    @enderror
</div>
