@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Campaign Details" />

    @php
        $campaign = [
            'id' => 1,
            'name' => 'Baisakhi Mega Sale 2026',
            'type' => 'Flash Sale',
            'discount' => '40% OFF',
            'start' => '10/04/2026 06:05 PM',
            'end' => '20/04/2026 06:06 PM',
            'status' => 'Active',
        ];
    @endphp

    {{-- Campaign Header --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mb-6">
        <div class="flex items-center justify-between px-6 py-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-brand-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white/90">{{ $campaign['name'] }}</h2>
                        <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-medium bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400">{{ $campaign['type'] }}</span>
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $campaign['status'] }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-sm font-semibold text-brand-600 dark:text-brand-400">{{ $campaign['discount'] }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">|</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $campaign['start'] }} - {{ $campaign['end'] }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('marketing.campaigns') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                    Back
                </a>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                    Edit Campaign
                </button>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Prime Views</p>
            <p class="text-xl font-bold text-violet-600 dark:text-violet-400 mt-1">4</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Products</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">696</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Orders</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">1,820</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Revenue</p>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">৳4,56,000</p>
        </div>
    </div>

    {{-- Prime Views Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Prime Views</h3>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Prime View
            </button>
        </div>

        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[800px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[5%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[25%]">Name</th>
                            <th class="px-4 py-3 text-center font-medium w-[15%] whitespace-nowrap">Total Products</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%] whitespace-nowrap">Log History</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[18%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $primeViews = [
                                    ['name' => 'Baisakhi Special Discount', 'products' => 262, 'status' => 'Active'],
                                    ['name' => '40% OFF Electronics', 'products' => 150, 'status' => 'Active'],
                                    ['name' => 'Free Delivery Zone', 'products' => 184, 'status' => 'Active'],
                                    ['name' => 'Flash Deal Hour', 'products' => 100, 'status' => 'Inactive'],
                                ];
                            @endphp

                            @foreach ($primeViews as $index => $pv)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[5%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                    <td class="px-4 py-4 w-[25%]"><span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $pv['name'] }}</span></td>
                                    <td class="px-4 py-4 text-center w-[15%]">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="inline-flex items-center justify-center min-w-[32px] h-7 rounded-md bg-brand-500 px-2 text-xs font-bold text-white">{{ $pv['products'] }}</span>
                                            <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-md border border-gray-200 text-gray-400 hover:text-emerald-500 hover:border-emerald-300 transition-colors dark:border-gray-700" title="Add Products">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]" x-data="{ open: false, status: '{{ $pv['status'] }}' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button"
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium cursor-pointer transition-colors"
                                                :class="status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : 'bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30'">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="status === 'Active' ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                                                <span x-text="status"></span>
                                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-1 w-32 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                <ul class="py-1 text-sm">
                                                    <li><button @click="status = 'Active'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active</button></li>
                                                    <li><button @click="status = 'Inactive'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-gray-400"></span> Inactive</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-800 text-white hover:bg-gray-700 transition-colors dark:bg-gray-700 dark:hover:bg-gray-600" title="View Log History">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[18%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="#" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 transition-colors">View</a>
                                            <button type="button" class="inline-flex items-center justify-center rounded-lg bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-yellow-600 transition-colors">Edit</button>
                                            <button type="button" class="inline-flex items-center justify-center rounded-lg bg-red-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-600 transition-colors">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
