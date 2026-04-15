@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Order Details (#INV68B94A5)" />

    @php
        $order = [
            'id' => 'INV68B94A5',
            'consignment' => '23871848',
            'date' => '2026-04-11 19:33:12',
            'status' => 'Ready to Ship',
            'payment_status' => 'Pending',
            'payment_method' => 'COD',
            'shop' => 'Cosmetics World Bangladesh',
            'shop_id' => 7580,
            'sub_invoice' => 'INV68B94A5',
            'customer' => 'Motin - Goree Cream Order',
            'phone' => '01731782346',
            'address' => 'komofulli, College Bazar, Chattogram, Chattogram - Patiya, Patiya Sadar, Bangladesh',
            'landmark' => '',
            'division' => '',
            'district' => '',
            'city' => '',
            'last_ready' => '11/04/2026 07:39 PM',
            'last_delivered' => '',
            'last_cancelled' => '',
        ];
        $items = [
            ['name' => 'Arche Pearl Cream - 3gm', 'price' => '140.00', 'qty' => 1, 'total' => '140.00', 'commission_percent' => 5, 'commission' => '7.00', 'merchant_get' => '133.00', 'status' => 'Ready to Ship'],
            ['name' => 'Goree Whitening Beauty Night Cream - 20g Original', 'price' => '620.00', 'qty' => 1, 'total' => '620.00', 'commission_percent' => 5, 'commission' => '31.00', 'merchant_get' => '589.00', 'status' => 'Ready to Ship'],
        ];
    @endphp

    {{-- Top Header Bar --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mb-6">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="space-y-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-gray-400 dark:text-gray-500">Consignment Id:</span>
                        <span class="text-sm font-bold font-mono text-gray-800 dark:text-white/90">{{ $order['consignment'] }}</span>
                        <button type="button" class="text-gray-400 hover:text-emerald-500 transition-colors"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg></button>
                    </div>
                    <span class="text-xs text-gray-300 dark:text-gray-600">|</span>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-gray-400 dark:text-gray-500">Order Id:</span>
                        <span class="text-sm font-medium font-mono text-gray-700 dark:text-gray-300">#{{ $order['id'] }}</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500">Placed on: {{ $order['date'] }}</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Status Dropdown --}}
                <div x-data="{ open: false, status: '{{ $order['status'] }}' }">
                    @php
                        $allStatuses = ['Pending', 'Processing', 'Ready to Ship', 'Shipped', 'Delivered', 'Cancelled', 'Returned'];
                        $stColors = ['Pending' => 'bg-yellow-500', 'Processing' => 'bg-blue-500', 'Ready to Ship' => 'bg-teal-500', 'Shipped' => 'bg-violet-500', 'Delivered' => 'bg-emerald-500', 'Cancelled' => 'bg-red-500', 'Returned' => 'bg-orange-500'];
                    @endphp
                    <div class="relative">
                        <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors"
                            :class="{
                                'bg-yellow-500 hover:bg-yellow-600': status === 'Pending',
                                'bg-blue-500 hover:bg-blue-600': status === 'Processing',
                                'bg-teal-500 hover:bg-teal-600': status === 'Ready to Ship',
                                'bg-violet-500 hover:bg-violet-600': status === 'Shipped',
                                'bg-emerald-500 hover:bg-emerald-600': status === 'Delivered',
                                'bg-red-500 hover:bg-red-600': status === 'Cancelled',
                                'bg-orange-500 hover:bg-orange-600': status === 'Returned',
                            }">
                            <span x-text="status"></span>
                            <svg class="w-3.5 h-3.5" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                            <ul class="py-1 text-sm">
                                @foreach ($allStatuses as $st)
                                    <li><button @click="status = '{{ $st }}'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full {{ $stColors[$st] }}"></span> {{ $st }}</button></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                    Edit
                </button>
            </div>
        </div>
        {{-- Timestamps --}}
        <div class="flex items-center gap-6 px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-white/[0.02]">
            <div class="flex items-center gap-1.5">
                <span class="text-xs text-gray-400 dark:text-gray-500">Last Ready to Ship:</span>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $order['last_ready'] ?: '-' }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-xs text-gray-400 dark:text-gray-500">Last Delivered:</span>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $order['last_delivered'] ?: '-' }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-xs text-gray-400 dark:text-gray-500">Last Cancelled:</span>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $order['last_cancelled'] ?: '-' }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Left --}}
        <div class="xl:col-span-2 space-y-6">
            {{-- Merchant + Items --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                {{-- Merchant Bar --}}
                <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35"/></svg>
                        </div>
                        <a href="{{ route('merchants.detail', $order['shop_id']) }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">{{ $order['shop'] }}</a>
                        <span class="text-xs text-gray-300 dark:text-gray-600">></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Invoice: <span class="font-mono font-medium text-gray-700 dark:text-gray-300">#{{ $order['sub_invoice'] }}</span></span>
                        <span class="text-xs text-gray-300 dark:text-gray-600">></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Payment Status: <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30"><span class="w-1 h-1 rounded-full bg-yellow-500"></span>{{ $order['payment_status'] }}</span></span>
                    </div>
                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-600 transition-colors">Ready to Ship</button>
                </div>

                {{-- Items Table --}}
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-emerald-500 text-white text-xs">
                                <th class="px-4 py-2.5 text-left font-medium w-[4%]">#</th>
                                <th class="px-4 py-2.5 text-left font-medium w-[7%]">Image</th>
                                <th class="px-4 py-2.5 text-left font-medium w-[20%]">Name</th>
                                <th class="px-4 py-2.5 text-right font-medium w-[10%]">Price</th>
                                <th class="px-4 py-2.5 text-center font-medium w-[6%]">QTY</th>
                                <th class="px-4 py-2.5 text-right font-medium w-[10%]">Total</th>
                                <th class="px-4 py-2.5 text-center font-medium w-[15%] whitespace-nowrap">Packly Commission</th>
                                <th class="px-4 py-2.5 text-right font-medium w-[12%] whitespace-nowrap">Merchant Get</th>
                                <th class="px-4 py-2.5 text-center font-medium w-[12%]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($items as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-4"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4">
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                                            <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4"><span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $item['name'] }}</span></td>
                                    <td class="px-4 py-4 text-right"><span class="text-sm text-gray-700 dark:text-gray-300">৳ {{ $item['price'] }}</span></td>
                                    <td class="px-4 py-4 text-center"><span class="text-sm text-gray-700 dark:text-gray-300">x{{ $item['qty'] }}</span></td>
                                    <td class="px-4 py-4 text-right"><span class="text-sm font-semibold text-gray-800 dark:text-white/90">৳ {{ $item['total'] }}</span></td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-sm font-medium text-red-500 dark:text-red-400">- ৳ {{ $item['commission'] }}</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 ml-0.5">({{ $item['commission_percent'] }}%)</span>
                                    </td>
                                    <td class="px-4 py-4 text-right"><span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">৳ {{ $item['merchant_get'] }}</span></td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full bg-teal-500 px-2.5 py-0.5 text-xs font-medium text-white">{{ $item['status'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Delivery Address --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Shipping Address</h3>
                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                        Edit
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $order['customer'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order['phone'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $order['address'] }}</p>
                            </div>
                        </div>
                        <div class="space-y-2.5">
                            @foreach ([['Landmark', $order['landmark']], ['Division', $order['division']], ['District', $order['district']], ['City', $order['city']]] as $f)
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-white/[0.02] px-4 py-2.5">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $f[0] }}</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $f[1] ?: '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity Log --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Activity Log</h3>
                </div>
                <div class="p-6">
                    @php
                        $logs = [
                            ['action' => 'Order status changed to Ready to Ship', 'by' => 'System', 'time' => '11/04/2026 07:39 PM'],
                            ['action' => 'Order placed by customer', 'by' => 'Customer', 'time' => '11/04/2026 07:33 PM'],
                        ];
                    @endphp
                    <div class="space-y-0">
                        @foreach ($logs as $index => $log)
                            <div class="flex gap-3 {{ $index < count($logs) - 1 ? 'pb-4' : '' }}">
                                <div class="flex flex-col items-center">
                                    <div class="w-2.5 h-2.5 rounded-full {{ $index === 0 ? 'bg-emerald-500 ring-4 ring-emerald-100 dark:ring-emerald-500/20' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                    @if($index < count($logs) - 1)
                                        <div class="w-0.5 flex-1 bg-gray-100 dark:bg-gray-800"></div>
                                    @endif
                                </div>
                                <div class="-mt-0.5 flex-1">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log['action'] }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $log['time'] }}</span>
                                        <span class="text-xs text-gray-300 dark:text-gray-600">|</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">by {{ $log['by'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">
            {{-- Order Summary --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Order Summary</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Sub Total (2 Items)</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90">৳ 760.00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Discount (Bear by merchant)</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">৳ 0.00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Shipping Fee</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">৳ 50.00</span>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">Total</span>
                        <span class="text-lg font-bold text-gray-800 dark:text-white/90">৳ 810.00</span>
                    </div>
                    <div class="space-y-2.5 rounded-xl bg-gray-50 dark:bg-white/[0.02] p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Packly Commission</span>
                            <span class="text-sm font-medium text-red-500 dark:text-red-400">- ৳ 38</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Gateway Charge</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">- ৳ 0</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4 px-4 py-3 rounded-xl bg-emerald-500 text-white">
                        <span class="text-sm font-semibold">Total Merchant Get</span>
                        <span class="text-lg font-bold">৳ 722</span>
                    </div>
                </div>
            </div>

            {{-- Order Timeline --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Order Timeline</h3>
                </div>
                <div class="p-6">
                    @php
                        $timeline = [
                            ['event' => 'Order Placed', 'time' => '11/04/2026 07:33 PM', 'done' => true],
                            ['event' => 'Ready to Ship', 'time' => '11/04/2026 07:39 PM', 'done' => true],
                            ['event' => 'Shipped', 'time' => '', 'done' => false],
                            ['event' => 'Delivered', 'time' => '', 'done' => false],
                        ];
                    @endphp
                    <div class="space-y-0">
                        @foreach ($timeline as $index => $step)
                            <div class="flex gap-3 {{ $index < count($timeline) - 1 ? 'pb-5' : '' }}">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full {{ $step['done'] ? 'bg-emerald-500 ring-4 ring-emerald-100 dark:ring-emerald-500/20' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                    @if($index < count($timeline) - 1)
                                        <div class="w-0.5 flex-1 {{ $step['done'] ? 'bg-emerald-200 dark:bg-emerald-500/30' : 'bg-gray-100 dark:bg-gray-800' }}"></div>
                                    @endif
                                </div>
                                <div class="-mt-0.5">
                                    <p class="text-sm font-medium {{ $step['done'] ? 'text-gray-800 dark:text-white/90' : 'text-gray-400 dark:text-gray-500' }}">{{ $step['event'] }}</p>
                                    @if($step['time'])
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $step['time'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Actions</h3>
                </div>
                <div class="p-4 space-y-2">
                    <button type="button" class="w-full flex items-center gap-2 rounded-lg bg-brand-50 px-4 py-2.5 text-sm font-medium text-brand-600 hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:text-brand-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 12H5.25"/></svg>
                        Print Invoice
                    </button>
                    <button type="button" class="w-full flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-600 hover:bg-emerald-100 transition-colors dark:bg-emerald-500/10 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        Download Invoice
                    </button>
                    <button type="button" class="w-full flex items-center gap-2 rounded-lg bg-red-50 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-100 transition-colors dark:bg-red-500/10 dark:text-red-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
