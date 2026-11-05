@props(['label' => null, 'name', 'value' => null, 'required' => false, 'rows' => 4, 'hint' => null])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="required">*</span>@endif</label>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        {{ $required ? 'required' : '' }}
    >{{ old($name, $value) }}</textarea>
    @if($hint && !$errors->has($name))
        <div class="form-hint">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
    @enderror
</div>
