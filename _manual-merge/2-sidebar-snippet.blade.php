{{-- Tambahkan di resources/views/layouts/sidebar.blade.php
     (samakan tag/class dengan menu Accounts yang sudah ada) --}}
<a href="{{ route('transactions.index') }}"
   class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">
    Transactions
</a>
