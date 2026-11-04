<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'month'       => ['nullable', 'date_format:Y-m'],
            'type'        => ['nullable', Rule::in(['income', 'expense'])],
            'account_id'  => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
        ]);

        $base = Transaction::query()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('account_id'), fn ($q) => $q->where('account_id', $request->account_id))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('month'), function ($q) use ($request) {
                $date = Carbon::createFromFormat('!Y-m', $request->month);
                $q->whereBetween('transaction_date', [
                    $date->copy()->startOfMonth()->toDateString(),
                    $date->copy()->endOfMonth()->toDateString(),
                ]);
            });

        $totalIncome  = (clone $base)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $base)->where('type', 'expense')->sum('amount');

        $transactions = $base
            ->with(['account', 'category'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'accounts'     => Account::orderBy('name')->get(),
            'categories'   => Category::orderBy('name')->get(),
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
        ]);
    }

    public function create()
    {
        return view('transactions.create', [
            'accounts'   => Account::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $account = Account::lockForUpdate()->findOrFail($data['account_id']);

            Transaction::create($data);

            $account->increment('balance', $this->delta($data['type'], $data['amount']));
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction)
    {
        return view('transactions.edit', [
            'transaction' => $transaction,
            'accounts'    => Account::orderBy('name')->get(),
            'categories'  => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($transaction, $data) {
            // Ambil ulang dengan lock agar data lama untuk rollback saldo pasti terbaru
            $transaction = Transaction::lockForUpdate()->findOrFail($transaction->id);

            // Kunci akun lama & baru dengan urutan id yang sama (mencegah deadlock)
            $ids = array_unique([$transaction->account_id, (int) $data['account_id']]);
            $accounts = Account::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            // 1) Batalkan efek transaksi lama pada akun lama
            $accounts[$transaction->account_id]
                ->increment('balance', -$this->delta($transaction->type, $transaction->amount));

            // 2) Terapkan efek transaksi baru pada akun baru
            $accounts[(int) $data['account_id']]
                ->increment('balance', $this->delta($data['type'], $data['amount']));

            $transaction->update($data);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $transaction = Transaction::lockForUpdate()->findOrFail($transaction->id);
            $account = Account::lockForUpdate()->findOrFail($transaction->account_id);

            // Batalkan efek transaksi terhadap saldo
            $account->increment('balance', -$this->delta($transaction->type, $transaction->amount));

            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Pengaruh transaksi ke saldo: income = +amount, expense = -amount.
     */
    private function delta(string $type, $amount): float
    {
        return $type === 'income' ? (float) $amount : -(float) $amount;
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'type'       => ['required', Rule::in(['income', 'expense'])],
            // Kategori harus ada DAN tipenya cocok dengan tipe transaksi
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('type', $request->input('type')),
            ],
            'amount'           => ['required', 'numeric', 'min:0.01', 'max:9999999999999'],
            'transaction_date' => ['required', 'date'],
            'description'      => ['nullable', 'string', 'max:500'],
        ]);
    }
}
