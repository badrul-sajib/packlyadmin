@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Payment Mismatch Orders" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters Row --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 px-5 sm:px-6">
            <p class="text-sm text-red-500 dark:text-gray-400">Delivered orders where the COD amount or delivery charge from the courier differs from the original values.</p>
            <div class="ml-auto">
                <div class="relative">
                    <input type="text" placeholder="Search by invoice / tracking / merchant" class="rounded-lg border border-gray-200 bg-white px-4 py-2 pl-9 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 w-72" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1200px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-3 py-3 text-center font-medium rounded-l-lg w-[3%]">#</th>
                            <th class="px-3 py-3 text-left font-medium w-[10%]">Invoice / CN</th>
                            <th class="px-3 py-3 text-left font-medium w-[10%]">Merchant</th>
                            <th class="px-3 py-3 text-center font-medium w-[7%]">Status</th>
                            <th class="px-3 py-3 text-left font-medium w-[10%]">Customer</th>
                            <th class="px-3 py-3 text-right font-medium w-[8%]">Order COD</th>
                            <th class="px-3 py-3 text-right font-medium w-[8%]">SFC COD</th>
                            <th class="px-3 py-3 text-right font-medium w-[8%]">Order Shipping</th>
                            <th class="px-3 py-3 text-right font-medium w-[8%]">SFC Shipping</th>
                            <th class="px-3 py-3 text-left font-medium w-[10%]">Detected At</th>
                            <th class="px-3 py-3 text-center font-medium rounded-r-lg w-[4%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $orders = [
                                    ['sl' => 1, 'invoice' => 'INV231C420', 'cn' => 'CN 237338259', 'shop' => 'Nanan Gadget', 'status' => 'Ready to Ship', 'customer' => 'A M Rubel', 'phone' => '01840012265', 'orderCod' => '1,750.00', 'sfcCod' => '—', 'codDiff' => '—', 'orderShipping' => '80.00', 'sfcShipping' => '120.00', 'deliveryDiff' => '+40.00', 'diffType' => 'fine', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '09:29 PM'],
                                    ['sl' => 2, 'invoice' => 'INV50E970', 'cn' => 'CN 238241991', 'shop' => 'Mh Online Shop', 'status' => 'Ready to Ship', 'customer' => 'Md Hasan', 'phone' => '01843913275', 'orderCod' => '549.00', 'sfcCod' => '—', 'codDiff' => '—', 'orderShipping' => '50.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '+30.00', 'diffType' => 'fine', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '09:07 PM'],
                                    ['sl' => 3, 'invoice' => 'INV34D2CA0', 'cn' => 'CN 238146001', 'shop' => 'POSTOFFICE SHOPPING', 'status' => 'Ready to Ship', 'customer' => 'Sk.', 'phone' => '01756600912', 'orderCod' => '348.00', 'sfcCod' => '—', 'codDiff' => '—', 'orderShipping' => '50.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '+30.00', 'diffType' => 'fine', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '08:52 PM'],
                                    ['sl' => 4, 'invoice' => 'INV75B1227', 'cn' => 'CN 238272091', 'shop' => 'GIRLS FASHION HOUSE', 'status' => 'Ready to Ship', 'customer' => 'MD Jibon', 'phone' => '01852517120', 'orderCod' => '310.00', 'sfcCod' => '—', 'codDiff' => '—', 'orderShipping' => '50.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '+30.00', 'diffType' => 'fine', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '08:50 PM'],
                                    ['sl' => 5, 'invoice' => 'INV47A9D33', 'cn' => 'CN 238238348', 'shop' => 'Nanan Gadget', 'status' => 'Ready to Ship', 'customer' => 'Md Ashik Alam', 'phone' => '01618488090', 'orderCod' => '449.00', 'sfcCod' => '—', 'codDiff' => '—', 'orderShipping' => '50.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '+30.00', 'diffType' => 'fine', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '08:44 PM'],
                                    ['sl' => 6, 'invoice' => 'INVD3F4F77', 'cn' => 'CN 238215670', 'shop' => 'BD Gadgets', 'status' => 'Ready to Ship', 'customer' => 'Israfil Hossen', 'phone' => '01756890123', 'orderCod' => '269.00', 'sfcCod' => '—', 'codDiff' => '—', 'orderShipping' => '50.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '+30.00', 'diffType' => 'fine', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '08:33 PM'],
                                    ['sl' => 7, 'invoice' => 'INV8A2C5D1', 'cn' => 'CN 238190445', 'shop' => 'TechZone Store', 'status' => 'Delivered', 'customer' => 'Rafiq Ahmed', 'phone' => '01912345678', 'orderCod' => '1,200.00', 'sfcCod' => '1,150.00', 'codDiff' => '-50.00', 'orderShipping' => '80.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '0.00', 'diffType' => 'none', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '07:15 PM'],
                                    ['sl' => 8, 'invoice' => 'INV6F3B9E4', 'cn' => 'CN 238175230', 'shop' => 'Fashion Hub BD', 'status' => 'Delivered', 'customer' => 'Nusrat Jahan', 'phone' => '01812345999', 'orderCod' => '890.00', 'sfcCod' => '890.00', 'codDiff' => '0.00', 'orderShipping' => '50.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '+30.00', 'diffType' => 'fine', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '06:40 PM'],
                                    ['sl' => 9, 'invoice' => 'INV2D8A1C7', 'cn' => 'CN 238162118', 'shop' => 'Gadget World', 'status' => 'Delivered', 'customer' => 'Kamal Hossain', 'phone' => '01612340987', 'orderCod' => '445.00', 'sfcCod' => '400.00', 'codDiff' => '-45.00', 'orderShipping' => '80.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '0.00', 'diffType' => 'none', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '05:55 PM'],
                                    ['sl' => 10, 'invoice' => 'INV9C4E7F2', 'cn' => 'CN 238148905', 'shop' => 'Style Studio', 'status' => 'Delivered', 'customer' => 'Fatema Begum', 'phone' => '01556789012', 'orderCod' => '680.00', 'sfcCod' => '680.00', 'codDiff' => '0.00', 'orderShipping' => '50.00', 'sfcShipping' => '80.00', 'deliveryDiff' => '+30.00', 'diffType' => 'fine', 'detectedDate' => '10 Apr 2026', 'detectedTime' => '04:30 PM'],
                                ];

                                $statusClasses = [
                                    'Ready to Ship' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                                    'Delivered' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-500',
                                ];
                            @endphp

                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- # --}}
                                    <td class="px-3 py-3.5 text-center w-[3%]">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $order['sl'] }}</span>
                                    </td>
                                    {{-- Invoice / CN --}}
                                    <td class="px-3 py-3.5 w-[10%]">
                                        <div>
                                            <a href="#" class="text-sm font-medium text-brand-500 hover:underline">{{ $order['invoice'] }}</a>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['cn'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Merchant --}}
                                    <td class="px-3 py-3.5 w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['shop'] }}</span>
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-3 py-3.5 text-center w-[7%]">
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $order['status'] }}
                                        </span>
                                    </td>
                                    {{-- Customer --}}
                                    <td class="px-3 py-3.5 w-[10%]">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order['customer'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['phone'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Order COD --}}
                                    <td class="px-3 py-3.5 text-right w-[8%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['orderCod'] }}</span>
                                    </td>
                                    {{-- SFC COD --}}
                                    <td class="px-3 py-3.5 text-right w-[8%]">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $order['sfcCod'] }}</span>
                                    </td>
                                    {{-- Order Shipping --}}
                                    <td class="px-3 py-3.5 text-right w-[8%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['orderShipping'] }}</span>
                                    </td>
                                    {{-- SFC Shipping --}}
                                    <td class="px-3 py-3.5 text-right w-[8%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['sfcShipping'] }}</span>
                                    </td>
                                    {{-- Mismatch Detected At --}}
                                    <td class="px-3 py-3.5 w-[10%]">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order['detectedDate'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['detectedTime'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Action --}}
                                    <td class="px-3 py-3.5 text-center w-[4%]">
                                        <a href="{{ route('orders.detail', $order['invoice']) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white hover:bg-emerald-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">10</span> of <span class="font-medium text-gray-700 dark:text-gray-300">328</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">33</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
