<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'kos_id',
        'paket',
        'harga',
        'mulai_aktif',
        'berakhir',
        'is_active',
        'order_id',
        'snap_token',
        'status_bayar',
        'marker_glow',
        'prioritas_pencarian',
        'max_foto',
    ];

    protected function casts(): array
    {
        return [
            'mulai_aktif' => 'date',
            'berakhir' => 'date',
            'is_active' => 'boolean',
            'marker_glow' => 'boolean',
            'prioritas_pencarian' => 'boolean',
        ];
    }

    /**
     * Paket subscription dengan fitur masing-masing.
     */
    public const PAKET_OPTIONS = [
        'basic' => [
            'nama' => 'Basic',
            'harga' => 50000,
            'durasi_hari' => 30,
            'marker_glow' => false,
            'prioritas_pencarian' => false,
            'max_foto' => 5,
        ],
        'premium' => [
            'nama' => 'Premium',
            'harga' => 100000,
            'durasi_hari' => 30,
            'marker_glow' => true,
            'prioritas_pencarian' => true,
            'max_foto' => 15,
        ],
        'ultimate' => [
            'nama' => 'Ultimate',
            'harga' => 200000,
            'durasi_hari' => 90,
            'marker_glow' => true,
            'prioritas_pencarian' => true,
            'max_foto' => 30,
        ],
    ];

    // ─── Relationships ────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kos()
    {
        return $this->belongsTo(Kos::class);
    }

    // ─── Scopes ───────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('status_bayar', 'paid')
                     ->where('berakhir', '>=', now());
    }

    // ─── Helpers ──────────────────────────────────────
    public function isExpired(): bool
    {
        return $this->berakhir->isPast();
    }
}
