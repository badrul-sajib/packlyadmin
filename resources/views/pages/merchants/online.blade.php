@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Online Merchants" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters Row --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 px-5 sm:px-6">
            {{-- Status --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>Status</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-40 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Active</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Pending</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Processing</button></li>
                    </ul>
                </div>
            </div>

            {{-- Verification Status --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>Verification Status</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Verified</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Unverified</button></li>
                    </ul>
                </div>
            </div>

            <div class="ml-auto flex items-center gap-3">
                <div class="relative">
                    <input type="text" placeholder="Enter value" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 w-44" />
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                        <span>Merchant ID</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                        <ul class="py-1 text-sm">
                            <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Merchant ID</button></li>
                            <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Shop Name</button></li>
                            <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Phone</button></li>
                            <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Email</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1000px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[8%]">Shop ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[18%]">Business Name</th>
                            <th class="px-4 py-3 text-left font-medium w-[15%]">Phone</th>
                            <th class="px-4 py-3 text-left font-medium w-[22%]">Owner Name</th>
                            <th class="px-4 py-3 text-center font-medium w-[10%]">Total Products</th>
                            <th class="px-4 py-3 text-center font-medium w-[12%]">Orders</th>
                            <th class="px-4 py-3 text-center font-medium w-[8%]">Last Active</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[7%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $merchants = [
                                    ['id' => 7595, 'shop' => 'Rojgar Telecom', 'status' => 'Active', 'phone' => '01842020291', 'email' => 'rojgartelecom@gmail.com', 'ownerName' => 'Mohammad Tuhin', 'address' => 'Keraniganj, Dhaka', 'products' => 45, 'orders' => '1,114', 'lastActive' => '2 min ago'],
                                    ['id' => 7593, 'shop' => 'Express Gadgets', 'status' => 'Active', 'phone' => '01605949962', 'email' => 'expressgadgets@gmail.com', 'ownerName' => 'Ziaul Hoque', 'address' => 'Mirpur, Dhaka', 'products' => 38, 'orders' => '890', 'lastActive' => '5 min ago'],
                                    ['id' => 7590, 'shop' => "Anaya's Collection", 'status' => 'Active', 'phone' => '01681850732', 'email' => 'anayacollection@gmail.com', 'ownerName' => 'Mukta Akter', 'address' => 'Gulshan, Dhaka', 'products' => 120, 'orders' => '1,114', 'lastActive' => '8 min ago'],
                                    ['id' => 7585, 'shop' => 'Gretees', 'status' => 'Active', 'phone' => '01608387271', 'email' => 'gretees.bd@gmail.com', 'ownerName' => 'Rajib Hossen', 'address' => 'Dhanmondi, Dhaka', 'products' => 85, 'orders' => '1,162', 'lastActive' => '12 min ago'],
                                    ['id' => 7580, 'shop' => 'Abdullah MotoGadget', 'status' => 'Active', 'phone' => '01319810041', 'email' => 'abdullahmoto@gmail.com', 'ownerName' => 'Abdullah Al Emu', 'address' => 'Uttara, Dhaka', 'products' => 64, 'orders' => '251', 'lastActive' => '15 min ago'],
                                    ['id' => 7575, 'shop' => 'White Horse Shop', 'status' => 'Active', 'phone' => '01609450703', 'email' => 'whitehorse@gmail.com', 'ownerName' => 'Ziaul Hoque (Jobayer)', 'address' => 'Banani, Dhaka', 'products' => 92, 'orders' => '465', 'lastActive' => '20 min ago'],
                                    ['id' => 7570, 'shop' => 'Fashion World BD', 'status' => 'Active', 'phone' => '01712890456', 'email' => 'fashionworld@gmail.com', 'ownerName' => 'Shamim Ahmed', 'address' => 'Motijheel, Dhaka', 'products' => 56, 'orders' => '890', 'lastActive' => '25 min ago'],
                                    ['id' => 7565, 'shop' => 'TechZone Store', 'status' => 'Active', 'phone' => '01812345987', 'email' => 'techzone@gmail.com', 'ownerName' => 'Rafiq Uddin', 'address' => 'Mohammadpur, Dhaka', 'products' => 78, 'orders' => '678', 'lastActive' => '30 min ago'],
                                    ['id' => 7560, 'shop' => 'Gadget Hub', 'status' => 'Active', 'phone' => '01912876540', 'email' => 'gadgethub@gmail.com', 'ownerName' => 'Sohel Rana', 'address' => 'Badda, Dhaka', 'products' => 43, 'orders' => '534', 'lastActive' => '45 min ago'],
                                    ['id' => 7555, 'shop' => 'Style Studio', 'status' => 'Active', 'phone' => '01556782345', 'email' => 'stylestudio@gmail.com', 'ownerName' => 'Nasima Begum', 'address' => 'Rampura, Dhaka', 'products' => 67, 'orders' => '412', 'lastActive' => '1 hr ago'],
                                ];

                                $statusClasses = [
                                    'Active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-500',
                                ];
                            @endphp

                            @foreach ($merchants as $merchant)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 py-3.5 w-[8%]">
                                        <div class="flex items-center gap-1">
                                            <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['id'] }}</span>
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 w-[18%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['shop'] }}</p>
                                            <span class="inline-flex items-center whitespace-nowrap rounded px-1.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-500 mt-0.5">E-Commerce</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 w-[15%]">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $merchant['phone'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[160px]">{{ $merchant['email'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 w-[22%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['ownerName'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 line-clamp-2">{{ $merchant['address'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $merchant['products'] }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center w-[12%]">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $merchant['orders'] }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center w-[8%]">
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400">{{ $merchant['lastActive'] }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center w-[7%]">
                                        <a href="{{ route('merchants.detail', $merchant['id']) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-500 px-3 py-1.5 text-xs font-medium text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:border-brand-400 dark:text-brand-400 dark:hover:bg-brand-500 dark:hover:text-white">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            View
                                        </a>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">10</span> of <span class="font-medium text-gray-700 dark:text-gray-300">2,250</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">225</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
