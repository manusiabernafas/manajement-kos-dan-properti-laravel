@php $room = $room ?? null; @endphp

<div class="form-row">
    <x-select label="Properti" name="property_id" id="property_id" required :options="$properties" :value="$room->property_id ?? $selected['property_id'] ?? ''" />
    <x-select label="Tipe Kamar" name="room_type_id" id="room_type_id" required :options="$roomTypes->pluck('name', 'id')" :value="$room->room_type_id ?? ''" />
</div>

<div class="form-row">
    <x-select label="Gedung" name="building_id" id="building_id" required :options="$buildings->pluck('name', 'id')" :value="$room->building_id ?? $selected['building_id'] ?? ''" />
    <x-select label="Lantai" name="floor_id" id="floor_id" required :options="$floors->pluck('floor_number', 'id')" :value="$room->floor_id ?? $selected['floor_id'] ?? ''" />
</div>

<div class="form-row">
    <x-input label="Nomor Kamar" name="room_number" :value="$room->room_number ?? ''" required />
    <x-input label="Nama Kamar" name="room_name" :value="$room->room_name ?? ''" hint="Opsional" />
</div>

<div class="form-row">
    <x-input label="Harga (Rp)" name="price" type="number" min="0" :value="$room->price ?? ''" required />
    <x-input label="Deposit (Rp)" name="deposit" type="number" min="0" :value="$room->deposit ?? 0" required />
</div>

<div class="form-row">
    <x-input label="Kapasitas (orang)" name="capacity" type="number" min="1" :value="$room->capacity ?? 1" required />
    <x-select label="Status" name="status" required :options="['available' => 'Available', 'occupied' => 'Occupied', 'reserved' => 'Reserved', 'maintenance' => 'Maintenance', 'inactive' => 'Inactive']" :value="$room->status ?? 'available'" hint="Status akan otomatis berubah saat check-in/check-out" />
</div>

<x-textarea label="Deskripsi" name="description" :value="$room->description ?? ''" />

<div class="form-section-title">Fasilitas Kamar</div>
<div class="grid grid-cols-3">
    @foreach($facilities as $facility)
        <x-checkbox :label="$facility->name" name="facilities[]" :value="$facility->id" :checked="in_array($facility->id, $selectedFacilities ?? [])" />
    @endforeach
</div>

<script>
(function() {
    var buildingsData = @json($buildings->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'property_id' => $b->property_id]));
    var floorsData = @json($floors->map(fn($f) => ['id' => $f->id, 'label' => 'Lantai '.$f->floor_number.($f->name ? ' - '.$f->name : ''), 'building_id' => $f->building_id]));
    var roomTypesData = @json($roomTypes->map(fn($rt) => ['id' => $rt->id, 'name' => $rt->name, 'property_id' => $rt->property_id]));

    var propertySelect = document.getElementById('property_id');
    var buildingSelect = document.getElementById('building_id');
    var floorSelect = document.getElementById('floor_id');
    var roomTypeSelect = document.getElementById('room_type_id');

    function fillSelect(select, items, valueKey, labelKey, currentValue) {
        var current = currentValue || select.value;
        select.innerHTML = '<option value="">-- Pilih --</option>';
        items.forEach(function(item) {
            var opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = item[labelKey];
            if (String(item[valueKey]) === String(current)) opt.selected = true;
            select.appendChild(opt);
        });
    }

    function syncBuildingsAndTypes(keepValues) {
        var propertyId = propertySelect.value;
        var filteredBuildings = buildingsData.filter(function(b) { return String(b.property_id) === String(propertyId); });
        var filteredTypes = roomTypesData.filter(function(t) { return String(t.property_id) === String(propertyId); });
        fillSelect(buildingSelect, filteredBuildings, 'id', 'name', keepValues ? buildingSelect.dataset.initial : null);
        fillSelect(roomTypeSelect, filteredTypes, 'id', 'name', keepValues ? roomTypeSelect.dataset.initial : null);
        syncFloors(keepValues);
    }

    function syncFloors(keepValues) {
        var buildingId = buildingSelect.value;
        var filteredFloors = floorsData.filter(function(f) { return String(f.building_id) === String(buildingId); });
        fillSelect(floorSelect, filteredFloors, 'id', 'label', keepValues ? floorSelect.dataset.initial : null);
    }

    buildingSelect.dataset.initial = buildingSelect.value;
    floorSelect.dataset.initial = floorSelect.value;
    roomTypeSelect.dataset.initial = roomTypeSelect.value;

    syncBuildingsAndTypes(true);

    propertySelect.addEventListener('change', function() { syncBuildingsAndTypes(false); });
    buildingSelect.addEventListener('change', function() { syncFloors(false); });
})();
</script>
