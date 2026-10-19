<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi';

    protected $fillable = [
        'order_id',
        'user_id',
        'tipe_kamar_id',
        'kos_id',
        'jumlah_bayar',
        'durasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'snap_token',
        'payment_type',
        'status',
        'midtrans_response',
        'paid_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'midtrans_response' => 'array',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'paid_at' => 'datetime',
            'jumlah_bayar' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kos()
    {
        return $this->belongsTo(Kos::class);
    }

    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class);
    }

    // ─── Helpers ──────────────────────────────────────
    /**
     * Generate order ID unik: KSS-{timestamp}-{userId}
     */
    public static function generateOrderId(int $userId): string
    {
        return 'KSS-' . time() . '-' . $userId;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'waiting_payment']);
    }
}
