@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Products Dashboard" />

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">44,856</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Products</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">38,241</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Live Products</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-yellow-50 dark:bg-yellow-500/10">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <a href="{{ route('products.pending') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View all</a>
            </div>
            <h4 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">924</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Pending Approval</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10">
                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                </div>
                <a href="{{ route('products.update-requests') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View all</a>
            </div>
            <h4 class="text-2xl font-bold text-orange-600 dark:text-orange-400">156</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Update Requests</p>
        </div>
    </div>

    {{-- Row 2 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25h2.25A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">993</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Categories</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">248</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Brands</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-red-600 dark:text-red-400">342</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Out of Stock</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">7,013</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Merchants</p>
        </div>
    </div>

    {{-- Top Categories + Recent Pending --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        {{-- Top Categories --}}
        <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
            <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Top Categories</h3>
                <a href="{{ route('products.categories') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View All</a>
            </div>
            <div class="px-5 sm:px-6 space-y-3">
                @php
                    $topCategories = [
                        ['name' => 'Electronics Device', 'products' => 12450, 'percentage' => 28],
                        ['name' => 'Electronic Accessories', 'products' => 8920, 'percentage' => 20],
                        ['name' => 'Fashion Accessories', 'products' => 5340, 'percentage' => 12],
                        ['name' => 'Home & Kitchen', 'products' => 4210, 'percentage' => 9],
                        ['name' => 'Food & Beverages', 'products' => 2890, 'percentage' => 6],
                    ];
                @endphp
                @foreach ($topCategories as $cat)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $cat['name'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($cat['products']) }} products</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $cat['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Pending Products --}}
        <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
            <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Recent Pending Products</h3>
                <a href="{{ route('products.pending') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View All</a>
            </div>
            <div class="px-5 sm:px-6 space-y-3">
                @php
                    $recentPending = [
                        ['name' => '3D Creative Visualization LED Lamp', 'merchant' => 'Home Shop BD.com', 'date' => '11/04/2026'],
                        ['name' => 'Spider Man Airpods Pro Cover', 'merchant' => 'LUXURY VIP', 'date' => '11/04/2026'],
                        ['name' => 'Recci Risk W30 Bluetooth Speaker', 'merchant' => 'WKL Marts', 'date' => '11/04/2026'],
                        ['name' => 'Premium Leather Wallet RFID', 'merchant' => 'LUXURY VIP', 'date' => '10/04/2026'],
                        ['name' => 'USB-C Power Bank 20000mAh', 'merchant' => 'WKL Marts', 'date' => '10/04/2026'],
                    ];
                @endphp
                @foreach ($recentPending as $product)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $product['name'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $product['merchant'] }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $product['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top Brands --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Top Brands by Products</h3>
            <a href="{{ route('products.brands') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View All</a>
        </div>
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[600px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[5%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[8%]">Logo</th>
                            <th class="px-4 py-3 text-left font-medium w-[35%]">Brand Name</th>
                            <th class="px-4 py-3 text-center font-medium w-[20%]">Products</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[15%]">Status</th>
                        </tr>
                    </thead>
                </table>
                <table class="w-full">
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @php
                            $topBrands = [
                                ['name' => 'No Brand', 'products' => 18450, 'status' => 'Active'],
                                ['name' => 'Xiaomi', 'products' => 4650, 'status' => 'Active'],
                                ['name' => 'Samsung', 'products' => 3420, 'status' => 'Active'],
                                ['name' => 'Apple', 'products' => 2180, 'status' => 'Active'],
                                ['name' => 'Realme', 'products' => 1890, 'status' => 'Active'],
                            ];
                        @endphp
                        @foreach ($topBrands as $index => $brand)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-4 w-[5%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                <td class="px-4 py-4 w-[8%]">
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($brand['name'], 0, 2)) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 w-[35%]"><span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $brand['name'] }}</span></td>
                                <td class="px-4 py-4 text-center w-[20%]"><span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($brand['products']) }}</span></td>
                                <td class="px-4 py-4 text-center w-[15%]">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $brand['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
