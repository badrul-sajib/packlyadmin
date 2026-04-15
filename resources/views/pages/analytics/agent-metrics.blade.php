@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Agent Metrics" />

    @php
        $agents = [
            ['name' => 'Sabbir Ahmed',  'avatar' => 'SA', 'color' => 'bg-brand-100 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400',
             'merchants' => 34, 'orders' => 1240, 'delivered' => 1102, 'cancelled' => 87,  'returned' => 51,  'revenue' => 842500,  'last_active' => '2h ago'],
            ['name' => 'Roni Islam',    'avatar' => 'RI', 'color' => 'bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400',
             'merchants' => 28, 'orders' => 980,  'delivered' => 854,  'cancelled' => 64,  'returned' => 62,  'revenue' => 618200,  'last_active' => '4h ago'],
            ['name' => 'Mim Akter',     'avatar' => 'MA', 'color' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400',
             'merchants' => 22, 'orders' => 760,  'delivered' => 698,  'cancelled' => 31,  'returned' => 31,  'revenue' => 492400,  'last_active' => '1d ago'],
            ['name' => 'Tanvir Hasan',  'avatar' => 'TH', 'color' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400',
             'merchants' => 19, 'orders' => 640,  'delivered' => 561,  'cancelled' => 52,  'returned' => 27,  'revenue' => 374800,  'last_active' => '3h ago'],
            ['name' => 'Sadia Parvin',  'avatar' => 'SP', 'color' => 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400',
             'merchants' => 15, 'orders' => 430,  'delivered' => 389,  'cancelled' => 22,  'returned' => 19,  'revenue' => 254600,  'last_active' => '30m ago'],
        ];

        $totals = [
            'merchants'  => collect($agents)->sum('merchants'),
            'orders'     => collect($agents)->sum('orders'),
            'delivered'  => collect($agents)->sum('delivered'),
            'cancelled'  => collect($agents)->sum('cancelled'),
            'returned'   => collect($agents)->sum('returned'),
            'revenue'    => collect($agents)->sum('revenue'),
        ];
    @endphp

    {{-- Summary row --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-5">
        @php
            $summaryItems = [
                ['label' => 'Total Orders',    'value' => number_format($totals['orders']),    'color' => 'text-brand-600 dark:text-brand-400'],
                ['label' => 'Delivered',       'value' => number_format($totals['delivered']), 'color' => 'text-emerald-600 dark:text-emerald-400'],
                ['label' => 'Cancelled',       'value' => number_format($totals['cancelled']), 'color' => 'text-red-500'],
                ['label' => 'Returned',        'value' => number_format($totals['returned']),  'color' => 'text-amber-600 dark:text-amber-400'],
                ['label' => 'Total Revenue',   'value' => '৳'.number_format($totals['revenue']), 'color' => 'text-emerald-600 dark:text-emerald-400'],
            ];
        @endphp
        @foreach ($summaryItems as $s)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] px-5 py-4 text-center">
                <p class="text-xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $s['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 p-1" x-data="{ range: '30d' }">
            @foreach (['7d' => 'Last 7 days', '30d' => 'Last 30 days', '90d' => 'Last 90 days'] as $key => $label)
                <button type="button" @click="range = '{{ $key }}'"
                    :class="range === '{{ $key }}' ? 'bg-brand-500 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.07]'"
                    class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        <a href="{{ url('/merchants/kam-dashboard') }}"
            class="ml-auto inline-flex items-center gap-1.5 text-xs text-brand-600 dark:text-brand-400 hover:underline">
            View KAM Dashboard
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
    </div>

    {{-- Agent table --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left">Agent</th>
                    <th class="px-5 py-3 text-center">Merchants</th>
                    <th class="px-5 py-3 text-center">Orders</th>
                    <th class="px-5 py-3 text-center">Delivered</th>
                    <th class="px-5 py-3 text-center">Cancelled</th>
                    <th class="px-5 py-3 text-center">Returned</th>
                    <th class="px-5 py-3 text-left w-52">Delivery / Cancel Rate</th>
                    <th class="px-5 py-3 text-right">Revenue</th>
                    <th class="px-5 py-3 text-center">Last Active</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                @foreach ($agents as $agent)
                    @php
                        $deliveryRate = $agent['orders'] > 0 ? round(($agent['delivered'] / $agent['orders']) * 100) : 0;
                        $cancelRate   = $agent['orders'] > 0 ? round(($agent['cancelled'] / $agent['orders']) * 100) : 0;
                        $returnRate   = $agent['orders'] > 0 ? round(($agent['returned']  / $agent['orders']) * 100) : 0;
                        $score        = $deliveryRate >= 90 ? 'Excellent' : ($deliveryRate >= 80 ? 'Good' : 'Needs Review');
                        $scoreBadge   = $deliveryRate >= 90
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30'
                            : ($deliveryRate >= 80
                                ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30'
                                : 'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30');
                    @endphp
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $agent['color'] }}">{{ $agent['avatar'] }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $agent['name'] }}</p>
                                    <span class="inline-flex text-xs rounded-full border px-2 py-0.5 {{ $scoreBadge }}">{{ $score }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center text-sm font-medium text-gray-700 dark:text-gray-300">{{ $agent['merchants'] }}</td>
                        <td class="px-5 py-4 text-center text-sm text-gray-600 dark:text-gray-400">{{ number_format($agent['orders']) }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($agent['delivered']) }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm font-semibold text-red-500">{{ number_format($agent['cancelled']) }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-sm text-amber-600 dark:text-amber-400">{{ number_format($agent['returned']) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                        <div class="h-full rounded-full bg-emerald-400" style="width: {{ $deliveryRate }}%"></div>
                                    </div>
                                    <span class="text-xs tabular-nums text-gray-500 dark:text-gray-400 w-8 text-right">{{ $deliveryRate }}%</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                        <div class="h-full rounded-full bg-red-400" style="width: {{ $cancelRate }}%"></div>
                                    </div>
                                    <span class="text-xs tabular-nums text-gray-500 dark:text-gray-400 w-8 text-right">{{ $cancelRate }}%</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">৳{{ number_format($agent['revenue']) }}</span>
                        </td>
                        <td class="px-5 py-4 text-center text-xs text-gray-400 dark:text-gray-500">{{ $agent['last_active'] }}</td>
                    </tr>
                @endforeach

                {{-- Totals row --}}
                <tr class="bg-gray-50/80 dark:bg-white/[0.02] font-semibold">
                    <td class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400">Totals</td>
                    <td class="px-5 py-3 text-center text-sm text-gray-700 dark:text-gray-300">{{ $totals['merchants'] }}</td>
                    <td class="px-5 py-3 text-center text-sm text-gray-700 dark:text-gray-300">{{ number_format($totals['orders']) }}</td>
                    <td class="px-5 py-3 text-center text-sm text-emerald-600 dark:text-emerald-400">{{ number_format($totals['delivered']) }}</td>
                    <td class="px-5 py-3 text-center text-sm text-red-500">{{ number_format($totals['cancelled']) }}</td>
                    <td class="px-5 py-3 text-center text-sm text-amber-600 dark:text-amber-400">{{ number_format($totals['returned']) }}</td>
                    <td class="px-5 py-3"></td>
                    <td class="px-5 py-3 text-right text-sm text-emerald-600 dark:text-emerald-400">৳{{ number_format($totals['revenue']) }}</td>
                    <td class="px-5 py-3"></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
