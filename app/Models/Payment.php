<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'verified_by', 'type', 'amount', 'due_date', 'method',
        'status', 'proof_path', 'notes', 'paid_at', 'verified_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Pemilik yang memverifikasi pembayaran
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'deposit' => 'Deposit',
            'rent' => 'Sewa',
            'late_fee' => 'Denda',
            default => 'Pengembalian Deposit',
        };
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'cash' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS',
            default => '-',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'Lunas',
            'waiting_verification' => 'Menunggu Verifikasi',
            'overdue' => 'Terlambat',
            default => 'Belum Dibayar',
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'paid' => 'badge-green',
            'waiting_verification' => 'badge-yellow',
            'overdue' => 'badge-red',
            default => 'badge-gray',
        };
    }
}