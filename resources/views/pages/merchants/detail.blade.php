@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Merchant Details" />

    @php
        $merchant = [
            'id' => 7622,
            'shop' => 'Unique cover collection',
            'owner' => 'Abu Rahat Ahtidy',
            'phone' => '01534847390',
            'email' => 'aburaheatahtidy5@gmail.com',
            'joined' => '05 Apr, 2026 09:00 PM',
            'address' => 'Mirpur Plaza shop 401 (4rd floor), dhaka',
            'website' => 'https://uniquecovercollection.com',
            'type' => 'E-Commerce',
            'status' => 'Active',
            'nid' => '1234567890',
            'trade_license' => 'TL-2026-0045',
        ];
    @endphp

    {{-- Shop Banner --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden mb-6">
        <div class="relative h-36 bg-gradient-to-r from-emerald-500 via-teal-500 to-brand-500">
            <div class="absolute inset-0 bg-black/10"></div>
        </div>
        <div class="relative px-6 pb-5">
            <div class="flex items-end gap-5 -mt-10">
                <div class="w-20 h-20 rounded-2xl bg-white dark:bg-gray-900 border-4 border-white dark:border-gray-900 shadow-lg flex items-center justify-center overflow-hidden">
                    <span class="text-2xl font-bold text-gray-300 dark:text-gray-600">{{ strtoupper(substr($merchant['shop'], 0, 2)) }}</span>
                </div>
                <div class="flex-1 pb-1">
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white/90">{{ $merchant['shop'] }}</h2>
                        <span class="text-xs font-mono text-gray-400 dark:text-gray-500">(ID: {{ $merchant['id'] }})</span>
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $merchant['status'] }}
                        </span>
                        <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">{{ $merchant['type'] }}</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $merchant['owner'] }} &middot; Joined {{ $merchant['joined'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Info + Actions --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        {{-- Merchant Details --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Merchant Details</h3>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.07]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
                    Edit
                </button>
            </div>
            <div class="p-6 space-y-3">
                @php
                    $details = [
                        ['label' => 'Shop Name', 'value' => $merchant['shop']],
                        ['label' => 'Owner', 'value' => $merchant['owner']],
                        ['label' => 'Phone', 'value' => $merchant['phone']],
                        ['label' => 'Email', 'value' => $merchant['email']],
                        ['label' => 'Joined', 'value' => $merchant['joined']],
                        ['label' => 'Address', 'value' => $merchant['address']],
                    ];
                @endphp
                @foreach ($details as $d)
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0 w-20">{{ $d['label'] }}</span>
                        <span class="text-sm text-gray-800 dark:text-white/90 text-right">{{ $d['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Shop Details --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Shop Details</h3>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.07]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
                    Edit
                </button>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 dark:text-gray-500">Website</span>
                    <a href="{{ $merchant['website'] }}" target="_blank" rel="noopener noreferrer" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 truncate max-w-[200px]">{{ $merchant['website'] }}</a>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 dark:text-gray-500">NID</span>
                    <span class="text-sm text-gray-800 dark:text-white/90 font-mono">{{ $merchant['nid'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 dark:text-gray-500">Trade License</span>
                    <span class="text-sm text-gray-800 dark:text-white/90 font-mono">{{ $merchant['trade_license'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 dark:text-gray-500">Shop Type</span>
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">{{ $merchant['type'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 dark:text-gray-500">Logo</span>
                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center"><span class="text-xs font-bold text-gray-400">{{ strtoupper(substr($merchant['shop'], 0, 2)) }}</span></div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 dark:text-gray-500">Banner</span>
                    <div class="w-20 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center"><svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg></div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Quick Actions</h3>
            </div>
            <div class="p-6 grid grid-cols-2 gap-2">
                <button type="button" class="flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-medium text-emerald-700 hover:bg-emerald-100 transition-colors dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Activate
                </button>
                <button type="button" class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Deactivate
                </button>
                <button type="button" class="flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                    Update Phone
                </button>
                <button type="button" class="flex items-center gap-2 rounded-lg border border-violet-200 bg-violet-50 px-3 py-2.5 text-xs font-medium text-violet-700 hover:bg-violet-100 transition-colors dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                    Enable Popup Mall
                </button>
                <button type="button" class="flex items-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2.5 text-xs font-medium text-orange-700 hover:bg-orange-100 transition-colors dark:border-orange-500/20 dark:bg-orange-500/10 dark:text-orange-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    Email Password
                </button>
                <button type="button" class="flex items-center gap-2 rounded-lg border border-yellow-200 bg-yellow-50 px-3 py-2.5 text-xs font-medium text-yellow-700 hover:bg-yellow-100 transition-colors dark:border-yellow-500/20 dark:bg-yellow-500/10 dark:text-yellow-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                    Reset Password
                </button>
                <a href="{{ route('products.all') }}" class="flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                    Add Product
                </a>
                <a href="{{ route('orders.create') }}" class="flex items-center gap-2 rounded-lg border border-teal-200 bg-teal-50 px-3 py-2.5 text-xs font-medium text-teal-700 hover:bg-teal-100 transition-colors dark:border-teal-500/20 dark:bg-teal-500/10 dark:text-teal-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    Create Order
                </a>
                <button type="button" class="col-span-2 flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Login History
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-7 gap-4 mb-6">
        @php
            $stats = [
                ['label' => 'Total Products', 'value' => '0', 'sub' => 'Active in Shop', 'color' => 'emerald'],
                ['label' => 'Total Orders', 'value' => '0', 'sub' => null, 'color' => 'brand'],
                ['label' => 'Cancel Orders', 'value' => '0', 'sub' => null, 'color' => 'red'],
                ['label' => 'Total Customers', 'value' => '1', 'sub' => null, 'color' => 'violet'],
                ['label' => 'Current Balance', 'value' => '৳0', 'sub' => null, 'color' => 'blue'],
                ['label' => 'Total Products', 'value' => '0', 'sub' => null, 'color' => 'orange'],
                ['label' => 'Total Reports', 'value' => '0', 'sub' => null, 'color' => 'yellow'],
            ];
            $iconColors = ['emerald' => 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10', 'brand' => 'bg-brand-50 text-brand-500 dark:bg-brand-500/10', 'red' => 'bg-red-50 text-red-500 dark:bg-red-500/10', 'violet' => 'bg-violet-50 text-violet-500 dark:bg-violet-500/10', 'blue' => 'bg-blue-50 text-blue-500 dark:bg-blue-500/10', 'orange' => 'bg-orange-50 text-orange-500 dark:bg-orange-500/10', 'yellow' => 'bg-yellow-50 text-yellow-500 dark:bg-yellow-500/10'];
        @endphp
        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wide leading-tight">{{ $stat['label'] }}</p>
                    <div class="w-7 h-7 rounded-lg {{ $iconColors[$stat['color']] }} flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                </div>
                <p class="text-lg font-bold text-gray-800 dark:text-white/90">{{ $stat['value'] }}</p>
                @if($stat['sub'])
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $stat['sub'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Products Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6 mb-6" x-data="{ productTab: 'shop', statusFilter: 'all' }">
        <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="productTab = 'shop'" type="button" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors" :class="productTab === 'shop' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Shop Product</button>
                <button @click="productTab = 'pending'" type="button" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors" :class="productTab === 'pending' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Update Product pending</button>
                <button @click="productTab = 'inventory'" type="button" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors" :class="productTab === 'inventory' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Inventory Product</button>
            </div>
            <div class="flex items-center gap-2">
                @foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'disabled' => 'Disabled'] as $key => $label)
                    <button @click="statusFilter = '{{ $key }}'" type="button" class="rounded-full px-3 py-1 text-xs font-medium transition-colors" :class="statusFilter === '{{ $key }}' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400'">{{ $label }}</button>
                @endforeach
                <div class="relative w-32">
                    <input type="text" placeholder="Search..." class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300" />
                </div>
            </div>
        </div>
        <div class="px-5 sm:px-6">
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21"/></svg>
                <p class="text-sm text-gray-400 dark:text-gray-500">Products not added yet</p>
            </div>
        </div>
    </div>

    {{-- Orders / Payouts / Reports / Settings Tabs --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6 mb-6" x-data="{ activeTab: 'orders' }">
        <div class="flex items-center gap-2 mb-5 px-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <button @click="activeTab = 'orders'" type="button" class="rounded-lg px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'orders' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Orders</button>
            <button @click="activeTab = 'payouts'" type="button" class="rounded-lg px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'payouts' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Payout Requests</button>
            <button @click="activeTab = 'reports'" type="button" class="rounded-lg px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'reports' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Reports</button>
            <button @click="activeTab = 'setting'" type="button" class="rounded-lg px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'setting' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Setting</button>
        </div>

        <div class="px-5 sm:px-6">
            <template x-for="tab in ['orders','payouts','reports','setting']" :key="tab">
                <div x-show="activeTab === tab">
                    <div class="text-center py-10">
                        <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        <p class="text-sm text-gray-400 dark:text-gray-500" x-text="'No ' + tab.replace('setting','settings') + ' found'"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Activity History --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6 mb-6">
        <div class="px-5 sm:px-6 mb-5">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Merchant Activity History</h3>
        </div>
        <div class="px-5 sm:px-6">
            <div class="text-center py-10">
                <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-gray-400 dark:text-gray-500">No user activities recorded yet</p>
            </div>
        </div>
    </div>

    {{-- ── Communication Bar ────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] px-6 py-5 mb-6" x-data="{ showSms: false }">
        @include('components.common.reject-reason-modal')
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90 mb-4">Communication</h3>
        <div class="flex flex-wrap items-center gap-3">
            {{-- WhatsApp --}}
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $merchant['phone']) }}" target="_blank"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </a>
            {{-- Call --}}
            <a href="tel:{{ $merchant['phone'] }}"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                Call Merchant
            </a>
            {{-- SMS --}}
            <button @click="showSms = !showSms" type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] px-4 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                Send SMS
            </button>
        </div>
        {{-- SMS panel --}}
        <div x-show="showSms" x-transition class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 p-4" style="display:none;">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Message Template</p>
            <div class="flex gap-2 mb-3 flex-wrap">
                @foreach (['Order Update', 'Payment Reminder', 'Account Notice', 'Custom'] as $tpl)
                    <button type="button" class="rounded-full border border-gray-200 dark:border-gray-700 px-3 py-1 text-xs text-gray-600 dark:text-gray-400 hover:bg-brand-50 hover:border-brand-300 hover:text-brand-600 dark:hover:bg-brand-500/10 transition-colors">{{ $tpl }}</button>
                @endforeach
            </div>
            <textarea rows="3" placeholder="Type your message…" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 placeholder-gray-400 dark:placeholder-gray-600 resize-none mb-3"></textarea>
            <div class="flex justify-end gap-2">
                <button @click="showSms = false" type="button" class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">Cancel</button>
                <button type="button" class="rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-sm font-medium text-white transition-colors">Send SMS</button>
            </div>
        </div>
    </div>

    {{-- ── KAM Profile Tab ──────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mb-6" x-data="{ shopType: 'Physical' }">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">KAM Profile</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- KAM Assignment --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Assigned KAM</label>
                <select class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    <option>— Unassigned —</option>
                    <option selected>Sabbir Ahmed</option>
                    <option>Roni Islam</option>
                    <option>Mim Akter</option>
                    <option>Tanvir Hasan</option>
                    <option>Sadia Parvin</option>
                </select>
            </div>
            {{-- Shop Type Toggle --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Shop Type Transition</label>
                <div class="flex items-center gap-2">
                    @foreach (['Physical', 'Digital'] as $type)
                        <button @click="shopType = '{{ $type }}'" type="button"
                            class="flex-1 rounded-lg py-2.5 text-sm font-medium border transition-colors"
                            :class="shopType === '{{ $type }}'
                                ? '{{ $type === 'Digital' ? 'bg-brand-500 border-brand-500 text-white' : 'bg-blue-500 border-blue-500 text-white' }}'
                                : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]'">
                            {{ $type }}
                        </button>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5" x-text="shopType === 'Digital' ? 'Merchant transitioned to digital-only shop.' : 'Merchant operating physical + online.'"></p>
            </div>
            {{-- KAM Notes --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">KAM Notes</label>
                <textarea rows="3" placeholder="Internal notes about this merchant for KAM reference…" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 placeholder-gray-400 dark:placeholder-gray-600 resize-none"></textarea>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="button" class="rounded-lg bg-brand-500 hover:bg-brand-600 px-5 py-2 text-sm font-medium text-white transition-colors">Save KAM Profile</button>
            </div>
        </div>
    </div>

    {{-- ── Billing & Transparency ───────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mb-6" x-data="{ showAdjModal: false }">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Billing & Transparency</h3>
            <button @click="showAdjModal = true" type="button"
                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white px-3 py-1.5 text-xs font-medium transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Adjustment
            </button>
        </div>
        <div class="p-6">
            {{-- Charge breakdown --}}
            <div class="grid grid-cols-3 gap-4 mb-5">
                @php
                    $charges = [
                        ['label' => 'Commission Rate', 'value' => '3%',     'note' => 'Per order commission', 'color' => 'brand'],
                        ['label' => 'Payout Charge',   'value' => '৳25',    'note' => 'Per withdrawal',       'color' => 'purple'],
                        ['label' => 'COD Charge',      'value' => '৳60',    'note' => 'Inside Dhaka',         'color' => 'amber'],
                    ];
                @endphp
                @foreach ($charges as $ch)
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800 p-4">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $ch['label'] }}</p>
                        <p class="text-xl font-bold
                            {{ $ch['color'] === 'brand'  ? 'text-brand-500'  : '' }}
                            {{ $ch['color'] === 'purple' ? 'text-purple-500' : '' }}
                            {{ $ch['color'] === 'amber'  ? 'text-amber-500'  : '' }}">{{ $ch['value'] }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $ch['note'] }}</p>
                    </div>
                @endforeach
            </div>
            {{-- Adjustment history --}}
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wide">Adjustment History</p>
            <div class="overflow-x-auto -mx-1">
            <table class="w-full text-sm min-w-[480px]">
                <thead>
                    <tr class="text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="pb-2 text-left font-medium">Date</th>
                        <th class="pb-2 text-left font-medium">Type</th>
                        <th class="pb-2 text-right font-medium">Amount</th>
                        <th class="pb-2 text-left font-medium px-3">Reason</th>
                        <th class="pb-2 text-left font-medium">Added By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @php
                        $adjustments = [
                            ['date' => '10 Apr 2026', 'type' => 'Credit', 'amount' => '+৳500', 'reason' => 'Promotional bonus', 'by' => 'Admin Karim'],
                            ['date' => '05 Mar 2026', 'type' => 'Debit',  'amount' => '-৳150', 'reason' => 'Charge correction',  'by' => 'Admin Sabbir'],
                        ];
                    @endphp
                    @foreach ($adjustments as $adj)
                        <tr>
                            <td class="py-2.5 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $adj['date'] }}</td>
                            <td class="py-2.5">
                                <span class="inline-flex items-center rounded border px-2 py-0.5 text-xs font-medium {{ $adj['type'] === 'Credit' ? 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : 'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30' }}">{{ $adj['type'] }}</span>
                            </td>
                            <td class="py-2.5 text-right font-semibold {{ $adj['type'] === 'Credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">{{ $adj['amount'] }}</td>
                            <td class="py-2.5 text-gray-600 dark:text-gray-400 px-3">{{ $adj['reason'] }}</td>
                            <td class="py-2.5 text-gray-500 dark:text-gray-400">{{ $adj['by'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        {{-- Add Adjustment Modal --}}
        <template x-teleport="body">
            <div x-show="showAdjModal" x-transition.opacity class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60" @click.self="showAdjModal = false" style="display:none;">
                <div @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Add Adjustment</h3>
                        <button @click="showAdjModal = false" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type</label>
                            <select class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-brand-500"><option>Credit</option><option>Debit</option></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Amount (৳)</label>
                            <input type="number" placeholder="0.00" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Reason</label>
                            <textarea rows="2" placeholder="Why is this adjustment being made?" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 placeholder-gray-400 dark:placeholder-gray-600 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-5">
                        <button @click="showAdjModal = false" type="button" class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">Cancel</button>
                        <button @click="showAdjModal = false" type="button" class="rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-sm font-medium text-white transition-colors">Save Adjustment</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ── Private Notes ────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mb-6" x-data="{ showNoteForm: false, noteText: '', noteType: 'Observation' }">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Private Notes</h3>
                <span class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded px-1.5 py-0.5">Admin only</span>
            </div>
            <button @click="showNoteForm = !showNoteForm" type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Note
            </button>
        </div>

        {{-- Add note form --}}
        <div x-show="showNoteForm" x-transition class="px-6 pt-4 pb-2 border-b border-gray-100 dark:border-gray-800" style="display:none;">
            <div class="flex gap-3 mb-3">
                @foreach (['Observation', 'Rejection', 'Deactivation'] as $t)
                    <button @click="noteType = '{{ $t }}'" type="button"
                        class="px-3 py-1 rounded-full text-xs font-medium border transition-colors"
                        :class="noteType === '{{ $t }}' ? 'bg-brand-500 border-brand-500 text-white' : 'border-gray-200 text-gray-500 dark:border-gray-700 dark:text-gray-400'">
                        {{ $t }}
                    </button>
                @endforeach
            </div>
            <textarea x-model="noteText" rows="3" placeholder="Write a private note… (not visible to merchant)" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 placeholder-gray-400 dark:placeholder-gray-600 resize-none mb-3"></textarea>
            <div class="flex justify-end gap-2 pb-3">
                <button @click="showNoteForm = false; noteText = ''" type="button" class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">Cancel</button>
                <button @click="showNoteForm = false" type="button" class="rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-medium text-white transition-colors">Save Note</button>
            </div>
        </div>

        {{-- Notes list --}}
        <div class="p-6 space-y-4">
            @php
                $notes = [
                    ['text' => 'Merchant requested a digital shop transition — KAM Sabbir confirmed eligibility after store audit.', 'type' => 'Observation', 'by' => 'Admin Karim',  'at' => '08 Apr 2026, 2:30 PM'],
                    ['text' => 'Rejected trade license update — document was expired by 3 months.',                                  'type' => 'Rejection',   'by' => 'Admin Roni',   'at' => '01 Apr 2026, 11:15 AM'],
                    ['text' => 'Account was briefly deactivated for 48h pending KYC re-submission. Reactivated on 25 Mar.',          'type' => 'Deactivation','by' => 'Admin Sabbir', 'at' => '23 Mar 2026, 9:00 AM'],
                ];
                $noteColors = ['Observation' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30', 'Rejection' => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30', 'Deactivation' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/30'];
            @endphp
            @foreach ($notes as $note)
                <div class="rounded-xl border border-gray-100 dark:border-gray-800 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        <span class="inline-flex items-center rounded border px-2 py-0.5 text-[10px] font-medium {{ $noteColors[$note['type']] }}">{{ $note['type'] }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-auto">{{ $note['by'] }} · {{ $note['at'] }}</span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $note['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
