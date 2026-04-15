@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Refund Requests" />

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Requests</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">156</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending</p>
            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">18</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Approved</p>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">৳1,24,500</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rejected</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">12</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">All</button>
                <button @click="activeTab = 'pending'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'pending' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Pending</button>
                <button @click="activeTab = 'approved'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'approved' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Approved</button>
                <button @click="activeTab = 'rejected'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'rejected' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Rejected</button>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-56">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg></div>
                <input type="text" placeholder="Search by order ID" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <x-common.date-range-picker id="refundDateRange" />
        </div>

        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%]">Order ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%]">Customer</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%]">Product</th>
                            <th class="px-4 py-3 text-right font-medium w-[10%] whitespace-nowrap">Refund Amount</th>
                            <th class="px-4 py-3 text-left font-medium w-[14%]">Reason</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%] whitespace-nowrap">Requested Date</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[6%]">Action</th>
                        </tr>
                    </thead>
                </table>
                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $refunds = [
                                    ['order' => 'INVE3F985C', 'customer' => 'Jaseem Uddin', 'phone' => '01797799044', 'product' => 'Instant Coffee 1 KG', 'amount' => '380.00', 'reason' => 'Product damaged during delivery', 'status' => 'Pending', 'date' => '11/04/2026 05:30 PM'],
                                    ['order' => 'INV68B94A5', 'customer' => 'Motin Haque', 'phone' => '01731782346', 'product' => 'Arche Pearl Cream - 3gm', 'amount' => '140.00', 'reason' => 'Wrong product received', 'status' => 'Approved', 'date' => '10/04/2026 02:15 PM'],
                                    ['order' => 'INV8FCDBA4', 'customer' => 'Rahim Uddin', 'phone' => '01712345678', 'product' => 'Wireless Bluetooth Earbuds', 'amount' => '999.00', 'reason' => 'Product not working', 'status' => 'Pending', 'date' => '10/04/2026 11:40 AM'],
                                    ['order' => 'INV563B0A9', 'customer' => 'Nasir Hossain', 'phone' => '01867543210', 'product' => 'Leather Wallet RFID', 'amount' => '650.00', 'reason' => 'Different color than shown', 'status' => 'Rejected', 'date' => '09/04/2026 04:20 PM'],
                                    ['order' => 'INVC3A2IED', 'customer' => 'Fatema Akter', 'phone' => '01945678901', 'product' => 'Power Bank 20000mAh', 'amount' => '1,450.00', 'reason' => 'Never received the order', 'status' => 'Approved', 'date' => '09/04/2026 09:00 AM'],
                                    ['order' => 'INV7D4E291', 'customer' => 'Kamal Hossen', 'phone' => '01511111222', 'product' => 'Smart Watch Fitness Tracker', 'amount' => '1,999.00', 'reason' => 'Stopped working after 3 days', 'status' => 'Pending', 'date' => '08/04/2026 03:45 PM'],
                                ];
                            @endphp
                            @foreach ($refunds as $index => $refund)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4 w-[12%]">
                                        <a href="{{ route('orders.detail', $refund['order']) }}" class="text-sm font-medium font-mono text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">#{{ $refund['order'] }}</a>
                                    </td>
                                    <td class="px-4 py-4 w-[15%]">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $refund['customer'] }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $refund['phone'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 w-[15%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $refund['product'] }}</span></td>
                                    <td class="px-4 py-4 text-right w-[10%]"><span class="text-sm font-semibold text-red-600 dark:text-red-400">৳ {{ $refund['amount'] }}</span></td>
                                    <td class="px-4 py-4 w-[14%]"><span class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $refund['reason'] }}</span></td>
                                    <td class="px-4 py-4 text-center w-[8%]" x-data="{ open: false, status: '{{ $refund['status'] }}' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium cursor-pointer transition-colors border" :class="{ 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30': status === 'Pending', 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30': status === 'Approved', 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30': status === 'Rejected' }">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="{ 'bg-yellow-500': status === 'Pending', 'bg-emerald-500': status === 'Approved', 'bg-red-500': status === 'Rejected' }"></span>
                                                <span x-text="status"></span>
                                                <svg class="w-3 h-3 ml-0.5" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-1 w-32 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                <ul class="py-1 text-sm">
                                                    <li><button @click="status = 'Pending'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Pending</button></li>
                                                    <li><button @click="status = 'Approved'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Approved</button></li>
                                                    <li><button @click="status = 'Rejected'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-red-500"></span> Rejected</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[12%]">
                                        @php $dp = explode(' ', $refund['date'], 2); @endphp
                                        <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[6%]">
                                        <a href="{{ route('orders.detail', $refund['order']) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="View"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">6</span> of <span class="font-medium text-gray-700 dark:text-gray-300">156</span> results</p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">16</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
