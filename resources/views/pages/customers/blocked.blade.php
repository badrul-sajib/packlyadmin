@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Blocked Customers" />

    @php
        $customers = [
            ['id' => 1004, 'name' => 'Sultana Begum',   'phone' => '+880 1614-567890', 'email' => 'sultana@example.com',  'reason' => 'Repeated spam orders',       'blocked_at' => '01 Mar 2025', 'avatar' => 'SB'],
            ['id' => 1007, 'name' => 'Monir Chowdhury', 'phone' => '+880 1817-890123', 'email' => 'monir@example.com',    'reason' => 'Fraudulent payment activity', 'blocked_at' => '05 Apr 2025', 'avatar' => 'MC'],
            ['id' => 1019, 'name' => 'Hasan Ali',       'phone' => '+880 1720-234567', 'email' => 'hasan@example.com',    'reason' => 'Abuse of return policy',      'blocked_at' => '10 Apr 2025', 'avatar' => 'HA'],
            ['id' => 1023, 'name' => 'Roksana Parvin',  'phone' => '+880 1821-345678', 'email' => 'roksana@example.com',  'reason' => 'Multiple fake reviews',       'blocked_at' => '11 Apr 2025', 'avatar' => 'RP'],
        ];
        $avatarColors = ['bg-red-100 text-red-600','bg-orange-100 text-orange-600','bg-rose-100 text-rose-600','bg-pink-100 text-pink-600'];
    @endphp

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- Header --}}
        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 border border-red-200 px-3 py-1.5 text-sm font-medium text-red-600 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    {{ count($customers) }} Blocked Accounts
                </span>
            </div>
            <div class="relative flex-1 min-w-[180px] max-w-sm ml-auto">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search blocked customers…"
                    class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">Customer</th>
                        <th class="px-5 py-3 text-left">Contact</th>
                        <th class="px-5 py-3 text-left">Block Reason</th>
                        <th class="px-5 py-3 text-left">Blocked On</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @foreach ($customers as $i => $c)
                        @php $color = $avatarColors[$i % count($avatarColors)]; @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-4">
                                <a href="/customers/{{ $c['id'] }}" class="flex items-center gap-3 group">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $color }}">
                                        {{ $c['avatar'] }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white/90 group-hover:text-brand-500 transition-colors">{{ $c['name'] }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">#{{ $c['id'] }}</p>
                                    </div>
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $c['phone'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $c['email'] }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-red-600 dark:text-red-400">{{ $c['reason'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $c['blocked_at'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="/customers/{{ $c['id'] }}" title="View Profile"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-gray-800 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>
                                    <button type="button" title="Unblock Customer"
                                        class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-colors text-xs font-medium dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Unblock
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-400 dark:text-gray-500">Showing {{ count($customers) }} of 29 blocked customers</p>
        </div>
    </div>
@endsection
