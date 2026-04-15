@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Reels" />

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
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Inactive</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by title" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Reel
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1000px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%]">Image/Video</th>
                            <th class="px-4 py-3 text-left font-medium w-[18%]">Title</th>
                            <th class="px-4 py-3 text-left font-medium w-[24%]">Description</th>
                            <th class="px-4 py-3 text-left font-medium w-[16%]">Link</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Status</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $reels = [
                                    ['title' => 'Summer Collection 2026', 'type' => 'video', 'description' => 'Check out our latest summer collection with amazing deals and discounts on trendy fashion.', 'link' => 'packly.com/summer-2026', 'status' => 'Active'],
                                    ['title' => 'Flash Sale Alert', 'type' => 'image', 'description' => 'Massive flash sale starting now! Up to 70% off on electronics and accessories.', 'link' => 'packly.com/flash-sale', 'status' => 'Active'],
                                    ['title' => 'New Arrivals - Gadgets', 'type' => 'video', 'description' => 'Explore the newest gadgets and tech products added to our store this week.', 'link' => 'packly.com/new-arrivals', 'status' => 'Active'],
                                    ['title' => 'Baisakhi Special Offer', 'type' => 'video', 'description' => 'Celebrate Baisakhi with exclusive offers on traditional wear and festive items.', 'link' => 'packly.com/baisakhi', 'status' => 'Active'],
                                    ['title' => 'Top 10 Best Sellers', 'type' => 'image', 'description' => 'Our most popular products this month. See what everyone is buying!', 'link' => 'packly.com/best-sellers', 'status' => 'Active'],
                                    ['title' => 'Ramadan Sale', 'type' => 'video', 'description' => 'Special Ramadan deals on home essentials, kitchen items and groceries.', 'link' => 'packly.com/ramadan', 'status' => 'Inactive'],
                                    ['title' => 'Brand Spotlight - Samsung', 'type' => 'image', 'description' => 'Exclusive Samsung products with warranty and best prices guaranteed.', 'link' => 'packly.com/samsung', 'status' => 'Active'],
                                    ['title' => 'Winter Clearance', 'type' => 'video', 'description' => 'Last chance to grab winter items at clearance prices before they are gone.', 'link' => 'packly.com/winter-clear', 'status' => 'Inactive'],
                                ];
                            @endphp

                            @foreach ($reels as $index => $reel)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[4%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[10%]">
                                        <div class="relative w-14 h-20 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-700">
                                            @if($reel['type'] === 'video')
                                                <div class="absolute inset-0 bg-gradient-to-b from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600"></div>
                                                <div class="relative flex items-center justify-center w-7 h-7 rounded-full bg-white/80 dark:bg-gray-900/80">
                                                    <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-300 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </div>
                                            @else
                                                <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                            @endif
                                            {{-- Type indicator --}}
                                            <span class="absolute bottom-1 left-1/2 -translate-x-1/2 rounded px-1.5 py-0.5 text-[9px] font-bold text-white {{ $reel['type'] === 'video' ? 'bg-red-500' : 'bg-blue-500' }}">
                                                {{ strtoupper($reel['type']) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[18%]">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $reel['title'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[24%]">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $reel['description'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 w-[16%]">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                                            <a href="#" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 truncate">{{ $reel['link'] }}</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[8%]" x-data="{ open: false, status: '{{ $reel['status'] }}' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button"
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium cursor-pointer transition-colors"
                                                :class="status === 'Active'
                                                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30'
                                                    : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30'">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="status === 'Active' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                                <span x-text="status"></span>
                                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-1 w-36 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                <ul class="py-1 text-sm">
                                                    <li>
                                                        <button @click="status = 'Active'; open = false" type="button" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button @click="status = 'Inactive'; open = false" type="button" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                                                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Inactive
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            </button>
                                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete">
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">8</span> of <span class="font-medium text-gray-700 dark:text-gray-300">18</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
