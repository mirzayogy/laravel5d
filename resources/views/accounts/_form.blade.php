@php
    $types = [
        'cash'     => ['Cash', 'Uang tunai', 'banknote'],
        'bank'     => ['Bank', 'Rekening bank', 'landmark'],
        'e-wallet' => ['E-Wallet', 'Dompet digital', 'smartphone'],
    ];
    $currentType = strtolower((string) old('type', $account?->type));
@endphp

<div class="field">
    <label class="label" for="name">Nama Akun</label>
    <!-- value old('name') agar ketikan tidak hilang saat error -->
    <input class="input @error('name') is-invalid @enderror" type="text" name="name" id="name"
           placeholder="Contoh: Dompet Harian, BCA, GoPay"
           value="{{ old('name', $account?->name) }}"
           @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
    @error('name')
        <div class="error-text" id="name-error">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <span class="label" id="type-label">Tipe Akun</span>
    <div class="type-cards @error('type') is-invalid @enderror" role="radiogroup" aria-labelledby="type-label">
        @foreach ($types as $value => $info)
            <input type="radio" name="type" id="type_{{ $value }}" value="{{ $value }}"
                   @checked($currentType === $value) @if ($loop->first) required @endif>
            <label for="type_{{ $value }}" class="type-{{ $value }}">
                <x-icon :name="$info[2]" size="22" />
                <span>{{ $info[0] }}</span>
                <small>{{ $info[1] }}</small>
            </label>
        @endforeach
    </div>
    @error('type')
        <div class="error-text">{{ $message }}</div>
    @enderror
</div>

<div class="field">
    <label class="label" for="balance">{{ $balanceLabel }}</label>
    <div class="input-group">
        <span class="prefix" aria-hidden="true">Rp</span>
        <input class="input @error('balance') is-invalid @enderror" type="number" step="0.01"
               name="balance" id="balance" placeholder="0"
               value="{{ old('balance', $account?->balance) }}"
               aria-describedby="balance-hint @error('balance') balance-error @enderror"
               @error('balance') aria-invalid="true" @enderror>
    </div>
    <div class="hint" id="balance-hint">{{ $balanceHint }}</div>
    @error('balance')
        <div class="error-text" id="balance-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-actions">
    <button type="submit" class="btn">{{ $submitLabel }}</button>
    <a href="{{ route('accounts.index') }}" class="btn btn-ghost">Batal</a>
</div>
