<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    // Mengizinkan 4 kolom ini diisi form
    protected $fillable = ['user_id', 'name', 'balance', 'type'];

    public function transactions(): HasMany           // <-- TAMBAH BLOK INI
    {
        return $this->hasMany(Transaction::class);
    }
}
