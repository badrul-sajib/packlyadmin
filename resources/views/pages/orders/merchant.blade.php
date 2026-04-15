@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Merchant Orders" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters Row --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 px-5 sm:px-6">
            {{-- Select Merchant --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>Select Merchant</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-48 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Merchants</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Rojgar Telecom</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Borshon Shop</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">ROYAL BD SHOP</button></li>
                    </ul>
                </div>
            </div>

            {{-- Select Status --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>Select Status</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Pending</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Processing</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Delivered</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Cancelled</button></li>
                    </ul>
                </div>
            </div>

            <div class="ml-auto flex items-center gap-3">
                {{-- Search by CN-ID --}}
                <div class="relative">
                    <input type="text" placeholder="Search by CN-ID ex: I2345678" class="rounded-lg border border-gray-200 bg-white px-4 py-2 pl-9 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 w-60" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>

                {{-- Search by Order ID --}}
                <div class="relative">
                    <input type="text" placeholder="Search by order id ex: INV0001" class="rounded-lg border border-gray-200 bg-white px-4 py-2 pl-9 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 w-60" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>

                {{-- Date Range --}}
                <x-common.date-range-picker id="merchantOrdersDateFilter" />
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1280px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[11%]">INV & CN ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%]">Merchant</th>
                            <th class="px-4 py-3 text-right font-medium w-[9%]">Total Amount</th>
                            <th class="px-4 py-3 text-right font-medium w-[8%]">Discount</th>
                            <th class="px-4 py-3 text-right font-medium w-[9%]">Shipping Charge</th>
                            <th class="px-4 py-3 text-right font-medium w-[9%]">Total Price</th>
                            <th class="px-4 py-3 text-center font-medium w-[6%]">Total Item</th>
                            <th class="px-4 py-3 text-left font-medium w-[9%]">Date</th>
                            <th class="px-4 py-3 text-center font-medium w-[7%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[7%]">Note</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Calling Status</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $orders = [
                                    ['trkId' => 'TRK0BE1FA5', 'invoice' => 'INV4A9E164', 'shop' => 'Rojgar Telecom',  'phone' => '01842020291', 'amount' => '360.00',   'discount' => '0.00',  'charge' => '80.00', 'price' => '280.00',   'items' => 1, 'date' => '2026-04-10', 'time' => '21:23:48', 'status' => 'Pending',    'note' => 'No Note',         'call_status' => 'Not Called'],
                                    ['trkId' => 'TRKB7F13BD', 'invoice' => 'INV1597F70', 'shop' => 'Borshon Shop',    'phone' => '01914535520', 'amount' => '169.00',   'discount' => '0.00',  'charge' => '80.00', 'price' => '89.00',    'items' => 1, 'date' => '2026-04-10', 'time' => '21:15:41', 'status' => 'Pending',    'note' => 'No Note',         'call_status' => 'Answered'],
                                    ['trkId' => 'TRK8DD0ICB8','invoice' => 'INV0EC487',  'shop' => 'ROYAL BD SHOP',   'phone' => '01617863727', 'amount' => '399.00',   'discount' => '0.00',  'charge' => '80.00', 'price' => '319.00',   'items' => 1, 'date' => '2026-04-10', 'time' => '21:15:03', 'status' => 'Pending',    'note' => 'No Note',         'call_status' => 'No Answer'],
                                    ['trkId' => 'TRK604CC2D', 'invoice' => 'INVA891E88', 'shop' => 'Express Gadgets', 'phone' => '01605949962', 'amount' => '776.00',   'discount' => '0.00',  'charge' => '80.00', 'price' => '696.00',   'items' => 1, 'date' => '2026-04-10', 'time' => '21:13:15', 'status' => 'Pending',    'note' => 'No Note',         'call_status' => 'Not Called'],
                                    ['trkId' => 'TRKC101A96', 'invoice' => 'INV5C9A0D7', 'shop' => 'AL INSAF SHOP',  'phone' => '01329128883', 'amount' => '580.00',   'discount' => '0.00',  'charge' => '80.00', 'price' => '500.00',   'items' => 1, 'date' => '2026-04-10', 'time' => '21:11:54', 'status' => 'Pending',    'note' => 'No Note',         'call_status' => 'Callback Requested'],
                                    ['trkId' => 'TRK8818743', 'invoice' => 'INV8AAFEB8', 'shop' => 'mobail bazar',   'phone' => '01786269206', 'amount' => '570.00',   'discount' => '0.00',  'charge' => '80.00', 'price' => '490.00',   'items' => 1, 'date' => '2026-04-10', 'time' => '21:11:42', 'status' => 'Pending',    'note' => 'No Note',         'call_status' => 'Busy'],
                                    ['trkId' => 'TRK9A2F5B1', 'invoice' => 'INV3D7C8E2', 'shop' => 'Fashion Hub BD', 'phone' => '01912345678', 'amount' => '1,250.00', 'discount' => '50.00', 'charge' => '80.00', 'price' => '1,120.00', 'items' => 3, 'date' => '2026-04-10', 'time' => '20:55:30', 'status' => 'Processing', 'note' => 'Urgent delivery',  'call_status' => 'Answered'],
                                    ['trkId' => 'TRK4C8D1E3', 'invoice' => 'INV6F2A9B5', 'shop' => 'TechZone Store', 'phone' => '01812345999', 'amount' => '890.00',   'discount' => '0.00',  'charge' => '50.00', 'price' => '840.00',   'items' => 2, 'date' => '2026-04-10', 'time' => '20:48:15', 'status' => 'Delivered',  'note' => 'No Note',         'call_status' => 'Answered'],
                                    ['trkId' => 'TRK7E3F2A8', 'invoice' => 'INV1B5D4C9', 'shop' => 'Gadget World',   'phone' => '01712340987', 'amount' => '445.00',   'discount' => '0.00',  'charge' => '50.00', 'price' => '395.00',   'items' => 1, 'date' => '2026-04-10', 'time' => '20:35:22', 'status' => 'Cancelled',  'note' => 'Customer refused','call_status' => 'Wrong Number'],
                                    ['trkId' => 'TRK2D9A7C4', 'invoice' => 'INV8E1F3B6', 'shop' => 'Style Studio',   'phone' => '01556782345', 'amount' => '680.00',   'discount' => '20.00', 'charge' => '80.00', 'price' => '580.00',   'items' => 2, 'date' => '2026-04-10', 'time' => '20:20:10', 'status' => 'Pending',    'note' => 'No Note',         'call_status' => 'No Answer'],
                                    ['trkId' => 'TRK5B1C8D6', 'invoice' => 'INV4A2E7F9', 'shop' => 'Home Essentials','phone' => '01612345678', 'amount' => '320.00',   'discount' => '0.00',  'charge' => '50.00', 'price' => '270.00',   'items' => 1, 'date' => '2026-04-10', 'time' => '20:10:45', 'status' => 'Delivered',  'note' => 'No Note',         'call_status' => 'Answered'],
                                    ['trkId' => 'TRK8F4A2D7', 'invoice' => 'INV9C3B5E1', 'shop' => 'Beauty Palace',  'phone' => '01712345000', 'amount' => '950.00',   'discount' => '0.00',  'charge' => '80.00', 'price' => '870.00',   'items' => 4, 'date' => '2026-04-10', 'time' => '19:58:33', 'status' => 'Processing', 'note' => 'No Note',         'call_status' => 'Not Called'],
                                ];
                                $callStatuses = ['Not Called', 'Answered', 'No Answer', 'Busy', 'Wrong Number', 'Callback Requested'];

                                $statusClasses = [
                                    'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-500',
                                    'Processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500',
                                    'Delivered' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-500',
                                    'Cancelled' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-500',
                                ];
                            @endphp

                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- TrkID --}}
                                    <td class="px-4 py-3.5 w-[12%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $order['trkId'] }}</p>
                                            <a href="{{ route('orders.detail', $order['invoice']) }}" class="text-xs text-brand-500 hover:underline">{{ $order['invoice'] }}</a>
                                        </div>
                                    </td>
                                    {{-- Merchant --}}
                                    <td class="px-4 py-3.5 w-[14%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $order['shop'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['phone'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Total Amount --}}
                                    <td class="px-4 py-3.5 text-right w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['amount'] }}</span>
                                    </td>
                                    {{-- Discount Amount --}}
                                    <td class="px-4 py-3.5 text-right w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['discount'] }}</span>
                                    </td>
                                    {{-- Shipping Charge --}}
                                    <td class="px-4 py-3.5 text-right w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['charge'] }}</span>
                                    </td>
                                    {{-- Total Price --}}
                                    <td class="px-4 py-3.5 text-right w-[10%]">
                                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $order['price'] }}</span>
                                    </td>
                                    {{-- Total Items --}}
                                    <td class="px-4 py-3.5 text-center w-[7%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['items'] }}</span>
                                    </td>
                                    {{-- Date --}}
                                    <td class="px-4 py-3.5 w-[11%]">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order['date'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['time'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-4 py-3.5 text-center w-[8%]">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClasses[$order['status']] }}">
                                            {{ $order['status'] }}
                                        </span>
                                    </td>
                                    {{-- Note --}}
                                    <td class="px-4 py-3.5 w-[8%]">
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $order['note'] }}</span>
                                            <button type="button" class="text-amber-500 hover:text-amber-600">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    {{-- Calling Status --}}
                                    <td class="px-4 py-3.5 text-center w-[10%]" x-data="{ open: false, status: '{{ $order['call_status'] }}' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button"
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium leading-snug transition-colors"
                                                :class="{
                                                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400':              status === 'Not Called',
                                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400': status === 'Answered',
                                                    'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400':        status === 'No Answer',
                                                    'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400':    status === 'Busy',
                                                    'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400':                status === 'Wrong Number',
                                                    'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400':            status === 'Callback Requested',
                                                }">
                                                <span x-text="status"></span>
                                                <svg class="w-3 h-3 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition
                                                class="absolute right-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900"
                                                style="display:none;">
                                                <ul class="py-1">
                                                    @foreach ($callStatuses as $cs)
                                                        <li>
                                                            <button type="button" @click="status = '{{ $cs }}'; open = false"
                                                                class="w-full px-3 py-1.5 text-left text-xs flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-white/[0.05]">
                                                                <span class="w-2 h-2 rounded-full flex-shrink-0
                                                                    {{ $cs === 'Not Called'         ? 'bg-gray-400' : '' }}
                                                                    {{ $cs === 'Answered'           ? 'bg-emerald-500' : '' }}
                                                                    {{ $cs === 'No Answer'          ? 'bg-amber-500' : '' }}
                                                                    {{ $cs === 'Busy'               ? 'bg-orange-500' : '' }}
                                                                    {{ $cs === 'Wrong Number'       ? 'bg-red-500' : '' }}
                                                                    {{ $cs === 'Callback Requested' ? 'bg-blue-500' : '' }}">
                                                                </span>
                                                                <span class="text-gray-700 dark:text-gray-300">{{ $cs }}</span>
                                                            </button>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">12</span> of <span class="font-medium text-gray-700 dark:text-gray-300">18,449</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">4</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">5</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">1,538</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
