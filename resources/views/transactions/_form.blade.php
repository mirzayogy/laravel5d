@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <x-icon name="alert-circle" size="20" />
        <div>
            <strong>Periksa kembali isian Anda:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@php $currentType = old('type', $transaction?->type ?? 'expense'); @endphp

<div class="field">
    <span class="label" id="type-label">Tipe Transaksi</span>
    <div class="segmented" role="radiogroup" aria-labelledby="type-label">
        <input type="radio" name="type" id="type_expense" value="expense" @checked($currentType === 'expense')>
        <label for="type_expense" class="is-expense"><x-icon name="arrow-down-circle" size="18" /> Pengeluaran</label>

        <input type="radio" name="type" id="type_income" value="income" @checked($currentType === 'income')>
        <label for="type_income" class="is-income"><x-icon name="arrow-up-circle" size="18" /> Pemasukan</label>
    </div>
</div>

<div class="form-row">
    <div class="field">
        <label class="label" for="account_id">Akun</label>
        <select class="input @error('account_id') is-invalid @enderror" name="account_id" id="account_id" required>
            <option value="">-- Pilih akun --</option>
            @foreach ($accounts as $account)
                <option value="{{ $account->id }}"
                    @selected((string) old('account_id', $transaction?->account_id) === (string) $account->id)>
                    {{ $account->name }} (Rp {{ number_format($account->balance, 0, ',', '.') }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label class="label" for="category_id">Kategori</label>
        <select class="input @error('category_id') is-invalid @enderror" name="category_id" id="category_id" required></select>
    </div>
</div>

<div class="form-row">
    <div class="field">
        <label class="label" for="amount">Jumlah</label>
        <div class="input-group">
            <span class="prefix" aria-hidden="true">Rp</span>
            <input class="input @error('amount') is-invalid @enderror" type="number" name="amount" id="amount"
                   step="0.01" min="0.01" placeholder="0"
                   value="{{ old('amount', $transaction?->amount) }}" required>
        </div>
    </div>

    <div class="field">
        <label class="label" for="transaction_date">Tanggal</label>
        <input class="input @error('transaction_date') is-invalid @enderror" type="date" name="transaction_date" id="transaction_date"
               value="{{ old('transaction_date', $transaction?->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
               required>
    </div>
</div>

<div class="field">
    <label class="label" for="description">Deskripsi <span class="label-opt">(opsional)</span></label>
    <textarea class="input @error('description') is-invalid @enderror" name="description" id="description" rows="3"
              placeholder="Contoh: makan siang, gaji bulanan...">{{ old('description', $transaction?->description) }}</textarea>
</div>

<div class="form-actions">
    <button type="submit" class="btn">{{ $submitLabel }}</button>
    <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Batal</a>
</div>

{{-- Dropdown kategori menyesuaikan tipe transaksi --}}
<script>
    (function () {
        const typeEls = document.querySelectorAll('input[name="type"]');
        const catEl = document.getElementById('category_id');
        const categories = @json($categories->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'type' => $c->type])->values());
        const selectedId = "{{ old('category_id', $transaction?->category_id) }}";

        function currentType() {
            return document.querySelector('input[name="type"]:checked').value;
        }

        function renderCategories() {
            const keep = catEl.value || selectedId;
            catEl.innerHTML = '<option value="">-- Pilih kategori --</option>';

            categories
                .filter(c => c.type === currentType())
                .forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    if (String(c.id) === String(keep)) opt.selected = true;
                    catEl.appendChild(opt);
                });
        }

        typeEls.forEach(el => el.addEventListener('change', () => { catEl.value = ''; renderCategories(); }));
        renderCategories();
    })();
</script>
