@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pages" />

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
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Published</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Draft</button></li>
                    </ul>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="relative w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" placeholder="Search by page title" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Page
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[900px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[5%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[25%]">Title</th>
                            <th class="px-4 py-3 text-left font-medium w-[18%]">Slug</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Position</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%] whitespace-nowrap">Last Updated</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $pages = [
                                    ['title' => 'About Us', 'slug' => '/about-us', 'position' => 'Footer', 'status' => 'Published', 'updated_at' => '10/04/2026 02:30 PM'],
                                    ['title' => 'Terms & Conditions', 'slug' => '/terms-and-conditions', 'position' => 'Footer', 'status' => 'Published', 'updated_at' => '08/04/2026 11:00 AM'],
                                    ['title' => 'Privacy Policy', 'slug' => '/privacy-policy', 'position' => 'Footer', 'status' => 'Published', 'updated_at' => '08/04/2026 11:05 AM'],
                                    ['title' => 'Return & Refund Policy', 'slug' => '/return-refund-policy', 'position' => 'Footer', 'status' => 'Published', 'updated_at' => '05/04/2026 09:20 AM'],
                                    ['title' => 'Shipping Policy', 'slug' => '/shipping-policy', 'position' => 'Footer', 'status' => 'Published', 'updated_at' => '05/04/2026 09:25 AM'],
                                    ['title' => 'Contact Us', 'slug' => '/contact-us', 'position' => 'Header', 'status' => 'Published', 'updated_at' => '01/04/2026 03:00 PM'],
                                    ['title' => 'How to Order', 'slug' => '/how-to-order', 'position' => 'Header', 'status' => 'Published', 'updated_at' => '28/03/2026 10:45 AM'],
                                    ['title' => 'Career', 'slug' => '/career', 'position' => 'Footer', 'status' => 'Draft', 'updated_at' => '25/03/2026 04:15 PM'],
                                    ['title' => 'Merchant Agreement', 'slug' => '/merchant-agreement', 'position' => 'None', 'status' => 'Draft', 'updated_at' => '20/03/2026 02:00 PM'],
                                ];
                            @endphp

                            @foreach ($pages as $index => $page)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-4 w-[5%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-4 py-4 w-[25%]">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                            <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $page['title'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[18%]">
                                        <span class="text-sm font-mono text-gray-500 dark:text-gray-400">{{ $page['slug'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]">
                                        @php
                                            $posColors = [
                                                'Header' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                'Footer' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                                'None' => 'bg-gray-100 text-gray-500 dark:bg-gray-500/10 dark:text-gray-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $posColors[$page['position']] ?? '' }}">{{ $page['position'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center w-[10%]" x-data="{ open: false, status: '{{ $page['status'] }}' }">
                                        <div class="relative inline-block">
                                            <button @click="open = !open" type="button"
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium cursor-pointer transition-colors"
                                                :class="status === 'Published'
                                                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30'
                                                    : 'bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30'">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="status === 'Published' ? 'bg-emerald-500' : 'bg-yellow-500'"></span>
                                                <span x-text="status"></span>
                                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-1/2 -translate-x-1/2 z-50 mt-1 w-36 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                <ul class="py-1 text-sm">
                                                    <li><button @click="status = 'Published'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Published</button></li>
                                                    <li><button @click="status = 'Draft'; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Draft</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-[15%]">
                                        @php $dp = explode(' ', $page['updated_at'], 2); @endphp
                                        <div>
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">9</span> of <span class="font-medium text-gray-700 dark:text-gray-300">9</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
