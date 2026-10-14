<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';

    protected $fillable = ['nama_layanan', 'harga_per_kg', 'estimasi_hari'];

    protected $casts = [
        'harga_per_kg' => 'decimal:2',
    ];

    public function detail(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_layanan', 'id_layanan');
    }
}
