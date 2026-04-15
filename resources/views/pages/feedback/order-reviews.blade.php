@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Order Reviews" />

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Reviews</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">2,847</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                </div>
            </div>
        </div>
        @php
            $stars = [
                ['label' => '5 Star', 'count' => 1420, 'color' => 'text-emerald-600 dark:text-emerald-400', 'bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'icon' => 'text-emerald-500'],
                ['label' => '4 Star', 'count' => 680, 'color' => 'text-blue-600 dark:text-blue-400', 'bg' => 'bg-blue-50 dark:bg-blue-500/10', 'icon' => 'text-blue-500'],
                ['label' => '3 Star', 'count' => 390, 'color' => 'text-yellow-600 dark:text-yellow-400', 'bg' => 'bg-yellow-50 dark:bg-yellow-500/10', 'icon' => 'text-yellow-500'],
                ['label' => '1-2 Star', 'count' => 357, 'color' => 'text-red-600 dark:text-red-400', 'bg' => 'bg-red-50 dark:bg-red-500/10', 'icon' => 'text-red-500'],
            ];
        @endphp
        @foreach ($stars as $star)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $star['label'] }}</p>
                        <p class="text-xl font-bold {{ $star['color'] }} mt-1">{{ number_format($star['count']) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg {{ $star['bg'] }} flex items-center justify-center">
                        <svg class="w-4 h-4 {{ $star['icon'] }}" fill="currentColor" viewBox="0 0 24 24"><path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"/></svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Reviews Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="relative w-36" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>All Ratings</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Ratings</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">5 Star</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">4 Star</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">3 Star</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">2 Star</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">1 Star</button></li>
                    </ul>
                </div>
            </div>
            <div class="relative w-40" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-500">
                    <span>All Status</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All Status</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Published</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Hidden</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by order ID or customer" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <x-common.date-range-picker id="reviewDateRange" />
        </div>

        {{-- Reviews List --}}
        <div class="px-5 sm:px-6 space-y-4">
            @php
                $reviews = [
                    ['order_id' => 'INV8FCDBA4', 'customer' => 'Rahim Uddin', 'phone' => '01712345678', 'product' => 'Wireless Bluetooth Earbuds TWS Pro Max', 'merchant' => 'Home Shop BD.com', 'rating' => 5, 'comment' => 'Excellent product! Sound quality is amazing and battery life is great. Delivered on time. Highly recommended for everyone.', 'date' => '11/04/2026 03:45 PM', 'status' => 'Published', 'has_image' => true],
                    ['order_id' => 'INV563B0A9', 'customer' => 'Nasir Hossain', 'phone' => '01867543210', 'product' => 'Premium Leather Wallet for Men with RFID Blocking', 'merchant' => 'LUXURY VIP', 'rating' => 4, 'comment' => 'Good quality wallet, stitching is nice. Color is slightly different from the picture but overall satisfied with the purchase.', 'date' => '10/04/2026 11:20 AM', 'status' => 'Published', 'has_image' => false],
                    ['order_id' => 'INVC3A2IED', 'customer' => 'Fatema Akter', 'phone' => '01945678901', 'product' => 'Portable USB-C Fast Charging Power Bank 20000mAh', 'merchant' => 'WKL Marts', 'rating' => 5, 'comment' => 'Best power bank I have ever used. Charges very fast and the build quality is solid. Worth every taka!', 'date' => '10/04/2026 09:15 AM', 'status' => 'Published', 'has_image' => true],
                    ['order_id' => 'INV7D4E291', 'customer' => 'Kamal Hossen', 'phone' => '01511111222', 'product' => 'Smart Watch Fitness Tracker with Heart Rate Monitor', 'merchant' => 'CarbonX Shop', 'rating' => 2, 'comment' => 'Watch stopped working after 3 days. Screen is unresponsive. Very disappointed with the quality.', 'date' => '09/04/2026 06:30 PM', 'status' => 'Published', 'has_image' => true],
                    ['order_id' => 'INVB5C0A83', 'customer' => 'Sumaiya Rahman', 'phone' => '01633445566', 'product' => 'Organic Green Tea Premium Collection Gift Box', 'merchant' => 'Home Shop BD.com', 'rating' => 3, 'comment' => 'Tea taste is okay but packaging was slightly damaged during delivery. Expected better packaging.', 'date' => '09/04/2026 02:10 PM', 'status' => 'Hidden', 'has_image' => false],
                    ['order_id' => 'INV92E1D47', 'customer' => 'Tanvir Ahmed', 'phone' => '01788990011', 'product' => 'Mechanical Gaming Keyboard RGB Backlit 104 Keys', 'merchant' => 'LUXURY VIP', 'rating' => 5, 'comment' => 'Amazing keyboard! RGB lighting is beautiful, keys are clicky and responsive. Great for gaming and typing.', 'date' => '08/04/2026 04:50 PM', 'status' => 'Published', 'has_image' => false],
                    ['order_id' => 'INV6FA3B82', 'customer' => 'Mitu Begum', 'phone' => '01922334455', 'product' => 'Stainless Steel Water Bottle Vacuum Insulated 750ml', 'merchant' => 'WKL Marts', 'rating' => 1, 'comment' => 'Bottle leaks from the cap. Completely useless. Want a refund immediately.', 'date' => '08/04/2026 10:00 AM', 'status' => 'Published', 'has_image' => true],
                ];
            @endphp

            @foreach ($reviews as $review)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                    <div class="flex items-start gap-4">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center shrink-0">
                            <span class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($review['customer'], 0, 2)) }}</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $review['customer'] }}</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $review['phone'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 mt-0.5">
                                        <span class="text-xs text-gray-400 dark:text-gray-500">Order: <a href="#" class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $review['order_id'] }}</a></span>
                                        <span class="text-xs text-gray-300 dark:text-gray-600">|</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $review['merchant'] }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @php $dp = explode(' ', $review['date'], 2); @endphp
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $dp[0] }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Product --}}
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <span class="text-gray-400 dark:text-gray-500">Product:</span> {{ $review['product'] }}
                            </p>

                            {{-- Stars --}}
                            <div class="flex items-center gap-1 mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-200 dark:text-gray-700' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"/></svg>
                                @endfor
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 ml-1">{{ $review['rating'] }}.0</span>
                            </div>

                            {{-- Comment --}}
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $review['comment'] }}</p>

                            {{-- Image indicator --}}
                            @if($review['has_image'])
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                        <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                    </div>
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                        <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                    </div>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-2" x-data="{ open: false, status: '{{ $review['status'] }}' }">
                                    <div class="relative">
                                        <button @click="open = !open" type="button"
                                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium cursor-pointer transition-colors"
                                            :class="status === 'Published'
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30'
                                                : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30'">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="status === 'Published' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                            <span x-text="status"></span>
                                            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 bottom-full mb-1 z-50 w-36 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                            <ul class="py-1 text-sm">
                                                <li><button @click="status = 'Published'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Published</button></li>
                                                <li><button @click="status = 'Hidden'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-red-500"></span> Hidden</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors dark:bg-red-500/10 dark:text-red-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 mt-4 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">7</span> of <span class="font-medium text-gray-700 dark:text-gray-300">2,847</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">407</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
