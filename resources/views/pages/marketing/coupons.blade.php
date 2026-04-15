@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Coupons" />

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Coupons</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">48</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active</p>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">12</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Used</p>
            <p class="text-xl font-bold text-brand-600 dark:text-brand-400 mt-1">8,920</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Discount Given</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">৳2,45,680</p>
        </div>
    </div>

    {{-- Coupons --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'all' }">
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="activeTab = 'all'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">All</button>
                <button @click="activeTab = 'active'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'active' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Active</button>
                <button @click="activeTab = 'expired'" type="button" class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors" :class="activeTab === 'expired' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">Expired</button>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-56">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by code" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Create Coupon
            </button>
        </div>

        {{-- Coupon Cards Grid --}}
        <div class="px-5 sm:px-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @php
                $coupons = [
                    ['code' => 'BAISAKHI40', 'type' => 'Percentage', 'value' => '40%', 'min_order' => '500', 'max_discount' => '200', 'used' => 1240, 'limit' => 2000, 'start' => '10/04/2026', 'end' => '20/04/2026', 'status' => 'Active'],
                    ['code' => 'WELCOME100', 'type' => 'Fixed', 'value' => '৳100', 'min_order' => '300', 'max_discount' => null, 'used' => 3450, 'limit' => null, 'start' => '01/01/2026', 'end' => '31/12/2026', 'status' => 'Active'],
                    ['code' => 'SUMMER25', 'type' => 'Percentage', 'value' => '25%', 'min_order' => '1000', 'max_discount' => '500', 'used' => 0, 'limit' => 500, 'start' => '01/05/2026', 'end' => '31/05/2026', 'status' => 'Active'],
                    ['code' => 'RAMADAN30', 'type' => 'Percentage', 'value' => '30%', 'min_order' => '800', 'max_discount' => '300', 'used' => 2100, 'limit' => 2000, 'start' => '01/03/2026', 'end' => '31/03/2026', 'status' => 'Expired'],
                    ['code' => 'FREESHIPBD', 'type' => 'Free Shipping', 'value' => 'Free', 'min_order' => '200', 'max_discount' => null, 'used' => 890, 'limit' => 1000, 'start' => '01/04/2026', 'end' => '30/04/2026', 'status' => 'Active'],
                    ['code' => 'NEWYEAR50', 'type' => 'Fixed', 'value' => '৳50', 'min_order' => '0', 'max_discount' => null, 'used' => 1240, 'limit' => 1500, 'start' => '01/01/2026', 'end' => '07/01/2026', 'status' => 'Expired'],
                ];
            @endphp

            @foreach ($coupons as $coupon)
                <div class="rounded-xl border {{ $coupon['status'] === 'Expired' ? 'border-gray-200 dark:border-gray-700 opacity-60' : 'border-gray-200 dark:border-gray-700' }} overflow-hidden">
                    {{-- Coupon Header --}}
                    <div class="relative px-5 py-4 {{ $coupon['status'] === 'Active' ? 'bg-gradient-to-r from-brand-500 to-brand-600' : 'bg-gradient-to-r from-gray-400 to-gray-500' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-lg font-bold text-white tracking-wider font-mono">{{ $coupon['code'] }}</p>
                                <p class="text-xs text-white/70 mt-0.5">{{ $coupon['type'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-white">{{ $coupon['value'] }}</p>
                                <p class="text-[10px] text-white/60 uppercase">OFF</p>
                            </div>
                        </div>
                        {{-- Decorative circles --}}
                        <div class="absolute -left-3 bottom-0 translate-y-1/2 w-6 h-6 rounded-full bg-white dark:bg-gray-900"></div>
                        <div class="absolute -right-3 bottom-0 translate-y-1/2 w-6 h-6 rounded-full bg-white dark:bg-gray-900"></div>
                    </div>

                    {{-- Coupon Body --}}
                    <div class="px-5 py-4 border-t border-dashed border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                            <div>
                                <span class="text-gray-400 dark:text-gray-500">Min Order</span>
                                <p class="font-medium text-gray-700 dark:text-gray-300">৳{{ $coupon['min_order'] }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-gray-500">Max Discount</span>
                                <p class="font-medium text-gray-700 dark:text-gray-300">{{ $coupon['max_discount'] ? '৳'.$coupon['max_discount'] : 'No limit' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-gray-500">Validity</span>
                                <p class="font-medium text-gray-700 dark:text-gray-300">{{ $coupon['start'] }} - {{ $coupon['end'] }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-gray-500">Usage</span>
                                <p class="font-medium text-gray-700 dark:text-gray-300">{{ number_format($coupon['used']) }} {{ $coupon['limit'] ? '/ '.number_format($coupon['limit']) : '/ Unlimited' }}</p>
                            </div>
                        </div>

                        {{-- Usage Progress --}}
                        @if($coupon['limit'])
                            <div class="mb-3">
                                <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="h-1.5 rounded-full {{ $coupon['used'] >= $coupon['limit'] ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ min(($coupon['used'] / $coupon['limit']) * 100, 100) }}%"></div>
                                </div>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-medium {{ $coupon['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                <span class="w-1 h-1 rounded-full {{ $coupon['status'] === 'Active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $coupon['status'] }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg></button>
                                <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors dark:bg-gray-800 dark:text-gray-400" title="Copy Code"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg></button>
                                <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
