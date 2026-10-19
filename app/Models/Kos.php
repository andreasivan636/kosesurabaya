<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kos extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kos';

    protected $fillable = [
        'user_id',
        'nama_kos',
        'alamat',
        'kecamatan',
        'kelurahan',
        'tipe_kos',
        'latitude',
        'longitude',
        'deskripsi',
        'foto',
        'foto_utama',
        'fasilitas_umum',
        'peraturan',
        'kontak_pemilik',
        'status',
        'is_highlighted',
        'alasan_reject',
        'approved_at',
        'rating_avg',
        'rating_count',
        'kampus_terdekat',
        'jarak_kampus_km',
    ];

    protected function casts(): array
    {
        return [
            'foto' => 'array',
            'fasilitas_umum' => 'array',
            'is_highlighted' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'rating_avg' => 'decimal:1',
            'approved_at' => 'datetime',
        ];
    }

    // ─── Constants ────────────────────────────────────
    /**
     * Koordinat kampus-kampus utama di Surabaya.
     * Digunakan untuk fitur filter radius pencarian.
     */
    public const KAMPUS_COORDS = [
        'UNTAG' => ['lat' => -7.3148, 'lng' => 112.7681, 'nama' => 'UNTAG Surabaya'],
        'ITS'   => ['lat' => -7.2820, 'lng' => 112.7952, 'nama' => 'Institut Teknologi Sepuluh Nopember'],
        'UNAIR' => ['lat' => -7.2696, 'lng' => 112.7583, 'nama' => 'Universitas Airlangga'],
        'UNESA' => ['lat' => -7.2871, 'lng' => 112.7189, 'nama' => 'Universitas Negeri Surabaya'],
    ];

    // ─── Scopes ───────────────────────────────────────
    /**
     * Hanya tampilkan kos yang sudah disetujui admin.
     * Keamanan: kos pending/rejected TIDAK boleh muncul di pencarian.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Filter kos berdasarkan radius dari kampus (Haversine formula).
     */
    public function scopeWithinRadius($query, float $lat, float $lng, float $radiusKm = 1.0)
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        return $query
            ->selectRaw("*, {$haversine} AS jarak", [$lat, $lng, $lat])
            ->having('jarak', '<=', $radiusKm)
            ->orderBy('jarak');
    }

    /**
     * Scope: kos yang punya kamar kosong.
     */
    public function scopeAdaKamarKosong($query)
    {
        return $query->whereHas('tipeKamar', function ($q) {
            $q->where('kamar_tersedia', '>', 0);
        });
    }

    // ─── Relationships ────────────────────────────────
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipeKamar()
    {
        return $this->hasMany(TipeKamar::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('is_active', true);
    }

    // ─── Helpers ──────────────────────────────────────
    /**
     * Cek apakah kos ini punya subscription aktif (marker glow).
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription()
            ->where('status_bayar', 'paid')
            ->where('berakhir', '>=', now())
            ->exists();
    }

    /**
     * Cek apakah kos ini berstatus Premium (is_highlighted atau subscription aktif).
     */
    public function isPremium(): bool
    {
        return (bool) $this->is_highlighted || $this->hasActiveSubscription();
    }

    /**
     * Harga termurah dari semua tipe kamar.
     */
    public function getHargaMulaiAttribute(): ?int
    {
        return $this->tipeKamar->min('harga_bulanan');
    }

    /**
     * Total kamar kosong dari semua tipe.
     */
    public function getTotalKamarKosongAttribute(): int
    {
        return $this->tipeKamar->sum('kamar_tersedia');
    }
}
