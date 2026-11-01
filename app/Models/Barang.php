<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $fillable = [
        'kategori_id',
        'nama_barang',
        'deskripsi',
        'harga_sewa_per_hari',
        'stok',
        'kondisi',
    ];

    protected $casts = [
        'harga_sewa_per_hari' => 'decimal:2',
        'stok' => 'integer',
    ];

    /** Barang dimiliki oleh satu kategori (N:1). */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /** Barang muncul di banyak baris detail penyewaan (1:N). */
    public function detailPenyewaans(): HasMany
    {
        return $this->hasMany(DetailPenyewaan::class);
    }

    /** Barang bisa disewa dalam banyak penyewaan (N:N via detail_penyewaans). */
    public function penyewaans(): BelongsToMany
    {
        return $this->belongsToMany(Penyewaan::class, 'detail_penyewaans')
            ->withPivot(['jumlah', 'harga_satuan', 'subtotal'])
            ->withTimestamps();
    }
}
