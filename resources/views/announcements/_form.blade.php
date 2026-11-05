@php $announcement = $announcement ?? null; @endphp

<x-input label="Judul" name="title" :value="$announcement->title ?? ''" required />
<x-textarea label="Isi Pengumuman" name="content" :value="$announcement->content ?? ''" required rows="6" />

<div class="form-section-title">Target Penerima</div>
<x-select label="Target" name="target_type" id="target_type" required :options="['all' => 'Semua Penghuni', 'property' => 'Properti Tertentu', 'building' => 'Gedung Tertentu', 'floor' => 'Lantai Tertentu', 'room' => 'Kamar Tertentu']" :value="$announcement->target_type ?? 'all'" />

<div id="target-property" class="target-field">
    <x-select label="Properti" name="property_id" :options="$properties->pluck('name', 'id')" :value="$announcement->property_id ?? ''" />
</div>
<div id="target-building" class="target-field">
    <x-select label="Gedung" name="building_id" :options="$buildings->pluck('name', 'id')" :value="$announcement->building_id ?? ''" />
</div>
<div id="target-floor" class="target-field">
    <x-select label="Lantai" name="floor_id" :options="$floors->mapWithKeys(fn($f) => [$f->id => 'Lantai '.$f->floor_number])" :value="$announcement->floor_id ?? ''" />
</div>
<div id="target-room" class="target-field">
    <x-select label="Kamar" name="room_id" :options="$rooms->pluck('room_number', 'id')" :value="$announcement->room_id ?? ''" />
</div>

<div class="form-section-title">Jadwal</div>
<div class="form-row">
    <x-input label="Tanggal Publish" name="publish_at" type="datetime-local" :value="$announcement?->publish_at?->format('Y-m-d\TH:i') ?? ''" hint="Kosongkan untuk publish langsung" />
    <x-input label="Tanggal Berakhir" name="expired_at" type="datetime-local" :value="$announcement?->expired_at?->format('Y-m-d\TH:i') ?? ''" />
</div>

<x-select label="Status" name="status" required :options="['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']" :value="$announcement->status ?? 'published'" />

<script>
    (function() {
        var select = document.getElementById('target_type');
        var fields = {
            all: [],
            property: ['target-property'],
            building: ['target-property', 'target-building'],
            floor: ['target-property', 'target-building', 'target-floor'],
            room: ['target-property', 'target-building', 'target-floor', 'target-room'],
        };
        function sync() {
            document.querySelectorAll('.target-field').forEach(function(el) { el.style.display = 'none'; });
            (fields[select.value] || []).forEach(function(id) {
                document.getElementById(id).style.display = 'block';
            });
        }
        select.addEventListener('change', sync);
        sync();
    })();
</script>
