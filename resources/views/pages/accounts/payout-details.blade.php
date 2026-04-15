@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Payout request Details (#PKLY-30344700)" />

    @php
        $payout = [
            'id' => 'PKLY-30344700',
            'owner' => 'Arafat',
            'shop' => 'Gadget Hawkers',
            'method' => 'Bank',
            'amount' => '7399.55',
            'status' => 'Pending',
            'created_at' => '11/04/2026 12:57 PM',
            'bank_name' => 'Mutual Trust Bank PLC',
            'account_holder' => 'ARAFAT',
            'account_number' => '1311000046418',
            'branch' => 'Mirpur-2 Branch (0516)',
            'routing' => '145261012',
        ];
    @endphp

    {{-- Top Action Bar --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mb-6" x-data="{ status: '{{ $payout['status'] }}' }">
        <div class="flex items-center justify-between px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                {{-- Status Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300">
                        <span class="w-2 h-2 rounded-full"
                            :class="{
                                'bg-yellow-500': status === 'Pending',
                                'bg-blue-500': status === 'Ready',
                                'bg-emerald-500': status === 'Paid',
                                'bg-red-500': status === 'Hold'
                            }"></span>
                        <span x-text="status"></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-40 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                        <ul class="py-1 text-sm">
                            <li><button @click="status = 'Pending'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Pending</button></li>
                            <li><button @click="status = 'Ready'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Ready</button></li>
                            <li><button @click="status = 'Paid'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Paid</button></li>
                            <li><button @click="status = 'Hold'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-red-500"></span> Hold</button></li>
                        </ul>
                    </div>
                </div>
                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors">
                    Submit
                </button>
            </div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Download Invoice
            </button>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        {{-- Basic Information --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Basic Information</h3>
            </div>
            <div class="px-5 py-5 sm:px-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Shop Owner's Name</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payout['owner'] }}</span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Shop Name</span>
                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ $payout['shop'] }}</span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Payment Method</span>
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">{{ $payout['method'] }}</span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Amount</span>
                    <span class="text-lg font-bold text-gray-800 dark:text-white/90">৳{{ $payout['amount'] }}</span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-yellow-50 border border-yellow-200 px-2.5 py-0.5 text-xs font-medium text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                        {{ $payout['status'] }}
                    </span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Created Date</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $payout['created_at'] }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-5 py-4 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Payment Information</h3>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                    Edit
                </button>
            </div>
            <div class="px-5 py-5 sm:px-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Bank Name</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payout['bank_name'] }}</span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Account Holder Name</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payout['account_holder'] }}</span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Account Number</span>
                    <div class="flex items-center gap-1.5">
                        <span class="text-sm font-medium text-gray-800 dark:text-white/90 font-mono">{{ $payout['account_number'] }}</span>
                        <button type="button" class="text-gray-400 hover:text-emerald-500 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                        </button>
                    </div>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Branch</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $payout['branch'] }}</span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Routing Number</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90 font-mono">{{ $payout['routing'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Beneficiary Accounts --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6 mb-6">
        <div class="px-5 sm:px-6 mb-5">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Merchant Payout Beneficiary Accounts</h3>
        </div>

        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[800px]">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-sm">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 w-[4%]">#</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 w-[12%]">Type</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 w-[18%]">Bank / Wallet</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 w-[14%]">Account Holder</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 w-[16%]">Account Number</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 w-[18%]">Branch</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 w-[14%]">Routing Number</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400 w-[8%]">Default</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr class="bg-yellow-50/50 dark:bg-yellow-500/5">
                            <td class="px-4 py-4"><span class="text-sm text-gray-600 dark:text-gray-400">1</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">Bank Account</span></td>
                            <td class="px-4 py-4"><span class="text-sm font-medium text-gray-800 dark:text-white/90">Mutual Trust Bank PLC</span></td>
                            <td class="px-4 py-4"><span class="text-sm text-gray-700 dark:text-gray-300">ARAFAT</span></td>
                            <td class="px-4 py-4"><span class="text-sm font-mono text-gray-700 dark:text-gray-300">1311000046418</span></td>
                            <td class="px-4 py-4"><span class="text-sm text-gray-700 dark:text-gray-300">Mirpur-2 Branch (0516)</span></td>
                            <td class="px-4 py-4"><span class="text-sm font-mono text-gray-700 dark:text-gray-300">145261012</span></td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center rounded-full bg-orange-500 px-2.5 py-0.5 text-xs font-medium text-white">No</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 mt-3 px-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    Highlighted row indicates the beneficiary selected for this payout request.
                </p>
            </div>
        </div>
    </div>

    {{-- Order List --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        <div class="px-5 sm:px-6 mb-5">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Cleared Orders</h3>
        </div>

        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1000px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[16%]">Date</th>
                            <th class="px-4 py-3 text-left font-medium w-[16%]">Invoice ID</th>
                            <th class="px-4 py-3 text-right font-medium w-[12%]">Sub Total</th>
                            <th class="px-4 py-3 text-right font-medium w-[14%]">Commission</th>
                            <th class="px-4 py-3 text-right font-medium w-[14%]">Gateway Charge</th>
                            <th class="px-4 py-3 text-right font-medium w-[12%]">Payable</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[8%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $orders = [
                                    ['date' => '13/03/2026 01:17 AM', 'order_id' => '231133579', 'invoice' => 'INV563B0A9', 'subtotal' => '299.00', 'commission' => '14.95', 'gateway' => '0', 'payable' => '284.05'],
                                    ['date' => '15/03/2026 03:19 AM', 'order_id' => '232076888', 'invoice' => 'INV8FCDBA4', 'subtotal' => '140.00', 'commission' => '7.00', 'gateway' => '0', 'payable' => '133.00'],
                                    ['date' => '05/04/2026 03:18 PM', 'order_id' => '236549196', 'invoice' => 'INVC3A2IED', 'subtotal' => '350.00', 'commission' => '17.50', 'gateway' => '0', 'payable' => '332.50'],
                                ];
                            @endphp

                            @foreach ($orders as $index => $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-5 w-[4%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-4 py-5 w-[16%]">
                                        @php $dp = explode(' ', $order['date'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] ?? '' }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] ?? '' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 w-[16%]">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order['order_id'] }}</p>
                                            <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">{{ $order['invoice'] }}</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 text-right w-[12%]">
                                        <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $order['subtotal'] }}</span>
                                    </td>
                                    <td class="px-4 py-5 text-right w-[14%]">
                                        <span class="text-sm font-medium text-red-500 dark:text-red-400">- {{ $order['commission'] }}</span>
                                    </td>
                                    <td class="px-4 py-5 text-right w-[14%]">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">- {{ $order['gateway'] }}</span>
                                    </td>
                                    <td class="px-4 py-5 text-right w-[12%]">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $order['payable'] }}</span>
                                    </td>
                                    <td class="px-4 py-5 text-center w-[8%]">
                                        <a href="#" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Order Summary --}}
                <div class="mt-4 flex justify-end px-1">
                    <div class="w-full max-w-sm rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02] overflow-hidden">
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div class="flex items-center justify-between px-5 py-3">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Sub Total</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">789.00</span>
                            </div>
                            <div class="flex items-center justify-between px-5 py-3">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Commission</span>
                                <span class="text-sm font-medium text-red-500 dark:text-red-400">- 39.45</span>
                            </div>
                            <div class="flex items-center justify-between px-5 py-3">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Gateway Charge</span>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">- 0</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3 bg-emerald-500 text-white">
                            <span class="text-sm font-semibold">Total Payable</span>
                            <span class="text-base font-bold">৳749.55</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
