@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Marketing Dashboard" />

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                    +4 this month
                </span>
            </div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">24</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Campaigns</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">42 total</span>
            </div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">18</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Active Prime Views</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                    +18.2%
                </span>
            </div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">1,24,580</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Visitors</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 dark:bg-yellow-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">12 active</span>
            </div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">48</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Coupons</p>
        </div>
    </div>

    {{-- Revenue + Campaign Performance --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Campaign Revenue</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">৳40,66,000</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">From 8 active campaigns</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Coupon Discounts Given</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">৳2,45,680</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">8,920 coupons redeemed</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Conversion Rate</p>
            <p class="text-2xl font-bold text-brand-600 dark:text-brand-400 mt-1">3.8%</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Visitor to order ratio</p>
        </div>
    </div>

    {{-- Active Campaigns + Traffic Sources --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        {{-- Active Campaigns --}}
        <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
            <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Active Campaigns</h3>
                <a href="{{ route('marketing.campaigns') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View All</a>
            </div>
            <div class="px-5 sm:px-6 space-y-3">
                @php
                    $activeCampaigns = [
                        ['name' => 'Baisakhi Mega Sale 2026', 'type' => 'Flash Sale', 'prime_views' => 4, 'products' => 696, 'revenue' => '৳4,56,000', 'progress' => 55, 'end' => '20/04/2026'],
                        ['name' => 'New User Welcome Offer', 'type' => 'Promotion', 'prime_views' => 1, 'products' => 0, 'revenue' => '৳2,45,000', 'progress' => 28, 'end' => '31/12/2026'],
                        ['name' => 'Friday Frenzy', 'type' => 'Weekly', 'prime_views' => 2, 'products' => 100, 'revenue' => '৳1,95,000', 'progress' => 80, 'end' => '11/04/2026'],
                    ];
                @endphp
                @foreach ($activeCampaigns as $campaign)
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $campaign['name'] }}</span>
                                @php
                                    $typeColors = ['Flash Sale' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400', 'Promotion' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400', 'Weekly' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'];
                                @endphp
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium {{ $typeColors[$campaign['type']] ?? '' }}">{{ $campaign['type'] }}</span>
                            </div>
                            <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $campaign['revenue'] }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-2">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $campaign['prime_views'] }} prime views
                            </span>
                            <span>{{ number_format($campaign['products']) }} products</span>
                            <span>Ends {{ $campaign['end'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-1.5 rounded-full bg-emerald-500" style="width: {{ $campaign['progress'] }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $campaign['progress'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Traffic Sources --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Traffic Sources</h3>
                <a href="{{ route('marketing.visitors') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Details</a>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $sources = [
                        ['name' => 'Direct', 'value' => '42,350', 'percent' => 34, 'color' => 'bg-brand-500'],
                        ['name' => 'Organic Search', 'value' => '31,200', 'percent' => 25, 'color' => 'bg-emerald-500'],
                        ['name' => 'Social Media', 'value' => '24,890', 'percent' => 20, 'color' => 'bg-blue-500'],
                        ['name' => 'Referral', 'value' => '15,640', 'percent' => 13, 'color' => 'bg-violet-500'],
                        ['name' => 'Paid Ads', 'value' => '10,500', 'percent' => 8, 'color' => 'bg-orange-500'],
                    ];
                @endphp
                @foreach ($sources as $source)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $source['color'] }}"></span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $source['name'] }}</span>
                            </div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $source['percent'] }}%</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-1.5 rounded-full {{ $source['color'] }}" style="width: {{ $source['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top Coupons + Badges --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Top Coupons --}}
        <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
            <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Top Coupons</h3>
                <a href="{{ route('marketing.coupons') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View All</a>
            </div>
            <div class="px-5 sm:px-6 space-y-3">
                @php
                    $topCoupons = [
                        ['code' => 'WELCOME100', 'type' => 'Fixed ৳100', 'used' => 3450, 'status' => 'Active'],
                        ['code' => 'BAISAKHI40', 'type' => '40% OFF', 'used' => 1240, 'status' => 'Active'],
                        ['code' => 'FREESHIPBD', 'type' => 'Free Shipping', 'used' => 890, 'status' => 'Active'],
                        ['code' => 'RAMADAN30', 'type' => '30% OFF', 'used' => 2100, 'status' => 'Expired'],
                    ];
                @endphp
                @foreach ($topCoupons as $coupon)
                    <div class="flex items-center justify-between p-3 rounded-xl {{ $coupon['status'] === 'Active' ? 'bg-gray-50 dark:bg-white/[0.02]' : 'bg-gray-50/50 dark:bg-white/[0.01] opacity-60' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-brand-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold font-mono text-gray-800 dark:text-white/90">{{ $coupon['code'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $coupon['type'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format($coupon['used']) }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">times used</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Badge Overview --}}
        <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
            <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Badge Overview</h3>
                <a href="{{ route('marketing.badges') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Manage</a>
            </div>
            <div class="px-5 sm:px-6 grid grid-cols-2 gap-3">
                @php
                    $badges = [
                        ['name' => 'Best Seller', 'count' => 245, 'bg' => 'bg-red-500', 'icon' => 'M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 6.75 6.75 0 009 4.5a.75.75 0 01.075-1.264 9.04 9.04 0 016.287 1.978z'],
                        ['name' => 'New Arrival', 'count' => 1820, 'bg' => 'bg-blue-500', 'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z'],
                        ['name' => 'Top Rated', 'count' => 380, 'bg' => 'bg-yellow-500', 'icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z'],
                        ['name' => 'Verified Seller', 'count' => 1250, 'bg' => 'bg-emerald-500', 'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
                        ['name' => 'Flash Deal', 'count' => 56, 'bg' => 'bg-orange-500', 'icon' => 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z'],
                        ['name' => 'VIP Customer', 'count' => 520, 'bg' => 'bg-violet-500', 'icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z'],
                    ];
                @endphp
                @foreach ($badges as $badge)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-white/[0.02]">
                        <div class="w-9 h-9 rounded-lg {{ $badge['bg'] }} flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $badge['icon'] }}"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $badge['name'] }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($badge['count']) }} assigned</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
