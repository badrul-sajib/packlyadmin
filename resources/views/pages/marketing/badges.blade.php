@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Badges" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'product' }">
        {{-- Tabs --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="activeTab = 'product'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'product' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Product Badges</button>
                <button @click="activeTab = 'merchant'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'merchant' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Merchant Badges</button>
                <button @click="activeTab = 'customer'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'customer' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Customer Badges</button>
            </div>
            <div class="flex-1"></div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Badge
            </button>
        </div>

        {{-- Product Badges --}}
        <div x-show="activeTab === 'product'" class="px-5 sm:px-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @php
                $productBadges = [
                    ['name' => 'Best Seller', 'icon' => 'fire', 'color' => 'red', 'bg' => 'bg-red-500', 'light' => 'bg-red-50 dark:bg-red-500/10', 'text' => 'text-red-600 dark:text-red-400', 'products' => 245, 'rule' => '50+ orders in last 30 days', 'status' => 'Active'],
                    ['name' => 'New Arrival', 'icon' => 'sparkles', 'color' => 'blue', 'bg' => 'bg-blue-500', 'light' => 'bg-blue-50 dark:bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400', 'products' => 1820, 'rule' => 'Added within last 7 days', 'status' => 'Active'],
                    ['name' => 'Top Rated', 'icon' => 'star', 'color' => 'yellow', 'bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50 dark:bg-yellow-500/10', 'text' => 'text-yellow-600 dark:text-yellow-400', 'products' => 380, 'rule' => '4.5+ rating with 20+ reviews', 'status' => 'Active'],
                    ['name' => 'Flash Deal', 'icon' => 'bolt', 'color' => 'orange', 'bg' => 'bg-orange-500', 'light' => 'bg-orange-50 dark:bg-orange-500/10', 'text' => 'text-orange-600 dark:text-orange-400', 'products' => 56, 'rule' => 'Manually assigned by admin', 'status' => 'Active'],
                    ['name' => 'Limited Stock', 'icon' => 'alert', 'color' => 'rose', 'bg' => 'bg-rose-500', 'light' => 'bg-rose-50 dark:bg-rose-500/10', 'text' => 'text-rose-600 dark:text-rose-400', 'products' => 342, 'rule' => 'Stock below 5 units', 'status' => 'Active'],
                    ['name' => 'Eco Friendly', 'icon' => 'leaf', 'color' => 'emerald', 'bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400', 'products' => 0, 'rule' => 'Manually assigned by admin', 'status' => 'Inactive'],
                ];
            @endphp

            @foreach ($productBadges as $badge)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-5 {{ $badge['status'] === 'Inactive' ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl {{ $badge['bg'] }} flex items-center justify-center">
                                @if($badge['icon'] === 'fire')
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177A7.547 7.547 0 016.648 6.61a.75.75 0 00-1.152-.082A9 9 0 1015.68 4.534a7.46 7.46 0 01-2.717-2.248zM15.75 14.25a3.75 3.75 0 11-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 011.925-3.545 3.75 3.75 0 013.255 3.717z" clip-rule="evenodd"/></svg>
                                @elseif($badge['icon'] === 'sparkles')
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5z" clip-rule="evenodd"/></svg>
                                @elseif($badge['icon'] === 'star')
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"/></svg>
                                @elseif($badge['icon'] === 'bolt')
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z" clip-rule="evenodd"/></svg>
                                @elseif($badge['icon'] === 'alert')
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $badge['name'] }}</h4>
                                <span class="text-xs {{ $badge['text'] }}">{{ number_format($badge['products']) }} products</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium {{ $badge['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                            <span class="w-1 h-1 rounded-full {{ $badge['status'] === 'Active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ $badge['status'] }}
                        </span>
                    </div>

                    <div class="rounded-lg {{ $badge['light'] }} px-3 py-2 mb-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400"><span class="font-medium text-gray-700 dark:text-gray-300">Rule:</span> {{ $badge['rule'] }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg></button>
                        <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Merchant Badges --}}
        <div x-show="activeTab === 'merchant'" style="display:none;" class="px-5 sm:px-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @php
                $merchantBadges = [
                    ['name' => 'Verified Seller', 'bg' => 'bg-blue-500', 'light' => 'bg-blue-50 dark:bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400', 'count' => 1250, 'rule' => 'Verified identity & documents', 'status' => 'Active'],
                    ['name' => 'Top Merchant', 'bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50 dark:bg-yellow-500/10', 'text' => 'text-yellow-600 dark:text-yellow-400', 'count' => 120, 'rule' => '500+ delivered orders & 4.5+ rating', 'status' => 'Active'],
                    ['name' => 'Trusted Shop', 'bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400', 'count' => 340, 'rule' => '100+ orders with <2% return rate', 'status' => 'Active'],
                    ['name' => 'New Merchant', 'bg' => 'bg-violet-500', 'light' => 'bg-violet-50 dark:bg-violet-500/10', 'text' => 'text-violet-600 dark:text-violet-400', 'count' => 890, 'rule' => 'Joined within last 30 days', 'status' => 'Active'],
                ];
            @endphp
            @foreach ($merchantBadges as $badge)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl {{ $badge['bg'] }} flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $badge['name'] }}</h4>
                                <span class="text-xs {{ $badge['text'] }}">{{ number_format($badge['count']) }} merchants</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="w-1 h-1 rounded-full bg-emerald-500"></span> Active</span>
                    </div>
                    <div class="rounded-lg {{ $badge['light'] }} px-3 py-2 mb-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400"><span class="font-medium text-gray-700 dark:text-gray-300">Rule:</span> {{ $badge['rule'] }}</p>
                    </div>
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg></button>
                        <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Customer Badges --}}
        <div x-show="activeTab === 'customer'" style="display:none;" class="px-5 sm:px-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @php
                $customerBadges = [
                    ['name' => 'VIP Customer', 'bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50 dark:bg-yellow-500/10', 'text' => 'text-yellow-600 dark:text-yellow-400', 'count' => 520, 'rule' => '20+ orders & ৳50,000+ total spend', 'status' => 'Active'],
                    ['name' => 'Loyal Buyer', 'bg' => 'bg-brand-500', 'light' => 'bg-brand-50 dark:bg-brand-500/10', 'text' => 'text-brand-600 dark:text-brand-400', 'count' => 2340, 'rule' => '10+ orders in last 6 months', 'status' => 'Active'],
                    ['name' => 'New Member', 'bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400', 'count' => 8900, 'rule' => 'Registered within last 30 days', 'status' => 'Active'],
                ];
            @endphp
            @foreach ($customerBadges as $badge)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl {{ $badge['bg'] }} flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $badge['name'] }}</h4>
                                <span class="text-xs {{ $badge['text'] }}">{{ number_format($badge['count']) }} customers</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="w-1 h-1 rounded-full bg-emerald-500"></span> Active</span>
                    </div>
                    <div class="rounded-lg {{ $badge['light'] }} px-3 py-2 mb-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400"><span class="font-medium text-gray-700 dark:text-gray-300">Rule:</span> {{ $badge['rule'] }}</p>
                    </div>
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg></button>
                        <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
