<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $fillable = ['nama_kategori', 'deskripsi'];

    /** Satu kategori memiliki banyak barang (1:N). */
    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
