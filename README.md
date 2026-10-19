# 🏠 KoSeSurabaya

🏆 **Pemenang "Best Digital Product" - ExplorAItion Batch 4**

KoSeSurabaya adalah sebuah *marketplace* penyewaan properti kos berbasis *Geographic Information System* (GIS). Aplikasi ini dirancang untuk menyelesaikan dua masalah utama di Surabaya: kesulitan pencari kos dalam menemukan lokasi yang valid dan aman dari penipuan, serta kurangnya visibilitas bagi pemilik kos skala menengah-bawah.

## 📌 Konsep & Model Bisnis
Proyek ini mengusung model *Two-Sided Market* dengan dua aliran pendapatan utama (*Revenue Stream*):
1. **B2B Subscription (Langganan Premium):** Pemilik kos dapat berlangganan paket premium untuk mengubah *pin* lokasi kos mereka di peta menjadi ikon kelap-kelip (animasi menyala). Ini adalah fitur *Growth Hack* yang terbukti meningkatkan *exposure* secara instan.
2. **B2C Transaction Fee (Komisi Transaksi):** Aplikasi menggunakan sistem pembayaran *Escrow* (rekening bersama). Uang sewa dari mahasiswa ditahan dengan aman oleh sistem, dipotong biaya platform (platform fee), lalu dicairkan ke rekening pemilik kos secara otomatis.

## 🚀 Fitur Utama
* **Interactive Clean Map:** Pemetaan cerdas menggunakan Leaflet.js yang membersihkan *cluster* pin tidak penting agar pengguna fokus pada ketersediaan kamar.
* **Integrated Escrow Payment:** Sistem pembayaran *real-time* menggunakan Midtrans Sandbox (Virtual Account, QRIS, dll).
* **AI Customer Service:** Asisten virtual 24/7 berbasis model *TinyLlama* untuk memberikan rekomendasi kos cerdas.
* **Smart Inventory:** Sinkronisasi kamar kosong secara otomatis setelah pembayaran sukses.

## 🛠️ Teknologi Pendukung
* **Backend:** Laravel 11, MySQL
* **Frontend:** Blade Templating, Alpine.js, Tailwind CSS
* **Mapping:** Leaflet.js
* **Payment Gateway:** Midtrans (Snap API)
* **AI Integrations:** Ollama (TinyLlama lokal)

---

## 📱 Cara Uji Coba Lintas Perangkat (Akses di HP via LAN/Hotspot)
Aplikasi ini dapat didemokan secara langsung melalui *smartphone* (HP) dengan memanfaatkan jaringan lokal yang sama dengan laptop *developer*. Berikut langkah-langkahnya:

**1. Pastikan Jaringan Sama**
Hubungkan HP dan Laptop ke jaringan Wi-Fi yang sama, atau nyalakan *Hotspot* dari HP lalu sambungkan Laptop ke *hotspot* tersebut.

**2. Cari Alamat IP Laptop**
* Buka **Command Prompt (cmd)** di laptop.
* Ketik `ipconfig` lalu tekan Enter.
* Cari deretan angka di sebelah **IPv4 Address** (contoh: `192.168.1.10` atau `10.89.x.x`). Catat IP tersebut.

**3. Perbarui Konfigurasi `.env`**
Buka file `.env` di *root* proyek, cari baris `SANCTUM_STATEFUL_DOMAINS` dan tambahkan IP laptop Anda beserta *port* 8000 agar tidak terjadi *error* saat *login* dari HP.
```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8000,127.0.0.1,127.0.0.1:8000,<IP_LAPTOP_ANDA>,<IP_LAPTOP_ANDA>:8000
