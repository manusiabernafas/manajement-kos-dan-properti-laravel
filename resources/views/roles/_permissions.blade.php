@php $checkedIds = $checkedIds ?? []; @endphp

<div class="grid grid-cols-2">
    @foreach($permissions as $group => $groupPermissions)
        <div class="card" style="box-shadow:none; border-color: var(--color-border);">
            <div class="card-header"><h3>{{ ucfirst(str_replace('-', ' ', $group)) }}</h3></div>
            <div class="card-body">
                @foreach($groupPermissions as $permission)
                    <x-checkbox :label="$permission->name" name="permissions[]" :value="$permission->id" :checked="in_array($permission->id, $checkedIds)" />
                @endforeach
            </div>
        </div>
    @endforeach
</div>
