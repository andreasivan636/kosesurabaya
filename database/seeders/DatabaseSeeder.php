<?php

namespace Database\Seeders;

use App\Models\Kos;
use App\Models\Subscription;
use App\Models\TipeKamar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data demo KoSeSurabaya.
     * Buat akun tiap role + data kos lengkap untuk presentasi lomba.
     */
    public function run(): void
    {
        // ─── ADMIN ────────────────────────────────────
        User::create([
            'name' => 'Admin KoSeSurabaya',
            'email' => 'admin@kosesurabaya.com',
            'password' => 'password123',
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // ─── CUSTOMER (Mahasiswa) ─────────────────────
        $customer1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mahasiswa.its.ac.id',
            'password' => 'password123',
            'role' => 'customer',
            'phone' => '082345678901',
        ]);

        $customer2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@mahasiswa.unair.ac.id',
            'password' => 'password123',
            'role' => 'customer',
            'phone' => '083456789012',
        ]);

        // ─── PEMILIK KOS ─────────────────────────────
        $owner1 = User::create([
            'name' => 'Pak Hartono',
            'email' => 'hartono@gmail.com',
            'password' => 'password123',
            'role' => 'owner',
            'phone' => '085678901234',
        ]);

        $owner2 = User::create([
            'name' => 'Bu Sumiati',
            'email' => 'sumiati@gmail.com',
            'password' => 'password123',
            'role' => 'owner',
            'phone' => '086789012345',
        ]);

        $owner3 = User::create([
            'name' => 'Pak Djoko Widodo',
            'email' => 'djoko@gmail.com',
            'password' => 'password123',
            'role' => 'owner',
            'phone' => '087890123456',
        ]);

        // ─── KOS DATA ────────────────────────────────
        // Kos 1: Dekat ITS (APPROVED + SUBSCRIPTION PREMIUM)
        $kos1 = Kos::create([
            'user_id' => $owner1->id,
            'nama_kos' => 'Kost Mawar Surabaya',
            'alamat' => 'Jl. Teknik Kimia No. 12, Keputih, Sukolilo',
            'kecamatan' => 'Sukolilo',
            'kelurahan' => 'Keputih',
            'tipe_kos' => 'campur',
            'latitude' => -7.2845,
            'longitude' => 112.7935,
            'deskripsi' => 'Kos nyaman dan strategis hanya 500 meter dari pintu gerbang ITS. Lingkungan aman dengan CCTV 24 jam dan penjaga kos. Cocok untuk mahasiswa yang mengutamakan kenyamanan dan keamanan.',
            'fasilitas_umum' => ['Parkir Motor', 'Dapur Bersama', 'CCTV', 'Penjaga Kos', 'WiFi Area', 'Jemuran'],
            'peraturan' => "1. Tamu lawan jenis max jam 21.00\n2. Tidak boleh membawa hewan peliharaan\n3. Bayar tepat waktu setiap tanggal 1\n4. Jaga kebersihan area bersama",
            'kontak_pemilik' => '085678901234',
            'status' => 'approved',
            'approved_at' => now(),
            'rating_avg' => 4.5,
            'rating_count' => 28,
            'kampus_terdekat' => 'ITS',
            'jarak_kampus_km' => 0.5,
        ]);

        // Tipe Kamar Kos 1
        TipeKamar::create([
            'kos_id' => $kos1->id,
            'tipe' => 'A',
            'nama_tipe' => 'Deluxe',
            'deskripsi' => 'Kamar premium dengan AC, kamar mandi dalam, dan fully furnished. Ideal untuk mahasiswa yang menginginkan privasi maksimal.',
            'harga_bulanan' => 1500000,
            'harga_tahunan' => 16000000,
            'jumlah_kamar' => 5,
            'kamar_tersedia' => 2,
            'ukuran_kamar' => 16,
            'fasilitas' => ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar', 'TV'],
        ]);

        TipeKamar::create([
            'kos_id' => $kos1->id,
            'tipe' => 'B',
            'nama_tipe' => 'Standard',
            'deskripsi' => 'Kamar nyaman dengan kipas angin dan kamar mandi dalam. Pilihan terbaik untuk harga terjangkau.',
            'harga_bulanan' => 1000000,
            'harga_tahunan' => 11000000,
            'jumlah_kamar' => 8,
            'kamar_tersedia' => 3,
            'ukuran_kamar' => 12,
            'fasilitas' => ['Kipas Angin', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari'],
        ]);

        TipeKamar::create([
            'kos_id' => $kos1->id,
            'tipe' => 'C',
            'nama_tipe' => 'Ekonomi',
            'deskripsi' => 'Kamar hemat dengan fasilitas dasar. Kamar mandi bersama. Cocok untuk mahasiswa dengan budget terbatas.',
            'harga_bulanan' => 600000,
            'harga_tahunan' => 6500000,
            'jumlah_kamar' => 10,
            'kamar_tersedia' => 5,
            'ukuran_kamar' => 9,
            'fasilitas' => ['Kipas Angin', 'Kasur', 'Lemari'],
        ]);

        // Subscription Premium untuk Kos 1 (marker GLOW!)
        Subscription::create([
            'user_id' => $owner1->id,
            'kos_id' => $kos1->id,
            'paket' => 'premium',
            'harga' => 100000,
            'mulai_aktif' => now(),
            'berakhir' => now()->addMonth(),
            'is_active' => true,
            'order_id' => 'SUB-' . time() . '-1',
            'status_bayar' => 'paid',
            'marker_glow' => true,
            'prioritas_pencarian' => true,
            'max_foto' => 15,
        ]);

        // Kos 2: Dekat UNAIR (APPROVED)
        $kos2 = Kos::create([
            'user_id' => $owner2->id,
            'nama_kos' => 'Kost Putri Melati',
            'alamat' => 'Jl. Dharmawangsa No. 8, Airlangga, Gubeng',
            'kecamatan' => 'Gubeng',
            'kelurahan' => 'Airlangga',
            'tipe_kos' => 'putri',
            'latitude' => -7.2710,
            'longitude' => 112.7600,
            'deskripsi' => 'Kost khusus putri dengan keamanan ekstra. Lokasi strategis dekat Kampus B Unair. Lingkungan tenang dan asri, cocok untuk belajar.',
            'fasilitas_umum' => ['Parkir Motor', 'CCTV', 'Penjaga Kos', 'Laundry', 'Dapur Bersama', 'Musholla'],
            'peraturan' => "1. Khusus wanita\n2. Tamu lawan jenis TIDAK diperbolehkan masuk\n3. Jam malam 22.00\n4. Wajib lapor jika menginap di luar",
            'kontak_pemilik' => '086789012345',
            'status' => 'approved',
            'approved_at' => now(),
            'rating_avg' => 4.8,
            'rating_count' => 42,
            'kampus_terdekat' => 'UNAIR',
            'jarak_kampus_km' => 0.3,
        ]);

        TipeKamar::create([
            'kos_id' => $kos2->id, 'tipe' => 'A', 'nama_tipe' => 'VIP Room',
            'harga_bulanan' => 1800000, 'harga_tahunan' => 19000000,
            'jumlah_kamar' => 4, 'kamar_tersedia' => 1, 'ukuran_kamar' => 18,
            'fasilitas' => ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar', 'TV', 'Dapur'],
        ]);

        TipeKamar::create([
            'kos_id' => $kos2->id, 'tipe' => 'B', 'nama_tipe' => 'Comfort',
            'harga_bulanan' => 1200000, 'harga_tahunan' => 13000000,
            'jumlah_kamar' => 6, 'kamar_tersedia' => 2, 'ukuran_kamar' => 14,
            'fasilitas' => ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar'],
        ]);

        TipeKamar::create([
            'kos_id' => $kos2->id, 'tipe' => 'C', 'nama_tipe' => 'Budget',
            'harga_bulanan' => 750000, 'harga_tahunan' => 8000000,
            'jumlah_kamar' => 8, 'kamar_tersedia' => 4, 'ukuran_kamar' => 10,
            'fasilitas' => ['Kipas Angin', 'WiFi', 'Kasur', 'Lemari'],
        ]);

        // Kos 3: Dekat UNTAG (APPROVED)
        $kos3 = Kos::create([
            'user_id' => $owner1->id,
            'nama_kos' => 'Kost Putra Semolowaru',
            'alamat' => 'Jl. Semolowaru Utara V No. 22, Semolowaru',
            'kecamatan' => 'Sukolilo',
            'kelurahan' => 'Semolowaru',
            'tipe_kos' => 'putra',
            'latitude' => -7.3130,
            'longitude' => 112.7700,
            'deskripsi' => 'Kost putra harga terjangkau dekat kampus UNTAG Surabaya. Akses mudah ke jalan raya. Parkir luas untuk motor.',
            'fasilitas_umum' => ['Parkir Motor', 'Parkir Mobil', 'WiFi Area', 'Jemuran'],
            'peraturan' => "1. Khusus pria\n2. Tidak merokok di dalam kamar\n3. Bayar H-3 sebelum tanggal jatuh tempo",
            'kontak_pemilik' => '085678901234',
            'status' => 'approved',
            'approved_at' => now(),
            'rating_avg' => 4.2,
            'rating_count' => 15,
            'kampus_terdekat' => 'UNTAG',
            'jarak_kampus_km' => 0.8,
        ]);

        TipeKamar::create([
            'kos_id' => $kos3->id, 'tipe' => 'A', 'nama_tipe' => 'Premium',
            'harga_bulanan' => 1200000, 'jumlah_kamar' => 3, 'kamar_tersedia' => 1,
            'ukuran_kamar' => 15,
            'fasilitas' => ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar'],
        ]);

        TipeKamar::create([
            'kos_id' => $kos3->id, 'tipe' => 'B', 'nama_tipe' => 'Regular',
            'harga_bulanan' => 800000, 'jumlah_kamar' => 6, 'kamar_tersedia' => 3,
            'ukuran_kamar' => 12,
            'fasilitas' => ['Kipas Angin', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari'],
        ]);

        TipeKamar::create([
            'kos_id' => $kos3->id, 'tipe' => 'C', 'nama_tipe' => 'Hemat',
            'harga_bulanan' => 500000, 'jumlah_kamar' => 10, 'kamar_tersedia' => 6,
            'ukuran_kamar' => 9,
            'fasilitas' => ['Kipas Angin', 'Kasur'],
        ]);

        // Kos 4: Dekat UNESA (APPROVED + SUBSCRIPTION)
        $kos4 = Kos::create([
            'user_id' => $owner3->id,
            'nama_kos' => 'Kost Harmoni Lidah Wetan',
            'alamat' => 'Jl. Lidah Wetan III No. 15, Lakarsantri',
            'kecamatan' => 'Lakarsantri',
            'kelurahan' => 'Lidah Wetan',
            'tipe_kos' => 'campur',
            'latitude' => -7.2885,
            'longitude' => 112.7210,
            'deskripsi' => 'Kos modern dekat UNESA Lidah Wetan. Fasilitas lengkap, lingkungan bersih dan aman. Free WiFi highspeed di semua kamar.',
            'fasilitas_umum' => ['Parkir Motor', 'CCTV', 'WiFi Area', 'Dapur Bersama', 'Taman', 'Laundry'],
            'kontak_pemilik' => '087890123456',
            'status' => 'approved',
            'approved_at' => now(),
            'rating_avg' => 4.6,
            'rating_count' => 33,
            'kampus_terdekat' => 'UNESA',
            'jarak_kampus_km' => 0.4,
        ]);

        TipeKamar::create([
            'kos_id' => $kos4->id, 'tipe' => 'A', 'nama_tipe' => 'Suite',
            'harga_bulanan' => 1600000, 'harga_tahunan' => 17000000,
            'jumlah_kamar' => 4, 'kamar_tersedia' => 2, 'ukuran_kamar' => 20,
            'fasilitas' => ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar', 'TV', 'Dapur'],
        ]);

        TipeKamar::create([
            'kos_id' => $kos4->id, 'tipe' => 'B', 'nama_tipe' => 'Standard Plus',
            'harga_bulanan' => 1100000, 'jumlah_kamar' => 8, 'kamar_tersedia' => 4,
            'ukuran_kamar' => 14,
            'fasilitas' => ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari'],
        ]);

        TipeKamar::create([
            'kos_id' => $kos4->id, 'tipe' => 'C', 'nama_tipe' => 'Ekonomis',
            'harga_bulanan' => 650000, 'jumlah_kamar' => 12, 'kamar_tersedia' => 7,
            'ukuran_kamar' => 9,
            'fasilitas' => ['Kipas Angin', 'WiFi', 'Kasur', 'Lemari'],
        ]);

        // Subscription untuk Kos 4 (marker GLOW!)
        Subscription::create([
            'user_id' => $owner3->id,
            'kos_id' => $kos4->id,
            'paket' => 'ultimate',
            'harga' => 200000,
            'mulai_aktif' => now(),
            'berakhir' => now()->addMonths(3),
            'is_active' => true,
            'order_id' => 'SUB-' . time() . '-4',
            'status_bayar' => 'paid',
            'marker_glow' => true,
            'prioritas_pencarian' => true,
            'max_foto' => 30,
        ]);

        // Kos 5: PENDING (belum diverifikasi admin — untuk demo flow admin)
        $kos5 = Kos::create([
            'user_id' => $owner2->id,
            'nama_kos' => 'Kost Baru Bu Sumiati',
            'alamat' => 'Jl. Mulyosari Baru No. 7, Mulyorejo',
            'kecamatan' => 'Mulyorejo',
            'kelurahan' => 'Mulyosari',
            'tipe_kos' => 'putri',
            'latitude' => -7.2650,
            'longitude' => 112.7880,
            'deskripsi' => 'Kos baru direnovasi, lingkungan perumahan tenang dekat kampus C Unair.',
            'fasilitas_umum' => ['Parkir Motor', 'CCTV', 'Dapur Bersama'],
            'kontak_pemilik' => '086789012345',
            'status' => 'pending',
        ]);

        TipeKamar::create([
            'kos_id' => $kos5->id, 'tipe' => 'A', 'nama_tipe' => 'Exclusive',
            'harga_bulanan' => 1400000, 'jumlah_kamar' => 3, 'kamar_tersedia' => 3,
            'fasilitas' => ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar'],
        ]);
        TipeKamar::create([
            'kos_id' => $kos5->id, 'tipe' => 'B', 'nama_tipe' => 'Standard',
            'harga_bulanan' => 900000, 'jumlah_kamar' => 5, 'kamar_tersedia' => 5,
            'fasilitas' => ['Kipas Angin', 'WiFi', 'Kamar Mandi Dalam', 'Kasur'],
        ]);
        TipeKamar::create([
            'kos_id' => $kos5->id, 'tipe' => 'C', 'nama_tipe' => 'Ekonomi',
            'harga_bulanan' => 550000, 'jumlah_kamar' => 7, 'kamar_tersedia' => 7,
            'fasilitas' => ['Kipas Angin', 'Kasur'],
        ]);

        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  🏠 KoSeSurabaya Demo Data Seeded!');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('');
        $this->command->info('  AKUN DEMO:');
        $this->command->info('  ┌─────────────┬──────────────────────────────┬───────────────┐');
        $this->command->info('  │ Role        │ Email                        │ Password      │');
        $this->command->info('  ├─────────────┼──────────────────────────────┼───────────────┤');
        $this->command->info('  │ Admin       │ admin@kosesurabaya.com       │ password123   │');
        $this->command->info('  │ Customer    │ budi@mahasiswa.its.ac.id     │ password123   │');
        $this->command->info('  │ Customer    │ siti@mahasiswa.unair.ac.id   │ password123   │');
        $this->command->info('  │ Owner       │ hartono@gmail.com            │ password123   │');
        $this->command->info('  │ Owner       │ sumiati@gmail.com            │ password123   │');
        $this->command->info('  │ Owner       │ djoko@gmail.com              │ password123   │');
        $this->command->info('  └─────────────┴──────────────────────────────┴───────────────┘');
        $this->command->info('');
        $this->command->info("  4 kos APPROVED (2 with glowing subscription)");
        $this->command->info("  1 kos PENDING (for admin demo)");
        $this->command->info('');
    }
}
