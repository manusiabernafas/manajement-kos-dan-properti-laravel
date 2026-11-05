<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Fasilitas']]">
    <x-page-header title="Fasilitas" description="Kelola master fasilitas kost">
        <x-slot:actions>
            @can('facilities.create')
                <button type="button" class="btn btn-primary" data-modal-open="modal-add-facility"><i class="fa-solid fa-plus"></i> Tambah Fasilitas</button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari fasilitas...">
                </div>
                <select name="category" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($facilities->isEmpty())
            <x-empty-state icon="fa-solid fa-wifi" title="Belum ada fasilitas" description="Tambahkan fasilitas seperti AC, WiFi, Kasur, dll.">
                <x-slot:action>
                    @can('facilities.create')
                        <button type="button" class="btn btn-primary" data-modal-open="modal-add-facility">+ Tambah Fasilitas</button>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fasilitas</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facilities as $facility)
                            <tr>
                                <td class="table-cell-primary"><i class="{{ $facility->icon ?: 'fa-solid fa-circle-check' }}"></i> {{ $facility->name }}</td>
                                <td>{{ $facility->category }}</td>
                                <td><x-status-badge :status="$facility->status" :colors="['active' => 'success', 'inactive' => 'muted']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif']" /></td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            @can('facilities.update')
                                                <button type="button" class="dropdown-item" data-modal-open="modal-edit-facility-{{ $facility->id }}"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
                                            @endcan
                                            @can('facilities.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('facilities.destroy', $facility)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <x-modal id="modal-edit-facility-{{ $facility->id }}" title="Edit Fasilitas">
                                <form action="{{ route('facilities.update', $facility) }}" method="POST" id="form-edit-facility-{{ $facility->id }}">
                                    @csrf
                                    @method('PUT')
                                    <x-input label="Nama Fasilitas" name="name" :value="$facility->name" required />
                                    <x-input label="Kategori" name="category" :value="$facility->category" required />
                                    <x-input label="Icon (Font Awesome class)" name="icon" :value="$facility->icon" hint="Contoh: fa-solid fa-wifi" />
                                    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$facility->status" />
                                    <x-textarea label="Deskripsi" name="description" :value="$facility->description" />
                                </form>
                                <x-slot:footer>
                                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                                    <button type="submit" form="form-edit-facility-{{ $facility->id }}" class="btn btn-primary">Simpan</button>
                                </x-slot:footer>
                            </x-modal>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $facilities->links() }}
        @endif
    </div>

    <x-modal id="modal-add-facility" title="Tambah Fasilitas">
        <form action="{{ route('facilities.store') }}" method="POST" id="form-add-facility">
            @csrf
            <x-input label="Nama Fasilitas" name="name" required />
            <x-input label="Kategori" name="category" required hint="Contoh: Kamar, Kamar Mandi, Umum" />
            <x-input label="Icon (Font Awesome class)" name="icon" hint="Contoh: fa-solid fa-wifi" />
            <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" value="active" />
            <x-textarea label="Deskripsi" name="description" />
        </form>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
            <button type="submit" form="form-add-facility" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-modal>
</x-layouts.app>
