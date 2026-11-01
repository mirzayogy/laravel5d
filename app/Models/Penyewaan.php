<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penyewaan extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal_sewa',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sewa' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_dikembalikan' => 'date',
        'total_harga' => 'decimal:2',
    ];

    /** Penyewaan dibuat oleh satu user (N:1). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Satu penyewaan memiliki banyak baris detail (1:N). */
    public function detailPenyewaans(): HasMany
    {
        return $this->hasMany(DetailPenyewaan::class);
    }

    /** Satu penyewaan menyewa banyak barang (N:N via detail_penyewaans). */
    public function barangs(): BelongsToMany
    {
        return $this->belongsToMany(Barang::class, 'detail_penyewaans')
            ->withPivot(['jumlah', 'harga_satuan', 'subtotal'])
            ->withTimestamps();
    }

    /** Satu penyewaan memiliki satu pembayaran (1:1). */
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class);
    }
}
