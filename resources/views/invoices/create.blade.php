<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Tagihan', 'url' => route('invoices.index')], ['label' => 'Buat Tagihan']]">
    <x-page-header title="Buat Tagihan Manual" />

    <div class="card" style="max-width: 900px;">
        <div class="card-body">
            <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
                @csrf

                <div class="form-row">
                    <x-select label="Kontrak Aktif" name="contract_id" id="contract_id" :options="$contracts->mapWithKeys(fn($c) => [$c->id => $c->contract_number.' — '.$c->tenant->full_name.' ('.$c->room->room_number.')'])" hint="Opsional, akan mengisi otomatis penghuni & kamar" />
                </div>

                <div class="form-row">
                    <x-select label="Penghuni" name="tenant_id" id="tenant_id" required :options="$tenants->mapWithKeys(fn($t) => [$t->id => $t->full_name.' ('.$t->tenant_code.')'])" />
                    <x-select label="Kamar" name="room_id" id="room_id" required :options="$rooms->mapWithKeys(fn($r) => [$r->id => $r->room_number])" />
                </div>

                <div class="form-row">
                    <x-input label="Tanggal Terbit" name="issue_date" type="date" :value="now()->format('Y-m-d')" required />
                    <x-input label="Jatuh Tempo" name="due_date" type="date" required />
                </div>

                <div class="form-section-title">Item Tagihan</div>
                <div id="item-rows"></div>
                <button type="button" class="btn btn-outline btn-sm mb-16" id="add-item-row"><i class="fa-solid fa-plus"></i> Tambah Item</button>

                <div class="form-row">
                    <x-input label="Diskon (Rp)" name="discount" type="number" min="0" value="0" />
                    <x-input label="Pajak (Rp)" name="tax" type="number" min="0" value="0" />
                </div>

                <x-textarea label="Catatan" name="notes" />

                <div class="form-actions">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Tagihan</x-button>
                </div>
            </form>
        </div>
    </div>

    <template id="item-row-template">
        <div class="form-row item-row" style="grid-template-columns: 1.2fr 2fr 0.7fr 1fr auto; align-items:end;">
            <div class="form-group">
                <label class="form-label">Jenis</label>
                <select name="items[__i__][item_type]" class="form-control" required>
                    <option value="rent">Sewa</option>
                    <option value="electricity">Listrik</option>
                    <option value="water">Air</option>
                    <option value="internet">Internet</option>
                    <option value="parking">Parkir</option>
                    <option value="laundry">Laundry</option>
                    <option value="cleaning">Kebersihan</option>
                    <option value="penalty">Denda</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <input type="text" name="items[__i__][description]" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Qty</label>
                <input type="number" name="items[__i__][quantity]" class="form-control" value="1" min="0.01" step="0.01" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga</label>
                <input type="number" name="items[__i__][price]" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-ghost btn-icon remove-item-row"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
    </template>

    <script>
        var contractsData = @json($contracts->mapWithKeys(fn($c) => [$c->id => ['tenant_id' => $c->tenant_id, 'room_id' => $c->room_id]]));
        var itemIndex = 0;
        var container = document.getElementById('item-rows');
        var template = document.getElementById('item-row-template');

        function addItemRow() {
            var clone = template.content.cloneNode(true);
            clone.querySelectorAll('[name]').forEach(function(el) {
                el.name = el.name.replace('__i__', itemIndex);
            });
            container.appendChild(clone);
            itemIndex++;
        }

        document.getElementById('add-item-row').addEventListener('click', addItemRow);
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-row')) {
                e.target.closest('.item-row').remove();
            }
        });

        document.getElementById('contract_id').addEventListener('change', function() {
            var data = contractsData[this.value];
            if (data) {
                document.getElementById('tenant_id').value = data.tenant_id;
                document.getElementById('room_id').value = data.room_id;
            }
        });

        addItemRow();
    </script>
</x-layouts.app>
