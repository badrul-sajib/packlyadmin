@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Today's Payments" />

    {{-- Summary Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-5 sm:px-6">
            <div class="rounded-lg border border-gray-200 bg-white px-5 py-4 dark:border-gray-700 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Payments</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">156</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white px-5 py-4 dark:border-gray-700 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Amount</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">124,580.00</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white px-5 py-4 dark:border-gray-700 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Charges</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">3,720.00</p>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeMethod: 'all' }">
        {{-- Method Filter Tabs --}}
        <div class="flex flex-wrap items-center justify-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeMethod = 'all'" type="button"
                class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition-colors"
                :class="activeMethod === 'all' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                All
            </button>
            <button @click="activeMethod = 'bank'" type="button"
                class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition-colors"
                :class="activeMethod === 'bank' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                Bank Transfer
            </button>
            <button @click="activeMethod = 'mobile'" type="button"
                class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition-colors"
                :class="activeMethod === 'mobile' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                Mobile Banking
            </button>
            <button @click="activeMethod = 'bkash'" type="button"
                class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition-colors"
                :class="activeMethod === 'bkash' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]'">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.5 2H6.5C5.12 2 4 3.12 4 4.5v15C4 20.88 5.12 22 6.5 22h11c1.38 0 2.5-1.12 2.5-2.5v-15C20 3.12 18.88 2 17.5 2zM12 20c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm5-4H7V5h10v11z"/></svg>
                bKash
            </button>
            <button @click="activeMethod = 'nagad'" type="button"
                class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition-colors"
                :class="activeMethod === 'nagad' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                Nagad
            </button>
            <button @click="activeMethod = 'rocket'" type="button"
                class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition-colors"
                :class="activeMethod === 'rocket' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/></svg>
                Rocket
            </button>
        </div>

        {{-- Search, Merchant, Date & Apply Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            {{-- Search --}}
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by Request ID, Merchant, Shop..." class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            {{-- Merchant --}}
            <div class="relative flex-1" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>Select merchant...</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <div class="p-2">
                        <input type="text" placeholder="Search merchant..." class="w-full rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                    </div>
                    <ul class="py-1 text-sm max-h-48 overflow-y-auto">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Merchants</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Cosmetics World Bangladesh</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">E-Hridoy Shop</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">JR Unique Gadgets</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">ShopIQ</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">BD Gadgets Corner</button></li>
                    </ul>
                </div>
            </div>
            {{-- Date Range --}}
            <x-common.date-range-picker id="todayPaymentDateRange" />
            {{-- Apply Button --}}
            <button type="button" class="rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                Apply
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%]">Payment ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[18%]">Merchant</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%]">Method</th>
                            <th class="px-4 py-3 text-right font-medium w-[10%]">Amount</th>
                            <th class="px-4 py-3 text-right font-medium w-[10%]">Charges</th>
                            <th class="px-4 py-3 text-right font-medium w-[10%]">Net Amount</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%]">Paid At</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[6%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $payments = [
                                    ['id' => 'PAY-38291', 'shop' => 'Cosmetics World Bangladesh', 'owner' => 'Shakil Ahmed', 'method' => 'Cash', 'amount' => '2,450.00', 'charges' => '73.50', 'net' => '2,376.50', 'status' => 'Paid', 'paid_at' => '10/04/2026 03:45 PM'],
                                    ['id' => 'PAY-38290', 'shop' => 'E-Hridoy Shop', 'owner' => 'MD Ashikur Jaman', 'method' => 'bKash', 'amount' => '5,800.00', 'charges' => '174.00', 'net' => '5,626.00', 'status' => 'Paid', 'paid_at' => '10/04/2026 02:30 PM'],
                                    ['id' => 'PAY-38289', 'shop' => 'JR Unique Gadgets', 'owner' => 'Md Jakir', 'method' => 'Cash', 'amount' => '1,200.00', 'charges' => '36.00', 'net' => '1,164.00', 'status' => 'Paid', 'paid_at' => '10/04/2026 01:20 PM'],
                                    ['id' => 'PAY-38288', 'shop' => 'Boisati', 'owner' => 'Belayet Hossen', 'method' => 'Nagad', 'amount' => '890.00', 'charges' => '26.70', 'net' => '863.30', 'status' => 'Pending', 'paid_at' => null],
                                    ['id' => 'PAY-38287', 'shop' => 'Mira gallery', 'owner' => 'MD Rakibul', 'method' => 'Cash', 'amount' => '15,600.00', 'charges' => '468.00', 'net' => '15,132.00', 'status' => 'Paid', 'paid_at' => '10/04/2026 11:50 AM'],
                                    ['id' => 'PAY-38286', 'shop' => 'Defense Academy', 'owner' => 'Nusrat Jahan', 'method' => 'bKash', 'amount' => '3,200.00', 'charges' => '96.00', 'net' => '3,104.00', 'status' => 'Paid', 'paid_at' => '10/04/2026 10:35 AM'],
                                    ['id' => 'PAY-38285', 'shop' => 'M I B Super Shop', 'owner' => 'MD. Kausar', 'method' => 'Cash', 'amount' => '7,450.00', 'charges' => '223.50', 'net' => '7,226.50', 'status' => 'Pending', 'paid_at' => null],
                                    ['id' => 'PAY-38284', 'shop' => 'ShopIQ', 'owner' => 'ShopIQ', 'method' => 'Bank Transfer', 'amount' => '45,000.00', 'charges' => '1,350.00', 'net' => '43,650.00', 'status' => 'Paid', 'paid_at' => '10/04/2026 09:15 AM'],
                                    ['id' => 'PAY-38283', 'shop' => 'BD Gadgets Corner', 'owner' => 'Md Mesbah Haque', 'method' => 'Nagad', 'amount' => '2,100.00', 'charges' => '63.00', 'net' => '2,037.00', 'status' => 'Paid', 'paid_at' => '10/04/2026 08:40 AM'],
                                ];
                            @endphp

                            @foreach ($payments as $index => $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- SL --}}
                                    <td class="px-4 py-5 w-[4%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    {{-- Payment ID --}}
                                    <td class="px-4 py-5 w-[12%]">
                                        <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment['id'] }}</span>
                                    </td>
                                    {{-- Merchant --}}
                                    <td class="px-4 py-5 w-[18%]">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $payment['shop'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $payment['owner'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Method --}}
                                    <td class="px-4 py-5 w-[10%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $payment['method'] }}</span>
                                    </td>
                                    {{-- Amount --}}
                                    <td class="px-4 py-5 text-right w-[10%]">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $payment['amount'] }}</span>
                                    </td>
                                    {{-- Charges --}}
                                    <td class="px-4 py-5 text-right w-[10%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $payment['charges'] }}</span>
                                    </td>
                                    {{-- Net Amount --}}
                                    <td class="px-4 py-5 text-right w-[10%]">
                                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $payment['net'] }}</span>
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-4 py-5 text-center w-[8%]">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $payment['status'] === 'Paid' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}
                                            {{ $payment['status'] === 'Pending' ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' : '' }}
                                        ">
                                            {{ $payment['status'] }}
                                        </span>
                                    </td>
                                    {{-- Paid At --}}
                                    <td class="px-4 py-5 w-[12%]">
                                        @if($payment['paid_at'])
                                            @php
                                                $parts = explode(' ', $payment['paid_at'], 2);
                                            @endphp
                                            <div>
                                                <p class="text-sm text-gray-800 dark:text-white/90">{{ $parts[0] ?? '' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $parts[1] ?? '' }}</p>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    {{-- Action --}}
                                    <td class="px-4 py-5 text-center w-[6%]">
                                        <a href="#" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300">
                                            View
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">9</span> of <span class="font-medium text-gray-700 dark:text-gray-300">156</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">16</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
