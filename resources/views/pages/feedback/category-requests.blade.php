@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Category Requests" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        {{-- Tabs --}}
        <div class="flex items-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">All <span class="ml-1 text-xs opacity-75">86</span></button>
            <button @click="activeTab = 'pending'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'pending' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Pending <span class="ml-1 text-xs opacity-75">14</span></button>
            <button @click="activeTab = 'approved'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'approved' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Approved <span class="ml-1 text-xs opacity-75">58</span></button>
            <button @click="activeTab = 'rejected'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'rejected' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Rejected <span class="ml-1 text-xs opacity-75">14</span></button>
        </div>

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
                <input type="text" placeholder="Search by category name" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1000px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[18%] whitespace-nowrap">Requested Category</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%] whitespace-nowrap">Parent Category</th>
                            <th class="px-4 py-3 text-left font-medium w-[14%]">Merchant</th>
                            <th class="px-4 py-3 text-left font-medium w-[18%]">Reason</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%] whitespace-nowrap">Requested Date</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $requests = [
                                    ['name' => 'Gaming Accessories', 'parent' => 'Electronics Device', 'merchant' => 'CarbonX Shop', 'phone' => '01775006663', 'reason' => 'We have many gaming peripherals but no specific category for gaming accessories.', 'status' => 'Pending', 'date' => '11/04/2026 02:45 PM'],
                                    ['name' => 'Organic Food', 'parent' => 'Food & Beverages', 'merchant' => 'Home Shop BD.com', 'phone' => '01910903717', 'reason' => 'Growing demand for organic food products. Need separate category for better visibility.', 'status' => 'Pending', 'date' => '11/04/2026 10:30 AM'],
                                    ['name' => 'Smart Home', 'parent' => 'Electronics Device', 'merchant' => 'WKL Marts', 'phone' => '01781951811', 'reason' => 'Smart home devices like bulbs, plugs, cameras need a dedicated category.', 'status' => 'Approved', 'date' => '10/04/2026 04:15 PM'],
                                    ['name' => 'Pet Supplies', 'parent' => null, 'merchant' => 'LUXURY VIP', 'phone' => '01342584477', 'reason' => 'Pet food, toys and accessories are popular. New parent category needed.', 'status' => 'Pending', 'date' => '10/04/2026 11:20 AM'],
                                    ['name' => 'Vintage Clothing', 'parent' => 'Fashion Accessories', 'merchant' => 'Mira gallery', 'phone' => '01748832370', 'reason' => 'Vintage and retro fashion is trending. Need subcategory for better classification.', 'status' => 'Rejected', 'date' => '09/04/2026 03:50 PM'],
                                    ['name' => 'EV Accessories', 'parent' => 'Electronic Accessories', 'merchant' => 'CarbonX Shop', 'phone' => '01775006663', 'reason' => 'Electric vehicle chargers, mounts and accessories are growing fast.', 'status' => 'Approved', 'date' => '09/04/2026 09:00 AM'],
                                    ['name' => 'Baby Products', 'parent' => null, 'merchant' => 'Home Shop BD.com', 'phone' => '01910903717', 'reason' => 'Baby clothing, diapers, toys need a parent category. Currently scattered.', 'status' => 'Approved', 'date' => '08/04/2026 02:30 PM'],
                                    ['name' => 'Cryptocurrency Mining', 'parent' => 'Electronics Device', 'merchant' => 'LUXURY VIP', 'phone' => '01342584477', 'reason' => 'Mining hardware and related equipment.', 'status' => 'Rejected', 'date' => '07/04/2026 05:00 PM'],
                                ];
                            @endphp

                            @foreach ($requests as $index => $req)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4 w-[18%]">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $req['name'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[12%]">
                                        @if($req['parent'])
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $req['parent'] }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">New Root</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 w-[14%]">
                                        <div>
                                            <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">{{ $req['merchant'] }}</a>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $req['phone'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[18%]">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $req['reason'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        @if($req['status'] === 'Pending')
                                            <div x-data="{ open: false, showReject: false, feedback: '' }">
                                                <div class="relative inline-block">
                                                    <button @click="open = !open; showReject = false; feedback = ''" type="button" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100 cursor-pointer transition-colors dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                                        Pending
                                                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-2 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" :class="showReject ? 'w-72' : 'w-40'" style="display:none;">
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
                                                        <div x-show="showReject" class="p-3">
                                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90 mb-2">Rejection Reason</p>
                                                            <textarea x-model="feedback" rows="3" placeholder="Enter reason..." class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 resize-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder-gray-500"></textarea>
                                                            <div class="flex items-center justify-end gap-2 mt-2">
                                                                <button @click="showReject = false" type="button" class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                                                                <button @click="open = false; showReject = false" type="button" class="px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">Reject</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($req['status'] === 'Approved')
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 w-[12%]">
                                        @php $dp = explode(' ', $req['date'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        <a href="#" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="View">
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">8</span> of <span class="font-medium text-gray-700 dark:text-gray-300">86</span> results
            </p>
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
