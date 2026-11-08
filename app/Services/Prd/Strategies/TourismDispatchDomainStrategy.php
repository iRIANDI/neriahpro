<?php

namespace App\Services\Prd\Strategies;

class TourismDispatchDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        $haystack = $title . ' ' . $desc;
        return (bool) (
            preg_match('/(?:bandara|airport|transfer in|transfer out|ngurah rai|\bdps\b|flight|penerbangan|antar jemput)/i', $haystack) ||
            preg_match('/(?:katalog|catalog|(?<!multi-)\bday tour\b|paket wisata|fleet configurator|konfigurasi armada|pilihan armada)/i', $haystack) ||
            preg_match('/(?:\bwishlist\b|\bwish list\b|\bai itinerary\b|generator itinerary|smart itinerary|itinerary generator)/i', $haystack) ||
            preg_match('/(?:overland|bromo|ijen|jawa timur|luar bali|multi-day|selat bali|ferry|penyeberangan)/i', $haystack) ||
            preg_match('/(?:dispatcher|dasbor pusat|monitoring armada|penugasan armada|fleet center|penugasan supir|alokasi supir)/i', $haystack) ||
            preg_match('/(?:voucher|qr code|e-voucher|tiket digital|boarding pass|barcode)/i', $haystack) ||
            preg_match('/(?:pembayaran|payment|midtrans|qris|\bdp\b|termin|pelunasan|checkout|transaksi)/i', $haystack)
        );
    }

    public function analyze(
        string $title,
        string $desc,
        string $businessName,
        string $clientActor,
        string $primaryActor,
        int $index,
        string $prefix,
        array $erdTables = [],
        bool $isEn = false
    ): array {
        $haystack = $title . ' ' . $desc;
        $relatedTable = $this->matchTable($title, $desc, $erdTables);

        // Archetype T1: AIRPORT & HOTEL TRANSFER DISPATCH ENGINE (TRANSFER IN/OUT DPS)
        if (preg_match('/(?:bandara|airport|transfer in|transfer out|ngurah rai|\bdps\b|flight|penerbangan|antar jemput)/i', $haystack)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'DPS Ngurah Rai Flight Arrival Radar Sync & Delay Recalibration' : 'Sinkronisasi Radar Kedatangan DPS & Rekalibrasi Delay Pesawat',
                    'given' => $isEn ? "Passenger scheduled on flight GA-401 with estimated landing at 14:30 WITA." : "Penumpang terjadwal pada nomor penerbangan GA-401 dengan estimasi mendarat pukul 14:30 WITA.",
                    'when' => $isEn ? "Airport radar feeds detect a 45-minute flight delay from departure origin." : "Sistem pelacak radar bandara mendeteksi penundaan penerbangan (delay) selama 45 menit dari bandara asal.",
                    'then' => $isEn ? "System automatically updates pickup schedule to 15:15 WITA and dispatches real-time WhatsApp alert to assigned driver and traveler." : "Sistem otomatis memundurkan jadwal penjemputan ke 15:15 WITA dan mengirim update WhatsApp otomatis ke pengemudi serta tamu.",
                ],
                [
                    'scenario' => $isEn ? 'Baggage Clearance Buffer & Meeting Point Verification' : 'Buffer Pengambilan Bagasi & Verifikasi Titik Temu Pengemudi',
                    'given' => $isEn ? "Flight touchdown confirmed at Ngurah Rai International Airport." : "Penerbangan telah terkonfirmasi mendarat resmi di Bandara Internasional I Gusti Ngurah Rai.",
                    'when' => $isEn ? "Passenger completes immigration and proceeds to baggage claim area." : "Penumpang menyelesaikan proses imigrasi dan menuju area klaim bagasi bandara.",
                    'then' => $isEn ? "System applies a mandatory 45-60 minute baggage grace buffer, verifies driver GPS presence at Meeting Point, and renders Digital Paging Board." : "Sistem menerapkan buffer standar 45-60 menit, memverifikasi kehadiran driver di Meeting Point via GPS, dan menampilkan Digital Paging Board.",
                ],
                [
                    'scenario' => $isEn ? 'Passenger No-Show Mitigation & Dispute-Proof Audit Trail' : 'Mitigasi No-Show Tamu & Audit Trail Pembatalan Terverifikasi',
                    'given' => $isEn ? "Assigned driver has waited beyond the 90-minute maximum grace threshold." : "Pengemudi telah menunggu di titik temu melebihi batas toleransi maksimal 90 menit.",
                    'when' => $isEn ? "Driver triggers uncontactable passenger status via mobile companion." : "Pengemudi memicu status tamu tidak dapat dihubungi melalui web/aplikasi companion.",
                    'then' => $isEn ? "System logs GPS telemetry timestamps, captures meeting point photo verification, and dispatches dispute-proof cancellation notice protecting partner compensation." : "Sistem mencatat log telemetri GPS, mengunggah foto bukti kehadiran di lobi bandara, dan menerbitkan pembatalan terverifikasi guna melindungi komisi mitra.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/transfers/dispatch',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'passenger_name' => 'Michael Smith',
                    'flight_number' => 'GA-401',
                    'flight_origin' => 'CGK',
                    'scheduled_touchdown' => '2026-11-08 14:30:00',
                    'destination_hotel' => 'The Mulia Resort Nusa Dua',
                    'passenger_count' => 4,
                    'cabin_bags' => 2,
                    'checkin_bags' => 3,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'TRANSFER_DISPATCHED',
                    'data' => [
                        'transfer_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                        'flight_status' => 'ON_SCHEDULE',
                        'driver' => [
                            'name' => 'Wayan Suartana',
                            'phone' => '+6281234567890',
                            'vehicle' => 'Innova Reborn (DK 1888 XY)',
                        ],
                        'pickup_buffer_minutes' => 45,
                        'meeting_point' => 'DPS International Arrival Hall Pick-Up Zone',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful transfer dispatch route with Sanctum auth and rate limiting.',
                    'Integrate Flight Radar webhook handler for Ngurah Rai flight status synchronization.',
                    'Enforce X-Idempotency-Key header to eliminate duplicate transfer booking submissions.',
                ] : [
                    'Daftarkan endpoint API dispatch transfer bandara dengan otentikasi Sanctum dan rate limiting.',
                    'Integrasikan webhook handler Flight Radar untuk sinkronisasi delay pesawat Ngurah Rai real-time.',
                    'Terapkan header X-Idempotency-Key untuk mencegah pemesanan transfer ganda.',
                ],
            ];
            $targetFiles = ['app/Models/AirportTransfer.php', 'app/Services/TransferDispatchService.php', 'app/Http/Controllers/AirportTransferController.php', 'tests/Feature/AirportTransferContractTest.php'];
            $mission = "Bangun modul Reservasi & Dispatch Transfer Bandara Ngurah Rai dengan pelacakan radar delay, digital paging board, dan proteksi no-show.";

            return [
                'category' => 'LOGISTICS_DISPATCH',
                'category_label' => $isEn ? 'Airport Transfer & Flight Dispatch' : 'Transfer Bandara & Dispatch Radar',
                'related_table' => $relatedTable['name'] ?? 'airport_transfers',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler arriving at Ngurah Rai Airport, I want my pickup to automatically synchronize with my flight's real-time landing time, so that a driver is reliably waiting for me without coordination stress."
                    : "Sebagai wisatawan yang mendarat di Bandara Ngurah Rai, saya ingin penjemputan saya otomatis sinkron dengan waktu mendarat pesawat aktual, sehingga pengemudi siap menyambut di lobi tanpa rasa cemas.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['AirportTransferDispatchIsland.blade.php', 'FlightRadarTracker.blade.php', 'DigitalPagingBoard.blade.php'],
                    'tasks' => $isEn ? [
                        'Build responsive flight intake form with airline IATA auto-suggest.',
                        'Render dynamic flight radar status pill with real-time landing estimates.',
                        'Generate high-contrast Digital Paging Board for airport arrival greeting.',
                    ] : [
                        'Bangun formulir reservasi penjemputan bandara dengan auto-suggest kode penerbangan IATA.',
                        'Tampilkan pill status radar kedatangan pesawat dengan estimasi waktu mendarat live.',
                        'Hasilkan Digital Paging Board beresolusi tinggi untuk papan nama penyambutan tamu di lobi.',
                    ],
                    'states' => $isEn ? 'Default, Tracking Flight, Driver Dispatched, Delayed Buffer, Error Toast' : 'Default, Melacak Radar, Supir Ditugaskan, Penyesuaian Delay, Error Toast',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/AirportTransfer.php', 'app/Services/TransferDispatchService.php', 'app/Http/Controllers/AirportTransferController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for airport_transfers table enforcing strict 26-char ULID primary key.',
                        'Implement TransferDispatchService with atomic DB::transaction() and lockForUpdate().',
                        'Construct flight tracking radar scheduler to poll/receive arrival state transitions.',
                    ] : [
                        'Migration tabel airport_transfers dengan Strict ULID 26 karakter PostgreSQL 16.',
                        'Implementasikan TransferDispatchService atomik dibungkus DB::transaction() dan lockForUpdate().',
                        'Bangun service sinkronisasi radar kedatangan maskapai untuk pembaruan status delay otomatis.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-AIRPORT_TRANSFER', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=AirportTransferContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T2: INTERACTIVE DAY TOUR CATALOG & FLEET CONFIGURATOR
        if (preg_match('/(?:katalog|catalog|(?<!multi-)\bday tour\b|paket wisata|fleet configurator|konfigurasi armada|pilihan armada)/i', $haystack) && !preg_match('/(?:overland|bromo|ijen)/i', $haystack)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Vehicle Volumetric Capacity Matching & Smart Upgrade Suggestion' : 'Kalkulasi Kapasitas Volumetrik Armada & Rekomendasi Upgrade',
                    'given' => $isEn ? "Client group consists of 5 adults and 4 large 28-inch check-in suitcases." : "Rombongan tamu terdiri dari 5 orang dewasa dan 4 koper bagasi besar ukuran 28-inci.",
                    'when' => $isEn ? "Client attempts to select standard compact vehicle (max 4 pax + 2 bags)." : "Tamu mencoba memilih armada tipe Avanza (kapasitas maksimal 4 penumpang + 2 bagasi).",
                    'then' => $isEn ? "System validates volumetric overcapacity and automatically suggests upgrading to Innova Reborn or HiAce Commuter with cost-benefit comparison." : "Sistem memvalidasi kelebihan muatan volumetrik dan otomatis menyarankan upgrade ke Innova Reborn atau HiAce Commuter beserta perbandingan tarif hemat.",
                ],
                [
                    'scenario' => $isEn ? 'Duration Tiering (Half Day vs Full Day) & Overtime Tariff Formulation' : 'Formulasi Tarif Berjenjang & Kalkulasi Biaya Lembur (Overtime)',
                    'given' => $isEn ? "Full Day tour package booked for standard 10-hour duration." : "Paket sewa Full Day berdurasi standar 10 jam telah dipesan.",
                    'when' => $isEn ? "Actual trip execution extends to 12 hours due to distant destination transit." : "Perjalanan di lapangan berlangsung selama 12 jam akibat rute destinasi yang jauh.",
                    'then' => $isEn ? "System calculates overtime surcharge at IDR 75,000/hour and displays itemized operational breakdown separating all-inclusive items from toll retributions." : "Sistem menghitung biaya lembur otomatis Rp 75.000/jam dan menampilkan invoice transparan yang memisahkan komponen All-in versus tiket tol Bali Mandara.",
                ],
                [
                    'scenario' => $isEn ? 'Transient Inventory Hold (15-Minute TTL) Preventing Concurrent Overbooking' : 'Penguncian Inventori Sementara (TTL 15 Menit) Anti-Overbooking',
                    'given' => $isEn ? "Available fleet inventory for HiAce Commuter on high-season date is 1 unit." : "Sisa ketersediaan unit HiAce Commuter pada tanggal high-season tersisa 1 unit.",
                    'when' => $isEn ? "Customer proceeds to checkout and enters payment window." : "Pelanggan masuk ke tahap checkout pembayaran.",
                    'then' => $isEn ? "System acquires a 15-minute Inventory TTL Hold locking the vehicle slot; if checkout expires without payment, slot is automatically released." : "Sistem mengunci inventori unit selama 15 menit (TTL Hold); jika pembayaran tidak diselesaikan, slot unit otomatis dilepas kembali ke katalog.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/tours/configure-fleet',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'tour_package_id' => 'tour-ubud-kintamani-sunset',
                    'vehicle_category' => 'innova_reborn',
                    'adult_passengers' => 4,
                    'child_passengers' => 1,
                    'cabin_bags' => 2,
                    'checkin_bags' => 2,
                    'duration_tier' => 'full_day_10h',
                    'booking_date' => '2026-11-10',
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'FLEET_CONFIGURED',
                    'data' => [
                        'quote_id' => '01J7K9M0P1Q2R3S4T5U6V7W8BB',
                        'base_tariff_idr' => 850000,
                        'overtime_rate_per_hour_idr' => 75000,
                        'all_inclusive_items' => ['multilingual_driver', 'fuel', 'destination_parking'],
                        'client_payable_expenses' => ['bali_mandara_toll_idr_13000', 'village_retribution'],
                        'inventory_hold_ttl_seconds' => 900,
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful fleet configuration endpoint with sub-10ms response latency.',
                    'Implement Redis / database TTL lock for 15-minute inventory reservation hold.',
                    'Format response with Keyset Cursor Pagination O(1) indexed by Bali geographical zones.',
                ] : [
                    'Daftarkan endpoint konfigurator armada dengan latensi respons sub-10ms.',
                    'Terapkan penguncian TTL inventori sementara 15 menit menggunakan Redis atau database lock.',
                    'Format respons katalog dengan Keyset Cursor Pagination O(1) berindeks zona destinasi Bali.',
                ],
            ];
            $targetFiles = ['app/Models/TourBooking.php', 'app/Services/FleetConfiguratorService.php', 'app/Http/Controllers/TourBookingController.php', 'tests/Feature/TourBookingContractTest.php'];
            $mission = "Implementasikan Katalog Paket Day Tour & Konfigurator Armada dengan kalkulasi volumetrik bagasi, tarif dinamis, dan penguncian TTL inventori.";

            return [
                'category' => 'TOUR_CATALOG_FLEET',
                'category_label' => $isEn ? 'Tour Catalog & Fleet Configurator' : 'Katalog Day Tour & Konfigurator Armada',
                'related_table' => $relatedTable['name'] ?? 'tour_bookings',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler planning day trips in Bali, I want to configure vehicle types, passenger counts, and luggage capacities with transparent pricing, so that our group travels comfortably without vehicle capacity surprises."
                    : "Sebagai wisatawan yang merencanakan day tour di Bali, saya ingin mengonfigurasi tipe kendaraan, jumlah penumpang, dan bagasi dengan kalkulasi tarif transparan, agar rombongan nyaman tanpa kendala kapasitas di lapangan.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['DayTourCatalogIsland.blade.php', 'FleetCapacitySelector.blade.php', 'LuggageVolumetricCalculator.blade.php'],
                    'tasks' => $isEn ? [
                        'Construct responsive tour catalog grid with filter tabs by Bali regional zones.',
                        'Implement real-time luggage & passenger volumetric capacity gauge (Safe / Full / Over-Capacity).',
                        'Integrate instant cost breakdown displaying All-Inclusive vs On-The-Road extras.',
                    ] : [
                        'Bangun grid katalog paket tour responsif dengan filter zona geografis Bali.',
                        'Implementasikan indikator kapasitas bagasi dan penumpang visual (Aman / Penuh / Over-Capacity).',
                        'Integrasikan rincian kalkulasi harga instan yang membedakan komponen All-In versus biaya tol di jalan.',
                    ],
                    'states' => $isEn ? 'Default, Capacity Warning, Inventory Holding, Confirmed, Error Toast' : 'Default, Peringatan Kapasitas, Menahan Inventori, Terkonfirmasi, Error Toast',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/TourBooking.php', 'app/Services/FleetConfiguratorService.php', 'app/Http/Controllers/TourBookingController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for tour_bookings table with strict 26-character ULID primary key.',
                        'Implement FleetConfiguratorService calculating vehicle assignments and luggage dimensions.',
                        'Enforce 15-minute transient TTL inventory holding state on selected vehicle dates.',
                    ] : [
                        'Migration tabel tour_bookings dengan Primary Key Strict ULID 26 karakter PostgreSQL 16.',
                        'Implementasikan FleetConfiguratorService yang menghitung pencocokan tipe armada dan dimensi bagasi.',
                        'Terapkan state holding inventori sementara (TTL 15 menit) pada unit kendaraan yang dipilih.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-TOUR_CATALOG', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=TourBookingContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T3: AI-POWERED WISHLIST & ITINERARY GENERATOR (ZERO-ASSUMPTION ENGINE)
        if (preg_match('/(?:\bwishlist\b|\bwish list\b|\bai itinerary\b|generator itinerary|smart itinerary|itinerary generator)/i', $haystack)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Zero-Assumption Entity Extraction & Intelligent Geo-Clustering' : 'Ekstraksi Wishlist Tanpa Asumsi & Klastering Geografis Cerdas',
                    'given' => $isEn ? "Traveler inputs free-text wish list: 'Tanah Lot sunset, Kecak dance at Uluwatu, Tirta Empul holy spring, and Ayung River rafting'." : "Wisatawan menginput wish list bebas: 'Sunset Tanah Lot, Tari Kecak Uluwatu, Tirta Empul, dan Rafting Sungai Ayung'.",
                    'when' => $isEn ? "Itinerary generator engine executes geographical clustering." : "Mesin generator itinerary menganalisis entitas lokasi dan melakukan pengelompokan geografis.",
                    'then' => $isEn ? "Destinations are rationally clustered into zones (Gianyar/Ubud Day 1, Tabanan/Badung Day 2) eliminating transit fatigue and illogical zig-zag routes." : "Destinasi dikelompokkan secara rasional (Gianyar/Ubud Hari 1, Tabanan/Badung Hari 2) guna mengeliminasi rute zig-zag dan menghemat waktu tempuh.",
                ],
                [
                    'scenario' => $isEn ? 'POI Operating Hours & Real-Traffic Buffer Synchronization' : 'Validasi Jam Operasional POI & Buffer Kemacetan Riil',
                    'given' => $isEn ? "Uluwatu Kecak performance starts strictly at 18:00 WITA." : "Pementasan Tari Kecak Uluwatu dimulai tepat pukul 18:00 WITA.",
                    'when' => $isEn ? "System sequences destination itinerary visits." : "Sistem menyusun urutan kunjungan destinasi dalam jadwal harian.",
                    'then' => $isEn ? "Arrival is scheduled by 16:30 WITA with historical Bali traffic buffers, guaranteeing guaranteed ticket acquisition before cutoff." : "Kedatangan di lokasi dijadwalkan pukul 16:30 WITA dengan buffer kemacetan lalu lintas Bali, menjamin ketersediaan tiket sebelum loket ditutup.",
                ],
                [
                    'scenario' => $isEn ? 'Integrated Real-Budget Breakdown (Domestic vs Foreign & Guide Options)' : 'Kalkulasi Anggaran Riil (Tarif Domestik vs Mancanegara & Opsi HPI)',
                    'given' => $isEn ? "Synthesized itinerary comprises 4 cultural attractions and 2 natural sites." : "Itinerary yang dihasilkan mencakup 4 objek wisata budaya dan 2 destinasi alam.",
                    'when' => $isEn ? "Client selects traveler nationality profile (Domestic vs Foreign Tourist)." : "Tamu memilih profil kewarganegaraan (Wisatawan Domestik vs Wisatawan Mancanegara).",
                    'then' => $isEn ? "System itemizes exact admission tickets, estimated road mileage, and optional certified HPI regional tourist guide fees." : "Sistem merinci tiket masuk resmi (tarif WNI vs WNA), estimasi biaya bahan bakar, dan opsi jasa pemandu lokal berlisensi (HPI).",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/itinerary/synthesize-wishlist',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'raw_wishlist_text' => 'Tanah Lot sunset, Kecak Uluwatu, Tirta Empul holy bath, Ayung rafting',
                    'duration_days' => 2,
                    'traveler_origin' => 'The Stones Legian',
                    'traveler_profile' => 'family_with_children',
                    'include_hpi_guide' => true,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'ITINERARY_SYNTHESIZED',
                    'data' => [
                        'itinerary_id' => '01J7K9M0P1Q2R3S4T5U6V7W8CC',
                        'geo_clusters' => ['Central Gianyar (Ubud)', 'South Badung & Tabanan'],
                        'daily_schedule' => [
                            ['day' => 1, 'route' => ['Tirta Empul', 'Ayung Rafting'], 'transit_minutes' => 50],
                            ['day' => 2, 'route' => ['Tanah Lot Sunset', 'Uluwatu Kecak'], 'transit_minutes' => 65],
                        ],
                        'admission_tickets_total_idr' => 450000,
                        'hpi_guide_cost_idr' => 350000,
                        'google_maps_route_url' => 'https://maps.google.com/?dir=...',
                        'status' => 'DRAFT_AI',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful itinerary synthesis endpoint with Sanctum authentication.',
                    'Implement state machine (DRAFT_AI -> CLIENT_CUSTOMIZING -> DISPATCHER_VERIFIED -> LOCKED_CONFIRMED).',
                    'Generate printable PDF itinerary voucher with anti-tamper QR verification.',
                ] : [
                    'Daftarkan endpoint sintesis itinerary dengan otentikasi token Sanctum.',
                    'Terapkan state machine perjalanan (DRAFT_AI -> CLIENT_CUSTOMIZING -> DISPATCHER_VERIFIED -> LOCKED_CONFIRMED).',
                    'Hasilkan voucher PDF siap cetak dengan kode QR verifikasi anti-pemalsuan.',
                ],
            ];
            $targetFiles = ['app/Models/Itinerary.php', 'app/Services/ItinerarySynthesisService.php', 'app/Http/Controllers/ItineraryController.php', 'tests/Feature/ItineraryContractTest.php'];
            $mission = "Bangun Mesin Generator Itinerary Cerdas Berbasis AI & Database dengan klastering geografis, jam operasional POI, dan kalkulasi tiket resmi.";

            return [
                'category' => 'AI_ITINERARY_PLANNER',
                'category_label' => $isEn ? 'AI Wishlist & Itinerary Planner' : 'Generator Itinerary Cerdas & Wishlist',
                'related_table' => $relatedTable['name'] ?? 'itineraries',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler with custom dream vacation ideas, I want an intelligent engine to analyze my wishlist against verified attraction databases, so that I receive an optimal, conflict-free travel schedule with transparent costs."
                    : "Sebagai wisatawan dengan daftar destinasi impian, saya ingin mesin cerdas menganalisis wishlist saya terhadap data resmi destinasi, sehingga menghasilkan rencana perjalanan hemat waktu dengan transparansi biaya.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['WishlistIntakeIsland.blade.php', 'InteractiveItineraryTimeline.blade.php', 'GeoClusterRouteMap.blade.php'],
                    'tasks' => $isEn ? [
                        'Construct free-text wishlist input with instant landmark tag chips.',
                        'Render interactive day-by-day timeline with drag-and-drop itinerary reordering.',
                        'Embed Google Maps GPS routing link with verified transit durations.',
                    ] : [
                        'Bangun kotak input wish list bebas dengan chips deteksi landmark otomatis.',
                        'Tampilkan timeline interaktif jadwal harian yang mendukung penyesuaian urutan kunjungan.',
                        'Sematkan tautan rute GPS Google Maps interaktif dengan durasi tempuh akurat.',
                    ],
                    'states' => $isEn ? 'Default, Analyzing Wishlist, Geo-Clustered, Customizing, Locked Confirmed' : 'Default, Menganalisis Wishlist, Klastering Lokasi, Disesuaikan Tamu, Terkunci Final',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/Itinerary.php', 'app/Services/ItinerarySynthesisService.php', 'app/Http/Controllers/ItineraryController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for itineraries table with Strict ULID 26-char primary key and JSONB routes.',
                        'Implement ItinerarySynthesisService with geographical clustering and operating hours validation.',
                        'Enforce state machine transitions locking itinerary upon customer confirmation.',
                    ] : [
                        'Migration tabel itineraries dengan Strict ULID 26 karakter PostgreSQL 16 dan rute JSONB.',
                        'Implementasikan ItinerarySynthesisService dengan algoritma klastering lokasi dan validasi jam buka POI.',
                        'Terapkan transisi state machine yang mengunci itinerary saat disetujui tamu.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-ITINERARY_AI', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=ItineraryContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T4: OVERLAND MULTI-DAY TOUR (BROMO & IJEN NATIONAL PARKS)
        if (preg_match('/(?:overland|bromo|ijen|jawa timur|luar bali|multi-day|selat bali|ferry|penyeberangan)/i', $haystack)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Multi-Day Stage Progression & Bali Strait Ferry Handoff' : 'Manajemen Etape Perjalanan Multi-Hari & Penyeberangan Feri',
                    'given' => $isEn ? "Overland booking confirmed for 3-Day/2-Night Bromo & Ijen expedition departing Bali." : "Pemesanan overland terkonfirmasi untuk ekspedisi 3-Hari/2-Malam Bromo & Ijen dari Bali.",
                    'when' => $isEn ? "Tour vehicle reaches Gilimanuk Port for Bali Strait ferry crossing to Ketapang, Banyuwangi." : "Armada tour tiba di Pelabuhan Gilimanuk untuk penyeberangan feri Selat Bali ke Ketapang, Banyuwangi.",
                    'then' => $isEn ? "System records automated stage check-in, validates ferry boarding e-ticket, and syncs vehicle handoff with Banyuwangi operations." : "Sistem mencatat check-in etape otomatis, memvalidasi tiket digital penyeberangan feri, dan menyinkronkan penjemputan armada Banyuwangi.",
                ],
                [
                    'scenario' => $isEn ? 'Regional Partner Dispatch & Conservation E-Ticketing' : 'Koordinasi Mitra Lokal & E-Tiket Konservasi TNBTS/Ijen',
                    'given' => $isEn ? "Expedition itinerary schedules midnight ascent to Mount Bromo Sunrise and Ijen Crater Blue Fire." : "Jadwal ekspedisi merencanakan pendakian dini hari Sunrise Bromo dan Api Biru Kawah Ijen.",
                    'when' => $isEn ? "System dispatches operational vouchers 24 hours prior to stage execution." : "Sistem menerbitkan voucher operasional H-1 sebelum etape pendakian dimulai.",
                    'then' => $isEn ? "Automated partner vouchers are dispatched to Sukapura 4x4 Jeep operators and certified Ijen Crater guides with specialized gas mask equipment checklists." : "Voucher kemitraan otomatis terkirim ke paguyuban Jeep 4x4 Sukapura dan pemandu resmi Kawah Ijen dengan checklist masker gas standar.",
                ],
                [
                    'scenario' => $isEn ? 'Volcanic Hazard Contingency & Driver Rotation Safety' : 'Protokol Mitigasi Erupsi Vulkanik & Pencegahan Kelelahan Pengemudi',
                    'given' => $isEn ? "Overland drive exceeds 8 continuous hours or regional vulcanology elevates hazard status to Alert (Siaga)." : "Durasi perjalanan overland melebihi 8 jam atau status vulkanik PVMBG naik ke level Waspada/Siaga.",
                    'when' => $isEn ? "Telematics sensor or supervisor triggers safety alert." : "Sensor telemetri armada atau supervisor memicu peringatan keselamatan operasional.",
                    'then' => $isEn ? "System enforces mandatory rest interval, triggers secondary driver rotation, or executes automated itinerary rerouting with client WhatsApp notification." : "Sistem mewajibkan istirahat pengemudi, mengaktifkan sopir cadangan, atau mengalihkan rute aman dengan notifikasi WhatsApp otomatis ke tamu.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/tours/overland-booking',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'package_id' => '01J7K9M0P1Q2R3S4T5U6V7W8DD',
                    'departure_date' => '2026-12-15',
                    'passengers_adult' => 4,
                    'passengers_child' => 0,
                    'pickup_location' => 'Seminyak Beach Resort',
                    'overland_tier' => 'bromo_ijen_bluefire_3d2n',
                    'jeep_4x4_required' => true,
                    'gas_masks_count' => 4,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'OVERLAND_BOOKING_INITIALIZED',
                    'data' => [
                        'booking_id' => '01J7K9M0P1Q2R3S4T5U6V7W8EE',
                        'stages' => [
                            ['stage' => 1, 'route' => 'Bali -> Gilimanuk -> Banyuwangi', 'status' => 'SCHEDULED'],
                            ['stage' => 2, 'route' => 'Banyuwangi -> Mount Ijen Blue Fire', 'status' => 'SCHEDULED'],
                            ['stage' => 3, 'route' => 'Probolinggo -> Mount Bromo Sunrise -> Bali', 'status' => 'SCHEDULED'],
                        ],
                        'ferry_crossing_ticket' => 'FERRY-GLM-KTP-8849',
                        'jeep_partner' => 'Paguyuban Sukapura 4x4 (Unit #24)',
                        'certified_guide' => 'Wayan Bromo Tours (HPI Lic. #7721)',
                        'status' => 'CONFIRMED',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful overland booking endpoint with Sanctum authentication.',
                    'Implement multi-stage journey lifecycle with ferry and conservation park voucher linking.',
                    'Integrate volcanic hazard alert webhook for emergency itinerary suspension.',
                ] : [
                    'Daftarkan endpoint pemesanan overland dengan otentikasi token Sanctum.',
                    'Implementasikan siklus hidup etape perjalanan multi-hari terintegrasi tiket feri & TNBTS.',
                    'Integrasikan webhook notifikasi status vulkanik PVMBG untuk penangguhan darurat.',
                ],
            ];
            $targetFiles = ['app/Models/OverlandTour.php', 'app/Services/OverlandDispatchService.php', 'app/Http/Controllers/OverlandTourController.php', 'tests/Feature/OverlandTourContractTest.php'];
            $mission = "Bangun modul Ekspedisi Overland Tour Multi-Hari (Bromo & Ijen) dengan manajemen etape perjalanan, koordinasi Jeep 4x4 lokal, dan tiket feri.";

            return [
                'category' => 'OVERLAND_EXPEDITION',
                'category_label' => $isEn ? 'Overland Multi-Day Expedition' : 'Ekspedisi Overland Multi-Hari',
                'related_table' => $relatedTable['name'] ?? 'overland_tours',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler embarking on an overland adventure to Mount Bromo and Ijen Crater, I want guaranteed multi-day vehicle staging, ferry crossing tickets, and certified local guides, so that my expedition is seamless, safe, and conflict-free."
                    : "Sebagai wisatawan ekspedisi overland ke Gunung Bromo dan Kawah Ijen, saya ingin kepastian armada multi-hari, tiket feri, dan pemandu lokal bersertifikat, sehingga petualangan berjalan aman, nyaman, dan bebas kendala.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['OverlandStageTimeline.blade.php', 'FerryVoucherCard.blade.php', 'VolcanicSafetyAlert.blade.php'],
                    'tasks' => $isEn ? [
                        'Construct multi-day overland itinerary timeline with stage progression trackers.',
                        'Display certified gear checklist (gas masks, warm jackets) with toggle confirmation.',
                        'Implement real-time hazard status banner with amber/rose safety warning accents.',
                    ] : [
                        'Bangun timeline jadwal overland multi-hari dengan indikator progres etape perjalanan.',
                        'Tampilkan checklist perlengkapan wajib (masker gas, jaket tebal) dengan toggle verifikasi.',
                        'Sediakan banner status keamanan vulkanik real-time dengan aksen amber/rose.',
                    ],
                    'states' => $isEn ? 'Default, Staging Active, Ferry Boarding, Summit Trekking, Return to Base' : 'Default, Persiapan Etape, Penyeberangan Feri, Pendakian Puncak, Kembali ke Base',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/OverlandTour.php', 'app/Services/OverlandDispatchService.php', 'app/Http/Controllers/OverlandTourController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for overland_tours and overland_stages tables with Strict ULID primary keys.',
                        'Implement OverlandDispatchService managing driver rotation and ferry booking vouchers.',
                        'Enforce atomic stage updates with DB::transaction and pessimistic concurrency locking.',
                    ] : [
                        'Migration tabel overland_tours dan overland_stages dengan Strict ULID 26 karakter PostgreSQL 16.',
                        'Implementasikan OverlandDispatchService yang mengelola rotasi supir dan voucher feri.',
                        'Terapkan update etape atomik dengan DB::transaction dan pessimistic locking.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-OVERLAND', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=OverlandTourContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T5: CENTRAL OPERATIONAL FLEET DISPATCHER & KANBAN
        if (preg_match('/(?:dispatcher|dasbor pusat|monitoring armada|penugasan armada|fleet center|penugasan supir|alokasi supir)/i', $haystack)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Real-Time Fleet Status Kanban & Zero-Polling Live Updates' : 'Papan Kendali Kanban Unit & Status Armada Real-Time',
                    'given' => $isEn ? "Fleet dispatcher opens the operational command center on Filament v5 admin dashboard." : "Dispatcher armada membuka pusat kendali operasional pada dasbor admin Filament v5.",
                    'when' => $isEn ? "Field drivers update trip milestones via mobile device or GPS beacons." : "Pengemudi di lapangan memperbarui status perjalanan melalui perangkat mobile atau sinyal GPS.",
                    'then' => $isEn ? "Fleet Kanban cards transition instantly across swimlanes (Available -> Dispatched -> In-Transit -> Maintenance) with zero page reloads." : "Kartu Kanban armada berpindah seketika antar-kolom (Tersedia -> Ditugaskan -> Dalam Perjalanan -> Perawatan) secara instan tanpa reload.",
                ],
                [
                    'scenario' => $isEn ? 'Intelligent Driver Matchmaking (Language, Capacity & Proximity)' : 'Algoritma Penugasan Pengemudi Cerdas (Bahasa, Kapasitas & Lokasi)',
                    'given' => $isEn ? "New booking arrives from foreign guests requesting English/Mandarin speaking guide with HiAce vehicle." : "Pesanan baru masuk dari tamu asing yang membutuhkan supir fasih Bahasa Inggris/Mandarin dengan unit HiAce.",
                    'when' => $isEn ? "Dispatcher executes automated dispatch recommendation." : "Dispatcher mengeksekusi rekomendasi penugasan pengemudi otomatis.",
                    'then' => $isEn ? "System ranks available drivers by language qualification, current GPS proximity, and fair workload distribution hours." : "Sistem meranking supir yang tersedia berdasarkan kualifikasi bahasa, jarak GPS terdekat, dan rotasi jam kerja yang seimbang.",
                ],
                [
                    'scenario' => $isEn ? 'Emergency Backup Auto-Reassignment Protocol' : 'Prosedur Auto-Reassignment Penggantian Armada Darurat',
                    'given' => $isEn ? "Assigned vehicle reports mechanical breakdown during en-route transit." : "Armada yang bertugas mengalami kendala teknis atau mogok di jalan saat menuju titik penjemputan.",
                    'when' => $isEn ? "Driver reports emergency malfunction via one-click SOS button." : "Pengemudi menekan tombol SOS darurat di aplikasi operasional.",
                    'then' => $isEn ? "System instantly alerts nearest standby unit within 15 km radius, re-routes pickup dispatch, and updates client WhatsApp ETA." : "Sistem seketika menugaskan unit cadangan terdekat dalam radius 15 km, mengalihkan order penjemputan, dan mengabari tamu via WhatsApp.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/dispatcher/assign-driver',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'booking_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                    'driver_id' => '01J7K9M0P1Q2R3S4T5U6V7W8FF',
                    'vehicle_id' => '01J7K9M0P1Q2R3S4T5U6V7W8GG',
                    'assignment_mode' => 'AUTOMATED_MATCH',
                    'dispatch_notes' => 'Airport pickup DPS international terminal gate 3',
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'DISPATCH_CONFIRMED',
                    'data' => [
                        'dispatch_id' => '01J7K9M0P1Q2R3S4T5U6V7W8HH',
                        'driver_name' => 'Ketut Sudarma',
                        'driver_phone' => '+6281234567890',
                        'vehicle_plate' => 'DK 1842 AB',
                        'vehicle_model' => 'Toyota HiAce Commuter 2024',
                        'dispatched_at' => '2026-11-07T10:00:00+08:00',
                        'status' => 'DISPATCHED',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register driver dispatch assignment endpoint with RBAC permission check (operator/superadmin).',
                    'Implement real-time WebSocket event broadcasting for fleet state updates.',
                    'Record comprehensive dispatch audit trail with GPS departure coordinates.',
                ] : [
                    'Daftarkan endpoint penugasan supir dengan validasi hak akses RBAC (operator/superadmin).',
                    'Implementasikan event broadcasting WebSocket real-time untuk pembaruan status armada.',
                    'Catat audit trail penugasan lengkap dengan koordinat GPS titik keberangkatan.',
                ],
            ];
            $targetFiles = ['app/Models/FleetDispatch.php', 'app/Services/FleetDispatchService.php', 'app/Http/Controllers/FleetDispatchController.php', 'tests/Feature/FleetDispatchContractTest.php'];
            $mission = "Bangun Pusat Kendali Dispatcher Armada Real-Time dengan visualisasi Kanban, algoritma pencocokan pengemudi, dan penanganan darurat.";

            return [
                'category' => 'FLEET_DISPATCH_CENTER',
                'category_label' => $isEn ? 'Operational Fleet Dispatcher' : 'Pusat Kendali Dispatcher Armada',
                'related_table' => $relatedTable['name'] ?? 'fleet_dispatches',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As an operational fleet dispatcher, I want a centralized live monitoring board to assign verified drivers and track trip milestones, so that fleet utilization is maximized and clients experience zero pickup delays."
                    : "Sebagai dispatcher operasional, saya ingin dasbor pemantauan terpusat untuk menugaskan supir dan memantau status perjalanan, sehingga utilisasi armada optimal dan penjemputan tamu tepat waktu.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['FleetKanbanBoard.blade.php', 'DriverAssignmentModal.blade.php', 'EmergencyReassignmentAlert.blade.php'],
                    'tasks' => $isEn ? [
                        'Build drag-and-drop Fleet Kanban board adhering strictly to WCAG AAA contrast.',
                        'Render driver matching card displaying foreign language badges and star ratings.',
                        'Integrate instant toast alerts for breakdown reports and emergency replacements.',
                    ] : [
                        'Bangun papan Kanban armada responsif dengan rasio kontras tinggi WCAG AAA.',
                        'Tampilkan kartu rekomendasi supir dengan badge kemampuan bahasa dan rating ulasan.',
                        'Integrasikan notifikasi toast melayang saat ada laporan kendala teknis atau unit pengganti.',
                    ],
                    'states' => $isEn ? 'Available, Dispatched, In-Transit, Maintenance, Emergency Replaced' : 'Tersedia, Ditugaskan, Dalam Perjalanan, Perawatan, Unit Pengganti Ditugaskan',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/FleetDispatch.php', 'app/Services/FleetDispatchService.php', 'app/Http/Controllers/FleetDispatchController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for fleet_dispatches table with Strict ULID primary and foreign keys.',
                        'Implement FleetDispatchService with atomic driver allocation and pessimistic locking.',
                        'Enforce driver rest-time validation preventing overtime dispatch.',
                    ] : [
                        'Migration tabel fleet_dispatches dengan Strict ULID 26 karakter standar PostgreSQL 16.',
                        'Implementasikan FleetDispatchService dengan alokasi supir atomik dan pessimistic locking.',
                        'Terapkan validasi batas jam istirahat pengemudi guna mencegah overwork.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-DISPATCH', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=FleetDispatchContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T6: CRYPTOGRAPHIC QR CODE E-VOUCHER & WHATSAPP CONFIRMATION
        if (preg_match('/(?:voucher|qr code|e-voucher|tiket digital|boarding pass|barcode)/i', $haystack)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Tamper-Proof HMAC SHA-256 QR Voucher Generation' : 'Penerbitan E-Voucher Ber-QR Code HMAC SHA-256 Anti-Pemalsuan',
                    'given' => $isEn ? "Reservation payment confirmed via payment gateway callback." : "Pembayaran pemesanan terkonfirmasi lunas dari callback payment gateway.",
                    'when' => $isEn ? "System executes automated e-voucher issuance." : "Sistem menjalankan proses penerbitan e-voucher otomatis.",
                    'then' => $isEn ? "System generates cryptographic HMAC SHA-256 QR code embedding reservation ULID without exposing sensitive personal data." : "Sistem membuat kode QR ber-tanda tangan kriptografi HMAC SHA-256 yang memuat ULID reservasi tanpa mengekspos data pribadi tamu.",
                ],
                [
                    'scenario' => $isEn ? 'Automated WhatsApp Cloud API Dispatch with PDF Attachment' : 'Pengiriman WhatsApp Business Cloud API Otomatis dengan Lampiran PDF',
                    'given' => $isEn ? "Customer provides verified WhatsApp contact number during checkout." : "Tamu memasukkan nomor kontak WhatsApp terverifikasi saat pemesanan.",
                    'when' => $isEn ? "Voucher generation pipeline finishes PDF rendering." : "Pipeline penerbitan voucher menyelesaikan kompilasi berkas PDF resmi.",
                    'then' => $isEn ? "System dispatches instant WhatsApp push notification with official printable PDF voucher and direct driver contact button." : "Sistem seketika mengirimkan push WhatsApp berisi berkas PDF voucher siap cetak dan tombol kontak langsung ke pengemudi.",
                ],
                [
                    'scenario' => $isEn ? 'Sub-Second Driver & Lobby Staff QR Scanner Validation' : 'Validasi Scanner QR Cepat oleh Pengemudi & Staf Lobi Hotel',
                    'given' => $isEn ? "Passenger presents mobile or printed QR voucher to driver at pickup point." : "Tamu menunjukkan QR voucher di layar ponsel atau kertas cetak kepada pengemudi saat penjemputan.",
                    'when' => $isEn ? "Driver scans voucher using web scanner camera module." : "Pengemudi memindai kode QR menggunakan modul kamera scanner web.",
                    'then' => $isEn ? "System validates cryptographic signature in <1.0 second, confirms boarding status, and transitions reservation to IN_TRANSIT." : "Sistem memvalidasi tanda tangan kriptografi dalam <1,0 detik, memverifikasi keabsahan tiket, dan mengubah status pesanan ke IN_TRANSIT.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/vouchers/verify-qr',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'voucher_ulid' => '01J7K9M0P1Q2R3S4T5U6V7W8JJ',
                    'hmac_signature' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
                    'scanned_by_driver_id' => '01J7K9M0P1Q2R3S4T5U6V7W8FF',
                    'gps_latitude' => -8.7482,
                    'gps_longitude' => 115.1672,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'VOUCHER_VERIFIED',
                    'data' => [
                        'voucher_id' => '01J7K9M0P1Q2R3S4T5U6V7W8JJ',
                        'passenger_name' => 'Yoseph Iriandi',
                        'pickup_time' => '14:30 WITA',
                        'destination' => 'Grand Hyatt Nusa Dua',
                        'service_tier' => 'AIRPORT_TRANSFER_IN',
                        'status' => 'BOARDED_IN_TRANSIT',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register voucher verification endpoint with HMAC SHA-256 signature validation.',
                    'Implement WhatsApp Business Cloud API client with exponential backoff retry.',
                    'Generate lightweight HTML5 canvas QR scanner with offline fallback capability.',
                ] : [
                    'Daftarkan endpoint verifikasi voucher dengan validasi tanda tangan HMAC SHA-256.',
                    'Implementasikan klien WhatsApp Business Cloud API dengan mekanisme retry bertahap.',
                    'Bangun modul scanner QR kamera HTML5 ringan dengan kapabilitas offline fallback.',
                ],
            ];
            $targetFiles = ['app/Models/Voucher.php', 'app/Services/VoucherIssuanceService.php', 'app/Http/Controllers/VoucherController.php', 'tests/Feature/VoucherContractTest.php'];
            $mission = "Bangun modul E-Voucher Kriptografis Ber-QR Code HMAC SHA-256 dengan otomatisasi notifikasi WhatsApp dan pemindai cepat staf lapangan.";

            return [
                'category' => 'DIGITAL_VOUCHER_QR',
                'category_label' => $isEn ? 'Digital E-Voucher & QR Code' : 'E-Voucher Digital & Kode QR',
                'related_table' => $relatedTable['name'] ?? 'vouchers',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn 
                    ? "As a traveler, I want to receive an instant official PDF e-voucher on WhatsApp with a secure QR code upon payment, so that I can seamlessly board my reserved vehicle without paper hassle."
                    : "Sebagai wisatawan, saya ingin menerima e-voucher resmi PDF ber-QR Code di WhatsApp sesaat setelah bayar, sehingga proses penjemputan berjalan lancar tanpa repot mencetak kertas.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'components' => ['VoucherDigitalCard.blade.php', 'QrCodeScannerModal.blade.php', 'WhatsAppShareButton.blade.php'],
                    'tasks' => $isEn ? [
                        'Build mobile-first printable digital e-voucher layout adhering strictly to WCAG AAA.',
                        'Integrate fast HTML5 QR scanner with audio/haptic feedback on successful scan.',
                        'Provide one-click WhatsApp re-send button for administrative dispatchers.',
                    ] : [
                        'Bangun layout e-voucher mobile-first siap cetak dengan rasio kontras WCAG AAA.',
                        'Integrasikan scanner QR kamera HTML5 cepat dengan feedback visual dan getar saat berhasil.',
                        'Sediakan tombol kirim ulang WhatsApp 1-klik untuk dispatcher dan admin.',
                    ],
                    'states' => $isEn ? 'Issued, Delivered WhatsApp, Scanned Verified, Redeemed, Cancelled' : 'Diterbitkan, Terkirim WhatsApp, Terverifikasi Scan, Digunakan, Dibatalkan',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => [
                    'target_files' => ['app/Models/Voucher.php', 'app/Services/VoucherIssuanceService.php', 'app/Http/Controllers/VoucherController.php'],
                    'tasks' => $isEn ? [
                        'Database migration for vouchers table with Strict ULID primary key and HMAC hash columns.',
                        'Implement VoucherIssuanceService with cryptographic signature generation and PDF rendering.',
                        'Enforce rate limiting on QR verification endpoints to prevent brute-force token scans.',
                    ] : [
                        'Migration tabel vouchers dengan Strict ULID 26 karakter dan kolom hash HMAC.',
                        'Implementasikan VoucherIssuanceService dengan pembuatan signature kriptografi dan PDF.',
                        'Terapkan rate limiting ketat pada endpoint verifikasi QR guna mencegah brute force.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-VOUCHER', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=VoucherContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype T7: TWO-STAGE PAYMENT GATEWAY (50% DP & 50% SETTLEMENT)
        $ac = [
            [
                'scenario' => $isEn ? 'Two-Stage Milestone Automation (50% DP Scope Freeze & 50% Settlement)' : 'Otomatisasi Termin 2 Tahap (DP 50% & Pelunasan 50%)',
                'given' => $isEn ? "Traveler selects custom day tour or overland booking with total cost IDR 5,000,000." : "Wisatawan memilih paket tour harian atau overland dengan total biaya Rp 5.000.000.",
                'when' => $isEn ? "Client checks out choosing Two-Stage Milestone payment structure." : "Tamu melakukan checkout dengan skema pembayaran termin 2 tahap.",
                'then' => $isEn ? "System generates 50% DP order (IDR 2,500,000), locks schedule on payment confirmation, and schedules 50% balance reminder H-2 prior to departure." : "Sistem membuat tagihan DP 50% (Rp 2.500.000), mengunci slot jadwal saat DP lunas, dan menjadwalkan tagihan pelunasan 50% pada H-2 keberangkatan.",
            ],
            [
                'scenario' => $isEn ? 'Idempotent Webhook Processing with Pessimistic Locking' : 'Pemrosesan Webhook Idempoten dengan Pessimistic Locking',
                'given' => $isEn ? "Payment gateway dispatches duplicate HTTP webhook notifications concurrently." : "Payment gateway mengirimkan duplikasi callback webhook secara serentak karena retries jaringan.",
                'when' => $isEn ? "System processes incoming payment status update." : "Sistem memproses callback webhook mutasi pembayaran.",
                'then' => $isEn ? "System executes atomic DB::transaction with pessimistic lock (lockForUpdate) on order record, preventing double confirmation or balance distortion." : "Sistem mengeksekusi DB::transaction atomik dengan lockForUpdate pada record order, mencegah konfirmasi ganda atau distorsi saldo.",
            ],
            [
                'scenario' => $isEn ? 'Tiered Cancellation & Refund Penalty Calculation' : 'Formula Pembatalan & Pemotongan Refund Otomatis Berjenjang',
                'given' => $isEn ? "Confirmed reservation with 50% DP or full settlement requests cancellation." : "Pemesanan terkonfirmasi mengajukan pembatalan perjalanan karena force majeure atau perubahan jadwal.",
                'when' => $isEn ? "Client submits cancellation request through client portal." : "Tamu mengajukan pembatalan melalui portal klien.",
                'then' => $isEn ? "System enforces automated tiered deduction policy: >=H-7 (100% refund), H-3 to H-6 (50% refund), <=H-2 (non-refundable), logging audit trail." : "Sistem menerapkan kebijakan refund berjenjang: >=H-7 (refund 100%), H-3 s/d H-6 (refund 50%), <=H-2 (hangus), lengkap dengan catatan audit.",
            ],
        ];
        $integration = [
            'endpoint' => 'POST /api/v1/payments/create-charge',
            'middleware' => ['api', 'auth:sanctum', 'throttle:30,1'],
            'request_schema' => json_encode([
                'booking_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                'payment_stage' => 'DOWN_PAYMENT_50',
                'payment_method' => 'qris',
                'amount_idr' => 2500000,
                'customer_email' => 'client@gayabalical.com',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'response_schema' => json_encode([
                'success' => true,
                'code' => 'CHARGE_CREATED',
                'data' => [
                    'transaction_id' => 'TRX-GBC-2026-9941',
                    'order_id' => '01J7K9M0P1Q2R3S4T5U6V7W8KK',
                    'payment_type' => 'qris',
                    'gross_amount' => 2500000,
                    'qr_string' => '00020101021226...IDR',
                    'qr_image_url' => 'https://api.midtrans.com/v2/qris/01J.../qr-code',
                    'expiry_time' => '2026-11-07 15:30:00 WIB',
                    'status' => 'PENDING',
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'tasks' => $isEn ? [
                'Register payment charge generation endpoint with Sanctum authentication.',
                'Implement Midtrans/Xendit webhook controller with HMAC SHA-512 signature validation.',
                'Automate DP scope freeze and settlement milestone invoice reminders.',
            ] : [
                'Daftarkan endpoint pembuatan pembayaran dengan otentikasi Sanctum.',
                'Implementasikan webhook controller Midtrans/Xendit dengan verifikasi signature HMAC SHA-512.',
                'Otomatisasi penguncian scope DP dan pengingat pelunasan sisa termin.',
            ],
        ];
        $targetFiles = ['app/Models/Payment.php', 'app/Services/PaymentService.php', 'app/Http/Controllers/PaymentController.php', 'tests/Feature/PaymentContractTest.php'];
        $mission = "Bangun modul Payment Gateway Termin 2 Tahap (DP 50% & Pelunasan) dengan proteksi webhook idempoten dan formula refund transparan.";

        return [
            'category' => 'FINANCIAL_PAYMENT_GATEWAY',
            'category_label' => $isEn ? 'Two-Stage Payment Gateway' : 'Gerbang Pembayaran Termin 2 Tahap',
            'related_table' => $relatedTable['name'] ?? 'payments',
            'related_table_data' => $relatedTable,
            'user_story' => $isEn 
                ? "As a traveler, I want to secure my tour reservation with a flexible 50% down payment and settle the remaining balance before departure, so that my travel budget is manageable and payment is 100% secure."
                : "Sebagai wisatawan, saya ingin mengamankan pesanan tour dengan uang muka (DP) 50% fleksibel dan melunasi sisanya sebelum jalan, sehingga anggaran liburan terencana dengan aman.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'components' => ['PaymentStageCard.blade.php', 'QrisDisplayModal.blade.php', 'RefundCalculationSummary.blade.php'],
                'tasks' => $isEn ? [
                    'Construct clear Two-Stage milestone payment card displaying DP and settlement due dates.',
                    'Render dynamic QRIS barcode modal with countdown expiration timer.',
                    'Integrate instant toast feedback upon webhook confirmation without page refresh.',
                ] : [
                    'Bangun kartu pembayaran termin 2 tahap yang memperjelas jatuh tempo DP dan pelunasan.',
                    'Tampilkan modal QRIS dinamis dengan penghitung waktu mundur batas kedaluwarsa.',
                    'Integrasikan notifikasi toast melayang instan saat webhook pembayaran berhasil dikonfirmasi.',
                ],
                'states' => $isEn ? 'Pending DP, DP Settled, Pending Balance, Fully Paid, Refunded' : 'Menunggu DP, DP Lunas, Menunggu Pelunasan, Lunas Penuh, Refund Diproses',
                'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette, sharp borders (max rounded-md), zero capsule buttons, strictly ban window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
            ],
            'backend' => [
                'target_files' => ['app/Models/Payment.php', 'app/Services/PaymentService.php', 'app/Http/Controllers/PaymentController.php'],
                'tasks' => $isEn ? [
                    'Database migration for payments table with Strict ULID primary and foreign keys.',
                    'Implement PaymentService with atomic DB::transaction and pessimistic lockForUpdate.',
                    'Enforce webhook signature verification discarding tampered callbacks.',
                ] : [
                    'Migration tabel payments dengan Strict ULID 26 karakter standar PostgreSQL 16.',
                    'Implementasikan PaymentService dengan transaksi atomik dan pessimistic lockForUpdate.',
                    'Terapkan verifikasi signature webhook ketat untuk menolak callback palsu.',
                ],
                'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-PAYMENT', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=PaymentContractTest', $relatedTable, $ac, $integration, $index),
        ];
    }
}

