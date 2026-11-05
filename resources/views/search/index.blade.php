<x-layouts.app :title="$title">
    <x-page-header title="Hasil Pencarian" :description="'Menampilkan hasil untuk &quot;' . $q . '&quot;'" />

    @php $hasResults = collect($results)->flatten()->count() > 0; @endphp

    @if(!$hasResults)
        <x-empty-state icon="fa-solid fa-magnifying-glass" title="Tidak ada hasil" description="Coba kata kunci lain untuk menemukan data yang Anda cari." />
    @else
        @if(($results['tenants'] ?? collect())->count())
            <div class="search-results-group">
                <h4>Penghuni</h4>
                <div class="card">
                    @foreach($results['tenants'] as $tenant)
                        <a href="{{ route('tenants.show', $tenant) }}" class="search-result-item">
                            <i class="fa-solid fa-user"></i> <span>{{ $tenant->full_name }} <span class="text-muted">({{ $tenant->tenant_code }})</span></span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($results['rooms'] ?? collect())->count())
            <div class="search-results-group">
                <h4>Kamar</h4>
                <div class="card">
                    @foreach($results['rooms'] as $room)
                        <a href="{{ route('rooms.show', $room) }}" class="search-result-item">
                            <i class="fa-solid fa-door-open"></i> <span>{{ $room->room_number }} {{ $room->room_name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($results['invoices'] ?? collect())->count())
            <div class="search-results-group">
                <h4>Tagihan</h4>
                <div class="card">
                    @foreach($results['invoices'] as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}" class="search-result-item">
                            <i class="fa-solid fa-file-invoice-dollar"></i> <span>{{ $invoice->invoice_number }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($results['payments'] ?? collect())->count())
            <div class="search-results-group">
                <h4>Pembayaran</h4>
                <div class="card">
                    @foreach($results['payments'] as $payment)
                        <a href="{{ route('payments.show', $payment) }}" class="search-result-item">
                            <i class="fa-solid fa-money-check-dollar"></i> <span>{{ $payment->payment_number }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($results['bookings'] ?? collect())->count())
            <div class="search-results-group">
                <h4>Booking</h4>
                <div class="card">
                    @foreach($results['bookings'] as $booking)
                        <a href="{{ route('bookings.show', $booking) }}" class="search-result-item">
                            <i class="fa-solid fa-calendar-check"></i> <span>{{ $booking->booking_number }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($results['contracts'] ?? collect())->count())
            <div class="search-results-group">
                <h4>Kontrak</h4>
                <div class="card">
                    @foreach($results['contracts'] as $contract)
                        <a href="{{ route('contracts.show', $contract) }}" class="search-result-item">
                            <i class="fa-solid fa-file-signature"></i> <span>{{ $contract->contract_number }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($results['maintenance'] ?? collect())->count())
            <div class="search-results-group">
                <h4>Maintenance</h4>
                <div class="card">
                    @foreach($results['maintenance'] as $item)
                        <a href="{{ route('maintenance.show', $item) }}" class="search-result-item">
                            <i class="fa-solid fa-screwdriver-wrench"></i> <span>{{ $item->ticket_number }} — {{ $item->title }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($results['complaints'] ?? collect())->count())
            <div class="search-results-group">
                <h4>Komplain</h4>
                <div class="card">
                    @foreach($results['complaints'] as $item)
                        <a href="{{ route('complaints.show', $item) }}" class="search-result-item">
                            <i class="fa-solid fa-comment-dots"></i> <span>{{ $item->ticket_number }} — {{ $item->title }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</x-layouts.app>
