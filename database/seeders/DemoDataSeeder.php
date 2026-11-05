<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Building;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\Facility;
use App\Models\Floor;
use App\Models\Inventory;
use App\Models\MaintenanceTicket;
use App\Models\Meter;
use App\Models\PenaltyConfig;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CheckInService;
use App\Services\CheckOutService;
use App\Services\MeterReadingService;
use App\Services\NumberGeneratorService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    protected \Faker\Generator $faker;

    protected int $adminId;

    public function run(): void
    {
        $this->faker = \Faker\Factory::create('id_ID');
        $this->adminId = User::where('email', 'admin@kostmanager.test')->value('id');

        $facilities = $this->seedFacilities();
        [$properties, $roomTypes, $rooms] = $this->seedProperties($facilities);
        $this->seedPenaltyConfig();
        $tenants = $this->seedTenants();
        $contracts = $this->seedLeasing($tenants, $rooms);
        $this->seedMetersAndReadings($rooms);
        $this->seedExpenses($properties);
        $this->seedMaintenance($properties, $rooms, $tenants);
        $this->seedComplaints($properties, $rooms, $tenants);
        $this->seedInventory($properties, $rooms);
        $this->seedAnnouncements($properties);
    }

    protected function seedFacilities(): array
    {
        $items = [
            ['name' => 'AC', 'category' => 'Kamar', 'icon' => 'fa-solid fa-snowflake'],
            ['name' => 'WiFi', 'category' => 'Umum', 'icon' => 'fa-solid fa-wifi'],
            ['name' => 'TV', 'category' => 'Kamar', 'icon' => 'fa-solid fa-tv'],
            ['name' => 'Kasur', 'category' => 'Kamar', 'icon' => 'fa-solid fa-bed'],
            ['name' => 'Lemari', 'category' => 'Kamar', 'icon' => 'fa-solid fa-door-closed'],
            ['name' => 'Meja Belajar', 'category' => 'Kamar', 'icon' => 'fa-solid fa-table'],
            ['name' => 'Kamar Mandi Dalam', 'category' => 'Kamar', 'icon' => 'fa-solid fa-bath'],
            ['name' => 'Parkir Motor', 'category' => 'Umum', 'icon' => 'fa-solid fa-square-parking'],
            ['name' => 'Dapur Bersama', 'category' => 'Umum', 'icon' => 'fa-solid fa-kitchen-set'],
            ['name' => 'Laundry', 'category' => 'Umum', 'icon' => 'fa-solid fa-shirt'],
            ['name' => 'CCTV', 'category' => 'Keamanan', 'icon' => 'fa-solid fa-video'],
            ['name' => 'Water Heater', 'category' => 'Kamar', 'icon' => 'fa-solid fa-droplet'],
        ];

        return collect($items)->map(fn ($item) => Facility::firstOrCreate(['name' => $item['name']], $item + ['status' => 'active']))->all();
    }

    protected function seedProperties(array $facilities): array
    {
        $propertiesData = [
            [
                'name' => 'Kost Melati', 'code' => 'KM01', 'city' => 'Jakarta Selatan', 'province' => 'DKI Jakarta',
                'address' => 'Jl. Melati Raya No. 10, Kebayoran Baru', 'postal_code' => '12180',
                'phone' => '021-7654321', 'email' => 'kostmelati@kostmanager.test',
                'bank_name' => 'BCA', 'bank_account_number' => '1234567890', 'bank_account_name' => 'PT Kost Sejahtera',
                'rules' => "1. Dilarang membawa tamu lawan jenis masuk kamar.\n2. Jam malam pukul 22.00 WIB.\n3. Dilarang merokok di dalam kamar.\n4. Wajib menjaga kebersihan area bersama.",
            ],
            [
                'name' => 'Kost Anggrek', 'code' => 'KA01', 'city' => 'Bandung', 'province' => 'Jawa Barat',
                'address' => 'Jl. Anggrek Indah No. 25, Coblong', 'postal_code' => '40132',
                'phone' => '022-1234567', 'email' => 'kostanggrek@kostmanager.test',
                'bank_name' => 'Mandiri', 'bank_account_number' => '0987654321', 'bank_account_name' => 'PT Kost Sejahtera',
                'rules' => "1. Dilarang membawa tamu lawan jenis masuk kamar.\n2. Jam malam pukul 22.00 WIB.\n3. Dilarang merokok di dalam kamar.\n4. Wajib menjaga kebersihan area bersama.",
            ],
        ];

        $properties = [];
        $roomTypes = [];
        $rooms = [];

        foreach ($propertiesData as $data) {
            $property = Property::firstOrCreate(['code' => $data['code']], $data + ['status' => 'active', 'created_by' => $this->adminId]);
            $properties[] = $property;

            $propertyRoomTypes = [
                RoomType::firstOrCreate(['property_id' => $property->id, 'code' => 'STD'], [
                    'name' => 'Standard', 'monthly_price' => 1200000, 'weekly_price' => 350000, 'daily_price' => 60000,
                    'deposit_amount' => 1200000, 'capacity' => 1, 'size' => '3x3 m', 'status' => 'active',
                    'description' => 'Kamar standar dengan fasilitas dasar.',
                ]),
                RoomType::firstOrCreate(['property_id' => $property->id, 'code' => 'DLX'], [
                    'name' => 'Deluxe', 'monthly_price' => 1750000, 'weekly_price' => 480000, 'daily_price' => 85000,
                    'deposit_amount' => 1750000, 'capacity' => 1, 'size' => '3x4 m', 'status' => 'active',
                    'description' => 'Kamar deluxe dengan AC dan kamar mandi dalam.',
                ]),
                RoomType::firstOrCreate(['property_id' => $property->id, 'code' => 'VIP'], [
                    'name' => 'VIP', 'monthly_price' => 2500000, 'weekly_price' => 700000, 'daily_price' => 120000,
                    'deposit_amount' => 2500000, 'capacity' => 2, 'size' => '4x4 m', 'status' => 'active',
                    'description' => 'Kamar VIP luas dengan fasilitas lengkap.',
                ]),
            ];

            $propertyRoomTypes[0]->facilities()->syncWithoutDetaching(collect($facilities)->whereIn('name', ['WiFi', 'Kasur', 'Lemari', 'Meja Belajar', 'Parkir Motor'])->pluck('id'));
            $propertyRoomTypes[1]->facilities()->syncWithoutDetaching(collect($facilities)->whereIn('name', ['WiFi', 'AC', 'Kasur', 'Lemari', 'Meja Belajar', 'Kamar Mandi Dalam', 'Parkir Motor'])->pluck('id'));
            $propertyRoomTypes[2]->facilities()->syncWithoutDetaching(collect($facilities)->pluck('id'));

            $roomTypes = array_merge($roomTypes, $propertyRoomTypes);

            $buildingLetters = ['A', 'B'];
            foreach ($buildingLetters as $letter) {
                $building = Building::firstOrCreate(['property_id' => $property->id, 'code' => $letter], [
                    'name' => 'Gedung '.$letter, 'number_of_floors' => 2, 'status' => 'active',
                ]);

                for ($floorNum = 1; $floorNum <= 2; $floorNum++) {
                    $floor = Floor::firstOrCreate(['building_id' => $building->id, 'floor_number' => $floorNum], [
                        'name' => $floorNum === 1 ? 'Lantai Dasar' : 'Lantai '.$floorNum, 'status' => 'active',
                    ]);

                    for ($roomNum = 1; $roomNum <= 3; $roomNum++) {
                        $roomType = $propertyRoomTypes[($roomNum - 1) % 3];
                        $roomNumber = $letter.$floorNum.str_pad($roomNum, 2, '0', STR_PAD_LEFT);

                        $room = Room::firstOrCreate(['property_id' => $property->id, 'room_number' => $roomNumber], [
                            'building_id' => $building->id, 'floor_id' => $floor->id, 'room_type_id' => $roomType->id,
                            'price' => $roomType->monthly_price, 'deposit' => $roomType->deposit_amount,
                            'capacity' => $roomType->capacity, 'status' => 'available',
                        ]);

                        $room->facilities()->syncWithoutDetaching($roomType->facilities->pluck('id'));

                        $rooms[] = $room;
                    }
                }
            }
        }

        return [$properties, $roomTypes, $rooms];
    }

    protected function seedPenaltyConfig(): void
    {
        PenaltyConfig::firstOrCreate(['property_id' => null], [
            'calculation_type' => 'fixed', 'amount' => 25000, 'grace_period_days' => 3, 'status' => 'active',
        ]);
    }

    protected function seedTenants(): array
    {
        $names = [
            ['Budi Santoso', 'male'], ['Siti Nurhaliza', 'female'], ['Andi Wijaya', 'male'],
            ['Dewi Lestari', 'female'], ['Rian Pratama', 'male'], ['Maya Sari', 'female'],
            ['Fajar Ramadhan', 'male'], ['Nur Aini', 'female'], ['Bayu Setiawan', 'male'],
            ['Indah Permata', 'female'],
        ];

        $tenants = [];

        foreach ($names as $i => [$name, $gender]) {
            $tenant = Tenant::firstOrCreate(['full_name' => $name], [
                'tenant_code' => NumberGeneratorService::generate('tenants', 'tenant_code', NumberGeneratorService::tenantPrefix()),
                'nik' => $this->faker->unique()->numerify('35##############'),
                'phone' => '08'.$this->faker->numerify('##########'),
                'email' => strtolower(str_replace(' ', '.', $name)).'@example.test',
                'gender' => $gender,
                'birth_place' => $this->faker->city(),
                'birth_date' => Carbon::now()->subYears(rand(20, 30))->subDays(rand(0, 365)),
                'address' => $this->faker->address(),
                'occupation' => $this->faker->randomElement(['Karyawan Swasta', 'Mahasiswa', 'Wiraswasta', 'PNS', 'Freelancer']),
                'company' => $this->faker->company(),
                'emergency_contact_name' => $this->faker->name(),
                'emergency_contact_phone' => '08'.$this->faker->numerify('##########'),
                'emergency_contact_relation' => $this->faker->randomElement(['Orang Tua', 'Saudara', 'Suami/Istri']),
                'status' => 'active',
                'created_by' => $this->adminId,
            ]);

            if ($i === 0) {
                $user = User::firstOrCreate(['email' => 'tenant@kostmanager.test'], [
                    'name' => $name, 'password' => Hash::make('password'), 'email_verified_at' => now(), 'status' => 'active',
                ]);
                if (! $user->hasRole('Tenant')) {
                    $user->assignRole('Tenant');
                }
                $tenant->update(['user_id' => $user->id]);
            }

            $tenants[] = $tenant;
        }

        return $tenants;
    }

    protected function seedLeasing(array $tenants, array $rooms): array
    {
        $checkInService = app(CheckInService::class);
        $checkOutService = app(CheckOutService::class);
        $paymentService = app(PaymentService::class);
        $contracts = [];

        // 7 tenants checked in, spread across rooms
        for ($i = 0; $i < 7; $i++) {
            $tenant = $tenants[$i];
            $room = $rooms[$i];

            $checkInDate = Carbon::now()->subMonths(rand(1, 4))->startOfMonth()->addDays(rand(0, 4));
            $billingDate = $checkInDate->copy()->addMonth()->day(5);

            $contract = $checkInService->process([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'billing_date' => $billingDate->format('Y-m-d'),
                'monthly_price' => $room->price,
                'deposit' => $room->deposit,
                'duration_months' => 12,
                'terms' => "Pembayaran sewa dilakukan setiap tanggal 5.\nDeposit dikembalikan maksimal 7 hari setelah checkout dikurangi potongan (jika ada).",
                'notes' => 'Data demo check-in',
            ], $this->adminId);

            $contracts[] = $contract;

            // Pay the first invoice for most tenants (simulate history), leave a couple unpaid/partial/overdue
            $firstInvoice = $contract->invoices()->first();
            if ($firstInvoice) {
                if ($i < 4) {
                    $this->payInvoiceFully($paymentService, $firstInvoice, $checkInDate->copy()->addDays(2));
                } elseif ($i === 4) {
                    $this->payInvoicePartially($paymentService, $firstInvoice, $checkInDate->copy()->addDays(2));
                }
                // i=5,6 left unpaid intentionally (will become overdue via billing:generate)
            }
        }

        // 1 tenant with booking only (not checked in)
        \App\Models\Booking::firstOrCreate(['tenant_id' => $tenants[7]->id, 'room_id' => $rooms[7]->id], [
            'booking_number' => NumberGeneratorService::generate('bookings', 'booking_number', NumberGeneratorService::bookingPrefix()),
            'booking_date' => Carbon::now()->subDays(3),
            'check_in_date' => Carbon::now()->addDays(5),
            'monthly_price' => $rooms[7]->price,
            'deposit' => $rooms[7]->deposit,
            'status' => 'confirmed',
            'notes' => 'Menunggu proses check-in.',
            'created_by' => $this->adminId,
        ]);
        $rooms[7]->update(['status' => 'reserved']);

        // 1 tenant checked in then checked out (former tenant)
        $formerTenant = $tenants[8];
        $formerRoom = $rooms[8];
        $ciDate = Carbon::now()->subMonths(6);
        $formerContract = $checkInService->process([
            'tenant_id' => $formerTenant->id,
            'room_id' => $formerRoom->id,
            'check_in_date' => $ciDate->format('Y-m-d'),
            'billing_date' => $ciDate->copy()->addMonth()->day(5)->format('Y-m-d'),
            'monthly_price' => $formerRoom->price,
            'deposit' => $formerRoom->deposit,
            'duration_months' => 3,
            'notes' => 'Data demo mantan penghuni',
        ], $this->adminId);

        $firstInvoice = $formerContract->invoices()->first();
        if ($firstInvoice) {
            $this->payInvoiceFully($paymentService, $firstInvoice, $ciDate->copy()->addDays(2));
        }

        $checkOutService->process([
            'contract_id' => $formerContract->id,
            'checkout_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'damage_cost' => 0,
            'penalty_amount' => 0,
            'reason' => 'Pindah kerja ke luar kota',
            'notes' => 'Checkout demo, deposit dikembalikan penuh.',
        ], $this->adminId);

        return $contracts;
    }

    protected function payInvoiceFully(PaymentService $service, $invoice, Carbon $date): void
    {
        $payment = \App\Models\Payment::create([
            'payment_number' => NumberGeneratorService::generate('payments', 'payment_number', NumberGeneratorService::paymentPrefix()),
            'invoice_id' => $invoice->id,
            'tenant_id' => $invoice->tenant_id,
            'amount' => $invoice->remaining_amount,
            'payment_date' => $date,
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'qris']),
            'reference_number' => strtoupper($this->faker->bothify('TRX-????####')),
            'status' => 'pending',
            'created_by' => $this->adminId,
        ]);

        $service->verify($payment, $this->adminId);
    }

    protected function payInvoicePartially(PaymentService $service, $invoice, Carbon $date): void
    {
        $payment = \App\Models\Payment::create([
            'payment_number' => NumberGeneratorService::generate('payments', 'payment_number', NumberGeneratorService::paymentPrefix()),
            'invoice_id' => $invoice->id,
            'tenant_id' => $invoice->tenant_id,
            'amount' => round($invoice->remaining_amount / 2, -3),
            'payment_date' => $date,
            'payment_method' => 'cash',
            'status' => 'pending',
            'created_by' => $this->adminId,
        ]);

        $service->verify($payment, $this->adminId);
    }

    protected function seedMetersAndReadings(array $rooms): void
    {
        foreach (array_slice($rooms, 0, 8) as $room) {
            $electricity = Meter::firstOrCreate(['room_id' => $room->id, 'meter_type' => 'electricity'], [
                'meter_number' => 'PLN-'.$room->room_number, 'unit' => 'kWh', 'tariff' => 1500, 'status' => 'active',
            ]);
            $water = Meter::firstOrCreate(['room_id' => $room->id, 'meter_type' => 'water'], [
                'meter_number' => 'PDAM-'.$room->room_number, 'unit' => 'm3', 'tariff' => 8000, 'status' => 'active',
            ]);

            app(MeterReadingService::class)->recordReading($electricity, [
                'reading_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'current_value' => rand(100, 400),
                'tariff' => 1500,
            ], $this->adminId);

            app(MeterReadingService::class)->recordReading($water, [
                'reading_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'current_value' => rand(10, 40),
                'tariff' => 8000,
            ], $this->adminId);
        }
    }

    protected function seedExpenses(array $properties): void
    {
        $categories = ['electricity', 'water', 'internet', 'salary', 'maintenance', 'cleaning', 'security', 'operational'];

        foreach ($properties as $property) {
            for ($i = 0; $i < 8; $i++) {
                $category = $categories[array_rand($categories)];

                Expense::create([
                    'expense_number' => NumberGeneratorService::generate('expenses', 'expense_number', 'EXP-'),
                    'property_id' => $property->id,
                    'category' => $category,
                    'date' => Carbon::now()->subDays(rand(1, 90)),
                    'description' => 'Pengeluaran '.$category.' untuk '.$property->name,
                    'amount' => rand(150, 2500) * 1000,
                    'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer']),
                    'vendor' => $this->faker->company(),
                    'created_by' => $this->adminId,
                ]);
            }
        }
    }

    protected function seedMaintenance(array $properties, array $rooms, array $tenants): void
    {
        $items = [
            ['AC bocor', 'AC di kamar menetes air', 'high', 'in_progress'],
            ['Lampu mati', 'Lampu kamar mandi tidak menyala', 'medium', 'resolved'],
            ['Keran air rusak', 'Keran wastafel tidak bisa dimatikan', 'urgent', 'open'],
            ['Pintu lemari lepas', 'Engsel pintu lemari lepas', 'low', 'closed'],
            ['WiFi lambat', 'Koneksi WiFi di lantai 2 lambat', 'medium', 'assigned'],
            ['Cat dinding mengelupas', 'Dinding kamar catnya mengelupas', 'low', 'open'],
        ];

        foreach ($items as $i => [$title, $desc, $priority, $status]) {
            $room = $rooms[$i % count($rooms)];

            $ticket = MaintenanceTicket::create([
                'ticket_number' => NumberGeneratorService::generate('maintenance_tickets', 'ticket_number', 'MNT-'),
                'property_id' => $room->property_id,
                'room_id' => $room->id,
                'category' => $this->faker->randomElement(['Listrik', 'Plumbing', 'AC', 'Furnitur', 'Jaringan']),
                'title' => $title,
                'description' => $desc,
                'priority' => $priority,
                'status' => $status,
                'resolved_at' => in_array($status, ['resolved', 'closed']) ? Carbon::now()->subDays(rand(1, 10)) : null,
                'created_by' => $this->adminId,
            ]);

            if (in_array($status, ['in_progress', 'resolved', 'closed'])) {
                $ticket->comments()->create(['user_id' => $this->adminId, 'comment' => 'Sudah dijadwalkan untuk diperbaiki oleh teknisi.']);
            }
        }
    }

    protected function seedComplaints(array $properties, array $rooms, array $tenants): void
    {
        $items = [
            ['Area parkir kotor', 'Mohon area parkir dibersihkan', 'medium', 'open'],
            ['Suara berisik', 'Tetangga kamar sering berisik malam hari', 'high', 'in_progress'],
            ['Sampah menumpuk', 'Tempat sampah bersama jarang diangkut', 'medium', 'resolved'],
            ['Air PDAM keruh', 'Air keran beberapa hari ini keruh', 'high', 'assigned'],
        ];

        foreach ($items as $i => [$title, $desc, $priority, $status]) {
            $tenant = $tenants[$i % 7];
            $room = $rooms[$i % count($rooms)];

            $complaint = Complaint::create([
                'ticket_number' => NumberGeneratorService::generate('complaints', 'ticket_number', 'CPL-'),
                'tenant_id' => $tenant->id,
                'property_id' => $room->property_id,
                'room_id' => $room->id,
                'category' => $this->faker->randomElement(['Kebersihan', 'Keamanan', 'Fasilitas', 'Kenyamanan']),
                'title' => $title,
                'description' => $desc,
                'priority' => $priority,
                'status' => $status,
                'resolved_at' => $status === 'resolved' ? Carbon::now()->subDays(rand(1, 5)) : null,
            ]);

            if (in_array($status, ['in_progress', 'resolved', 'assigned'])) {
                $complaint->comments()->create(['user_id' => $this->adminId, 'comment' => 'Terima kasih atas laporannya, sedang kami tindak lanjuti.']);
            }
        }
    }

    protected function seedInventory(array $properties, array $rooms): void
    {
        $items = [
            ['Elektronik', 'AC Split 1/2 PK'], ['Elektronik', 'TV LED 24 inch'], ['Furnitur', 'Kasur Busa 120x200'],
            ['Furnitur', 'Lemari Pakaian 2 Pintu'], ['Furnitur', 'Meja Belajar'], ['Elektronik', 'Water Heater'],
            ['Keamanan', 'CCTV Indoor'], ['Elektronik', 'Router WiFi'],
        ];

        foreach ($items as $i => [$category, $name]) {
            $room = $rooms[$i % count($rooms)];

            Inventory::create([
                'inventory_code' => NumberGeneratorService::generate('inventories', 'inventory_code', 'INV-AST-'),
                'property_id' => $room->property_id,
                'room_id' => $room->id,
                'category' => $category,
                'name' => $name,
                'serial_number' => strtoupper($this->faker->bothify('SN-####????')),
                'purchase_date' => Carbon::now()->subMonths(rand(3, 24)),
                'purchase_price' => rand(300, 3500) * 1000,
                'condition' => $this->faker->randomElement(['good', 'good', 'good', 'minor_damage']),
                'status' => 'active',
            ]);
        }
    }

    protected function seedAnnouncements(array $properties): void
    {
        Announcement::create([
            'title' => 'Selamat Datang di KostManager',
            'content' => 'Sistem manajemen kost terbaru kini aktif. Silakan gunakan portal ini untuk melihat tagihan, mengajukan pembayaran, dan menghubungi pengelola.',
            'target_type' => 'all',
            'status' => 'published',
            'publish_at' => Carbon::now()->subDays(10),
            'created_by' => $this->adminId,
        ]);

        Announcement::create([
            'title' => 'Pemeliharaan Tangki Air',
            'content' => 'Akan ada pemeliharaan tangki air pada tanggal 20 bulan ini pukul 09.00 - 12.00 WIB. Mohon maaf atas ketidaknyamanannya.',
            'target_type' => 'property',
            'property_id' => $properties[0]->id,
            'status' => 'published',
            'publish_at' => Carbon::now()->subDays(2),
            'created_by' => $this->adminId,
        ]);

        Announcement::create([
            'title' => 'Jadwal Pembayaran Sewa',
            'content' => 'Mengingatkan kembali bahwa pembayaran sewa jatuh tempo setiap tanggal 5. Keterlambatan akan dikenakan denda sesuai ketentuan.',
            'target_type' => 'all',
            'status' => 'published',
            'publish_at' => Carbon::now()->subDays(20),
            'created_by' => $this->adminId,
        ]);
    }
}
