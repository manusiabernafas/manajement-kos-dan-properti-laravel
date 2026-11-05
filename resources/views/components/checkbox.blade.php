@props(['label' => null, 'name', 'value' => '1', 'checked' => false])

<label class="checkbox-wrap">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked(old($name, $checked))>
    <span>{{ $label }}</span>
</label>
