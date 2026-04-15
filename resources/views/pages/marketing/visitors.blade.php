@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Visitors" />

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        @php
            $stats = [
                ['label' => 'Total Visitors', 'value' => '1,24,580', 'change' => '+18.2%', 'up' => true, 'color' => 'brand'],
                ['label' => 'Today', 'value' => '3,245', 'change' => '+12.5%', 'up' => true, 'color' => 'emerald'],
                ['label' => 'Unique Users', 'value' => '82,340', 'change' => '+9.8%', 'up' => true, 'color' => 'blue'],
                ['label' => 'Bounce Rate', 'value' => '34.2%', 'change' => '-2.1%', 'up' => false, 'color' => 'emerald'],
                ['label' => 'Avg. Session', 'value' => '4m 32s', 'change' => '+0.8%', 'up' => true, 'color' => 'violet'],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $stat['label'] }}</p>
                <div class="flex items-end justify-between mt-1">
                    <p class="text-xl font-bold text-gray-800 dark:text-white/90">{{ $stat['value'] }}</p>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium {{ $stat['up'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                        @if($stat['up'])
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        @endif
                        {{ $stat['change'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Traffic Chart + Sources --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        {{-- Chart --}}
        <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Traffic Overview</h3>
                <x-common.date-range-picker id="visitorDateRange" />
            </div>
            <div class="p-6">
                <div class="h-64 flex items-end gap-2 justify-between">
                    @php
                        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        $heights = [60, 75, 55, 85, 95, 70, 45];
                    @endphp
                    @foreach ($days as $i => $day)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full rounded-t-lg bg-brand-500/20 dark:bg-brand-500/30 relative" style="height: {{ $heights[$i] }}%">
                                <div class="absolute bottom-0 left-0 right-0 rounded-t-lg bg-brand-500" style="height: {{ $heights[$i] * 0.65 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-center gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-brand-500"></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Visitors</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-brand-500/20"></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Page Views</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Traffic Sources --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Traffic Sources</h3>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $sources = [
                        ['name' => 'Direct', 'visitors' => '42,350', 'percent' => 34, 'color' => 'bg-brand-500'],
                        ['name' => 'Organic Search', 'visitors' => '31,200', 'percent' => 25, 'color' => 'bg-emerald-500'],
                        ['name' => 'Social Media', 'visitors' => '24,890', 'percent' => 20, 'color' => 'bg-blue-500'],
                        ['name' => 'Referral', 'visitors' => '15,640', 'percent' => 13, 'color' => 'bg-violet-500'],
                        ['name' => 'Paid Ads', 'visitors' => '10,500', 'percent' => 8, 'color' => 'bg-orange-500'],
                    ];
                @endphp
                @foreach ($sources as $source)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $source['color'] }}"></span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $source['name'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $source['visitors'] }}</span>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $source['percent'] }}%</span>
                            </div>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-1.5 rounded-full {{ $source['color'] }}" style="width: {{ $source['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Visitors Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Recent Visitors</h3>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by IP or location" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1000px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[14%]">IP Address</th>
                            <th class="px-4 py-3 text-left font-medium w-[14%]">Location</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%]">Device</th>
                            <th class="px-4 py-3 text-left font-medium w-[12%]">Browser</th>
                            <th class="px-4 py-3 text-left font-medium w-[16%]">Page Visited</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Source</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Duration</th>
                            <th class="px-4 py-3 text-left font-medium rounded-r-lg w-[12%]">Time</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $visitors = [
                                    ['ip' => '103.24.56.12', 'location' => 'Dhaka, BD', 'device' => 'Mobile', 'browser' => 'Chrome', 'page' => '/products/wireless-earbuds', 'source' => 'Direct', 'duration' => '5m 12s', 'time' => '11/04/2026 03:45 PM'],
                                    ['ip' => '172.31.34.97', 'location' => 'Chattogram, BD', 'device' => 'Desktop', 'browser' => 'Firefox', 'page' => '/home', 'source' => 'Google', 'duration' => '2m 34s', 'time' => '11/04/2026 03:42 PM'],
                                    ['ip' => '45.120.89.3', 'location' => 'Rajshahi, BD', 'device' => 'Mobile', 'browser' => 'Safari', 'page' => '/categories/electronics', 'source' => 'Facebook', 'duration' => '8m 05s', 'time' => '11/04/2026 03:38 PM'],
                                    ['ip' => '192.168.1.45', 'location' => 'Sylhet, BD', 'device' => 'Tablet', 'browser' => 'Chrome', 'page' => '/cart', 'source' => 'Direct', 'duration' => '1m 20s', 'time' => '11/04/2026 03:35 PM'],
                                    ['ip' => '103.48.72.18', 'location' => 'Khulna, BD', 'device' => 'Desktop', 'browser' => 'Edge', 'page' => '/products/power-bank-20000', 'source' => 'Google', 'duration' => '6m 48s', 'time' => '11/04/2026 03:30 PM'],
                                    ['ip' => '58.97.214.5', 'location' => 'Dhaka, BD', 'device' => 'Mobile', 'browser' => 'Chrome', 'page' => '/checkout', 'source' => 'Instagram', 'duration' => '3m 15s', 'time' => '11/04/2026 03:28 PM'],
                                    ['ip' => '114.130.45.22', 'location' => 'Gazipur, BD', 'device' => 'Mobile', 'browser' => 'Samsung', 'page' => '/products/gaming-keyboard', 'source' => 'Referral', 'duration' => '4m 50s', 'time' => '11/04/2026 03:22 PM'],
                                    ['ip' => '103.105.68.91', 'location' => 'Comilla, BD', 'device' => 'Desktop', 'browser' => 'Chrome', 'page' => '/home', 'source' => 'Google', 'duration' => '1m 05s', 'time' => '11/04/2026 03:18 PM'],
                                ];
                            @endphp

                            @foreach ($visitors as $index => $v)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4 w-[14%]"><span class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ $v['ip'] }}</span></td>
                                    <td class="px-4 py-4 w-[14%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $v['location'] }}</span></td>
                                    <td class="px-4 py-4 w-[12%]">
                                        @php
                                            $deviceColors = [
                                                'Mobile' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                'Desktop' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                                'Tablet' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $deviceColors[$v['device']] ?? '' }}">{{ $v['device'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[12%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $v['browser'] }}</span></td>
                                    <td class="px-4 py-4 w-[16%]"><span class="text-sm text-brand-500 dark:text-brand-400 truncate block">{{ $v['page'] }}</span></td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        @php
                                            $srcColors = [
                                                'Direct' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                'Google' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'Facebook' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                'Instagram' => 'bg-pink-50 text-pink-700 dark:bg-pink-500/10 dark:text-pink-400',
                                                'Referral' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $srcColors[$v['source']] ?? '' }}">{{ $v['source'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $v['duration'] }}</span></td>
                                    <td class="px-4 py-4 w-[12%]">
                                        @php $tp = explode(' ', $v['time'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $tp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $tp[1] }}</p>
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
            <p class="text-sm text-gray-500 dark:text-gray-400">Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">8</span> of <span class="font-medium text-gray-700 dark:text-gray-300">3,245</span> results</p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">406</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
