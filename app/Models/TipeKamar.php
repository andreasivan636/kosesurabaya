<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeKamar extends Model
{
    use HasFactory;

    protected $table = 'tipe_kamar';

    protected $fillable = [
        'kos_id',
        'tipe',
        'nama_tipe',
        'deskripsi',
        'harga_bulanan',
        'harga_tahunan',
        'jumlah_kamar',
        'kamar_tersedia',
        'ukuran_kamar',
        'fasilitas',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'fasilitas' => 'array',
            'foto' => 'array',
            'harga_bulanan' => 'integer',
            'harga_tahunan' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────
    public function kos()
    {
        return $this->belongsTo(Kos::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    // ─── Helpers ──────────────────────────────────────
    public function isAvailable(): bool
    {
        return $this->kamar_tersedia > 0;
    }

    /**
     * Label fasilitas dengan ikon.
     */
    public const FASILITAS_ICONS = [
        'AC' => 'snowflake',
        'WiFi' => 'wifi',
        'Kamar Mandi Dalam' => 'bath',
        'Kasur' => 'bed',
        'Lemari' => 'archive',
        'Meja Belajar' => 'edit',
        'Kipas Angin' => 'wind',
        'TV' => 'tv',
        'Dapur' => 'coffee',
        'Parkir Motor' => 'truck',
        'Parkir Mobil' => 'truck',
        'Laundry' => 'loader',
        'CCTV' => 'video',
        'Penjaga Kos' => 'shield',
    ];
}
