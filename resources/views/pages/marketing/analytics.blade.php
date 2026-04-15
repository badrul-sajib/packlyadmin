@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Analytics" />

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @php
            $kpis = [
                ['label' => 'Page Views', 'value' => '3,48,920', 'change' => '+14.2%', 'up' => true],
                ['label' => 'Unique Visitors', 'value' => '82,340', 'change' => '+9.8%', 'up' => true],
                ['label' => 'Conversion Rate', 'value' => '3.8%', 'change' => '+0.5%', 'up' => true],
                ['label' => 'Bounce Rate', 'value' => '34.2%', 'change' => '-2.1%', 'up' => false],
            ];
        @endphp
        @foreach ($kpis as $kpi)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $kpi['label'] }}</p>
                <div class="flex items-end justify-between mt-1">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $kpi['value'] }}</p>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium {{ $kpi['up'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $kpi['up'] ? 'M4.5 15.75l7.5-7.5 7.5 7.5' : 'M19.5 8.25l-7.5 7.5-7.5-7.5' }}"/></svg>
                        {{ $kpi['change'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        {{-- Traffic Chart --}}
        <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Traffic Trend</h3>
                <x-common.date-range-picker id="analyticsRange" />
            </div>
            <div class="p-6">
                <div class="h-56 flex items-end gap-1.5 justify-between">
                    @php $bars = [40, 55, 35, 65, 80, 70, 90, 60, 75, 85, 50, 95]; $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                    @foreach ($bars as $i => $h)
                        <div class="flex-1 flex flex-col items-center gap-1.5">
                            <div class="w-full rounded-t-md bg-brand-500" style="height: {{ $h }}%"></div>
                            <span class="text-[10px] text-gray-400">{{ $months[$i] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Device Breakdown --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Device Breakdown</h3>
            </div>
            <div class="p-6 space-y-5">
                @php
                    $devices = [
                        ['name' => 'Mobile', 'percent' => 68, 'count' => '56,000', 'color' => 'bg-brand-500'],
                        ['name' => 'Desktop', 'percent' => 24, 'count' => '19,760', 'color' => 'bg-emerald-500'],
                        ['name' => 'Tablet', 'percent' => 8, 'count' => '6,580', 'color' => 'bg-orange-500'],
                    ];
                @endphp
                @foreach ($devices as $d)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $d['name'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $d['count'] }} ({{ $d['percent'] }}%)</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full {{ $d['color'] }}" style="width: {{ $d['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top Pages + Geographic --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
            <div class="px-5 sm:px-6 mb-5">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Top Pages</h3>
            </div>
            <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
                <table class="w-full">
                    <thead><tr class="bg-emerald-500 text-white text-xs"><th class="px-4 py-2.5 text-left font-medium rounded-l-lg">Page</th><th class="px-4 py-2.5 text-center font-medium">Views</th><th class="px-4 py-2.5 text-center font-medium">Unique</th><th class="px-4 py-2.5 text-center font-medium rounded-r-lg">Bounce</th></tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ([['/', '48,200', '32,100', '28%'], ['/categories/electronics', '22,450', '18,300', '35%'], ['/products/wireless-earbuds', '18,900', '15,200', '22%'], ['/cart', '12,600', '10,400', '45%'], ['/checkout', '8,900', '7,200', '18%']] as $page)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="px-4 py-3"><span class="text-sm text-brand-500 dark:text-brand-400">{{ $page[0] }}</span></td>
                                <td class="px-4 py-3 text-center"><span class="text-sm text-gray-700 dark:text-gray-300">{{ $page[1] }}</span></td>
                                <td class="px-4 py-3 text-center"><span class="text-sm text-gray-700 dark:text-gray-300">{{ $page[2] }}</span></td>
                                <td class="px-4 py-3 text-center"><span class="text-sm text-gray-700 dark:text-gray-300">{{ $page[3] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
            <div class="px-5 sm:px-6 mb-5">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Top Locations</h3>
            </div>
            <div class="px-5 sm:px-6 space-y-3">
                @foreach ([['Dhaka', '45,200', 55], ['Chattogram', '12,800', 16], ['Rajshahi', '6,400', 8], ['Sylhet', '4,900', 6], ['Khulna', '4,200', 5], ['Others', '8,840', 10]] as $loc)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-white/[0.02]">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $loc[0] }}</span>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $loc[1] }}</span>
                            <div class="w-16 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700"><div class="h-1.5 rounded-full bg-brand-500" style="width:{{ $loc[2] }}%"></div></div>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-300 w-8 text-right">{{ $loc[2] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
