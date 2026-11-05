@props(['label' => null, 'name', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => '-- Pilih --', 'hint' => null])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="required">*</span>@endif</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        {{ $required ? 'required' : '' }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected((string) old($name, $value) === (string) $optValue)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @if($hint && !$errors->has($name))
        <div class="form-hint">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
    @enderror
</div>
