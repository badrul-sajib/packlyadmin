@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Merchant Issues" />

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Issues</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">324</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending</p>
            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">28</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Investigating</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">12</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Resolved</p>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">284</p>
        </div>
    </div>

    {{-- Issues --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        {{-- Tabs --}}
        <div class="flex items-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">All</button>
            <button @click="activeTab = 'pending'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'pending' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Pending</button>
            <button @click="activeTab = 'investigating'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'investigating' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Investigating</button>
            <button @click="activeTab = 'resolved'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'resolved' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Resolved</button>
        </div>

        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="relative w-40" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>All Types</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-48 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Types</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Payout Issue</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Product Issue</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Order Dispute</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Account Issue</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Policy Violation</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by merchant or issue" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <x-common.date-range-picker id="merchantIssueDateRange" />
        </div>

        {{-- Issues List --}}
        <div class="px-5 sm:px-6 space-y-3">
            @php
                $issues = [
                    ['id' => 'ISS-4021', 'merchant' => 'Home Shop BD.com', 'shop_id' => 7580, 'phone' => '01910903717', 'type' => 'Payout Issue', 'subject' => 'Payout amount mismatch for last 3 orders. Received ৳2,450 instead of ৳2,890. Commission deducted incorrectly.', 'status' => 'Pending', 'priority' => 'High', 'date' => '11/04/2026 03:15 PM', 'messages' => 0],
                    ['id' => 'ISS-4020', 'merchant' => 'LUXURY VIP', 'shop_id' => 7455, 'phone' => '01342584477', 'type' => 'Product Issue', 'subject' => 'Multiple products rejected without clear reason. Need explanation for SKU 6GIA0XDZ and UMV3C1MC.', 'status' => 'Investigating', 'priority' => 'Medium', 'date' => '11/04/2026 11:40 AM', 'messages' => 3],
                    ['id' => 'ISS-4019', 'merchant' => 'WKL Marts', 'shop_id' => 7320, 'phone' => '01781951811', 'type' => 'Order Dispute', 'subject' => 'Customer claiming product not received but delivery confirmed. Order INV8FCDBA4.', 'status' => 'Investigating', 'priority' => 'High', 'date' => '10/04/2026 06:30 PM', 'messages' => 5],
                    ['id' => 'ISS-4018', 'merchant' => 'CarbonX Shop', 'shop_id' => 7210, 'phone' => '01775006663', 'type' => 'Account Issue', 'subject' => 'Unable to update bank account details. Getting error when trying to change beneficiary information.', 'status' => 'Pending', 'priority' => 'Low', 'date' => '10/04/2026 02:20 PM', 'messages' => 1],
                    ['id' => 'ISS-4017', 'merchant' => 'Mira gallery', 'shop_id' => 7150, 'phone' => '01748832370', 'type' => 'Payout Issue', 'subject' => 'Payout delayed for more than 10 days. Request ID PKLY-50499210 still showing as Pending.', 'status' => 'Resolved', 'priority' => 'High', 'date' => '09/04/2026 04:00 PM', 'messages' => 4],
                    ['id' => 'ISS-4016', 'merchant' => 'Defense Academy', 'shop_id' => 7088, 'phone' => '07165024098', 'type' => 'Policy Violation', 'subject' => 'Warning received for duplicate product listings. Need clarification on listing policy.', 'status' => 'Resolved', 'priority' => 'Medium', 'date' => '09/04/2026 10:45 AM', 'messages' => 6],
                    ['id' => 'ISS-4015', 'merchant' => 'LUXURY VIP', 'shop_id' => 7455, 'phone' => '01342584477', 'type' => 'Order Dispute', 'subject' => 'Customer returned fake product instead of original. Refund should not be processed for order INVC3A2IED.', 'status' => 'Pending', 'priority' => 'High', 'date' => '08/04/2026 05:10 PM', 'messages' => 2],
                ];
            @endphp

            @foreach ($issues as $issue)
                <div class="rounded-xl border {{ $issue['priority'] === 'High' ? 'border-red-200 dark:border-red-500/20' : 'border-gray-200 dark:border-gray-700' }} p-5 hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                    <div class="flex items-start gap-4">
                        {{-- Priority indicator --}}
                        <div class="shrink-0 mt-1">
                            @if($issue['priority'] === 'High')
                                <div class="w-2 h-2 rounded-full bg-red-500 ring-4 ring-red-100 dark:ring-red-500/20"></div>
                            @elseif($issue['priority'] === 'Medium')
                                <div class="w-2 h-2 rounded-full bg-yellow-500 ring-4 ring-yellow-100 dark:ring-yellow-500/20"></div>
                            @else
                                <div class="w-2 h-2 rounded-full bg-gray-400 ring-4 ring-gray-100 dark:ring-gray-500/20"></div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-mono text-gray-400 dark:text-gray-500">{{ $issue['id'] }}</span>
                                        @php
                                            $typeColors = [
                                                'Payout Issue' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'Product Issue' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                                'Order Dispute' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                                'Account Issue' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                'Policy Violation' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                            ];
                                            $stColors = [
                                                'Pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30',
                                                'Investigating' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30',
                                                'Resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30',
                                            ];
                                            $stDots = ['Pending' => 'bg-yellow-500', 'Investigating' => 'bg-blue-500', 'Resolved' => 'bg-emerald-500'];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $typeColors[$issue['type']] ?? '' }}">{{ $issue['type'] }}</span>
                                        <span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[10px] font-medium {{ $stColors[$issue['status']] ?? '' }}">
                                            <span class="w-1 h-1 rounded-full {{ $stDots[$issue['status']] ?? '' }}"></span>
                                            {{ $issue['status'] }}
                                        </span>
                                    </div>
                                </div>
                                @php $dp = explode(' ', $issue['date'], 2); @endphp
                                <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0 whitespace-nowrap">{{ $dp[0] }} <span class="text-gray-300 dark:text-gray-600">{{ $dp[1] }}</span></span>
                            </div>

                            {{-- Merchant --}}
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($issue['merchant'], 0, 2)) }}</span>
                                </div>
                                <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">{{ $issue['merchant'] }}</a>
                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500">#{{ $issue['shop_id'] }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $issue['phone'] }}</span>
                            </div>

                            {{-- Subject --}}
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $issue['subject'] }}</p>

                            {{-- Footer --}}
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                                        {{ $issue['messages'] }} {{ $issue['messages'] === 1 ? 'message' : 'messages' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-xs {{ $issue['priority'] === 'High' ? 'text-red-500' : ($issue['priority'] === 'Medium' ? 'text-yellow-500' : 'text-gray-400') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/></svg>
                                        {{ $issue['priority'] }}
                                    </span>
                                </div>
                                <a href="#" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:text-brand-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 mt-4 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">7</span> of <span class="font-medium text-gray-700 dark:text-gray-300">324</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">47</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
