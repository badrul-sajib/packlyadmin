@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Help Requests" />

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Tickets</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">486</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Open</p>
            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">42</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">In Progress</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">18</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Resolved</p>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">412</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Closed</p>
            <p class="text-xl font-bold text-gray-500 dark:text-gray-400 mt-1">14</p>
        </div>
    </div>

    {{-- Tickets Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        {{-- Tabs --}}
        <div class="flex items-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">All</button>
            <button @click="activeTab = 'open'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'open' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Open</button>
            <button @click="activeTab = 'progress'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'progress' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">In Progress</button>
            <button @click="activeTab = 'resolved'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'resolved' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Resolved</button>
            <button @click="activeTab = 'closed'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'closed' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Closed</button>
        </div>

        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="relative w-40" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>All Priority</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Priority</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">High</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Medium</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Low</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by ticket ID or subject" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <x-common.date-range-picker id="helpDateRange" />
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%]">Ticket ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[22%]">Subject</th>
                            <th class="px-4 py-3 text-left font-medium w-[14%]">Submitted By</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Category</th>
                            <th class="px-4 py-3 text-center font-medium w-[7%]">Priority</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[11%] whitespace-nowrap">Created At</th>
                            <th class="px-4 py-3 text-left font-medium w-[11%] whitespace-nowrap">Last Reply</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[5%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $tickets = [
                                    ['id' => 'TKT-20461', 'subject' => 'Order not delivered after 7 days', 'customer' => 'Rahim Uddin', 'phone' => '01712345678', 'type' => 'Customer', 'category' => 'Delivery', 'priority' => 'High', 'status' => 'Open', 'created_at' => '11/04/2026 02:30 PM', 'last_reply' => null],
                                    ['id' => 'TKT-20460', 'subject' => 'Payout not received for last week', 'customer' => 'Home Shop BD.com', 'phone' => '01910903717', 'type' => 'Merchant', 'category' => 'Payment', 'priority' => 'High', 'status' => 'In Progress', 'created_at' => '11/04/2026 11:15 AM', 'last_reply' => '11/04/2026 01:40 PM'],
                                    ['id' => 'TKT-20459', 'subject' => 'Cannot update product price from dashboard', 'customer' => 'LUXURY VIP', 'phone' => '01342584477', 'type' => 'Merchant', 'category' => 'Technical', 'priority' => 'Medium', 'status' => 'Open', 'created_at' => '11/04/2026 09:45 AM', 'last_reply' => null],
                                    ['id' => 'TKT-20458', 'subject' => 'Received damaged product, need replacement', 'customer' => 'Fatema Akter', 'phone' => '01945678901', 'type' => 'Customer', 'category' => 'Return', 'priority' => 'High', 'status' => 'In Progress', 'created_at' => '10/04/2026 06:20 PM', 'last_reply' => '11/04/2026 10:00 AM'],
                                    ['id' => 'TKT-20457', 'subject' => 'How to add bank account for payout?', 'customer' => 'CarbonX Shop', 'phone' => '01775006663', 'type' => 'Merchant', 'category' => 'Account', 'priority' => 'Low', 'status' => 'Resolved', 'created_at' => '10/04/2026 03:10 PM', 'last_reply' => '10/04/2026 05:30 PM'],
                                    ['id' => 'TKT-20456', 'subject' => 'Wrong item in my order, need refund', 'customer' => 'Tanvir Ahmed', 'phone' => '01788990011', 'type' => 'Customer', 'category' => 'Refund', 'priority' => 'Medium', 'status' => 'Resolved', 'created_at' => '10/04/2026 01:00 PM', 'last_reply' => '10/04/2026 04:45 PM'],
                                    ['id' => 'TKT-20455', 'subject' => 'App crashes when uploading product images', 'customer' => 'WKL Marts', 'phone' => '01781951811', 'type' => 'Merchant', 'category' => 'Technical', 'priority' => 'Medium', 'status' => 'Open', 'created_at' => '10/04/2026 10:30 AM', 'last_reply' => null],
                                    ['id' => 'TKT-20454', 'subject' => 'Request to change shop name', 'customer' => 'Mira gallery', 'phone' => '01748832370', 'type' => 'Merchant', 'category' => 'Account', 'priority' => 'Low', 'status' => 'Closed', 'created_at' => '09/04/2026 04:00 PM', 'last_reply' => '10/04/2026 09:00 AM'],
                                ];
                            @endphp

                            @foreach ($tickets as $index => $ticket)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4 w-[10%]">
                                        <span class="text-sm font-medium font-mono text-gray-800 dark:text-white/90">{{ $ticket['id'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[22%]">
                                        <a href="#" class="text-sm font-medium text-gray-800 hover:text-brand-500 dark:text-white/90 dark:hover:text-brand-400 line-clamp-2">{{ $ticket['subject'] }}</a>
                                    </td>
                                    <td class="px-4 py-4 w-[14%]">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $ticket['customer'] }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium {{ $ticket['type'] === 'Merchant' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400' }}">{{ $ticket['type'] }}</span>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $ticket['phone'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        @php
                                            $catColors = [
                                                'Delivery' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                                'Payment' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'Technical' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                'Return' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                                'Account' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                                'Refund' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $catColors[$ticket['category']] ?? '' }}">{{ $ticket['category'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[7%]">
                                        @php
                                            $priColors = [
                                                'High' => 'text-red-600 dark:text-red-400',
                                                'Medium' => 'text-yellow-600 dark:text-yellow-400',
                                                'Low' => 'text-gray-500 dark:text-gray-400',
                                            ];
                                            $priDots = ['High' => 'bg-red-500', 'Medium' => 'bg-yellow-500', 'Low' => 'bg-gray-400'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ $priColors[$ticket['priority']] ?? '' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $priDots[$ticket['priority']] ?? '' }}"></span>
                                            {{ $ticket['priority'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]" x-data="{ open: false, status: '{{ $ticket['status'] }}' }">
                                        @php
                                            $stStyles = [
                                                'Open' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30',
                                                'In Progress' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30',
                                                'Resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30',
                                                'Closed' => 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30',
                                            ];
                                            $stDots = ['Open' => 'bg-yellow-500', 'In Progress' => 'bg-blue-500', 'Resolved' => 'bg-emerald-500', 'Closed' => 'bg-gray-400'];
                                        @endphp
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button"
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium border cursor-pointer transition-colors"
                                                :class="{
                                                    '{{ $stStyles['Open'] }}': status === 'Open',
                                                    '{{ $stStyles['In Progress'] }}': status === 'In Progress',
                                                    '{{ $stStyles['Resolved'] }}': status === 'Resolved',
                                                    '{{ $stStyles['Closed'] }}': status === 'Closed',
                                                }">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="{ '{{ $stDots['Open'] }}': status === 'Open', '{{ $stDots['In Progress'] }}': status === 'In Progress', '{{ $stDots['Resolved'] }}': status === 'Resolved', '{{ $stDots['Closed'] }}': status === 'Closed' }"></span>
                                                <span x-text="status"></span>
                                                <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-1 w-36 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                <ul class="py-1 text-sm">
                                                    <li><button @click="status = 'Open'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Open</button></li>
                                                    <li><button @click="status = 'In Progress'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-blue-500"></span> In Progress</button></li>
                                                    <li><button @click="status = 'Resolved'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Resolved</button></li>
                                                    <li><button @click="status = 'Closed'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-gray-400"></span> Closed</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[11%]">
                                        @php $cp = explode(' ', $ticket['created_at'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $cp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $cp[1] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[11%]">
                                        @if($ticket['last_reply'])
                                            @php $lp = explode(' ', $ticket['last_reply'], 2); @endphp
                                            <div>
                                                <p class="text-sm text-gray-800 dark:text-white/90">{{ $lp[0] }}</p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $lp[1] }}</p>
                                            </div>
                                        @else
                                            <span class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Awaiting reply</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center w-[5%]">
                                        <a href="#" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="View">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </a>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">8</span> of <span class="font-medium text-gray-700 dark:text-gray-300">486</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">49</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
