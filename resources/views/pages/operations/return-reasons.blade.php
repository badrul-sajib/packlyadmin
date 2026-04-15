@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Return Reasons" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="relative w-40" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>All Status</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Status</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Active</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Inactive</button></li>
                    </ul>
                </div>
            </div>
            <div class="relative w-40" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>All Types</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Types</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Return</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Refund</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Exchange</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Reason
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[800px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[5%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[30%]">Reason</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Type</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%] whitespace-nowrap">Requires Image</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%] whitespace-nowrap">Used Count</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[13%] whitespace-nowrap">Created Date</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $reasons = [
                                    ['reason' => 'Product is damaged or broken', 'type' => 'Return', 'requires_image' => true, 'used' => 245, 'status' => 'Active', 'created_at' => '15/01/2025 10:00 AM'],
                                    ['reason' => 'Wrong product delivered', 'type' => 'Return', 'requires_image' => true, 'used' => 189, 'status' => 'Active', 'created_at' => '15/01/2025 10:05 AM'],
                                    ['reason' => 'Product does not match description', 'type' => 'Return', 'requires_image' => true, 'used' => 156, 'status' => 'Active', 'created_at' => '15/01/2025 10:10 AM'],
                                    ['reason' => 'Size or color is different', 'type' => 'Exchange', 'requires_image' => false, 'used' => 320, 'status' => 'Active', 'created_at' => '16/01/2025 09:00 AM'],
                                    ['reason' => 'Changed my mind', 'type' => 'Refund', 'requires_image' => false, 'used' => 410, 'status' => 'Active', 'created_at' => '16/01/2025 09:05 AM'],
                                    ['reason' => 'Found a better price elsewhere', 'type' => 'Refund', 'requires_image' => false, 'used' => 98, 'status' => 'Active', 'created_at' => '16/01/2025 09:10 AM'],
                                    ['reason' => 'Product quality is poor', 'type' => 'Return', 'requires_image' => true, 'used' => 134, 'status' => 'Active', 'created_at' => '17/01/2025 11:00 AM'],
                                    ['reason' => 'Missing accessories or parts', 'type' => 'Return', 'requires_image' => true, 'used' => 78, 'status' => 'Active', 'created_at' => '17/01/2025 11:05 AM'],
                                    ['reason' => 'Late delivery', 'type' => 'Refund', 'requires_image' => false, 'used' => 56, 'status' => 'Active', 'created_at' => '18/01/2025 02:00 PM'],
                                    ['reason' => 'Duplicate order placed by mistake', 'type' => 'Refund', 'requires_image' => false, 'used' => 23, 'status' => 'Inactive', 'created_at' => '20/01/2025 10:30 AM'],
                                ];
                            @endphp

                            @foreach ($reasons as $index => $reason)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[5%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4 w-[30%]"><span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $reason['reason'] }}</span></td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        @php
                                            $typeColors = [
                                                'Return' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                                'Refund' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                'Exchange' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$reason['type']] ?? '' }}">{{ $reason['type'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        @if($reason['requires_image'])
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-50 dark:bg-emerald-500/10">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        <span class="text-sm font-medium {{ $reason['used'] === 0 ? 'text-gray-400' : 'text-gray-800 dark:text-white/90' }}">{{ number_format($reason['used']) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]" x-data="{ open: false, status: '{{ $reason['status'] }}' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button"
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium cursor-pointer transition-colors"
                                                :class="status === 'Active'
                                                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30'
                                                    : 'bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30'">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="status === 'Active' ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                                                <span x-text="status"></span>
                                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-1 w-36 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                <ul class="py-1 text-sm">
                                                    <li><button @click="status = 'Active'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active</button></li>
                                                    <li><button @click="status = 'Inactive'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-gray-400"></span> Inactive</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[13%]">
                                        @php $dp = explode(' ', $reason['created_at'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg></button>
                                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">10</span> of <span class="font-medium text-gray-700 dark:text-gray-300">10</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
