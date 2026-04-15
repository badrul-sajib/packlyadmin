@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Spam Orders List" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters --}}
        <div class="flex items-center justify-end gap-3 mb-5 px-5 sm:px-6">
            {{-- Search --}}
            <div class="relative w-64">
                <input type="text" placeholder="Search By Invoice ID" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            {{-- Date Range --}}
            <x-common.date-range-picker id="spamDateRange" />
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[15%]">Invoice</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%]">Customer</th>
                            <th class="px-4 py-3 text-center font-medium w-[12%]">Ip</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Score</th>
                            <th class="px-4 py-3 text-center font-medium w-[16%]">Reasons</th>
                            <th class="px-4 py-3 text-center font-medium w-[14%]">Status</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[18%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $spamOrders = [
                                    ['invoice' => 'INVF96BA77', 'date' => '11-04-2026 12:20 PM', 'customer' => 'saim', 'phone' => '01616720037', 'ip' => '172.31.34.97', 'score' => '30%', 'reason' => 'Multiple COD orders', 'status' => 'SOFT_CHALLENGE'],
                                    ['invoice' => 'INV3A82F10', 'date' => '11-04-2026 11:45 AM', 'customer' => 'Rahim Uddin', 'phone' => '01712345678', 'ip' => '103.24.56.12', 'score' => '65%', 'reason' => 'Suspicious IP pattern', 'status' => 'HARD_CHALLENGE'],
                                    ['invoice' => 'INV7D4E291', 'date' => '11-04-2026 10:30 AM', 'customer' => 'test user', 'phone' => '01800000000', 'ip' => '192.168.1.45', 'score' => '85%', 'reason' => 'Fake phone number', 'status' => 'BLOCKED'],
                                    ['invoice' => 'INVB5C0A83', 'date' => '10-04-2026 09:15 PM', 'customer' => 'Karim Sheikh', 'phone' => '01945678901', 'ip' => '172.31.34.97', 'score' => '25%', 'reason' => 'Multiple COD orders', 'status' => 'SOFT_CHALLENGE'],
                                    ['invoice' => 'INV92E1D47', 'date' => '10-04-2026 06:40 PM', 'customer' => 'abcd', 'phone' => '01511111111', 'ip' => '45.120.89.3', 'score' => '90%', 'reason' => 'Disposable phone + fake name', 'status' => 'BLOCKED'],
                                    ['invoice' => 'INV6FA3B82', 'date' => '10-04-2026 03:22 PM', 'customer' => 'Nasir Hossain', 'phone' => '01867543210', 'ip' => '103.48.72.18', 'score' => '40%', 'reason' => 'High value COD order', 'status' => 'SOFT_CHALLENGE'],
                                ];
                            @endphp

                            @foreach ($spamOrders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- Invoice --}}
                                    <td class="px-4 py-5 w-[15%]">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-gray-400 dark:text-gray-500">#</span>
                                                <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">{{ $order['invoice'] }}</a>
                                                <button type="button" class="text-emerald-500 hover:text-emerald-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $order['date'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Customer --}}
                                    <td class="px-4 py-5 w-[15%]">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $order['customer'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $order['phone'] }}</p>
                                        </div>
                                    </td>
                                    {{-- IP --}}
                                    <td class="px-4 py-5 text-center w-[12%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $order['ip'] }}</span>
                                    </td>
                                    {{-- Score --}}
                                    <td class="px-4 py-5 text-center w-[10%]">
                                        <span class="text-sm font-semibold
                                            @php
                                                $scoreVal = intval($order['score']);
                                            @endphp
                                            {{ $scoreVal >= 70 ? 'text-red-600 dark:text-red-400' : ($scoreVal >= 40 ? 'text-yellow-600 dark:text-yellow-400' : 'text-emerald-600 dark:text-emerald-400') }}
                                        ">{{ $order['score'] }}</span>
                                    </td>
                                    {{-- Reasons --}}
                                    <td class="px-4 py-5 text-center w-[16%]">
                                        <span class="text-sm text-orange-500 dark:text-orange-400">{{ $order['reason'] }}</span>
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-4 py-5 text-center w-[14%]">
                                        <span class="inline-flex items-center rounded px-2.5 py-1 text-xs font-bold text-white
                                            {{ $order['status'] === 'SOFT_CHALLENGE' ? 'bg-yellow-500' : '' }}
                                            {{ $order['status'] === 'HARD_CHALLENGE' ? 'bg-orange-500' : '' }}
                                            {{ $order['status'] === 'BLOCKED' ? 'bg-red-500' : '' }}
                                        ">
                                            {{ $order['status'] }}
                                        </span>
                                    </td>
                                    {{-- Action --}}
                                    <td class="px-4 py-5 text-center w-[18%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-1.5 text-xs font-medium text-white hover:bg-emerald-600 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                Approve
                                            </button>
                                            <a href="{{ route('orders.detail', $order['invoice']) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                                View
                                            </a>
                                        </div>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">6</span> of <span class="font-medium text-gray-700 dark:text-gray-300">24</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
