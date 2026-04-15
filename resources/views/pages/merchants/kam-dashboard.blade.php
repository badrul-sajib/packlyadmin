@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="KAM Dashboard" />

    @php
        $agents = [
            ['name' => 'Sabbir Ahmed', 'merchants' => 34, 'orders' => 1240, 'delivered' => 1102, 'cancelled' => 87, 'revenue' => '8,42,500', 'last_activity' => '2h ago',  'avatar' => 'SA'],
            ['name' => 'Roni Islam',   'merchants' => 28, 'orders' => 980,  'delivered' => 854,  'cancelled' => 64, 'revenue' => '6,18,200', 'last_activity' => '4h ago',  'avatar' => 'RI'],
            ['name' => 'Mim Akter',    'merchants' => 22, 'orders' => 760,  'delivered' => 698,  'cancelled' => 31, 'revenue' => '4,92,400', 'last_activity' => '1d ago',  'avatar' => 'MA'],
            ['name' => 'Tanvir Hasan', 'merchants' => 19, 'orders' => 640,  'delivered' => 561,  'cancelled' => 52, 'revenue' => '3,74,800', 'last_activity' => '3h ago',  'avatar' => 'TH'],
            ['name' => 'Sadia Parvin', 'merchants' => 15, 'orders' => 430,  'delivered' => 389,  'cancelled' => 22, 'revenue' => '2,54,600', 'last_activity' => '30m ago', 'avatar' => 'SP'],
        ];
        $colors = ['bg-brand-100 text-brand-600','bg-purple-100 text-purple-600','bg-emerald-100 text-emerald-600','bg-amber-100 text-amber-600','bg-rose-100 text-rose-600'];

        // Summary stats
        $totalMerchants = collect($agents)->sum('merchants');
        $totalOrders    = collect($agents)->sum('orders');
        $totalDelivered = collect($agents)->sum('delivered');
        $totalCancelled = collect($agents)->sum('cancelled');
    @endphp

    {{-- Summary row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        @php
            $summary = [
                ['label' => 'Total Agents',         'value' => count($agents),         'color' => 'blue'],
                ['label' => 'Merchants Managed',    'value' => $totalMerchants,        'color' => 'purple'],
                ['label' => 'Orders Delivered',     'value' => number_format($totalDelivered), 'color' => 'emerald'],
                ['label' => 'Orders Cancelled',     'value' => number_format($totalCancelled), 'color' => 'red'],
            ];
        @endphp
        @foreach ($summary as $s)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] px-5 py-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $s['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-500/10'       : '' }}
                    {{ $s['color'] === 'purple'  ? 'bg-purple-50 dark:bg-purple-500/10'   : '' }}
                    {{ $s['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-500/10' : '' }}
                    {{ $s['color'] === 'red'     ? 'bg-red-50 dark:bg-red-500/10'         : '' }}">
                    @if ($s['color'] === 'blue')
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    @elseif ($s['color'] === 'purple')
                        <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                    @elseif ($s['color'] === 'emerald')
                        <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    @endif
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-800 dark:text-white/90">{{ $s['value'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Agent table --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">KAM Agent Performance</h3>
            <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span> Delivery rate
                <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block ml-2"></span> Cancel rate
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">Agent</th>
                        <th class="px-5 py-3 text-center">Merchants</th>
                        <th class="px-5 py-3 text-center">Orders</th>
                        <th class="px-5 py-3 text-center">Delivered</th>
                        <th class="px-5 py-3 text-center">Cancelled</th>
                        <th class="px-5 py-3 text-left w-48">Performance</th>
                        <th class="px-5 py-3 text-right">Revenue</th>
                        <th class="px-5 py-3 text-center">Last Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @foreach ($agents as $i => $agent)
                        @php
                            $deliveryRate = $agent['orders'] > 0 ? round(($agent['delivered'] / $agent['orders']) * 100) : 0;
                            $cancelRate   = $agent['orders'] > 0 ? round(($agent['cancelled'] / $agent['orders']) * 100) : 0;
                            $color = $colors[$i % count($colors)];
                        @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $color }}">{{ $agent['avatar'] }}</div>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $agent['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-medium text-gray-700 dark:text-gray-300">{{ $agent['merchants'] }}</td>
                            <td class="px-5 py-4 text-center text-sm text-gray-600 dark:text-gray-400">{{ number_format($agent['orders']) }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ number_format($agent['delivered']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-sm font-medium text-red-500">{{ number_format($agent['cancelled']) }}</span>
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
                                <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">৳{{ $agent['revenue'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-xs text-gray-400 dark:text-gray-500">{{ $agent['last_activity'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
