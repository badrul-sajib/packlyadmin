@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Merchant Update Requests" />

    @include('components.common.reject-reason-modal')

    @php
        $requests = [
            ['id' => 1, 'merchant' => 'StyleNest BD',   'field' => 'Shop Name',        'old' => 'StyleNest',          'new' => 'StyleNest BD',             'requested_at' => '12 Apr 2026', 'status' => 'Pending'],
            ['id' => 2, 'merchant' => 'TechZone Shop',  'field' => 'Shop Description',  'old' => 'Gadgets & more.',    'new' => 'Premium electronics & gadgets at best prices.', 'requested_at' => '11 Apr 2026', 'status' => 'Pending'],
            ['id' => 3, 'merchant' => 'FreshMart',      'field' => 'Shop Name',        'old' => 'Fresh Mart',          'new' => 'FreshMart',                'requested_at' => '10 Apr 2026', 'status' => 'Approved'],
            ['id' => 4, 'merchant' => 'GreenLeaf Store','field' => 'Shop Type',        'old' => 'Physical',            'new' => 'Digital',                  'requested_at' => '09 Apr 2026', 'status' => 'Pending'],
            ['id' => 5, 'merchant' => 'BDFashion Hub',  'field' => 'Shop Description',  'old' => 'Clothing store.',    'new' => 'Latest fashion trends for men, women & kids.', 'requested_at' => '08 Apr 2026', 'status' => 'Rejected'],
            ['id' => 6, 'merchant' => 'Electro Point',  'field' => 'Shop Name',        'old' => 'ElectroPoint',        'new' => 'Electro Point',            'requested_at' => '07 Apr 2026', 'status' => 'Approved'],
        ];
    @endphp

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        @php
            $pending  = collect($requests)->where('status', 'Pending')->count();
            $approved = collect($requests)->where('status', 'Approved')->count();
            $rejected = collect($requests)->where('status', 'Rejected')->count();
        @endphp
        <div class="rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-500/30 dark:bg-amber-500/10 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><p class="text-xl font-bold text-amber-700 dark:text-amber-400">{{ $pending }}</p><p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">Pending Review</p></div>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-500/30 dark:bg-emerald-500/10 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><p class="text-xl font-bold text-emerald-700 dark:text-emerald-400">{{ $approved }}</p><p class="text-xs text-emerald-600 dark:text-emerald-500 mt-0.5">Approved</p></div>
        </div>
        <div class="rounded-2xl border border-red-200 bg-red-50 dark:border-red-500/30 dark:bg-red-500/10 px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div><p class="text-xl font-bold text-red-700 dark:text-red-400">{{ $rejected }}</p><p class="text-xs text-red-600 dark:text-red-500 mt-0.5">Rejected</p></div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]" x-data="{ filter: 'Pending' }">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 p-1">
                @foreach (['All', 'Pending', 'Approved', 'Rejected'] as $f)
                    <button type="button"
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors"
                        :class="filter === '{{ $f }}' ? 'bg-brand-500 text-white' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/[0.07]'"
                        @click="filter = '{{ $f }}'">{{ $f }}</button>
                @endforeach
            </div>
            <div class="relative flex-1 min-w-[180px] max-w-xs ml-auto">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search merchant…" class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">Merchant</th>
                        <th class="px-5 py-3 text-left">Field</th>
                        <th class="px-5 py-3 text-left">Old Value</th>
                        <th class="px-5 py-3 text-left">New Value</th>
                        <th class="px-5 py-3 text-left">Requested</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @foreach ($requests as $req)
                        <tr x-show="filter === 'All' || filter === '{{ $req['status'] }}'"
                            class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-4">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $req['merchant'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">{{ $req['field'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-gray-500 dark:text-gray-400 line-through">{{ $req['old'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $req['new'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $req['requested_at'] }}</td>
                            <td class="px-5 py-4 text-center">
                                @if ($req['status'] === 'Pending')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                    </span>
                                @elseif ($req['status'] === 'Approved')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($req['status'] === 'Pending')
                                        <button type="button" title="Approve"
                                            class="inline-flex items-center gap-1 h-8 px-3 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-colors text-xs font-medium dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                        <button type="button" title="Reject"
                                            @click="$dispatch('open-reject-modal', { label: '{{ $req['field'] }} Change' })"
                                            class="inline-flex items-center gap-1 h-8 px-3 rounded-lg bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition-colors text-xs font-medium dark:bg-red-500/10 dark:text-red-400">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-600">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
