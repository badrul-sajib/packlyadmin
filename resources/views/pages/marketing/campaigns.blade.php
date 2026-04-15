@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Campaigns" />

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Campaigns</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">24</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">8</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Prime Views</p>
                    <p class="text-xl font-bold text-violet-600 dark:text-violet-400 mt-1">42</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Revenue</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">৳40,66,000</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Campaigns --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">All</button>
                <button @click="activeTab = 'active'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'active' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Active</button>
                <button @click="activeTab = 'scheduled'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'scheduled' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Scheduled</button>
                <button @click="activeTab = 'ended'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'ended' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Ended</button>
                <button @click="activeTab = 'draft'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'draft' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Draft</button>
            </div>
            <div class="flex-1"></div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Create Campaign
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[20%]">Campaign</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%] whitespace-nowrap">Prime Views</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Products</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Orders</th>
                            <th class="px-4 py-3 text-right font-medium w-[10%]">Revenue</th>
                            <th class="px-4 py-3 text-left font-medium w-[14%] whitespace-nowrap">Date Range</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Status</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $campaigns = [
                                    ['id' => 1, 'name' => 'Baisakhi Mega Sale 2026', 'type' => 'Flash Sale', 'discount' => '40% OFF', 'prime_views' => 4, 'products' => 696, 'orders' => 1820, 'revenue' => '৳4,56,000', 'start' => '10/04/2026', 'end' => '20/04/2026', 'status' => 'Active'],
                                    ['id' => 2, 'name' => 'Summer Clearance', 'type' => 'Seasonal', 'discount' => 'Up to 60%', 'prime_views' => 3, 'products' => 480, 'orders' => 0, 'revenue' => '৳0', 'start' => '01/05/2026', 'end' => '31/05/2026', 'status' => 'Scheduled'],
                                    ['id' => 3, 'name' => 'Electronics Week', 'type' => 'Category Sale', 'discount' => '25% OFF', 'prime_views' => 5, 'products' => 1320, 'orders' => 3450, 'revenue' => '৳12,80,000', 'start' => '01/04/2026', 'end' => '07/04/2026', 'status' => 'Ended'],
                                    ['id' => 4, 'name' => '12:12 Grand Sale', 'type' => 'Flash Sale', 'discount' => 'Multi', 'prime_views' => 6, 'products' => 2050, 'orders' => 8900, 'revenue' => '৳28,50,000', 'start' => '12/12/2025', 'end' => '12/12/2025', 'status' => 'Ended'],
                                    ['id' => 5, 'name' => 'New User Welcome Offer', 'type' => 'Promotion', 'discount' => '৳100 OFF', 'prime_views' => 1, 'products' => 0, 'orders' => 890, 'revenue' => '৳2,45,000', 'start' => '01/01/2026', 'end' => '31/12/2026', 'status' => 'Active'],
                                    ['id' => 6, 'name' => 'Ramadan Special', 'type' => 'Seasonal', 'discount' => '30% OFF', 'prime_views' => 4, 'products' => 890, 'orders' => 5200, 'revenue' => '৳18,90,000', 'start' => '01/03/2026', 'end' => '31/03/2026', 'status' => 'Ended'],
                                    ['id' => 7, 'name' => 'Eid Collection Launch', 'type' => 'Flash Sale', 'discount' => '35% OFF', 'prime_views' => 0, 'products' => 0, 'orders' => 0, 'revenue' => '৳0', 'start' => '', 'end' => '', 'status' => 'Draft'],
                                ];
                            @endphp

                            @foreach ($campaigns as $index => $campaign)
                                @php
                                    $stStyles = [
                                        'Active' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30',
                                        'Scheduled' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30',
                                        'Ended' => 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30',
                                        'Draft' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30',
                                    ];
                                    $stDots = ['Active' => 'bg-emerald-500', 'Scheduled' => 'bg-blue-500', 'Ended' => 'bg-gray-400', 'Draft' => 'bg-yellow-500'];
                                    $typeColors = [
                                        'Flash Sale' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                        'Seasonal' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                        'Category Sale' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                        'Promotion' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors {{ $campaign['status'] === 'Ended' ? 'opacity-60' : '' }}">
                                    <td class="px-4 py-5 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-5 w-[20%]">
                                        <div>
                                            <a href="{{ route('marketing.campaign-detail', $campaign['id']) }}" class="text-sm font-semibold text-gray-800 hover:text-brand-500 dark:text-white/90 dark:hover:text-brand-400">{{ $campaign['name'] }}</a>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium {{ $typeColors[$campaign['type']] ?? 'bg-gray-100 text-gray-600' }}">{{ $campaign['type'] }}</span>
                                                <span class="text-xs font-semibold text-brand-600 dark:text-brand-400">{{ $campaign['discount'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 text-center w-[10%]">
                                        @if($campaign['prime_views'] > 0)
                                            <a href="{{ route('marketing.campaign-detail', $campaign['id']) }}" class="inline-flex items-center gap-1.5 rounded-full bg-violet-50 px-2.5 py-1 text-xs font-medium text-violet-700 hover:bg-violet-100 transition-colors dark:bg-violet-500/10 dark:text-violet-400">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $campaign['prime_views'] }} Views
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">None</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5 text-center w-[8%]"><span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($campaign['products']) }}</span></td>
                                    <td class="px-4 py-5 text-center w-[8%]"><span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($campaign['orders']) }}</span></td>
                                    <td class="px-4 py-5 text-right w-[10%]"><span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $campaign['revenue'] }}</span></td>
                                    <td class="px-4 py-5 w-[14%]">
                                        @if($campaign['start'])
                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                <span>{{ $campaign['start'] }}</span>
                                                <span class="text-gray-300 dark:text-gray-600 mx-1">-</span>
                                                <span>{{ $campaign['end'] }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-yellow-500">Not set</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5 text-center w-[8%]">
                                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $stStyles[$campaign['status']] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $stDots[$campaign['status']] }}"></span>
                                            {{ $campaign['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-5 text-center w-[10%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('marketing.campaign-detail', $campaign['id']) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="View Details">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </a>
                                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
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
            <p class="text-sm text-gray-500 dark:text-gray-400">Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">7</span> of <span class="font-medium text-gray-700 dark:text-gray-300">24</span> results</p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
