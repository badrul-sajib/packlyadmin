@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="All Merchants" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters Row --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 px-5 sm:px-6">
            {{-- Add New --}}
            <a href="#" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add new
            </a>

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
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Disabled</button></li>
                    </ul>
                </div>
            </div>

            {{-- Shop Type --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>Shop Type</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-40 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">E-Commerce</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Offline</button></li>
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
                {{-- Search --}}
                <div class="relative">
                    <input type="text" placeholder="Enter value" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 w-44" />
                </div>

                {{-- Search By --}}
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
                            <th class="px-4 py-3 text-center font-medium w-[20%]">Orders</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[7%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $merchants = [
                                    ['id' => 7603, 'shop' => 'Health and Beauty Tahiya Gallery', 'status' => 'Active', 'type' => 'E-Commerce', 'online' => false, 'phone' => '01955277657', 'email' => 'mztuhin575@gmail.com', 'ownerName' => 'Mohammad Tuhin Asraf', 'address' => 'Dhaka Keraniganj burhani bag bismillah tower', 'products' => 1, 'orders' => '0'],
                                    ['id' => 7602, 'shop' => 'The Gadget Pro', 'status' => 'Processing', 'type' => 'Offline', 'online' => false, 'phone' => '01814553968', 'email' => 'thegadgetprobd@gmail.com', 'ownerName' => 'MD. Sabbir', 'address' => 'Manda, Naogaon', 'products' => 0, 'orders' => '0'],
                                    ['id' => 7601, 'shop' => 'Dhaka Exclusive', 'status' => 'Processing', 'type' => 'Offline', 'online' => false, 'phone' => '01819673597', 'email' => 'dhakaexclusive.bd@gmail.com', 'ownerName' => 'Md Matiur Rahman', 'address' => 'Moulvibazar trade center Chawkbazar Dhaka', 'products' => 0, 'orders' => '0'],
                                    ['id' => 7600, 'shop' => 'Ventora', 'status' => 'Pending', 'type' => 'Offline', 'online' => false, 'phone' => '01576992904', 'email' => 'wasifrahman788@gmail.com', 'ownerName' => 'Wasif Rahman', 'address' => 'North Badda, Dhaka - 1212', 'products' => 0, 'orders' => '0'],
                                    ['id' => 7599, 'shop' => 'Happy Hometex ind.', 'status' => 'Processing', 'type' => 'Offline', 'online' => false, 'phone' => '01874733904', 'email' => 'mdsoburahman38@gmail.com', 'ownerName' => 'Md Sobur Rahman', 'address' => 'Hours 15, road 8 Uttara 10 Dhaka 1230', 'products' => 0, 'orders' => '0'],
                                    ['id' => 7597, 'shop' => 'siam', 'status' => 'Pending', 'type' => 'Offline', 'online' => false, 'phone' => '01796094969', 'email' => 'copamagi863@parsitv.com', 'ownerName' => 'Siam Sarker', 'address' => 'Ulpur', 'products' => 0, 'orders' => '0'],
                                    ['id' => 7596, 'shop' => 'Sm Shop', 'status' => 'Pending', 'type' => 'Offline', 'online' => false, 'phone' => '01329967768', 'email' => 'sharifulislam65feni@gmail.com', 'ownerName' => 'H. Shariful Islam', 'address' => 'Habib Shop, 4th Floor, Sundarban Square Super Merket, Fulbaria, Gulistan, Dhaka', 'products' => 0, 'orders' => '0'],
                                    ['id' => 7595, 'shop' => 'Rojgar Telecom', 'status' => 'Active', 'type' => 'E-Commerce', 'online' => true, 'phone' => '01842020291', 'email' => 'rojgartelecom@gmail.com', 'ownerName' => 'Mohammad Tuhin', 'address' => 'Keraniganj, Dhaka', 'products' => 45, 'orders' => '1,114'],
                                    ['id' => 7594, 'shop' => 'Borshon Shop', 'status' => 'Active', 'type' => 'E-Commerce', 'online' => false, 'phone' => '01914535520', 'email' => 'borshonshop@gmail.com', 'ownerName' => 'MD. Sabbir Ahmed', 'address' => 'Naogaon', 'products' => 22, 'orders' => '465'],
                                    ['id' => 7593, 'shop' => 'Express Gadgets', 'status' => 'Active', 'type' => 'E-Commerce', 'online' => true, 'phone' => '01605949962', 'email' => 'expressgadgets@gmail.com', 'ownerName' => 'Ziaul Hoque', 'address' => 'Mirpur, Dhaka', 'products' => 38, 'orders' => '890'],
                                ];

                                $statusClasses = [
                                    'Active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-500',
                                    'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-500',
                                    'Processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500',
                                    'Disabled' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-500',
                                ];

                                $typeClasses = [
                                    'E-Commerce' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-500',
                                    'Offline' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-500',
                                ];
                            @endphp

                            @foreach ($merchants as $merchant)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- Shop ID --}}
                                    <td class="px-4 py-3.5 w-[8%]">
                                        <div class="flex items-center gap-1">
                                            <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['id'] }}</span>
                                            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    {{-- Business Name --}}
                                    <td class="px-4 py-3.5 w-[18%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['shop'] }}</p>
                                            <div class="flex items-center gap-1 mt-1">
                                                <span class="inline-flex items-center whitespace-nowrap rounded px-1.5 py-0.5 text-xs font-medium {{ $statusClasses[$merchant['status']] }}">{{ $merchant['status'] }}</span>
                                                <span class="inline-flex items-center whitespace-nowrap rounded px-1.5 py-0.5 text-xs font-medium {{ $typeClasses[$merchant['type']] }}">{{ $merchant['type'] === 'E-Commerce' ? 'E-Commerce' : 'Offline' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Phone --}}
                                    <td class="px-4 py-3.5 w-[15%]">
                                        <div>
                                            <div class="flex items-center gap-1">
                                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $merchant['phone'] }}</p>
                                                <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[160px]">{{ $merchant['email'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Owner Name --}}
                                    <td class="px-4 py-3.5 w-[22%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['ownerName'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 line-clamp-2">{{ $merchant['address'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Total Products --}}
                                    <td class="px-4 py-3.5 text-center w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $merchant['products'] }}</span>
                                    </td>
                                    {{-- Orders --}}
                                    <td class="px-4 py-3.5 text-center w-[20%]">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $merchant['orders'] }}</span>
                                    </td>
                                    {{-- Action --}}
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">10</span> of <span class="font-medium text-gray-700 dark:text-gray-300">7,013</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">4</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">5</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">702</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
