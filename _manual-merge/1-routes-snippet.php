<?php
// Tambahkan di routes/web.php

use App\Http\Controllers\TransactionController;

Route::resource('transactions', TransactionController::class)->except('show');
