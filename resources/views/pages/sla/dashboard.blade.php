@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="SLA Dashboard" />

    @php
        $stats = [
            ['label' => 'Avg Resolution Time', 'value' => '4.2h',  'sub' => 'last 7 days',  'color' => 'blue',    'trend' => '-0.8h', 'up' => false],
            ['label' => 'Within SLA',           'value' => '87%',   'sub' => 'of all tickets','color' => 'emerald', 'trend' => '+3%',   'up' => true],
            ['label' => 'SLA Breaches',         'value' => '14',    'sub' => 'this week',     'color' => 'red',     'trend' => '+2',    'up' => false],
            ['label' => 'Open Tickets',         'value' => '38',    'sub' => 'awaiting action','color' => 'amber',  'trend' => '-5',    'up' => true],
        ];

        $categoryStats = [
            ['category' => 'Help Request',   'total' => 120, 'within_sla' => 108, 'breached' => 12, 'avg_hours' => 3.1, 'target_hours' => 4],
            ['category' => 'Order Issue',    'total' => 85,  'within_sla' => 70,  'breached' => 15, 'avg_hours' => 5.8, 'target_hours' => 6],
            ['category' => 'Merchant Issue', 'total' => 46,  'within_sla' => 38,  'breached' => 8,  'avg_hours' => 7.2, 'target_hours' => 8],
            ['category' => 'Payment',        'total' => 30,  'within_sla' => 28,  'breached' => 2,  'avg_hours' => 2.4, 'target_hours' => 4],
            ['category' => 'Returns',        'total' => 22,  'within_sla' => 17,  'breached' => 5,  'avg_hours' => 9.1, 'target_hours' => 8],
        ];

        $agentStats = [
            ['name' => 'Sabbir Ahmed',  'avatar' => 'SA', 'resolved' => 48, 'within_sla' => 44, 'breached' => 4,  'avg_hours' => 3.8, 'color' => 'bg-brand-100 text-brand-600'],
            ['name' => 'Roni Islam',    'avatar' => 'RI', 'resolved' => 39, 'within_sla' => 35, 'breached' => 4,  'avg_hours' => 4.1, 'color' => 'bg-purple-100 text-purple-600'],
            ['name' => 'Mim Akter',     'avatar' => 'MA', 'resolved' => 31, 'within_sla' => 29, 'breached' => 2,  'avg_hours' => 3.5, 'color' => 'bg-emerald-100 text-emerald-600'],
            ['name' => 'Tanvir Hasan',  'avatar' => 'TH', 'resolved' => 25, 'within_sla' => 20, 'breached' => 5,  'avg_hours' => 5.6, 'color' => 'bg-amber-100 text-amber-600'],
            ['name' => 'Sadia Parvin',  'avatar' => 'SP', 'resolved' => 18, 'within_sla' => 17, 'breached' => 1,  'avg_hours' => 2.9, 'color' => 'bg-rose-100 text-rose-600'],
        ];

        $recentBreaches = [
            ['id' => 'T-1042', 'merchant' => 'StyleNest BD',   'category' => 'Order Issue',    'opened_at' => '10 Apr 09:14', 'resolved_at' => '10 Apr 18:42', 'target_hours' => 6, 'actual_hours' => 9.5, 'agent' => 'Tanvir Hasan'],
            ['id' => 'T-1038', 'merchant' => 'TechZone Shop',  'category' => 'Returns',        'opened_at' => '09 Apr 14:00', 'resolved_at' => '10 Apr 08:20', 'target_hours' => 8, 'actual_hours' => 18.3,'agent' => 'Sabbir Ahmed'],
            ['id' => 'T-1031', 'merchant' => 'GreenLeaf Store','category' => 'Merchant Issue', 'opened_at' => '08 Apr 11:30', 'resolved_at' => '09 Apr 10:45', 'target_hours' => 8, 'actual_hours' => 23.3,'agent' => 'Tanvir Hasan'],
            ['id' => 'T-1027', 'merchant' => 'BDFashion Hub',  'category' => 'Returns',        'opened_at' => '07 Apr 16:20', 'resolved_at' => '09 Apr 09:10', 'target_hours' => 8, 'actual_hours' => 40.8,'agent' => 'Roni Islam'],
        ];
    @endphp

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        @foreach ($stats as $s)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] px-5 py-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-2xl font-bold
                            {{ $s['color'] === 'blue'    ? 'text-blue-600 dark:text-blue-400'       : '' }}
                            {{ $s['color'] === 'emerald' ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                            {{ $s['color'] === 'red'     ? 'text-red-600 dark:text-red-400'         : '' }}
                            {{ $s['color'] === 'amber'   ? 'text-amber-600 dark:text-amber-400'     : '' }}">
                            {{ $s['value'] }}
                        </p>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-0.5">{{ $s['label'] }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $s['sub'] }}</p>
                    </div>
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium rounded-full px-2 py-0.5 mt-1
                        {{ $s['up'] ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            @if ($s['up'])
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3"/>
                            @endif
                        </svg>
                        {{ $s['trend'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- SLA by Category --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">SLA Performance by Category</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/60">
                @foreach ($categoryStats as $cat)
                    @php
                        $rate = $cat['total'] > 0 ? round(($cat['within_sla'] / $cat['total']) * 100) : 0;
                        $over = $cat['avg_hours'] > $cat['target_hours'];
                    @endphp
                    <div class="px-5 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $cat['category'] }}</span>
                                @if ($over)
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-500/30">Over Target</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                <span>Avg {{ $cat['avg_hours'] }}h / Target {{ $cat['target_hours'] }}h</span>
                                <span class="font-semibold {{ $rate >= 90 ? 'text-emerald-600 dark:text-emerald-400' : ($rate >= 75 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">{{ $rate }}%</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                <div class="h-full rounded-full transition-all
                                    {{ $rate >= 90 ? 'bg-emerald-400' : ($rate >= 75 ? 'bg-amber-400' : 'bg-red-400') }}"
                                    style="width: {{ $rate }}%">
                                </div>
                            </div>
                            <span class="text-xs text-red-500 w-16 text-right">{{ $cat['breached'] }} breach{{ $cat['breached'] !== 1 ? 'es' : '' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Agent SLA performance --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Agent SLA Performance</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/60">
                @foreach ($agentStats as $agent)
                    @php
                        $rate = $agent['resolved'] > 0 ? round(($agent['within_sla'] / $agent['resolved']) * 100) : 0;
                    @endphp
                    <div class="px-5 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $agent['color'] }}">{{ $agent['avatar'] }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $agent['name'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Avg {{ $agent['avg_hours'] }}h</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                    <div class="h-full rounded-full {{ $rate >= 90 ? 'bg-emerald-400' : ($rate >= 75 ? 'bg-amber-400' : 'bg-red-400') }}"
                                        style="width: {{ $rate }}%"></div>
                                </div>
                                <span class="text-xs tabular-nums text-gray-500 dark:text-gray-400 w-8 text-right">{{ $rate }}%</span>
                                @if ($agent['breached'] > 0)
                                    <span class="text-xs text-red-500">{{ $agent['breached'] }}✗</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Breaches --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent SLA Breaches</h3>
            <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-full px-2.5 py-0.5">{{ count($recentBreaches) }} this week</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">Ticket</th>
                        <th class="px-5 py-3 text-left">Merchant</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-center">Target</th>
                        <th class="px-5 py-3 text-center">Actual</th>
                        <th class="px-5 py-3 text-center">Overrun</th>
                        <th class="px-5 py-3 text-left">Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @foreach ($recentBreaches as $b)
                        @php $overrun = round($b['actual_hours'] - $b['target_hours'], 1); @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-4">
                                <span class="text-xs font-mono font-medium text-brand-600 dark:text-brand-400">{{ $b['id'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $b['merchant'] }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">{{ $b['category'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ $b['target_hours'] }}h</td>
                            <td class="px-5 py-4 text-center text-sm font-medium text-red-600 dark:text-red-400">{{ $b['actual_hours'] }}h</td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 rounded-full px-2 py-0.5">+{{ $overrun }}h</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $b['agent'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
