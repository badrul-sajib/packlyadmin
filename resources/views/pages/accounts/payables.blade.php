@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Payables" />

    {{-- Section 1: Summary --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6 mb-6">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-5 sm:px-6">
            <div class="rounded-lg border border-gray-200 bg-white px-5 py-4 dark:border-gray-700 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Payable Count</p>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90">248</p>
                    <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    </button>
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white px-5 py-4 dark:border-gray-700 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Payable Sum</p>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90">516,706.98</p>
                    <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        {{-- Tab Filter --}}
        <div class="flex flex-wrap items-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeTab = 'all'" type="button"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                All
            </button>
            <button @click="activeTab = 'payout'" type="button"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === 'payout' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Payout Request
            </button>
            <button @click="activeTab = 'available'" type="button"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === 'available' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Available Balance
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[900px]">
                {{-- Table Header --}}
                <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-1">
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-3">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Merchant Info</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Type</span>
                        </div>
                        <div class="col-span-1">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Amount</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Orders</span>
                        </div>
                        <div class="col-span-2 text-right">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Action</span>
                        </div>
                    </div>
                </div>

                {{-- Table Body --}}
                <div class="max-h-[600px] overflow-y-auto custom-scrollbar divide-y divide-gray-100 dark:divide-gray-800">
                    @php
                        $payables = [
                            ['shop' => 'Cosmetics World Bangladesh', 'owner' => 'Shakil Ahmed', 'phone' => '01883751224', 'type' => 'Payout Request', 'status' => 'Pending', 'amount' => '275.50', 'orders' => 1],
                            ['shop' => 'E-Hridoy Shop', 'owner' => 'MD Ashikur Jaman', 'phone' => '01950127535', 'type' => 'Payout Request', 'status' => 'Pending', 'amount' => '997.50', 'orders' => 1],
                            ['shop' => 'JR Unique Gadgets', 'owner' => 'Md Jakir', 'phone' => '01886304506', 'type' => 'Payout Request', 'status' => 'Pending', 'amount' => '616.55', 'orders' => 1],
                            ['shop' => 'Boisati', 'owner' => 'Belayet Hossen', 'phone' => '01825541401', 'type' => 'Payout Request', 'status' => 'Pending', 'amount' => '325.85', 'orders' => 1],
                            ['shop' => 'Mira gallery', 'owner' => 'MD Rakibul', 'phone' => '01748832370', 'type' => 'Payout Request', 'status' => 'Pending', 'amount' => '1,425.00', 'orders' => 5],
                            ['shop' => 'Defense Academy', 'owner' => 'Nusrat Jahan', 'phone' => '07165024098', 'type' => 'Payout Request', 'status' => 'Pending', 'amount' => '367.65', 'orders' => 3],
                            ['shop' => 'M I B Super Shop', 'owner' => 'MD. Kausar', 'phone' => '01845090575', 'type' => 'Payout Request', 'status' => 'Pending', 'amount' => '1,128.55', 'orders' => 2],
                            ['shop' => 'JR COCARISE', 'owner' => 'Md BIDIRUJ JAMAN JIBRAN', 'phone' => '01823031656', 'type' => 'Payout Request', 'status' => 'Pending', 'amount' => '551.00', 'orders' => 2],
                            ['shop' => 'ShopIQ', 'owner' => 'ShopIQ', 'phone' => '01334919539', 'type' => 'Available Balance', 'status' => 'Available', 'amount' => '69,774.49', 'orders' => 125],
                            ['shop' => 'BD Gadgets Corner', 'owner' => 'Md Mesbah Haque', 'phone' => '01925900237', 'type' => 'Available Balance', 'status' => 'Available', 'amount' => '38,990.88', 'orders' => 103],
                        ];
                    @endphp

                    @foreach ($payables as $payable)
                        <div class="grid grid-cols-12 gap-2 items-center py-4 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                            {{-- Merchant Info --}}
                            <div class="col-span-3">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $payable['shop'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $payable['owner'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $payable['phone'] }}</p>
                            </div>
                            {{-- Type --}}
                            <div class="col-span-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $payable['type'] === 'Payout Request' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : '' }}
                                    {{ $payable['type'] === 'Available Balance' ? 'bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400' : '' }}
                                ">
                                    {{ $payable['type'] }}
                                </span>
                            </div>
                            {{-- Status --}}
                            <div class="col-span-1">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $payable['status'] === 'Pending' ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' : '' }}
                                    {{ $payable['status'] === 'Available' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}
                                    {{ $payable['status'] === 'Completed' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : '' }}
                                ">
                                    {{ $payable['status'] }}
                                </span>
                            </div>
                            {{-- Amount --}}
                            <div class="col-span-2">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $payable['amount'] }}</span>
                            </div>
                            {{-- Orders --}}
                            <div class="col-span-2">
                                <span class="inline-flex items-center rounded-full border border-gray-200 px-3 py-0.5 text-xs font-medium text-gray-600 dark:border-gray-700 dark:text-gray-400">{{ $payable['orders'] }} Orders</span>
                            </div>
                            {{-- Action --}}
                            <div class="col-span-2 text-right">
                                <a href="#" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500 px-4 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-500 hover:text-white transition-colors dark:border-emerald-400 dark:text-emerald-400 dark:hover:bg-emerald-500 dark:hover:text-white">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    View Orders
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">10</span> of <span class="font-medium text-gray-700 dark:text-gray-300">248</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">25</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
