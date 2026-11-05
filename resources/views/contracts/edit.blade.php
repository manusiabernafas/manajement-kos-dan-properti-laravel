<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Kontrak', 'url' => route('contracts.index')], ['label' => $contract->contract_number, 'url' => route('contracts.show', $contract)], ['label' => 'Edit']]">
    <x-page-header title="Edit Kontrak" :description="$contract->contract_number" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('contracts.update', $contract) }}" method="POST">
                @csrf
                @method('PUT')
                <x-input label="Tanggal Berakhir" name="end_date" type="date" :value="$contract->end_date->format('Y-m-d')" required />
                <x-input label="Harga Bulanan (Rp)" name="monthly_price" type="number" min="0" :value="$contract->monthly_price" required />
                <x-input label="Tanggal Billing" name="billing_day" type="number" min="1" max="31" :value="$contract->billing_day" required />
                <x-select label="Status" name="status" required :options="['draft' => 'Draft', 'active' => 'Aktif', 'expired' => 'Expired', 'terminated' => 'Terminated']" :value="$contract->status" />
                <x-textarea label="Syarat & Ketentuan" name="terms" :value="$contract->terms" rows="6" />
                <div class="form-actions">
                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
