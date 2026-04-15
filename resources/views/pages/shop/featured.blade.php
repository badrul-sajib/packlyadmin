@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Featured Shops" />

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
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Expired</button></li>
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
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Featured
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-center font-medium rounded-l-lg w-[4%]">Sort</th>
                            <th class="px-4 py-3 text-left font-medium w-[3%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[5%]">Logo</th>
                            <th class="px-4 py-3 text-left font-medium w-[18%]">Shop Info</th>
                            <th class="px-4 py-3 text-left font-medium w-[8%]">Shop ID</th>
                            <th class="px-4 py-3 text-center font-medium w-[7%]">Products</th>
                            <th class="px-4 py-3 text-center font-medium w-[7%]">Orders</th>
                            <th class="px-4 py-3 text-center font-medium w-[7%]">Position</th>
                            <th class="px-4 py-3 text-left font-medium w-[11%] whitespace-nowrap">Featured Since</th>
                            <th class="px-4 py-3 text-left font-medium w-[11%] whitespace-nowrap">Expires At</th>
                            <th class="px-4 py-3 text-center font-medium w-[7%]">Status</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[7%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $shops = [
                                    ['name' => 'Gadget Hawkers', 'owner' => 'Arafat', 'phone' => '01989375224', 'shop_id' => 7603, 'products' => 245, 'orders' => 1820, 'position' => 1, 'featured_at' => '01/03/2026 10:00 AM', 'expires_at' => '01/06/2026 10:00 AM', 'status' => 'Active'],
                                    ['name' => 'Home Shop BD.com', 'owner' => 'Rakib Hasan', 'phone' => '01910903717', 'shop_id' => 7580, 'products' => 532, 'orders' => 3450, 'position' => 2, 'featured_at' => '15/02/2026 09:30 AM', 'expires_at' => '15/05/2026 09:30 AM', 'status' => 'Active'],
                                    ['name' => 'LUXURY VIP', 'owner' => 'Kamrul Islam', 'phone' => '01342584477', 'shop_id' => 7455, 'products' => 890, 'orders' => 5670, 'position' => 3, 'featured_at' => '20/01/2026 02:00 PM', 'expires_at' => '20/04/2026 02:00 PM', 'status' => 'Expired'],
                                    ['name' => 'WKL Marts', 'owner' => 'Wahid Khan', 'phone' => '01781951811', 'shop_id' => 7320, 'products' => 178, 'orders' => 920, 'position' => 4, 'featured_at' => '05/03/2026 11:15 AM', 'expires_at' => '05/06/2026 11:15 AM', 'status' => 'Active'],
                                    ['name' => 'CarbonX Shop', 'owner' => 'Shahriar Rahman', 'phone' => '01775006663', 'shop_id' => 7210, 'products' => 312, 'orders' => 2100, 'position' => 5, 'featured_at' => '10/03/2026 04:00 PM', 'expires_at' => '10/06/2026 04:00 PM', 'status' => 'Active'],
                                    ['name' => 'Express Gadgets', 'owner' => 'Ziaul Hoque', 'phone' => '01605949962', 'shop_id' => 7593, 'products' => 420, 'orders' => 2890, 'position' => 6, 'featured_at' => '01/02/2026 10:00 AM', 'expires_at' => '01/05/2026 10:00 AM', 'status' => 'Expired'],
                                    ['name' => 'Mira gallery', 'owner' => 'MD Rakibul', 'phone' => '01748832370', 'shop_id' => 7150, 'products' => 156, 'orders' => 780, 'position' => 7, 'featured_at' => '25/03/2026 09:00 AM', 'expires_at' => '25/06/2026 09:00 AM', 'status' => 'Active'],
                                    ['name' => 'Defense Academy', 'owner' => 'Nusrat Jahan', 'phone' => '07165024098', 'shop_id' => 7088, 'products' => 95, 'orders' => 450, 'position' => 8, 'featured_at' => '01/04/2026 12:00 PM', 'expires_at' => '01/07/2026 12:00 PM', 'status' => 'Active'],
                                ];
                            @endphp

                            @foreach ($shops as $index => $shop)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- Sort --}}
                                    <td class="px-4 py-4 w-[4%]">
                                        <div class="flex flex-col items-center gap-0.5">
                                            <button type="button" class="p-0.5 rounded text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 transition-colors dark:hover:bg-emerald-500/10 {{ $index === 0 ? 'opacity-30 cursor-not-allowed' : '' }}" title="Move Up" {{ $index === 0 ? 'disabled' : '' }}>
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                            </button>
                                            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">{{ $shop['position'] }}</span>
                                            <button type="button" class="p-0.5 rounded text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 transition-colors dark:hover:bg-emerald-500/10 {{ $index === count($shops) - 1 ? 'opacity-30 cursor-not-allowed' : '' }}" title="Move Down" {{ $index === count($shops) - 1 ? 'disabled' : '' }}>
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[3%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[5%]">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                                            <span class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($shop['name'], 0, 2)) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[18%]">
                                        <div>
                                            <a href="#" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">{{ $shop['name'] }}</a>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $shop['owner'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $shop['phone'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[10%]">
                                        <span class="text-sm font-mono text-gray-600 dark:text-gray-400">#{{ $shop['shop_id'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($shop['products']) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($shop['orders']) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-brand-50 text-xs font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $shop['position'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[11%]">
                                        @php $fp = explode(' ', $shop['featured_at'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $fp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $fp[1] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[11%]">
                                        @php $ep = explode(' ', $shop['expires_at'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $ep[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $ep[1] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $shop['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30' }}
                                        ">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $shop['status'] === 'Active' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                            {{ $shop['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            </button>
                                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Remove">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">8</span> of <span class="font-medium text-gray-700 dark:text-gray-300">32</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">4</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
