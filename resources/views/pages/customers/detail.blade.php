@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Customer Details" />

    @php
        $customer = [
            'id'       => 1001,
            'name'     => 'Rafiq Hossain',
            'phone'    => '+880 1711-234567',
            'email'    => 'rafiq@example.com',
            'address'  => 'House 12, Road 4, Mirpur-10, Dhaka',
            'joined'   => '12 Jan 2025',
            'status'   => 'Active',
            'avatar'   => 'RH',
            'orders'   => 24,
            'spent'    => '12,450',
            'returns'  => 1,
            'devices'  => 2,
        ];

        $orders = [
            ['id' => 'ORD-48921', 'date' => '10 Apr 2026', 'items' => 3, 'total' => '2,340', 'status' => 'Delivered',  'status_color' => 'emerald'],
            ['id' => 'ORD-47103', 'date' => '28 Mar 2026', 'items' => 1, 'total' => '890',   'status' => 'Delivered',  'status_color' => 'emerald'],
            ['id' => 'ORD-45566', 'date' => '15 Mar 2026', 'items' => 5, 'total' => '4,120', 'status' => 'Returned',   'status_color' => 'red'],
            ['id' => 'ORD-43210', 'date' => '02 Mar 2026', 'items' => 2, 'total' => '1,680', 'status' => 'Delivered',  'status_color' => 'emerald'],
            ['id' => 'ORD-41087', 'date' => '20 Feb 2026', 'items' => 1, 'total' => '540',   'status' => 'Cancelled',  'status_color' => 'gray'],
        ];
    @endphp

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- ── Left: Profile Card ── --}}
        <div class="flex flex-col gap-5">

            {{-- Profile --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center text-2xl font-bold mb-4">
                        {{ $customer['avatar'] }}
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white/90">{{ $customer['name'] }}</h2>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">#{{ $customer['id'] }}</p>
                    <span class="mt-3 inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium
                        {{ $customer['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : 'bg-red-50 text-red-600 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $customer['status'] === 'Active' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        {{ $customer['status'] }}
                    </span>
                </div>

                <div class="mt-5 space-y-3 border-t border-gray-100 dark:border-gray-800 pt-5">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $customer['phone'] }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        <span class="text-sm text-gray-700 dark:text-gray-300 break-all">{{ $customer['email'] }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $customer['address'] }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Joined {{ $customer['joined'] }}</span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800 flex gap-2">
                    <button type="button"
                        class="flex-1 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium py-2 transition-colors">
                        Send Message
                    </button>
                    <button type="button"
                        class="flex-1 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 text-sm font-medium py-2 transition-colors dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                        Block
                    </button>
                </div>
            </div>

            {{-- Stats --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Account Summary</h3>
                <div class="grid grid-cols-2 gap-3">
                    @php
                        $summary = [
                            ['label' => 'Total Orders', 'value' => $customer['orders'],  'color' => 'brand'],
                            ['label' => 'Total Spent',  'value' => '৳'.$customer['spent'], 'color' => 'emerald'],
                            ['label' => 'Returns',      'value' => $customer['returns'], 'color' => 'red'],
                            ['label' => 'Devices',      'value' => $customer['devices'], 'color' => 'purple'],
                        ];
                    @endphp
                    @foreach ($summary as $s)
                        <div class="rounded-xl border border-gray-100 dark:border-gray-800 p-3 text-center">
                            <p class="text-lg font-bold
                                {{ $s['color'] === 'brand'   ? 'text-brand-500'   : '' }}
                                {{ $s['color'] === 'emerald' ? 'text-emerald-500' : '' }}
                                {{ $s['color'] === 'red'     ? 'text-red-500'     : '' }}
                                {{ $s['color'] === 'purple'  ? 'text-purple-500'  : '' }}">
                                {{ $s['value'] }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $s['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Right: Orders & Activity ── --}}
        <div class="xl:col-span-2 flex flex-col gap-5">

            {{-- Recent Orders --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Order History</h3>
                    <a href="/orders/all" class="text-xs text-brand-500 hover:underline">View all orders</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left">Order ID</th>
                                <th class="px-5 py-3 text-left">Date</th>
                                <th class="px-5 py-3 text-center">Items</th>
                                <th class="px-5 py-3 text-right">Total</th>
                                <th class="px-5 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a href="/orders/1" class="text-sm font-medium text-brand-500 hover:underline">{{ $order['id'] }}</a>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $order['date'] }}</td>
                                    <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400 text-center">{{ $order['items'] }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">৳{{ $order['total'] }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium border
                                            {{ $order['status_color'] === 'emerald' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : '' }}
                                            {{ $order['status_color'] === 'red'     ? 'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30'         : '' }}
                                            {{ $order['status_color'] === 'gray'    ? 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'         : '' }}">
                                            {{ $order['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Recent Activity</h3>
                <ol class="space-y-0">
                    @php
                        $activities = [
                            ['text' => 'Placed order #ORD-48921 (3 items)',      'time' => '10 Apr 2026, 3:45 PM', 'type' => 'order'],
                            ['text' => 'Updated delivery address',               'time' => '08 Apr 2026, 11:20 AM','type' => 'profile'],
                            ['text' => 'Left a review on Product #PRD-2211',     'time' => '02 Apr 2026, 6:10 PM', 'type' => 'review'],
                            ['text' => 'Placed order #ORD-47103 (1 item)',       'time' => '28 Mar 2026, 1:32 PM', 'type' => 'order'],
                            ['text' => 'Initiated return for order #ORD-45566',  'time' => '20 Mar 2026, 9:05 AM', 'type' => 'return'],
                        ];
                    @endphp
                    @foreach ($activities as $i => $act)
                        <li class="flex gap-4">
                            {{-- Timeline spine --}}
                            <div class="flex flex-col items-center">
                                <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full
                                    {{ $act['type'] === 'order'   ? 'bg-brand-50 ring-2 ring-brand-200 dark:bg-brand-500/10 dark:ring-brand-500/30'   : '' }}
                                    {{ $act['type'] === 'profile' ? 'bg-blue-50 ring-2 ring-blue-200 dark:bg-blue-500/10 dark:ring-blue-500/30'         : '' }}
                                    {{ $act['type'] === 'review'  ? 'bg-amber-50 ring-2 ring-amber-200 dark:bg-amber-500/10 dark:ring-amber-500/30'   : '' }}
                                    {{ $act['type'] === 'return'  ? 'bg-red-50 ring-2 ring-red-200 dark:bg-red-500/10 dark:ring-red-500/30'           : '' }}">
                                    @if ($act['type'] === 'order')
                                        <svg class="w-3.5 h-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    @elseif ($act['type'] === 'profile')
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    @elseif ($act['type'] === 'review')
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    @endif
                                </span>
                                @if (!$loop->last)
                                    <div class="w-px flex-1 bg-gray-100 dark:bg-gray-800 my-1"></div>
                                @endif
                            </div>
                            {{-- Content --}}
                            <div class="pb-5 pt-1 min-w-0">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-snug">{{ $act['text'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $act['time'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

        </div>
    </div>
@endsection
