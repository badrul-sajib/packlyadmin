@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Product Questions" />

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Questions</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">1,245</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Answered</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">980</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Unanswered</p>
                    <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">265</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-yellow-50 dark:bg-yellow-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Hidden</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">18</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Questions --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        {{-- Tabs + Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">All</button>
                <button @click="activeTab = 'unanswered'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'unanswered' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Unanswered</button>
                <button @click="activeTab = 'answered'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'answered' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Answered</button>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by product or question" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
        </div>

        {{-- Questions List --}}
        <div class="px-5 sm:px-6 space-y-4">
            @php
                $questions = [
                    ['customer' => 'Rahim Uddin', 'phone' => '01712345678', 'product' => 'Wireless Bluetooth Earbuds TWS Pro Max', 'sku' => 'BT9X2KLP', 'merchant' => 'Home Shop BD.com', 'question' => 'Is this earbuds compatible with iPhone 15? And does it support ANC (Active Noise Cancellation)?', 'answer' => 'Yes, it is fully compatible with iPhone 15 and all iOS/Android devices. However, it does not support ANC. It has passive noise isolation.', 'answered_by' => 'Merchant', 'date' => '11/04/2026 03:20 PM', 'status' => 'Answered'],
                    ['customer' => 'Fatema Akter', 'phone' => '01945678901', 'product' => 'Portable USB-C Fast Charging Power Bank 20000mAh', 'sku' => 'PB20KUSB', 'merchant' => 'WKL Marts', 'question' => 'How many times can this power bank fully charge a Samsung Galaxy S24? What is the actual capacity?', 'answer' => null, 'answered_by' => null, 'date' => '11/04/2026 01:45 PM', 'status' => 'Unanswered'],
                    ['customer' => 'Tanvir Ahmed', 'phone' => '01788990011', 'product' => 'Mechanical Gaming Keyboard RGB Backlit 104 Keys', 'sku' => 'KB104RGB', 'merchant' => 'LUXURY VIP', 'question' => 'What type of switches does this keyboard use? Is it Cherry MX or Outemu? And can I change keycaps?', 'answer' => 'This keyboard uses Outemu Blue switches. Yes, the keycaps are standard size and can be replaced with any MX-compatible keycaps.', 'answered_by' => 'Admin', 'date' => '10/04/2026 06:10 PM', 'status' => 'Answered'],
                    ['customer' => 'Sumaiya Rahman', 'phone' => '01633445566', 'product' => 'Smart Watch Fitness Tracker with Heart Rate Monitor', 'sku' => 'SW4FTRHR', 'merchant' => 'CarbonX Shop', 'question' => 'Does this smartwatch support Bangla language? Can I receive WhatsApp notifications?', 'answer' => null, 'answered_by' => null, 'date' => '10/04/2026 02:30 PM', 'status' => 'Unanswered'],
                    ['customer' => 'Kamal Hossen', 'phone' => '01511111222', 'product' => 'Premium Leather Wallet for Men with RFID Blocking', 'sku' => 'LW7HQDMZ', 'merchant' => 'LUXURY VIP', 'question' => 'Is this genuine leather or PU leather? How many card slots does it have?', 'answer' => 'This is genuine cowhide leather with RFID blocking technology. It has 8 card slots, 2 bill compartments, and 1 coin pocket with zipper.', 'answered_by' => 'Merchant', 'date' => '09/04/2026 11:00 AM', 'status' => 'Answered'],
                    ['customer' => 'Nasir Hossain', 'phone' => '01867543210', 'product' => 'Stainless Steel Water Bottle Vacuum Insulated 750ml', 'sku' => 'WB750SSV', 'merchant' => 'WKL Marts', 'question' => 'How long does this bottle keep water hot? Is it safe for kids?', 'answer' => null, 'answered_by' => null, 'date' => '09/04/2026 09:15 AM', 'status' => 'Unanswered'],
                ];
            @endphp

            @foreach ($questions as $q)
                <div x-data="{ showReply: false, reply: '' }" class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {{-- Question --}}
                    <div class="p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 mb-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $q['customer'] }}</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $q['phone'] }}</span>
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium
                                            {{ $q['status'] === 'Answered' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' }}">
                                            <span class="w-1 h-1 rounded-full {{ $q['status'] === 'Answered' ? 'bg-emerald-500' : 'bg-yellow-500' }}"></span>
                                            {{ $q['status'] }}
                                        </span>
                                    </div>
                                    @php $dp = explode(' ', $q['date'], 2); @endphp
                                    <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0 whitespace-nowrap">{{ $dp[0] }} <span class="text-gray-300 dark:text-gray-600">{{ $dp[1] }}</span></span>
                                </div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Product:</span>
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $q['product'] }}</span>
                                    <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500">({{ $q['sku'] }})</span>
                                    <span class="text-xs text-gray-300 dark:text-gray-600">|</span>
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400">{{ $q['merchant'] }}</span>
                                </div>
                                <p class="text-sm text-gray-800 dark:text-white/90 leading-relaxed">{{ $q['question'] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Answer --}}
                    @if($q['answer'])
                        <div class="px-5 pb-4">
                            <div class="ml-12 rounded-lg bg-emerald-50/50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/10 p-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Answered by {{ $q['answered_by'] }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $q['answer'] }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="px-5 pb-4">
                        <div class="ml-12 flex items-center gap-2">
                            @if(!$q['answer'])
                                <button @click="showReply = !showReply" type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:text-brand-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                                    Reply
                                </button>
                            @else
                                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200 transition-colors dark:bg-gray-800 dark:text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                    Edit Answer
                                </button>
                            @endif
                            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors dark:bg-red-500/10 dark:text-red-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                Hide
                            </button>
                            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors dark:bg-red-500/10 dark:text-red-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                Delete
                            </button>
                        </div>
                    </div>

                    {{-- Reply Form --}}
                    <div x-show="showReply" x-collapse class="px-5 pb-4">
                        <div class="ml-12">
                            <textarea x-model="reply" rows="3" placeholder="Write your answer..." class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 resize-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder-gray-500"></textarea>
                            <div class="flex items-center justify-end gap-2 mt-2">
                                <button @click="showReply = false; reply = ''" type="button" class="px-4 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                                <button @click="showReply = false" type="button" class="px-4 py-1.5 text-xs font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">Submit Answer</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 mt-4 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">6</span> of <span class="font-medium text-gray-700 dark:text-gray-300">1,245</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">208</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
