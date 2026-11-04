<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    // Menampilkan semua data akun
    public function index()
    {
        $accounts = Account::all();
        return view('accounts.index', compact('accounts'));
    }

    // Menampilkan form tambah akun
    public function create()
    {
        return view('accounts.create');
    }

    // Menyimpan data dari form ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|in:cash,bank,e-wallet',
            'balance' => 'required|numeric'
        ], [
            'name.required' => 'Nama akun wajib diisi!',
            'type.in' => 'Pilihan tipe tidak valid!',
            'balance.numeric' => 'Saldo harus berupa angka!'
        ]);

        Account::create([
            'name' => $request->name,
            'balance' => $request->balance,
            'type' => $request->type
        ]);

        return redirect()->route('accounts.index');
    }

    // Menampilkan form edit data (INI YANG HILANG)
    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    // Menyimpan perubahan data ke database
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|in:cash,bank,e-wallet',
            'balance' => 'required|numeric'
        ]);

        $account->update([
            'name' => $request->name,
            'balance' => $request->balance,
            'type' => $request->type
        ]);

        return redirect()->route('accounts.index');
    }

    // Menghapus data dari database
    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index');
    }
}