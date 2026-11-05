@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="required">*</span>@endif</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        {{ $required ? 'required' : '' }}
    >
    @if($hint && !$errors->has($name))
        <div class="form-hint">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
    @enderror
</div>
