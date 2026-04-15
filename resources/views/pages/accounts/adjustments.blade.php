@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Manual Adjustments" />

    @php
        $adjustments = [
            ['id' => 1,  'merchant' => 'StyleNest BD',    'type' => 'Credit', 'amount' => 500,    'reason' => 'Overpaid commission refund for March.',        'added_by' => 'Admin Karim',  'date' => '10 Apr 2026', 'balance_after' => 12500],
            ['id' => 2,  'merchant' => 'TechZone Shop',   'type' => 'Debit',  'amount' => 200,    'reason' => 'Penalty for late shipment SLA breach.',        'added_by' => 'Admin Sabbir', 'date' => '09 Apr 2026', 'balance_after' => 8300],
            ['id' => 3,  'merchant' => 'FreshMart',       'type' => 'Credit', 'amount' => 1200,   'reason' => 'Promotional cashback — April campaign.',       'added_by' => 'Admin Roni',   'date' => '08 Apr 2026', 'balance_after' => 15700],
            ['id' => 4,  'merchant' => 'GreenLeaf Store', 'type' => 'Debit',  'amount' => 350,    'reason' => 'Chargeback from disputed customer order.',     'added_by' => 'Admin Mim',    'date' => '07 Apr 2026', 'balance_after' => 6450],
            ['id' => 5,  'merchant' => 'BDFashion Hub',   'type' => 'Credit', 'amount' => 800,    'reason' => 'Referral bonus — 2 new merchants onboarded.',  'added_by' => 'Admin Karim',  'date' => '06 Apr 2026', 'balance_after' => 9800],
            ['id' => 6,  'merchant' => 'Electro Point',   'type' => 'Debit',  'amount' => 150,    'reason' => 'COD collection fee correction.',               'added_by' => 'Admin Sabbir', 'date' => '05 Apr 2026', 'balance_after' => 11250],
            ['id' => 7,  'merchant' => 'QuickDrop BD',    'type' => 'Credit', 'amount' => 2500,   'reason' => 'Year-end loyalty reward.',                     'added_by' => 'Admin Roni',   'date' => '04 Apr 2026', 'balance_after' => 22500],
            ['id' => 8,  'merchant' => 'NovaMart',        'type' => 'Debit',  'amount' => 450,    'reason' => 'Return handling fee for bulk return batch.',   'added_by' => 'Admin Mim',    'date' => '03 Apr 2026', 'balance_after' => 7050],
        ];

        $totalCredit = collect($adjustments)->where('type', 'Credit')->sum('amount');
        $totalDebit  = collect($adjustments)->where('type', 'Debit')->sum('amount');
        $netBalance  = $totalCredit - $totalDebit;
    @endphp

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-500/30 dark:bg-emerald-500/10 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-emerald-700 dark:text-emerald-400">৳{{ number_format($totalCredit) }}</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-0.5">Total Credits</p>
            </div>
        </div>
        <div class="rounded-2xl border border-red-200 bg-red-50 dark:border-red-500/30 dark:bg-red-500/10 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-red-700 dark:text-red-400">৳{{ number_format($totalDebit) }}</p>
                <p class="text-xs text-red-600 dark:text-red-500 mt-0.5">Total Debits</p>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-brand-50 dark:border-brand-500/30 dark:bg-brand-500/10 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-brand-100 dark:bg-brand-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-brand-700 dark:text-brand-400">৳{{ number_format($netBalance) }}</p>
                <p class="text-xs text-brand-600 dark:text-brand-500 mt-0.5">Net Adjustment</p>
            </div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
         x-data="{ showAddModal: false, addType: 'Credit', typeFilter: 'All Types' }">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="relative flex-1 min-w-[180px] max-w-xs">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search merchant…" class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <select x-model="typeFilter" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300">
                <option>All Types</option>
                <option>Credit</option>
                <option>Debit</option>
            </select>
            <button type="button" @click="showAddModal = true"
                class="ml-auto inline-flex items-center gap-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-4 py-2 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Adjustment
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">#</th>
                        <th class="px-5 py-3 text-left">Merchant</th>
                        <th class="px-5 py-3 text-center">Type</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-right">Balance After</th>
                        <th class="px-5 py-3 text-left">Reason</th>
                        <th class="px-5 py-3 text-left">Added By</th>
                        <th class="px-5 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @foreach ($adjustments as $adj)
                        <tr x-show="typeFilter === 'All Types' || typeFilter === '{{ $adj['type'] }}'"
                            class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-4 text-xs text-gray-400 dark:text-gray-600">{{ $adj['id'] }}</td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $adj['merchant'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($adj['type'] === 'Credit')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Credit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                        Debit
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <span class="text-sm font-semibold {{ $adj['type'] === 'Credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                                    {{ $adj['type'] === 'Credit' ? '+' : '-' }}৳{{ number_format($adj['amount']) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-sm text-gray-600 dark:text-gray-400">৳{{ number_format($adj['balance_after']) }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $adj['reason'] }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $adj['added_by'] }}</td>
                            <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $adj['date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ count($adjustments) }} adjustments total</p>
        </div>

        {{-- Add Adjustment Modal --}}
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center" x-transition.opacity style="display:none;">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showAddModal = false"></div>
            <div class="relative w-full max-w-md mx-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Add Manual Adjustment</h3>
                    <button @click="showAddModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Merchant</label>
                        <select class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            <option>Select merchant…</option>
                            <option>StyleNest BD</option>
                            <option>TechZone Shop</option>
                            <option>FreshMart</option>
                            <option>GreenLeaf Store</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Type</label>
                        <div class="flex gap-2">
                            <button type="button" @click="addType = 'Credit'"
                                :class="addType === 'Credit' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white dark:bg-white/[0.03] text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-emerald-400'"
                                class="flex-1 rounded-lg border px-3 py-2 text-sm font-medium transition-colors">
                                Credit (+)
                            </button>
                            <button type="button" @click="addType = 'Debit'"
                                :class="addType === 'Debit' ? 'bg-red-500 text-white border-red-500' : 'bg-white dark:bg-white/[0.03] text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-red-400'"
                                class="flex-1 rounded-lg border px-3 py-2 text-sm font-medium transition-colors">
                                Debit (-)
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Amount (৳)</label>
                        <input type="number" min="1" placeholder="0.00"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Reason</label>
                        <textarea rows="3" placeholder="Describe the reason for this adjustment…"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="showAddModal = false"
                        class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="showAddModal = false"
                        :class="addType === 'Credit' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-red-500 hover:bg-red-600'"
                        class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold text-white transition-colors">
                        Save Adjustment
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
