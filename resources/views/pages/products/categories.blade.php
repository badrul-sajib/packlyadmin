@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Categories" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'parent' }">
        {{-- Tabs --}}
        <div class="flex items-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeTab = 'parent'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeTab === 'parent' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Parent Category
            </button>
            <button @click="activeTab = 'sub'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeTab === 'sub' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Sub Category
            </button>
            <button @click="activeTab = 'child'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeTab === 'child' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Child Category
            </button>
        </div>

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
                <input type="text" placeholder="Search by category name" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Category
            </button>
        </div>

        {{-- Parent Category Table --}}
        <div x-show="activeTab === 'parent'">
            <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
                <div class="min-w-[900px]">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-emerald-500 text-white text-sm">
                                <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                                <th class="px-4 py-3 text-left font-medium w-[6%]">Image</th>
                                <th class="px-4 py-3 text-left font-medium w-[22%]">Category Name</th>
                                <th class="px-4 py-3 text-center font-medium w-[10%]">Products</th>
                                <th class="px-4 py-3 text-center font-medium w-[12%] whitespace-nowrap">Set Commission</th>
                                <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                                <th class="px-4 py-3 text-left font-medium w-[14%] whitespace-nowrap">Created Date</th>
                                <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @php
                                    $parentCategories = [
                                        ['name' => 'Electronics Device', 'slug' => 'electronics-device', 'products' => 12450, 'commission' => '5', 'status' => 'Active', 'created_at' => '15/01/2025 10:30 AM'],
                                        ['name' => 'Electronic Accessories', 'slug' => 'electronic-accessories', 'products' => 8920, 'commission' => '5', 'status' => 'Active', 'created_at' => '15/01/2025 10:32 AM'],
                                        ['name' => 'Fashion Accessories', 'slug' => 'fashion-accessories', 'products' => 5340, 'commission' => '7', 'status' => 'Active', 'created_at' => '16/01/2025 09:15 AM'],
                                        ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'products' => 4210, 'commission' => '6', 'status' => 'Active', 'created_at' => '16/01/2025 09:20 AM'],
                                        ['name' => 'Food & Beverages', 'slug' => 'food-beverages', 'products' => 2890, 'commission' => '4', 'status' => 'Active', 'created_at' => '17/01/2025 11:00 AM'],
                                        ['name' => 'Health & Beauty', 'slug' => 'health-beauty', 'products' => 3150, 'commission' => '8', 'status' => 'Active', 'created_at' => '18/01/2025 02:00 PM'],
                                        ['name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'products' => 1780, 'commission' => '6', 'status' => 'Active', 'created_at' => '20/01/2025 11:30 AM'],
                                        ['name' => 'Seasonal Offers', 'slug' => 'seasonal-offers', 'products' => 0, 'commission' => '0', 'status' => 'Inactive', 'created_at' => '01/03/2025 04:30 PM'],
                                    ];
                                @endphp
                                @foreach ($parentCategories as $index => $category)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                        <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                        <td class="px-4 py-4 w-[6%]">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                                                <span class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($category['name'], 0, 2)) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 w-[22%]">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $category['name'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $category['slug'] }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[10%]">
                                            <span class="text-sm font-medium {{ $category['products'] === 0 ? 'text-gray-400' : 'text-gray-800 dark:text-white/90' }}">{{ number_format($category['products']) }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[12%]" x-data="{ showModal: false, value: '{{ $category['commission'] }}', saved: {{ $category['commission'] > 0 ? 'true' : 'false' }}, savedValue: '{{ $category['commission'] }}' }">
                                            <template x-if="!saved">
                                                <button @click="showModal = true" type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-dashed border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-500 hover:border-brand-400 hover:text-brand-500 transition-colors dark:border-gray-600 dark:text-gray-400 dark:hover:border-brand-400 dark:hover:text-brand-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                                    Set
                                                </button>
                                            </template>
                                            <template x-if="saved">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <span class="text-sm font-semibold text-brand-600 dark:text-brand-400" x-text="savedValue + '%'"></span>
                                                    <button @click="showModal = true" type="button" class="text-gray-400 hover:text-brand-500 transition-colors" title="Edit Commission">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            {{-- Commission Modal --}}
                                            <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50" style="display:none;" @keydown.escape.window="showModal = false">
                                                <div @click.outside="showModal = false" class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900">
                                                    {{-- Modal Header --}}
                                                    <div class="flex items-center justify-between px-6 pt-5 pb-4">
                                                        <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Update Commission</h4>
                                                        <button @click="showModal = false" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                    {{-- Type + Value + Update --}}
                                                    <div class="px-6 pb-5">
                                                        <div class="flex items-end gap-3">
                                                            <div class="w-44">
                                                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5 block">Type</label>
                                                                <div class="relative" x-data="{ typeOpen: false, selectedType: 'Percentage (%)' }">
                                                                    <button @click="typeOpen = !typeOpen" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                                                        <span x-text="selectedType"></span>
                                                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                                                    </button>
                                                                    <div x-show="typeOpen" @click.outside="typeOpen = false" x-transition class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                                                                        <ul class="py-1 text-sm">
                                                                            <li><button @click="selectedType = 'Percentage (%)'; typeOpen = false" class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Percentage (%)</button></li>
                                                                            <li><button @click="selectedType = 'Flat Amount'; typeOpen = false" class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Flat Amount</button></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-1">
                                                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5 block">Value</label>
                                                                <input x-model="value" type="number" min="0" max="100" step="0.5" placeholder="Enter value" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder-gray-500" />
                                                            </div>
                                                            <button @click="savedValue = value; saved = true; showModal = false" type="button" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-600 transition-colors whitespace-nowrap">
                                                                Update
                                                            </button>
                                                        </div>
                                                    </div>
                                                    {{-- Commission History --}}
                                                    <div class="px-6 pb-5">
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Commission History</span>
                                                        </div>
                                                        <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                                            <table class="w-full">
                                                                <thead>
                                                                    <tr class="bg-emerald-500 text-white text-xs">
                                                                        <th class="px-3 py-2 text-left font-medium">Date</th>
                                                                        <th class="px-3 py-2 text-left font-medium">User</th>
                                                                        <th class="px-3 py-2 text-center font-medium">Type Change</th>
                                                                        <th class="px-3 py-2 text-center font-medium">Value Change</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-400 dark:text-gray-500">No history found</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[10%]">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $category['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : 'bg-gray-100 text-gray-500 border border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $category['status'] === 'Active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                {{ $category['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 w-[15%]">
                                            @php $dp = explode(' ', $category['created_at'], 2); @endphp
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
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
        </div>

        {{-- Sub Category Table --}}
        <div x-show="activeTab === 'sub'" style="display:none;">
            <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
                <div class="min-w-[900px]">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-emerald-500 text-white text-sm">
                                <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                                <th class="px-4 py-3 text-left font-medium w-[6%]">Image</th>
                                <th class="px-4 py-3 text-left font-medium w-[18%]">Category Name</th>
                                <th class="px-4 py-3 text-left font-medium w-[13%]">Parent</th>
                                <th class="px-4 py-3 text-center font-medium w-[10%]">Products</th>
                                <th class="px-4 py-3 text-center font-medium w-[12%] whitespace-nowrap">Set Commission</th>
                                <th class="px-4 py-3 text-center font-medium w-[9%]">Status</th>
                                <th class="px-4 py-3 text-left font-medium w-[12%] whitespace-nowrap">Created Date</th>
                                <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[9%]">Action</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @php
                                    $subCategories = [
                                        ['name' => 'Smartphones', 'slug' => 'smartphones', 'parent' => 'Electronics Device', 'products' => 3200, 'commission' => '5', 'status' => 'Active', 'created_at' => '18/01/2025 02:45 PM'],
                                        ['name' => 'Laptops', 'slug' => 'laptops', 'parent' => 'Electronics Device', 'products' => 1850, 'commission' => '4', 'status' => 'Active', 'created_at' => '18/01/2025 02:50 PM'],
                                        ['name' => 'Tablets', 'slug' => 'tablets', 'parent' => 'Electronics Device', 'products' => 920, 'commission' => '5', 'status' => 'Active', 'created_at' => '18/01/2025 03:00 PM'],
                                        ['name' => 'Headphones', 'slug' => 'headphones', 'parent' => 'Electronic Accessories', 'products' => 2100, 'commission' => '6', 'status' => 'Active', 'created_at' => '19/01/2025 10:10 AM'],
                                        ['name' => 'Phone Cases', 'slug' => 'phone-cases', 'parent' => 'Electronic Accessories', 'products' => 4500, 'commission' => '8', 'status' => 'Active', 'created_at' => '19/01/2025 10:15 AM'],
                                        ['name' => 'Chargers & Cables', 'slug' => 'chargers-cables', 'parent' => 'Electronic Accessories', 'products' => 1340, 'commission' => '5', 'status' => 'Active', 'created_at' => '19/01/2025 10:20 AM'],
                                        ['name' => 'Men Fashion', 'slug' => 'men-fashion', 'parent' => 'Fashion Accessories', 'products' => 2800, 'commission' => '7', 'status' => 'Active', 'created_at' => '20/01/2025 09:00 AM'],
                                        ['name' => 'Women Fashion', 'slug' => 'women-fashion', 'parent' => 'Fashion Accessories', 'products' => 2540, 'commission' => '7', 'status' => 'Active', 'created_at' => '20/01/2025 09:05 AM'],
                                    ];
                                @endphp
                                @foreach ($subCategories as $index => $category)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                        <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                        <td class="px-4 py-4 w-[6%]">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                                                <span class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($category['name'], 0, 2)) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 w-[18%]">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $category['name'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $category['slug'] }}</p>
                                        </td>
                                        <td class="px-4 py-4 w-[13%]">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $category['parent'] }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[10%]">
                                            <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($category['products']) }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[12%]" x-data="{ showModal: false, value: '{{ $category['commission'] }}', saved: {{ $category['commission'] > 0 ? 'true' : 'false' }}, savedValue: '{{ $category['commission'] }}' }">
                                            <template x-if="!saved">
                                                <button @click="showModal = true" type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-dashed border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-500 hover:border-brand-400 hover:text-brand-500 transition-colors dark:border-gray-600 dark:text-gray-400 dark:hover:border-brand-400 dark:hover:text-brand-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                                    Set
                                                </button>
                                            </template>
                                            <template x-if="saved">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <span class="text-sm font-semibold text-brand-600 dark:text-brand-400" x-text="savedValue + '%'"></span>
                                                    <button @click="showModal = true" type="button" class="text-gray-400 hover:text-brand-500 transition-colors" title="Edit Commission">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50" style="display:none;" @keydown.escape.window="showModal = false">
                                                <div @click.outside="showModal = false" class="w-full max-w-sm rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900">
                                                    <h4 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-1">Set Commission</h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Enter commission percentage for <strong x-text="'{{ $category['name'] }}'"></strong></p>
                                                    <div class="relative mb-4">
                                                        <input x-model="value" type="number" min="0" max="100" step="0.5" placeholder="e.g. 5" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 pr-10 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                                                    </div>
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button @click="showModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                                                        <button @click="savedValue = value; saved = true; showModal = false" type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[10%]">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $category['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 w-[13%]">
                                            @php $dp = explode(' ', $category['created_at'], 2); @endphp
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[10%]">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg></button>
                                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>
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

        {{-- Child Category Table --}}
        <div x-show="activeTab === 'child'" style="display:none;">
            <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
                <div class="min-w-[900px]">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-emerald-500 text-white text-sm">
                                <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                                <th class="px-4 py-3 text-left font-medium w-[5%]">Image</th>
                                <th class="px-4 py-3 text-left font-medium w-[16%]">Category Name</th>
                                <th class="px-4 py-3 text-left font-medium w-[12%]">Parent</th>
                                <th class="px-4 py-3 text-left font-medium w-[12%]">Sub Category</th>
                                <th class="px-4 py-3 text-center font-medium w-[8%]">Products</th>
                                <th class="px-4 py-3 text-center font-medium w-[11%] whitespace-nowrap">Set Commission</th>
                                <th class="px-4 py-3 text-center font-medium w-[8%]">Status</th>
                                <th class="px-4 py-3 text-left font-medium w-[11%] whitespace-nowrap">Created Date</th>
                                <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[8%]">Action</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="max-h-[650px] overflow-y-auto custom-scrollbar">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @php
                                    $childCategories = [
                                        ['name' => 'iPhone Cases', 'slug' => 'iphone-cases', 'parent' => 'Electronic Accessories', 'sub' => 'Phone Cases', 'products' => 1200, 'commission' => '8', 'status' => 'Active', 'created_at' => '22/01/2025 10:00 AM'],
                                        ['name' => 'Samsung Cases', 'slug' => 'samsung-cases', 'parent' => 'Electronic Accessories', 'sub' => 'Phone Cases', 'products' => 980, 'commission' => '8', 'status' => 'Active', 'created_at' => '22/01/2025 10:05 AM'],
                                        ['name' => 'Wireless Earbuds', 'slug' => 'wireless-earbuds', 'parent' => 'Electronic Accessories', 'sub' => 'Headphones', 'products' => 850, 'commission' => '6', 'status' => 'Active', 'created_at' => '23/01/2025 09:30 AM'],
                                        ['name' => 'Over-Ear Headphones', 'slug' => 'over-ear-headphones', 'parent' => 'Electronic Accessories', 'sub' => 'Headphones', 'products' => 420, 'commission' => '6', 'status' => 'Active', 'created_at' => '23/01/2025 09:35 AM'],
                                        ['name' => 'Gaming Laptops', 'slug' => 'gaming-laptops', 'parent' => 'Electronics Device', 'sub' => 'Laptops', 'products' => 380, 'commission' => '4', 'status' => 'Active', 'created_at' => '25/01/2025 02:15 PM'],
                                        ['name' => 'T-Shirts', 'slug' => 't-shirts', 'parent' => 'Fashion Accessories', 'sub' => 'Men Fashion', 'products' => 1450, 'commission' => '10', 'status' => 'Active', 'created_at' => '26/01/2025 11:00 AM'],
                                    ];
                                @endphp
                                @foreach ($childCategories as $index => $category)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                        <td class="px-4 py-4 w-[4%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                        <td class="px-4 py-4 w-[5%]">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                                                <span class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($category['name'], 0, 2)) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 w-[16%]">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $category['name'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $category['slug'] }}</p>
                                        </td>
                                        <td class="px-4 py-4 w-[12%]"><span class="text-sm text-gray-700 dark:text-gray-300">{{ $category['parent'] }}</span></td>
                                        <td class="px-4 py-4 w-[12%]"><span class="text-sm text-gray-700 dark:text-gray-300">{{ $category['sub'] }}</span></td>
                                        <td class="px-4 py-4 text-center w-[8%]"><span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($category['products']) }}</span></td>
                                        <td class="px-4 py-4 text-center w-[12%]" x-data="{ showModal: false, value: '{{ $category['commission'] }}', saved: {{ $category['commission'] > 0 ? 'true' : 'false' }}, savedValue: '{{ $category['commission'] }}' }">
                                            <template x-if="!saved">
                                                <button @click="showModal = true" type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-dashed border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-500 hover:border-brand-400 hover:text-brand-500 transition-colors dark:border-gray-600 dark:text-gray-400 dark:hover:border-brand-400 dark:hover:text-brand-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                                    Set
                                                </button>
                                            </template>
                                            <template x-if="saved">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <span class="text-sm font-semibold text-brand-600 dark:text-brand-400" x-text="savedValue + '%'"></span>
                                                    <button @click="showModal = true" type="button" class="text-gray-400 hover:text-brand-500 transition-colors" title="Edit Commission">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50" style="display:none;" @keydown.escape.window="showModal = false">
                                                <div @click.outside="showModal = false" class="w-full max-w-sm rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900">
                                                    <h4 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-1">Set Commission</h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Enter commission percentage for <strong x-text="'{{ $category['name'] }}'"></strong></p>
                                                    <div class="relative mb-4">
                                                        <input x-model="value" type="number" min="0" max="100" step="0.5" placeholder="e.g. 5" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 pr-10 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                                                    </div>
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button @click="showModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                                                        <button @click="savedValue = value; saved = true; showModal = false" type="button" class="px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[8%]">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $category['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 w-[12%]">
                                            @php $dp = explode(' ', $category['created_at'], 2); @endphp
                                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $dp[0] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $dp[1] }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-center w-[9%]">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-brand-500/10" title="Edit"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg></button>
                                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-red-500/10" title="Delete"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>
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

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">8</span> of <span class="font-medium text-gray-700 dark:text-gray-300">993</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">100</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
