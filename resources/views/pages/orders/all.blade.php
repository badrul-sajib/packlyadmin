@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="All Orders" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters Row --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 px-5 sm:px-6">
            {{-- Shipping Type --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>Shipping Type</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">OSD</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">ISD</button></li>
                    </ul>
                </div>
            </div>

            {{-- Order From --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>Order From</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Web</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">App</button></li>
                    </ul>
                </div>
            </div>

            <div class="ml-auto flex items-center gap-3">
                {{-- Search --}}
                <div class="relative">
                    <input type="text" placeholder="Search By Invoice ID" class="rounded-lg border border-gray-200 bg-white px-4 py-2 pl-9 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 w-56" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>

                {{-- Date Range --}}
                <x-common.date-range-picker id="ordersDateFilter" />
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1180px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[13%]">Invoice</th>
                            <th class="px-4 py-3 text-left font-medium w-[13%]">Customer</th>
                            <th class="px-4 py-3 text-right font-medium w-[9%]">Price</th>
                            <th class="px-4 py-3 text-center font-medium w-[5%]">Items</th>
                            <th class="px-4 py-3 text-right font-medium w-[9%]">Sub Total</th>
                            <th class="px-4 py-3 text-right font-medium w-[9%]">Shipping Charge</th>
                            <th class="px-4 py-3 text-right font-medium w-[8%]">Discount</th>
                            <th class="px-4 py-3 text-right font-medium w-[9%]">Total Amount</th>
                            <th class="px-4 py-3 text-center font-medium w-[7%]">Source</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[11%]">Calling Status</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $orders = [
                                    ['invoice' => 'INV99E3733', 'date' => '10-04-2026 05:59 PM', 'name' => 'Shohan Ahmed raj', 'phone' => '01631366798', 'shipping' => 'OSD', 'charge' => '80.00', 'price' => '255.00', 'discount' => '0.00', 'items' => 1, 'source' => 'Web', 'type' => 'Organic', 'amount' => '335.00', 'payment' => 'COD', 'call_status' => 'Not Called'],
                                    ['invoice' => 'INV1BC8D9E', 'date' => '10-04-2026 05:56 PM', 'name' => 'হিমাদ্রী কবির ঋষি', 'phone' => '01890241069', 'shipping' => 'OSD', 'charge' => '80.00', 'price' => '370.00', 'discount' => '0.00', 'items' => 1, 'source' => 'Web', 'type' => 'Organic', 'amount' => '450.00', 'payment' => 'COD', 'call_status' => 'Answered'],
                                    ['invoice' => 'INV9A3306C', 'date' => '10-04-2026 05:53 PM', 'name' => 'Hridoy', 'phone' => '01601603177', 'shipping' => 'ISD', 'charge' => '50.00', 'price' => '450.00', 'discount' => '0.00', 'items' => 1, 'source' => 'Web', 'type' => 'Organic', 'amount' => '500.00', 'payment' => 'COD', 'call_status' => 'No Answer'],
                                    ['invoice' => 'INVBD9F30C', 'date' => '10-04-2026 05:53 PM', 'name' => 'বিশাল', 'phone' => '01724513237', 'shipping' => 'ISD', 'charge' => '50.00', 'price' => '550.00', 'discount' => '0.00', 'items' => 1, 'source' => 'Web', 'type' => 'Organic', 'amount' => '600.00', 'payment' => 'COD', 'call_status' => 'Answered'],
                                    ['invoice' => 'INV13C12F6', 'date' => '10-04-2026 05:47 PM', 'name' => 'Limon Sarkar', 'phone' => '01903750809', 'shipping' => 'ISD', 'charge' => '50.00', 'price' => '400.00', 'discount' => '0.00', 'items' => 1, 'source' => 'Web', 'type' => 'Organic', 'amount' => '450.00', 'payment' => 'COD', 'call_status' => 'Callback Requested'],
                                    ['invoice' => 'INV950CBD2', 'date' => '10-04-2026 05:45 PM', 'name' => 'Sonamui', 'phone' => '01874022686', 'shipping' => 'OSD', 'charge' => '80.00', 'price' => '255.00', 'discount' => '0.00', 'items' => 1, 'source' => 'Web', 'type' => 'Organic', 'amount' => '335.00', 'payment' => 'COD', 'call_status' => 'Not Called'],
                                    ['invoice' => 'INVA5876EA', 'date' => '10-04-2026 05:40 PM', 'name' => 'Hridoy Hasan', 'phone' => '01756234890', 'shipping' => 'OSD', 'charge' => '80.00', 'price' => '820.00', 'discount' => '0.00', 'items' => 2, 'source' => 'Web', 'type' => 'Organic', 'amount' => '900.00', 'payment' => 'COD', 'call_status' => 'Busy'],
                                    ['invoice' => 'INV3D4E8A1', 'date' => '10-04-2026 05:35 PM', 'name' => 'Rafiq Ahmed', 'phone' => '01912345678', 'shipping' => 'ISD', 'charge' => '50.00', 'price' => '1,200.00', 'discount' => '50.00', 'items' => 3, 'source' => 'App', 'type' => 'Organic', 'amount' => '1,200.00', 'payment' => 'SSL', 'call_status' => 'Answered'],
                                    ['invoice' => 'INV7F2C9B5', 'date' => '10-04-2026 05:30 PM', 'name' => 'Nusrat Jahan', 'phone' => '01812345999', 'shipping' => 'OSD', 'charge' => '80.00', 'price' => '680.00', 'discount' => '0.00', 'items' => 2, 'source' => 'Web', 'type' => 'Organic', 'amount' => '760.00', 'payment' => 'COD', 'call_status' => 'Wrong Number'],
                                    ['invoice' => 'INV6A8D3E2', 'date' => '10-04-2026 05:25 PM', 'name' => 'Kamal Hossain', 'phone' => '01612340987', 'shipping' => 'ISD', 'charge' => '50.00', 'price' => '350.00', 'discount' => '0.00', 'items' => 1, 'source' => 'Web', 'type' => 'Organic', 'amount' => '400.00', 'payment' => 'COD', 'call_status' => 'No Answer'],
                                    ['invoice' => 'INV2B5F7C8', 'date' => '10-04-2026 05:20 PM', 'name' => 'Fatema Begum', 'phone' => '01556789012', 'shipping' => 'OSD', 'charge' => '80.00', 'price' => '920.00', 'discount' => '20.00', 'items' => 4, 'source' => 'App', 'type' => 'Organic', 'amount' => '980.00', 'payment' => 'SSL', 'call_status' => 'Callback Requested'],
                                    ['invoice' => 'INV8C1D4E9', 'date' => '10-04-2026 05:15 PM', 'name' => 'Ariful Islam', 'phone' => '01712345222', 'shipping' => 'ISD', 'charge' => '50.00', 'price' => '480.00', 'discount' => '0.00', 'items' => 1, 'source' => 'Web', 'type' => 'Organic', 'amount' => '530.00', 'payment' => 'COD', 'call_status' => 'Not Called'],
                                ];
                                $callStatuses = ['Not Called', 'Answered', 'No Answer', 'Busy', 'Wrong Number', 'Callback Requested'];
                                $callStatusClasses = [
                                    'Not Called'         => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                    'Answered'           => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
                                    'No Answer'          => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
                                    'Busy'               => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
                                    'Wrong Number'       => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
                                    'Callback Requested' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                                ];
                            @endphp

                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- Invoice --}}
                                    <td class="px-4 py-3.5 w-[15%]">
                                        <div>
                                            <a href="{{ route('orders.detail', $order['invoice']) }}" class="text-sm font-medium text-brand-500 hover:underline flex items-center gap-1">
                                                # {{ $order['invoice'] }}
                                                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-blue-100 dark:bg-blue-500/15">
                                                    <svg class="w-2.5 h-2.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </span>
                                            </a>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $order['date'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Customer --}}
                                    <td class="px-4 py-3.5 w-[15%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $order['name'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['phone'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Price --}}
                                    <td class="px-4 py-3.5 text-right w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['price'] }}</span>
                                    </td>
                                    {{-- Items --}}
                                    <td class="px-4 py-3.5 text-center w-[6%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['items'] }}</span>
                                    </td>
                                    {{-- Sub Total --}}
                                    <td class="px-4 py-3.5 text-right w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['discount'] }}</span>
                                    </td>
                                    {{-- Shipping Charge --}}
                                    <td class="px-4 py-3.5 text-right w-[10%]">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order['charge'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['shipping'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Discount --}}
                                    <td class="px-4 py-3.5 text-right w-[9%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['discount'] }}</span>
                                    </td>
                                    {{-- Total Amount --}}
                                    <td class="px-4 py-3.5 text-right w-[10%]">
                                        <div>
                                            <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $order['amount'] }}</p>
                                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium mt-0.5 {{ $order['payment'] === 'COD' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-500' : 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' }}">{{ $order['payment'] }}</span>
                                        </div>
                                    </td>
                                    {{-- Source --}}
                                    <td class="px-4 py-3.5 text-center w-[8%]">
                                        <div class="flex items-center justify-center gap-1 flex-wrap">
                                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $order['source'] === 'Web' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' : 'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-400' }}">{{ $order['source'] }}</span>
                                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $order['type'] === 'Organic' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-500' }}">{{ $order['type'] }}</span>
                                        </div>
                                    </td>
                                    {{-- Calling Status --}}
                                    <td class="px-4 py-3.5 text-center w-[11%]" x-data="{ open: false, status: '{{ $order['call_status'] }}' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button"
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium leading-snug transition-colors"
                                                :class="{
                                                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400':          status === 'Not Called',
                                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400': status === 'Answered',
                                                    'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400':   status === 'No Answer',
                                                    'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400': status === 'Busy',
                                                    'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400':           status === 'Wrong Number',
                                                    'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400':       status === 'Callback Requested',
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">12</span> of <span class="font-medium text-gray-700 dark:text-gray-300">1,248</span> results
            </p>
            <nav class="flex items-center gap-1">
                {{-- Previous --}}
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>

                {{-- Page Numbers --}}
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">4</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">5</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">104</button>

                {{-- Next --}}
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
