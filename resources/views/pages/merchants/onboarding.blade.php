@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Merchant Onboarding" />

    @php
        $stages = [
            'Lead'                => ['color' => 'gray',    'icon' => 'lead'],
            'Details Submitted'   => ['color' => 'blue',    'icon' => 'details'],
            'Communication'       => ['color' => 'purple',  'icon' => 'comm'],
            'Active'              => ['color' => 'emerald', 'icon' => 'active'],
            'Terms Signed'        => ['color' => 'brand',   'icon' => 'terms'],
        ];

        $merchants = [
            ['name' => 'Arafat Trading',  'phone' => '+880 1711-001122', 'kam' => 'Sabbir',  'date' => '01 Apr 2026', 'stage' => 'Lead'],
            ['name' => 'Nova Electronics','phone' => '+880 1812-223344', 'kam' => 'Roni',    'date' => '28 Mar 2026', 'stage' => 'Lead'],
            ['name' => 'Dhaka Fabrics',   'phone' => '+880 1913-334455', 'kam' => 'Sabbir',  'date' => '25 Mar 2026', 'stage' => 'Details Submitted'],
            ['name' => 'Quick Delivery',  'phone' => '+880 1614-445566', 'kam' => 'Mim',     'date' => '20 Mar 2026', 'stage' => 'Details Submitted'],
            ['name' => 'SmartHome BD',    'phone' => '+880 1515-556677', 'kam' => 'Roni',    'date' => '18 Mar 2026', 'stage' => 'Communication'],
            ['name' => 'Organic Bazaar',  'phone' => '+880 1716-667788', 'kam' => 'Mim',     'date' => '15 Mar 2026', 'stage' => 'Active'],
            ['name' => 'FashionHouse',    'phone' => '+880 1817-778899', 'kam' => 'Sabbir',  'date' => '10 Mar 2026', 'stage' => 'Active'],
            ['name' => 'ProTech Store',   'phone' => '+880 1918-889900', 'kam' => 'Roni',    'date' => '05 Mar 2026', 'stage' => 'Terms Signed'],
            ['name' => 'SkyMart',         'phone' => '+880 1619-990011', 'kam' => 'Mim',     'date' => '01 Mar 2026', 'stage' => 'Terms Signed'],
        ];

        $stageOrder = array_keys($stages);
        $colorMap = [
            'gray'    => ['bg' => 'bg-gray-100 dark:bg-gray-800',      'text' => 'text-gray-600 dark:text-gray-400',     'border' => 'border-gray-200 dark:border-gray-700',    'badge' => 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'],
            'blue'    => ['bg' => 'bg-blue-50 dark:bg-blue-500/10',    'text' => 'text-blue-600 dark:text-blue-400',      'border' => 'border-blue-200 dark:border-blue-500/30', 'badge' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30'],
            'purple'  => ['bg' => 'bg-purple-50 dark:bg-purple-500/10','text' => 'text-purple-600 dark:text-purple-400',  'border' => 'border-purple-200 dark:border-purple-500/30','badge' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/30'],
            'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/10','text' => 'text-emerald-600 dark:text-emerald-400','border' => 'border-emerald-200 dark:border-emerald-500/30','badge' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30'],
            'brand'   => ['bg' => 'bg-brand-50 dark:bg-brand-500/10',  'text' => 'text-brand-600 dark:text-brand-400',    'border' => 'border-brand-200 dark:border-brand-500/30','badge' => 'bg-brand-50 text-brand-600 border-brand-200 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/30'],
        ];
    @endphp

    {{-- Stage summary bar --}}
    <div class="grid grid-cols-5 gap-3 mb-5">
        @foreach ($stages as $stageName => $stageInfo)
            @php
                $count = collect($merchants)->where('stage', $stageName)->count();
                $c = $colorMap[$stageInfo['color']];
            @endphp
            <div class="rounded-2xl border {{ $c['border'] }} {{ $c['bg'] }} px-4 py-3 text-center">
                <p class="text-2xl font-bold {{ $c['text'] }}">{{ $count }}</p>
                <p class="text-xs {{ $c['text'] }} mt-0.5 leading-tight">{{ $stageName }}</p>
            </div>
        @endforeach
    </div>

    {{-- Kanban Board --}}
    <div class="flex gap-4 overflow-x-auto pb-4">
        @foreach ($stages as $stageName => $stageInfo)
            @php
                $cards = collect($merchants)->where('stage', $stageName);
                $c = $colorMap[$stageInfo['color']];
            @endphp
            <div class="flex-shrink-0 w-64">
                {{-- Column header --}}
                <div class="flex items-center justify-between mb-3 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full
                            {{ $stageInfo['color'] === 'gray'    ? 'bg-gray-400'    : '' }}
                            {{ $stageInfo['color'] === 'blue'    ? 'bg-blue-500'    : '' }}
                            {{ $stageInfo['color'] === 'purple'  ? 'bg-purple-500'  : '' }}
                            {{ $stageInfo['color'] === 'emerald' ? 'bg-emerald-500' : '' }}
                            {{ $stageInfo['color'] === 'brand'   ? 'bg-brand-500'   : '' }}">
                        </span>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $stageName }}</span>
                    </div>
                    <span class="text-xs font-medium rounded-full px-2 py-0.5 {{ $c['badge'] }} border">{{ $cards->count() }}</span>
                </div>

                {{-- Cards --}}
                <div class="space-y-3">
                    @foreach ($cards as $m)
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90 leading-tight">{{ $m['name'] }}</p>
                                @if ($stageName !== 'Terms Signed')
                                    <button type="button" title="Move to next stage"
                                        class="flex-shrink-0 w-6 h-6 rounded-md {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center hover:opacity-80 transition-opacity">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                @else
                                    <span class="flex-shrink-0 w-6 h-6 rounded-md bg-brand-100 dark:bg-brand-500/20 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                                    <span>{{ $m['phone'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    <span>KAM: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $m['kam'] }}</span></span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/></svg>
                                    <span>{{ $m['date'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($cards->isEmpty())
                        <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-6 text-center">
                            <p class="text-xs text-gray-400 dark:text-gray-600">No merchants in this stage</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection
