@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="SSL Payments" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Search & Filter Row --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            {{-- Search --}}
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search with Transaction ID, Order Invoice ID" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>

            {{-- Status Filter --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>All Status</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Status</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Paid</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Unattempted</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Failed</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Cancelled</button></li>
                    </ul>
                </div>
            </div>

            {{-- Date Range --}}
            <x-common.date-range-picker id="sslDateRange" />
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1200px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[16%]">Order Invoice ID</th>
                            <th class="px-4 py-3 text-right font-medium w-[10%]">Total Amount</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%]">Customer</th>
                            <th class="px-4 py-3 text-left font-medium w-[13%]">Payment Method</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%]">Transaction ID</th>
                            <th class="px-4 py-3 text-center font-medium w-[12%]">Status</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[6%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $payments = [
                                    ['invoice' => 'INV839036', 'date' => '10-04-2026 10:18 PM', 'amount' => '305.00', 'customer' => 'Omey', 'phone' => '01923634461', 'method' => 'SslCommerz', 'txn_id' => 'SSLDF8A31', 'status' => 'Paid'],
                                    ['invoice' => 'INV839036', 'date' => '10-04-2026 10:18 PM', 'amount' => '305.00', 'customer' => 'Omey', 'phone' => '01923634461', 'method' => 'SslCommerz', 'txn_id' => 'SSL5010ED9', 'status' => 'Unattempt...'],
                                    ['invoice' => 'INV86A608', 'date' => '10-04-2026 10:13 PM', 'amount' => '478.00', 'customer' => 'Md raju', 'phone' => '01321937575', 'method' => 'SslCommerz', 'txn_id' => 'SSL2529FE', 'status' => 'Unattempt...'],
                                    ['invoice' => 'INV010415', 'date' => '10-04-2026 04:37 PM', 'amount' => '449.00', 'customer' => 'Au joor', 'phone' => '01906541002', 'method' => 'SslCommerz', 'txn_id' => 'SSL692C092', 'status' => 'Unattempt...'],
                                    ['invoice' => 'INV870F2A5', 'date' => '10-04-2026 04:35 PM', 'amount' => '1300.00', 'customer' => 'Adrian', 'phone' => '01748664427', 'method' => 'SslCommerz', 'txn_id' => 'SSLB5CF9', 'status' => 'Paid'],
                                    ['invoice' => 'INV97C8004', 'date' => '10-04-2026 03:58 PM', 'amount' => '50.00', 'customer' => 'abir', 'phone' => '01733459129', 'method' => 'SslCommerz', 'txn_id' => 'SSLDC00D0A', 'status' => 'Unattempt...'],
                                    ['invoice' => 'INV2057862', 'date' => '10-04-2026 03:34 PM', 'amount' => '350.00', 'customer' => 'রোজা ক্রাস পাটুন্দ', 'phone' => '01316312500', 'method' => 'SslCommerz', 'txn_id' => 'SSL9F6234D', 'status' => 'Unattempt...'],
                                    ['invoice' => 'INV0F76658', 'date' => '10-04-2026 12:57 PM', 'amount' => '730.00', 'customer' => 'Sania Rahman', 'phone' => '01313829639', 'method' => 'SslCommerz', 'txn_id' => 'SSL6F1EB64', 'status' => 'Paid'],
                                    ['invoice' => 'INV414878', 'date' => '10-04-2026 12:51 PM', 'amount' => '80.00', 'customer' => 'Sania Rahman', 'phone' => '01313829639', 'method' => 'SslCommerz', 'txn_id' => 'SSL27C8401', 'status' => 'Unattempt...'],
                                ];
                            @endphp

                            @foreach ($payments as $index => $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- SL --}}
                                    <td class="px-4 py-5 w-[4%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    {{-- Order Invoice ID --}}
                                    <td class="px-4 py-5 w-[16%]">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">{{ $payment['invoice'] }}</a>
                                                <button type="button" class="text-emerald-500 hover:text-emerald-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $payment['date'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Total Amount --}}
                                    <td class="px-4 py-5 text-right w-[10%]">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $payment['amount'] }}</span>
                                    </td>
                                    {{-- Customer --}}
                                    <td class="px-4 py-5 w-[15%]">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $payment['customer'] }}</p>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment['phone'] }}</p>
                                                <button type="button" class="text-emerald-500 hover:text-emerald-600">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Payment Method --}}
                                    <td class="px-4 py-5 w-[13%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $payment['method'] }}</span>
                                    </td>
                                    {{-- Transaction ID --}}
                                    <td class="px-4 py-5 w-[15%]">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $payment['txn_id'] }}</span>
                                            <button type="button" class="text-emerald-500 hover:text-emerald-600">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-4 py-5 w-[12%]" x-data="{
                                        open: false,
                                        search: '',
                                        selected: '{{ $payment['status'] }}',
                                        statuses: ['Paid', 'Failed', 'Cancelled', 'Unattempted', 'Expired'],
                                        get filtered() {
                                            if (!this.search) return this.statuses;
                                            return this.statuses.filter(s => s.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        statusColor(status) {
                                            const colors = {
                                                'Paid': 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'Failed': 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                                'Cancelled': 'bg-gray-100 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400',
                                                'Unattempted': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                                                'Unattempt...': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                                                'Expired': 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                            };
                                            return colors[status] || 'bg-gray-100 text-gray-600';
                                        },
                                        truncated(status) {
                                            return status.length > 10 ? status.substring(0, 9) + '...' : status;
                                        }
                                    }">
                                        <div class="relative flex justify-center">
                                            <button @click="open = !open; $nextTick(() => { if(open) $refs.searchInput.focus(); search = ''; })" type="button"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-white/[0.05]"
                                                :class="statusColor(selected)">
                                                <span x-text="truncated(selected)"></span>
                                                <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-8 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                <div class="p-2">
                                                    <input x-ref="searchInput" x-model="search" type="text" placeholder="" class="w-full rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                </div>
                                                <ul class="py-1 text-sm max-h-48 overflow-y-auto">
                                                    <template x-for="status in filtered" :key="status">
                                                        <li>
                                                            <button @click="selected = status; open = false;" type="button"
                                                                class="w-full px-4 py-2 text-left text-sm transition-colors"
                                                                :class="selected === status ? 'bg-emerald-500 text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]'"
                                                                x-text="status">
                                                            </button>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Action --}}
                                    <td class="px-4 py-5 text-center w-[6%]">
                                        <a href="#" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">9</span> of <span class="font-medium text-gray-700 dark:text-gray-300">256</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">28</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
