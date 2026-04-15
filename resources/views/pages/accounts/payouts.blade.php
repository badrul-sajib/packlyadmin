@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Payout request list" />

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Requests</p>
                    <p class="text-2xl font-bold text-brand-500 dark:text-brand-400 mt-1">258,363.05</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">87 Requests</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Amount</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">4,008.05</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">6 Requests</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-50 dark:bg-yellow-500/10">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Paid</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">245,680.00</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">72 Paid out</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/10">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">On Hold</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">8,675.00</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">9 Requests</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-50 dark:bg-red-500/10">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeMethod: 'all' }">
        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-4 px-5 sm:px-6">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by Request ID, Merchant Name/Phone/Shop Name..." class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <div class="relative w-48" x-data="{ open: false }">
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
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Boisati</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Mira gallery</button></li>
                    </ul>
                </div>
            </div>
            <div class="relative w-36" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>All Status</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Status</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Pending</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Ready</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Paid</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Hold</button></li>
                    </ul>
                </div>
            </div>
            <x-common.date-range-picker id="payoutDateRange" />
        </div>

        {{-- Method Tabs --}}
        <div class="flex items-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeMethod = 'all'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeMethod === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                All
            </button>
            <button @click="activeMethod = 'bank'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeMethod === 'bank' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Bank
            </button>
            <button @click="activeMethod = 'bkash_nagad_rocket'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeMethod === 'bkash_nagad_rocket' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Bkash/Nagad/Rocket
            </button>
            <button @click="activeMethod = 'bkash'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeMethod === 'bkash' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Bkash
            </button>
            <button @click="activeMethod = 'nagad'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeMethod === 'nagad' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Nagad
            </button>
            <button @click="activeMethod = 'rocket'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeMethod === 'rocket' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Rocket
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%]">Request ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[20%]">Merchant Info</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%]">Method</th>
                            <th class="px-4 py-3 text-right font-medium w-[12%]">Amount</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[16%]">Date</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $payouts = [
                                    ['id' => 'PKLY-21213292', 'shop' => 'Cosmetics World Bangladesh', 'phone' => '01989375224', 'method' => 'Bank', 'amount' => '275.50', 'status' => 'Pending', 'date' => '10/04/2026 10:11 PM'],
                                    ['id' => 'PKLY-12718368', 'shop' => 'E-Hridoy Shop', 'phone' => '01960127335', 'method' => 'Bank', 'amount' => '997.50', 'status' => 'Ready', 'date' => '10/04/2026 05:24 PM'],
                                    ['id' => 'PKLY-46196774', 'shop' => 'JR Unique Gadgets', 'phone' => '01886304506', 'method' => 'Bkash', 'amount' => '616.55', 'status' => 'Pending', 'date' => '10/04/2026 04:21 PM'],
                                    ['id' => 'PKLY-82792083', 'shop' => 'Boisati', 'phone' => '01825514101', 'method' => 'Bkash', 'amount' => '325.85', 'status' => 'Paid', 'date' => '10/04/2026 04:06 PM'],
                                    ['id' => 'PKLY-50499210', 'shop' => 'Mira gallery', 'phone' => '01714882370', 'method' => 'Nagad', 'amount' => '1425.00', 'status' => 'Hold', 'date' => '10/04/2026 03:58 PM'],
                                    ['id' => 'PKLY-14713031', 'shop' => 'Defense Academy', 'phone' => '01765024098', 'method' => 'Rocket', 'amount' => '367.65', 'status' => 'Pending', 'date' => '10/04/2026 03:48 PM'],
                                ];
                            @endphp

                            @foreach ($payouts as $index => $payout)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-5 w-[4%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-4 py-5 w-[12%]">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payout['id'] }}</span>
                                            <button type="button" class="text-gray-400 hover:text-emerald-500 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 w-[20%]">
                                        <div>
                                            <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">{{ $payout['shop'] }}</a>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $payout['phone'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 w-[10%]">
                                        @php
                                            $methodColors = [
                                                'Bank' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                'Bkash' => 'bg-pink-50 text-pink-700 dark:bg-pink-500/10 dark:text-pink-400',
                                                'Nagad' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                                'Rocket' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $methodColors[$payout['method']] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $payout['method'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-5 text-right w-[12%]">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $payout['amount'] }}</span>
                                    </td>
                                    <td class="px-4 py-5 text-center w-[10%]">
                                        @php
                                            $statusStyles = [
                                                'Pending' => 'bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30',
                                                'Ready' => 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30',
                                                'Paid' => 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30',
                                                'Hold' => 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30',
                                            ];
                                            $dotColors = [
                                                'Pending' => 'bg-yellow-500',
                                                'Ready' => 'bg-blue-500',
                                                'Paid' => 'bg-emerald-500',
                                                'Hold' => 'bg-red-500',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$payout['status']] ?? '' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$payout['status']] ?? 'bg-gray-400' }}"></span>
                                            {{ $payout['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-5 w-[16%]">
                                        @php $dateParts = explode(' ', $payout['date'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dateParts[0] ?? '' }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dateParts[1] ?? '' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 text-center w-[10%]">
                                        <a href="{{ route('accounts.payout-details', $payout['id']) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="View Details">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">6</span> of <span class="font-medium text-gray-700 dark:text-gray-300">87</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">9</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
