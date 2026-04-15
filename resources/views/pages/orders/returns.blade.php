@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Return Orders" />

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Returns</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">89</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending Pickup</p>
            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">14</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Received</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">8</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Completed</p>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">67</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">All</button>
                <button @click="activeTab = 'pending'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'pending' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Pending Pickup</button>
                <button @click="activeTab = 'received'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'received' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Received</button>
                <button @click="activeTab = 'completed'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'completed' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">Completed</button>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-56">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg></div>
                <input type="text" placeholder="Search by order ID" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <x-common.date-range-picker id="returnDateRange" />
        </div>

        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1200px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%]">Order ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[13%]">Customer</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%]">Product</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%]">Return Reason</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Images</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%] whitespace-nowrap">Return Date</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%]">Merchant</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[6%]">Action</th>
                        </tr>
                    </thead>
                </table>
                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $returns = [
                                    ['order' => 'INVE3F985C', 'customer' => 'Jaseem Uddin', 'phone' => '01797799044', 'product' => 'Instant Coffee 1 KG', 'reason' => 'Product damaged', 'images' => 2, 'status' => 'Pending Pickup', 'date' => '11/04/2026 04:00 PM', 'merchant' => 'Velora Shop'],
                                    ['order' => 'INV8FCDBA4', 'customer' => 'Rahim Uddin', 'phone' => '01712345678', 'product' => 'Wireless Bluetooth Earbuds', 'reason' => 'Not working', 'images' => 3, 'status' => 'Received', 'date' => '10/04/2026 01:20 PM', 'merchant' => 'Home Shop BD.com'],
                                    ['order' => 'INV563B0A9', 'customer' => 'Nasir Hossain', 'phone' => '01867543210', 'product' => 'Leather Wallet RFID', 'reason' => 'Wrong color', 'images' => 1, 'status' => 'Completed', 'date' => '09/04/2026 11:45 AM', 'merchant' => 'LUXURY VIP'],
                                    ['order' => 'INV7D4E291', 'customer' => 'Kamal Hossen', 'phone' => '01511111222', 'product' => 'Smart Watch Fitness Tracker', 'reason' => 'Screen defect', 'images' => 4, 'status' => 'Pending Pickup', 'date' => '09/04/2026 09:30 AM', 'merchant' => 'CarbonX Shop'],
                                    ['order' => 'INVC3A2IED', 'customer' => 'Fatema Akter', 'phone' => '01945678901', 'product' => 'Power Bank 20000mAh', 'reason' => 'Not as described', 'images' => 2, 'status' => 'Completed', 'date' => '08/04/2026 06:15 PM', 'merchant' => 'WKL Marts'],
                                    ['order' => 'INV6FA3B82', 'customer' => 'Mitu Begum', 'phone' => '01922334455', 'product' => 'Water Bottle 750ml', 'reason' => 'Leaking cap', 'images' => 1, 'status' => 'Received', 'date' => '08/04/2026 02:00 PM', 'merchant' => 'WKL Marts'],
                                ];
                            @endphp
                            @foreach ($returns as $index => $return)
                                @php
                                    $stStyles = ['Pending Pickup' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30', 'Received' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30', 'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30'];
                                    $stDots = ['Pending Pickup' => 'bg-yellow-500', 'Received' => 'bg-blue-500', 'Completed' => 'bg-emerald-500'];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4 w-[10%]"><a href="{{ route('orders.detail', $return['order']) }}" class="text-sm font-medium font-mono text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">#{{ $return['order'] }}</a></td>
                                    <td class="px-4 py-4 w-[13%]">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $return['customer'] }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $return['phone'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 w-[15%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $return['product'] }}</span></td>
                                    <td class="px-4 py-4 w-[12%]"><span class="text-sm text-orange-500 dark:text-orange-400">{{ $return['reason'] }}</span></td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                            {{ $return['images'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $stStyles[$return['status']] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $stDots[$return['status']] }}"></span>{{ $return['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 w-[10%]">
                                        @php $dp = explode(' ', $return['date'], 2); @endphp
                                        <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
                                    </td>
                                    <td class="px-4 py-4 w-[10%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $return['merchant'] }}</span></td>
                                    <td class="px-4 py-4 text-center w-[6%]">
                                        <a href="{{ route('orders.detail', $return['order']) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="View"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">6</span> of <span class="font-medium text-gray-700 dark:text-gray-300">89</span> results</p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">9</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
