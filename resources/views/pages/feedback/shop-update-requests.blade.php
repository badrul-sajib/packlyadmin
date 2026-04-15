@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Shop Update Requests" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        {{-- Tabs --}}
        <div class="flex items-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">All <span class="ml-1 text-xs opacity-75">124</span></button>
            <button @click="activeTab = 'pending'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'pending' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Pending <span class="ml-1 text-xs opacity-75">18</span></button>
            <button @click="activeTab = 'approved'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'approved' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Approved <span class="ml-1 text-xs opacity-75">96</span></button>
            <button @click="activeTab = 'rejected'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'rejected' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Rejected <span class="ml-1 text-xs opacity-75">10</span></button>
        </div>

        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="relative w-48" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>Select merchant...</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-56 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <div class="p-2">
                        <input type="text" placeholder="Search merchant..." class="w-full rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                    </div>
                    <ul class="py-1 text-sm max-h-48 overflow-y-auto">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Merchants</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Home Shop BD.com</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">LUXURY VIP</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">WKL Marts</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by shop name or ID" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        {{-- Requests List --}}
        <div class="px-5 sm:px-6 space-y-4">
            @php
                $requests = [
                    ['shop' => 'Home Shop BD.com', 'shop_id' => 7580, 'owner' => 'Rakib Hasan', 'phone' => '01910903717', 'changes' => [['field' => 'Shop Name', 'old' => 'Home Shop BD', 'new' => 'Home Shop BD.com'], ['field' => 'Logo', 'old' => 'Previous logo', 'new' => 'New logo uploaded'], ['field' => 'Address', 'old' => 'Mirpur 10, Dhaka', 'new' => 'Mirpur 10, Block C, Dhaka 1216']], 'status' => 'Pending', 'date' => '11/04/2026 03:20 PM'],
                    ['shop' => 'LUXURY VIP', 'shop_id' => 7455, 'owner' => 'Kamrul Islam', 'phone' => '01342584477', 'changes' => [['field' => 'Phone', 'old' => '01342584477', 'new' => '01342584499'], ['field' => 'Banner', 'old' => 'Previous banner', 'new' => 'New banner uploaded']], 'status' => 'Pending', 'date' => '11/04/2026 10:45 AM'],
                    ['shop' => 'WKL Marts', 'shop_id' => 7320, 'owner' => 'Wahid Khan', 'phone' => '01781951811', 'changes' => [['field' => 'Shop Description', 'old' => 'Electronics store in Dhaka', 'new' => 'Your one-stop electronics & gadgets store in Dhaka with genuine products']], 'status' => 'Approved', 'date' => '10/04/2026 04:30 PM'],
                    ['shop' => 'CarbonX Shop', 'shop_id' => 7210, 'owner' => 'Shahriar Rahman', 'phone' => '01775006663', 'changes' => [['field' => 'Shop Name', 'old' => 'CarbonX', 'new' => 'CarbonX Shop'], ['field' => 'Email', 'old' => 'carbon@gmail.com', 'new' => 'info@carbonxshop.com'], ['field' => 'Logo', 'old' => 'Old logo', 'new' => 'Redesigned logo'], ['field' => 'Address', 'old' => 'Uttara, Dhaka', 'new' => 'Uttara Sector 7, Dhaka 1230']], 'status' => 'Approved', 'date' => '10/04/2026 11:00 AM'],
                    ['shop' => 'Mira gallery', 'shop_id' => 7150, 'owner' => 'MD Rakibul', 'phone' => '01748832370', 'changes' => [['field' => 'Shop Name', 'old' => 'Mira gallery', 'new' => 'Mira Gallery Official']], 'status' => 'Rejected', 'date' => '09/04/2026 02:15 PM'],
                    ['shop' => 'Defense Academy', 'shop_id' => 7088, 'owner' => 'Nusrat Jahan', 'phone' => '07165024098', 'changes' => [['field' => 'Phone', 'old' => '07165024098', 'new' => '01765024098'], ['field' => 'Banner', 'old' => 'Current banner', 'new' => 'Updated promotional banner']], 'status' => 'Pending', 'date' => '09/04/2026 09:30 AM'],
                    ['shop' => 'Express Gadgets', 'shop_id' => 7593, 'owner' => 'Ziaul Hoque', 'phone' => '01605949962', 'changes' => [['field' => 'Address', 'old' => 'Dhanmondi, Dhaka', 'new' => 'Dhanmondi 27, Road 4, Dhaka 1205']], 'status' => 'Approved', 'date' => '08/04/2026 03:45 PM'],
                ];
            @endphp

            @foreach ($requests as $req)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden" x-data="{ expanded: false }">
                    {{-- Header --}}
                    <div class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors" @click="expanded = !expanded">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center shrink-0">
                            <span class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($req['shop'], 0, 2)) }}</span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $req['shop'] }}</span>
                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500">#{{ $req['shop_id'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $req['owner'] }}</span>
                                <span class="text-xs text-gray-300 dark:text-gray-600">|</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $req['phone'] }}</span>
                            </div>
                        </div>

                        {{-- Changed fields count --}}
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 shrink-0">
                            {{ count($req['changes']) }} {{ count($req['changes']) === 1 ? 'change' : 'changes' }}
                        </span>

                        {{-- Status --}}
                        @php
                            $stStyles = [
                                'Pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30',
                                'Approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30',
                                'Rejected' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30',
                            ];
                            $stDots = ['Pending' => 'bg-yellow-500', 'Approved' => 'bg-emerald-500', 'Rejected' => 'bg-red-500'];
                        @endphp
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $stStyles[$req['status']] }} shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full {{ $stDots[$req['status']] }}"></span>
                            {{ $req['status'] }}
                        </span>

                        {{-- Date --}}
                        @php $dp = explode(' ', $req['date'], 2); @endphp
                        <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0 whitespace-nowrap">{{ $dp[0] }}</span>

                        {{-- Expand --}}
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    {{-- Changes Detail --}}
                    <div x-show="expanded" x-collapse>
                        <div class="px-5 pb-5 border-t border-gray-100 dark:border-gray-800 pt-4">
                            {{-- Changes Table --}}
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-4">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-white/[0.02] text-xs">
                                            <th class="px-4 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 w-[20%]">Field</th>
                                            <th class="px-4 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 w-[35%]">Previous Value</th>
                                            <th class="px-4 py-2.5 text-center font-medium text-gray-500 dark:text-gray-400 w-[5%]"></th>
                                            <th class="px-4 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 w-[35%]">New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        @foreach ($req['changes'] as $change)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $change['field'] }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="text-sm text-red-500 dark:text-red-400 line-through">{{ $change['old'] }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <svg class="w-4 h-4 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ $change['new'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Actions --}}
                            @if($req['status'] === 'Pending')
                                <div class="flex items-center justify-end gap-2" x-data="{ showReject: false, feedback: '' }">
                                    <div x-show="!showReject" class="flex items-center gap-2">
                                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-medium text-white hover:bg-emerald-600 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            Approve All
                                        </button>
                                        <button @click="showReject = true" type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors dark:bg-red-500/10 dark:text-red-400">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </div>
                                    <div x-show="showReject" class="w-full max-w-md">
                                        <textarea x-model="feedback" rows="2" placeholder="Enter rejection reason..." class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 resize-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder-gray-500 mb-2"></textarea>
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="showReject = false; feedback = ''" type="button" class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                                            <button @click="showReject = false" type="button" class="px-4 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">Submit Rejection</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 mt-4 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">7</span> of <span class="font-medium text-gray-700 dark:text-gray-300">124</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">18</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
