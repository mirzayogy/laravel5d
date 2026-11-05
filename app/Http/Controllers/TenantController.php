<?php

namespace App\Http\Controllers;

use App\Models\Tenant;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount('bookings')->get();

        return view('tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('bookings.room.kost');

        return view('tenants.show', compact('tenant'));
    }
}