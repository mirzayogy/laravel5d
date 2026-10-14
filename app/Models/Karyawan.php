<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Extends Authenticatable supaya bisa dipakai untuk login
class Karyawan extends Authenticatable
{
    use Notifiable;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';

    protected $fillable = ['nama', 'username', 'password', 'role'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            // Password otomatis di-hash saat disimpan
            'password' => 'hashed',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_karyawan', 'id_karyawan');
    }
}
