@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Analytics Dashboard" />

    @php
        $kpis = [
            ['label' => 'Gross Revenue',    'value' => '৳42,18,500', 'trend' => '+12.4%', 'up' => true,  'sub' => 'last 30 days', 'color' => 'emerald'],
            ['label' => 'Total Orders',     'value' => '8,342',       'trend' => '+8.1%',  'up' => true,  'sub' => 'last 30 days', 'color' => 'blue'],
            ['label' => 'Avg Order Value',  'value' => '৳505',        'trend' => '+3.7%',  'up' => true,  'sub' => 'per order',    'color' => 'brand'],
            ['label' => 'Cancellation Rate','value' => '6.8%',        'trend' => '-1.2%',  'up' => true,  'sub' => 'down from 8%', 'color' => 'amber'],
            ['label' => 'Active Merchants', 'value' => '318',         'trend' => '+24',    'up' => true,  'sub' => 'this month',   'color' => 'purple'],
            ['label' => 'New Customers',    'value' => '1,204',       'trend' => '+18.3%', 'up' => true,  'sub' => 'this month',   'color' => 'rose'],
        ];

        $revenueData = [
            ['day' => 'Mon', 'revenue' => 142000, 'orders' => 280],
            ['day' => 'Tue', 'revenue' => 168000, 'orders' => 312],
            ['day' => 'Wed', 'revenue' => 134000, 'orders' => 256],
            ['day' => 'Thu', 'revenue' => 198000, 'orders' => 394],
            ['day' => 'Fri', 'revenue' => 224000, 'orders' => 448],
            ['day' => 'Sat', 'revenue' => 286000, 'orders' => 524],
            ['day' => 'Sun', 'revenue' => 176000, 'orders' => 334],
        ];
        $maxRevenue = collect($revenueData)->max('revenue');

        $topMerchants = [
            ['rank' => 1, 'name' => 'StyleNest BD',    'orders' => 842, 'revenue' => '৳5,24,800', 'growth' => '+14%',  'up' => true],
            ['rank' => 2, 'name' => 'TechZone Shop',   'orders' => 718, 'revenue' => '৳4,82,100', 'growth' => '+9%',   'up' => true],
            ['rank' => 3, 'name' => 'FreshMart',       'orders' => 634, 'revenue' => '৳3,94,500', 'growth' => '-2%',   'up' => false],
            ['rank' => 4, 'name' => 'Electro Point',   'orders' => 520, 'revenue' => '৳3,18,200', 'growth' => '+21%',  'up' => true],
            ['rank' => 5, 'name' => 'GreenLeaf Store', 'orders' => 468, 'revenue' => '৳2,74,600', 'growth' => '+5%',   'up' => true],
        ];

        $topProducts = [
            ['name' => 'Wireless Earbuds Pro',   'orders' => 312, 'revenue' => '৳1,24,800', 'stock' => 48],
            ['name' => 'Leather Wallet — Black', 'orders' => 284, 'revenue' => '৳85,200',   'stock' => 12],
            ['name' => 'Cotton T-Shirt (Pack 3)','orders' => 256, 'revenue' => '৳76,800',   'stock' => 0],
            ['name' => 'Phone Stand Adjustable', 'orders' => 218, 'revenue' => '৳43,600',   'stock' => 94],
            ['name' => 'Organic Face Cream',     'orders' => 196, 'revenue' => '৳98,000',   'stock' => 28],
        ];

        $orderFunnel = [
            ['stage' => 'Placed',    'count' => 8342, 'pct' => 100],
            ['stage' => 'Confirmed', 'count' => 7914, 'pct' => 95],
            ['stage' => 'Shipped',   'count' => 7280, 'pct' => 87],
            ['stage' => 'Delivered', 'count' => 6874, 'pct' => 82],
            ['stage' => 'Returned',  'count' => 284,  'pct' => 3],
        ];
    @endphp

    {{-- Date range picker --}}
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-gray-500 dark:text-gray-400">Showing data for <span class="font-medium text-gray-700 dark:text-gray-300">last 30 days</span></p>
        <div class="flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 p-1" x-data="{ range: '30d' }">
            @foreach (['7d' => 'Last 7 days', '30d' => 'Last 30 days', '90d' => 'Last 90 days'] as $key => $label)
                <button type="button" @click="range = '{{ $key }}'"
                    :class="range === '{{ $key }}' ? 'bg-brand-500 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.07]'"
                    class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-5">
        @foreach ($kpis as $kpi)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] px-4 py-4">
                <p class="text-lg font-bold text-gray-800 dark:text-white/90 truncate">{{ $kpi['value'] }}</p>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5 leading-tight">{{ $kpi['label'] }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="text-xs font-semibold {{ $kpi['up'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">{{ $kpi['trend'] }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $kpi['sub'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- Revenue chart --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Daily Revenue (This Week)</h3>
                <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-brand-500 inline-block"></span> Revenue</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-gray-200 dark:bg-gray-700 inline-block"></span> Orders</span>
                </div>
            </div>
            <div class="flex items-end gap-2 h-36">
                @foreach ($revenueData as $d)
                    @php $barH = $maxRevenue > 0 ? round(($d['revenue'] / $maxRevenue) * 100) : 0; @endphp
                    <div class="flex-1 flex flex-col items-center gap-1.5">
                        <div class="w-full flex flex-col justify-end" style="height: 112px;">
                            <div class="w-full rounded-t-md bg-brand-500/80 hover:bg-brand-500 transition-colors cursor-pointer"
                                style="height: {{ $barH }}%"
                                title="{{ $d['day'] }}: ৳{{ number_format($d['revenue']) }}">
                            </div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $d['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Order funnel --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-5">Order Funnel</h3>
            <div class="space-y-3">
                @foreach ($orderFunnel as $f)
                    @php
                        $colors = ['Placed' => 'bg-brand-500', 'Confirmed' => 'bg-blue-500', 'Shipped' => 'bg-purple-500', 'Delivered' => 'bg-emerald-500', 'Returned' => 'bg-red-400'];
                        $barColor = $colors[$f['stage']] ?? 'bg-gray-400';
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $f['stage'] }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ number_format($f['count']) }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $f['pct'] }}%</span>
                            </div>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $f['pct'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Top Merchants --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Top Merchants by Revenue</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/60">
                @foreach ($topMerchants as $m)
                    <div class="px-5 py-3.5 flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $m['rank'] }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $m['name'] }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($m['orders']) }} orders</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $m['revenue'] }}</p>
                            <p class="text-xs {{ $m['up'] ? 'text-emerald-500' : 'text-red-500' }}">{{ $m['growth'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top Products --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Top Products by Orders</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/60">
                @foreach ($topProducts as $i => $p)
                    <div class="px-5 py-3.5 flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $p['name'] }}</p>
                                @if ($p['stock'] === 0)
                                    <span class="flex-shrink-0 text-xs rounded bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/30 px-1.5 py-0.5">Out of stock</span>
                                @elseif ($p['stock'] < 20)
                                    <span class="flex-shrink-0 text-xs rounded bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 px-1.5 py-0.5">Low: {{ $p['stock'] }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($p['orders']) }} orders</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $p['revenue'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
