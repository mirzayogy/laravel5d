<?php
// Tambahkan method ini di app/Models/Account.php DAN app/Models/Category.php
// (dan satu baris use di bagian atas file)

use Illuminate\Database\Eloquent\Relations\HasMany;

public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class);
}
