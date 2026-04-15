@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Prime Views List" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add new
            </button>
            <div class="relative w-48" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>All Campaigns</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-56 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm max-h-48 overflow-y-auto">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Campaigns</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Baisakhi Mega Sale 2026</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Summer Clearance</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Electronics Week</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">12:12 Grand Sale</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Ramadan Special</button></li>
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
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Active</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Inactive</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-56">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by name" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[900px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[20%]">Name</th>
                            <th class="px-4 py-3 text-left font-medium w-[18%]">Campaign</th>
                            <th class="px-4 py-3 text-center font-medium w-[12%] whitespace-nowrap">Total Products</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%] whitespace-nowrap">Log History</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[16%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $primeViews = [
                                    ['name' => 'Baisakhi Special Discount', 'campaign' => 'Baisakhi Mega Sale 2026', 'campaign_id' => 1, 'products' => 262, 'status' => 'Active'],
                                    ['name' => '40% OFF Electronics', 'campaign' => 'Baisakhi Mega Sale 2026', 'campaign_id' => 1, 'products' => 150, 'status' => 'Active'],
                                    ['name' => 'Free Delivery Zone', 'campaign' => 'Baisakhi Mega Sale 2026', 'campaign_id' => 1, 'products' => 184, 'status' => 'Active'],
                                    ['name' => 'Flash Deal Hour', 'campaign' => 'Baisakhi Mega Sale 2026', 'campaign_id' => 1, 'products' => 100, 'status' => 'Inactive'],
                                    ['name' => 'Summer Fashion 60%', 'campaign' => 'Summer Clearance', 'campaign_id' => 2, 'products' => 234, 'status' => 'Active'],
                                    ['name' => 'Summer Gadgets Sale', 'campaign' => 'Summer Clearance', 'campaign_id' => 2, 'products' => 186, 'status' => 'Active'],
                                    ['name' => 'Free Shipping Weekend', 'campaign' => 'Summer Clearance', 'campaign_id' => 2, 'products' => 60, 'status' => 'Active'],
                                    ['name' => '12 Taka Sale', 'campaign' => '12:12 Grand Sale', 'campaign_id' => 4, 'products' => 520, 'status' => 'Inactive'],
                                    ['name' => '12% Discount All', 'campaign' => '12:12 Grand Sale', 'campaign_id' => 4, 'products' => 1325, 'status' => 'Inactive'],
                                    ['name' => '12 Tk Delivery', 'campaign' => '12:12 Grand Sale', 'campaign_id' => 4, 'products' => 59, 'status' => 'Inactive'],
                                    ['name' => 'Flash Sale 12:12', 'campaign' => '12:12 Grand Sale', 'campaign_id' => 4, 'products' => 170, 'status' => 'Inactive'],
                                ];
                            @endphp

                            @foreach ($primeViews as $index => $pv)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4 w-[20%]"><span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $pv['name'] }}</span></td>
                                    <td class="px-4 py-4 w-[18%]">
                                        <a href="{{ route('marketing.campaign-detail', $pv['campaign_id']) }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">{{ $pv['campaign'] }}</a>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[12%]">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="inline-flex items-center justify-center min-w-[32px] h-7 rounded-md bg-brand-500 px-2 text-xs font-bold text-white">{{ $pv['products'] }}</span>
                                            <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-md border border-gray-200 text-gray-400 hover:text-emerald-500 hover:border-emerald-300 transition-colors dark:border-gray-700" title="Add Products">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]" x-data="{ open: false, status: '{{ $pv['status'] }}' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button"
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium cursor-pointer transition-colors"
                                                :class="status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : 'bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30'">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="status === 'Active' ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                                                <span x-text="status"></span>
                                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-1 w-32 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                <ul class="py-1 text-sm">
                                                    <li><button @click="status = 'Active'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active</button></li>
                                                    <li><button @click="status = 'Inactive'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-gray-400"></span> Inactive</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-800 text-white hover:bg-gray-700 transition-colors dark:bg-gray-700 dark:hover:bg-gray-600" title="View Log History">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[16%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="#" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 transition-colors">View</a>
                                            <button type="button" class="inline-flex items-center justify-center rounded-lg bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-yellow-600 transition-colors">Edit</button>
                                            <button type="button" class="inline-flex items-center justify-center rounded-lg bg-red-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-600 transition-colors">Delete</button>
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
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Showing 1 to 11 of 42 entries per page</span>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                        10
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute bottom-full mb-1 left-0 z-50 w-20 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                        <ul class="py-1 text-sm">
                            <li><button class="w-full px-3 py-1.5 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">10</button></li>
                            <li><button class="w-full px-3 py-1.5 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">25</button></li>
                            <li><button class="w-full px-3 py-1.5 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">50</button></li>
                        </ul>
                    </div>
                </div>
            </div>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">5</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
