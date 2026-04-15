@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="All Customers" />

    @php
        $customers = [
            ['id' => 1001, 'name' => 'Rafiq Hossain',   'phone' => '+880 1711-234567', 'email' => 'rafiq@example.com',   'orders' => 24, 'spent' => '12,450', 'joined' => '12 Jan 2025', 'status' => 'Active',  'avatar' => 'RH'],
            ['id' => 1002, 'name' => 'Nasrin Akter',    'phone' => '+880 1812-345678', 'email' => 'nasrin@example.com',  'orders' => 11, 'spent' => '5,870',  'joined' => '03 Feb 2025', 'status' => 'Active',  'avatar' => 'NA'],
            ['id' => 1003, 'name' => 'Karim Uddin',     'phone' => '+880 1913-456789', 'email' => 'karim@example.com',   'orders' => 7,  'spent' => '3,210',  'joined' => '20 Feb 2025', 'status' => 'Active',  'avatar' => 'KU'],
            ['id' => 1004, 'name' => 'Sultana Begum',   'phone' => '+880 1614-567890', 'email' => 'sultana@example.com', 'orders' => 0,  'spent' => '0',      'joined' => '01 Mar 2025', 'status' => 'Blocked', 'avatar' => 'SB'],
            ['id' => 1005, 'name' => 'Jahangir Alam',   'phone' => '+880 1515-678901', 'email' => 'jahangir@example.com','orders' => 38, 'spent' => '21,900', 'joined' => '15 Mar 2025', 'status' => 'Active',  'avatar' => 'JA'],
            ['id' => 1006, 'name' => 'Fahmida Islam',   'phone' => '+880 1716-789012', 'email' => 'fahmida@example.com', 'orders' => 5,  'spent' => '2,340',  'joined' => '22 Mar 2025', 'status' => 'Active',  'avatar' => 'FI'],
            ['id' => 1007, 'name' => 'Monir Chowdhury', 'phone' => '+880 1817-890123', 'email' => 'monir@example.com',   'orders' => 0,  'spent' => '0',      'joined' => '05 Apr 2025', 'status' => 'Blocked', 'avatar' => 'MC'],
            ['id' => 1008, 'name' => 'Tania Rahman',    'phone' => '+880 1918-901234', 'email' => 'tania@example.com',   'orders' => 17, 'spent' => '8,650',  'joined' => '10 Apr 2025', 'status' => 'Active',  'avatar' => 'TR'],
            ['id' => 1009, 'name' => 'Sumon Das',       'phone' => '+880 1619-012345', 'email' => 'sumon@example.com',   'orders' => 3,  'spent' => '1,120',  'joined' => '18 Apr 2025', 'status' => 'Active',  'avatar' => 'SD'],
            ['id' => 1010, 'name' => 'Rima Khatun',     'phone' => '+880 1520-123456', 'email' => 'rima@example.com',    'orders' => 9,  'spent' => '4,380',  'joined' => '25 Apr 2025', 'status' => 'Active',  'avatar' => 'RK'],
        ];

        $avatarColors = ['bg-blue-100 text-blue-600','bg-emerald-100 text-emerald-600','bg-purple-100 text-purple-600','bg-amber-100 text-amber-600','bg-rose-100 text-rose-600'];
    @endphp

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        @php
            $stats = [
                ['label' => 'Total Customers', 'value' => '4,832', 'icon' => 'users',    'color' => 'blue'],
                ['label' => 'Active Today',    'value' => '318',   'icon' => 'active',   'color' => 'emerald'],
                ['label' => 'New This Month',  'value' => '241',   'icon' => 'new',      'color' => 'purple'],
                ['label' => 'Blocked',         'value' => '29',    'icon' => 'blocked',  'color' => 'red'],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] px-5 py-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $stat['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-500/10'    : '' }}
                    {{ $stat['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-500/10' : '' }}
                    {{ $stat['color'] === 'purple'  ? 'bg-purple-50 dark:bg-purple-500/10'  : '' }}
                    {{ $stat['color'] === 'red'     ? 'bg-red-50 dark:bg-red-500/10'     : '' }}">
                    @if ($stat['icon'] === 'users')
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    @elseif ($stat['icon'] === 'active')
                        <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif ($stat['icon'] === 'new')
                        <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    @endif
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-800 dark:text-white/90">{{ $stat['value'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">

            {{-- Status filter --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button"
                    class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>All Status</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute left-0 z-50 mt-1 w-36 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Status</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Active</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Blocked</button></li>
                    </ul>
                </div>
            </div>

            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px] max-w-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by name, phone or email…"
                    class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>

            <a href="/customers/blocked"
                class="ml-auto inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-600 hover:bg-red-100 transition-colors dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Blocked (29)
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left w-8"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-brand-500 dark:border-gray-600"></th>
                        <th class="px-5 py-3 text-left">Customer</th>
                        <th class="px-5 py-3 text-left">Contact</th>
                        <th class="px-5 py-3 text-center">Orders</th>
                        <th class="px-5 py-3 text-right">Total Spent</th>
                        <th class="px-5 py-3 text-left">Joined</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @foreach ($customers as $i => $c)
                        @php $color = $avatarColors[$i % count($avatarColors)]; @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-3.5"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-brand-500 dark:border-gray-600"></td>
                            <td class="px-5 py-3.5">
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
                            <td class="px-5 py-3.5">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $c['phone'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $c['email'] }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $c['orders'] }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">৳{{ $c['spent'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $c['joined'] }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if ($c['status'] === 'Active')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Blocked
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="/customers/{{ $c['id'] }}" title="View"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-gray-800 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>
                                    @if ($c['status'] === 'Active')
                                        <button type="button" title="Block"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-gray-800 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </button>
                                    @else
                                        <button type="button" title="Unblock"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-emerald-500 hover:text-white transition-colors dark:bg-gray-800 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to
                <span class="font-medium text-gray-700 dark:text-gray-300">10</span> of
                <span class="font-medium text-gray-700 dark:text-gray-300">4,832</span> customers
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">484</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
