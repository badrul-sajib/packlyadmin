@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Approval Pending" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="relative w-48" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>Select merchant...</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-56 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <div class="p-2">
                        <input type="text" placeholder="Search merchant..." class="w-full rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                    </div>
                    <ul class="py-1 text-sm max-h-48 overflow-y-auto">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Merchants</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Home Shop BD.com</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">LUXURY VIP</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">WKL Marts</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">CarbonX Shop</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by product name" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[25%]">Product Info</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%]">Merchant Info</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[8%]">SKU</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%]">Category</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%] whitespace-nowrap">Regular Price</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%] whitespace-nowrap">Discount Price</th>
                            <th class="px-4 py-3 text-left font-medium w-[5%]">Stock</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%] whitespace-nowrap">Requested Date</th>
                            <th class="px-4 py-3 text-left font-medium rounded-r-lg w-[5%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $products = [
                                    ['name' => '3D Creative Visualization LED Lamp with Calendar Design Night Light for Home Office', 'merchant' => 'Home Shop BD.com', 'phone' => '01910903717', 'sku' => 'TSWFA1LP', 'category' => 'Electronic Accessories', 'price' => '599.00', 'discount' => '499.00', 'stock' => 6, 'requested_at' => '11/04/2026 02:35 PM'],
                                    ['name' => 'Spider Man Only Cover Airpods Pro and Pro 2nd Gen Premium Earbuds Protective Cover', 'merchant' => 'LUXURY VIP', 'phone' => '01342584477', 'sku' => '6GIA0XDZ', 'category' => 'Electronics Device', 'price' => '400.00', 'discount' => '150.00', 'stock' => 100, 'requested_at' => '11/04/2026 01:12 PM'],
                                    ['name' => 'Recci Risk W30 Transparent RGB Bluetooth Speaker', 'merchant' => 'WKL Marts', 'phone' => '01781951811', 'sku' => 'I8FV8AJY', 'category' => 'Electronics Device', 'price' => '750.00', 'discount' => '690.00', 'stock' => 1, 'requested_at' => '11/04/2026 11:48 AM'],
                                    ['name' => 'SHAHRIAR RAHMAN Custom Phone Case Premium Edition', 'merchant' => 'CarbonX Shop', 'phone' => '01775006663', 'sku' => 'VXDCVX', 'category' => 'Electronics Device', 'price' => '123.00', 'discount' => '123.00', 'stock' => 122, 'requested_at' => '10/04/2026 09:20 PM'],
                                    ['name' => 'Only Cover Airpods Pro and Pro 2nd Gen Premium Earbuds Protective Cover Pikachu', 'merchant' => 'LUXURY VIP', 'phone' => '01342584477', 'sku' => 'UMV3C1MC', 'category' => 'Electronics Device', 'price' => '400.00', 'discount' => '150.00', 'stock' => 100, 'requested_at' => '10/04/2026 06:45 PM'],
                                    ['name' => 'Only Cover Airpods Pro and Pro 2nd Gen Premium Earbuds Protective Cover French Bulldog', 'merchant' => 'LUXURY VIP', 'phone' => '01342584477', 'sku' => 'JAKWLSPL', 'category' => 'Electronics Device', 'price' => '400.00', 'discount' => '160.00', 'stock' => 100, 'requested_at' => '10/04/2026 04:30 PM'],
                                ];
                            @endphp

                            @foreach ($products as $index => $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[4%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[25%]">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden shrink-0">
                                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90 line-clamp-2">{{ $product['name'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[15%]">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">{{ $product['merchant'] }}</a>
                                            </div>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 pl-3.5">{{ $product['phone'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]" x-data="{ open: false, showReject: false, feedback: '' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open; showReject = false; feedback = ''" type="button" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100 transition-colors cursor-pointer dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                                Pending
                                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-2 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" :class="showReject ? 'w-72' : 'w-40'" style="display:none;">
                                                {{-- Approve / Reject Options --}}
                                                <div x-show="!showReject">
                                                    <div class="py-1">
                                                        <button @click="open = false" type="button" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-emerald-600 hover:bg-emerald-50 transition-colors dark:text-emerald-400 dark:hover:bg-emerald-500/10">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Approve
                                                        </button>
                                                        <button @click="showReject = true" type="button" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors dark:text-red-400 dark:hover:bg-red-500/10">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Reject
                                                        </button>
                                                    </div>
                                                </div>
                                                {{-- Reject Feedback --}}
                                                <div x-show="showReject" class="p-3">
                                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90 mb-2">Rejection Reason</p>
                                                    <textarea x-model="feedback" rows="3" placeholder="Enter feedback for merchant..." class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 resize-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder-gray-500"></textarea>
                                                    <div class="flex items-center justify-end gap-2 mt-2">
                                                        <button @click="showReject = false" type="button" class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                                                        <button @click="open = false; showReject = false" type="button" class="px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">Reject</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[8%]">
                                        <span class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $product['sku'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[10%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $product['category'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right w-[10%]">
                                        <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap {{ $product['price'] !== $product['discount'] ? 'line-through' : '' }}">{{ $product['price'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right w-[10%]">
                                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">{{ $product['discount'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[5%]">
                                        <span class="text-sm font-medium {{ $product['stock'] <= 5 ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white/90' }}">{{ $product['stock'] }}</span>
                                    </td>
                                    {{-- Requested Date --}}
                                    <td class="px-4 py-4 w-[10%]">
                                        @php $dp = explode(' ', $product['requested_at'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] ?? '' }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] ?? '' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[5%]">
                                        <a href="#" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View</a>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">6</span> of <span class="font-medium text-gray-700 dark:text-gray-300">924</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">93</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
