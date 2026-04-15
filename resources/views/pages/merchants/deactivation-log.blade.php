@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Deactivation Log" />

    @php
        $logs = [
            ['id' => 1, 'merchant' => 'SpamStore BD',    'reason' => 'Multiple spam orders and fraudulent payment activity detected over 3 weeks.', 'deactivated_by' => 'Admin Karim',  'deactivated_at' => '10 Apr 2026', 'reactivated_at' => null,          'status' => 'Deactivated'],
            ['id' => 2, 'merchant' => 'FakeMart',        'reason' => 'Selling counterfeit products reported by customers. Verified by operations team.', 'deactivated_by' => 'Admin Sabbir', 'deactivated_at' => '05 Apr 2026', 'reactivated_at' => null,          'status' => 'Deactivated'],
            ['id' => 3, 'merchant' => 'QuickDrop BD',    'reason' => 'Failed to comply with KYC document submission after 3 reminders.',              'deactivated_by' => 'Admin Roni',   'deactivated_at' => '01 Apr 2026', 'reactivated_at' => '08 Apr 2026', 'status' => 'Reactivated'],
            ['id' => 4, 'merchant' => 'OldShop Express', 'reason' => 'Inactive for over 90 days with no orders or updates.',                          'deactivated_by' => 'Admin Mim',    'deactivated_at' => '20 Mar 2026', 'reactivated_at' => null,          'status' => 'Deactivated'],
            ['id' => 5, 'merchant' => 'DuplicateStore',  'reason' => 'Duplicate account — same owner as StyleNest BD. Merged and deactivated.',       'deactivated_by' => 'Admin Karim',  'deactivated_at' => '15 Mar 2026', 'reactivated_at' => null,          'status' => 'Deactivated'],
            ['id' => 6, 'merchant' => 'TempTrader',      'reason' => 'Seasonal account closed by merchant request.',                                  'deactivated_by' => 'Admin Sabbir', 'deactivated_at' => '01 Mar 2026', 'reactivated_at' => '25 Mar 2026', 'status' => 'Reactivated'],
        ];
    @endphp

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]" x-data="{ expanded: null }">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                Records are permanent — no auto-purge policy applied.
            </div>
            <div class="relative flex-1 min-w-[180px] max-w-xs ml-auto">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search merchant or admin…" class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left w-8"></th>
                        <th class="px-5 py-3 text-left">Merchant</th>
                        <th class="px-5 py-3 text-left">Deactivated By</th>
                        <th class="px-5 py-3 text-left">Deactivated On</th>
                        <th class="px-5 py-3 text-left">Reactivated On</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr class="border-b border-gray-50 dark:border-gray-800/60 hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors cursor-pointer"
                            @click="expanded = expanded === {{ $log['id'] }} ? null : {{ $log['id'] }}">
                            <td class="px-5 py-4">
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                    :class="expanded === {{ $log['id'] }} ? 'rotate-90' : ''"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $log['merchant'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $log['deactivated_by'] }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $log['deactivated_at'] }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $log['reactivated_at'] ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($log['status'] === 'Deactivated')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Deactivated
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Reactivated
                                    </span>
                                @endif
                            </td>
                        </tr>
                        {{-- Expandable note row --}}
                        <tr x-show="expanded === {{ $log['id'] }}" style="display:none;">
                            <td colspan="6" class="px-5 pb-4">
                                <div class="ml-4 rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Private Deactivation Note</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log['reason'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ count($logs) }} records — click a row to view the private deactivation note</p>
        </div>
    </div>
@endsection
