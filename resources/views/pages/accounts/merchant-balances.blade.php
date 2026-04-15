@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Merchant Balances" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters Row --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="relative">
                <input type="text" placeholder="Enter value" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 w-44" />
            </div>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                    <span>All</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 z-50 mt-1 w-44 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900" style="display:none;">
                    <ul class="py-1 text-sm">
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">All</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Shop ID</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Shop Name</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Phone</button></li>
                        <li><button class="w-full px-4 py-2 text-left text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]">Email</button></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[900px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[10%]">Shop ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[25%]">Business</th>
                            <th class="px-4 py-3 text-left font-medium w-[25%]">Owner Name</th>
                            <th class="px-4 py-3 text-right font-medium w-[20%]">Payable Balance</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[20%]">Actions</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $merchants = [
                                    ['id' => 7603, 'shop' => 'Health and Beauty Tahiya Gallery', 'address' => 'Dhaka Keraniganj burhani bag bismillah tower', 'ownerName' => 'Mohammad Tuhin Asraf', 'phone' => '01955277657', 'email' => 'mztuhin5757@gmail.com', 'balance' => '0'],
                                    ['id' => 7602, 'shop' => 'The Gadget Pro', 'address' => 'Manda, Naogaon', 'ownerName' => 'Md. Jahaingir alom', 'phone' => '01814553968', 'email' => 'thegadgetprobd@gmail.com', 'balance' => '0'],
                                    ['id' => 7601, 'shop' => 'Dhaka Exclusive', 'address' => 'Moulvibazar trade center Chawkbazar Dhaka', 'ownerName' => 'Md Matiur Rahman', 'phone' => '01819673597', 'email' => 'dhakaexclusive.bd@gmail.com', 'balance' => '0'],
                                    ['id' => 7600, 'shop' => 'Ventora', 'address' => 'North Badda, Dhaka - 1212', 'ownerName' => 'Wasif Rahman', 'phone' => '01576992904', 'email' => 'wasifrahman788@gmail.com', 'balance' => '0'],
                                    ['id' => 7599, 'shop' => 'Happy Hometex ind.', 'address' => 'Hours 15 road 6 Uttara 10 Dhaka 1230', 'ownerName' => 'Happy Hometex ind.', 'phone' => '01874733904', 'email' => 'mdsobujrahman28@gmail.com', 'balance' => '0'],
                                    ['id' => 7597, 'shop' => 'siam', 'address' => 'ulpur', 'ownerName' => 'siam sarker', 'phone' => '01796094969', 'email' => 'copamagi863@parsitv.com', 'balance' => '0'],
                                    ['id' => 7595, 'shop' => 'Rojgar Telecom', 'address' => 'Keraniganj, Dhaka', 'ownerName' => 'Mohammad Tuhin', 'phone' => '01842020291', 'email' => 'rojgartelecom@gmail.com', 'balance' => '12,450.00'],
                                    ['id' => 7594, 'shop' => 'Borshon Shop', 'address' => 'Naogaon', 'ownerName' => 'MD. Sabbir Ahmed', 'phone' => '01914535520', 'email' => 'borshonshop@gmail.com', 'balance' => '5,280.00'],
                                    ['id' => 7593, 'shop' => 'Express Gadgets', 'address' => 'Mirpur, Dhaka', 'ownerName' => 'Ziaul Hoque', 'phone' => '01605949962', 'email' => 'expressgadgets@gmail.com', 'balance' => '8,920.00'],
                                    ['id' => 7590, 'shop' => "Anaya's Collection", 'address' => 'Gulshan, Dhaka', 'ownerName' => 'Mukta Akter', 'phone' => '01681850732', 'email' => 'anayacollection@gmail.com', 'balance' => '15,340.00'],
                                ];
                            @endphp

                            @foreach ($merchants as $merchant)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- Shop ID --}}
                                    <td class="px-4 py-4 w-[10%]">
                                        <div class="flex items-center gap-1">
                                            <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['id'] }}</span>
                                            <button type="button" class="text-emerald-500 hover:text-emerald-600">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    {{-- Business --}}
                                    <td class="px-4 py-4 w-[25%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['shop'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $merchant['address'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Owner Name --}}
                                    <td class="px-4 py-4 w-[25%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $merchant['ownerName'] }}</p>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $merchant['phone'] }}</p>
                                                <button type="button" class="text-emerald-500 hover:text-emerald-600">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </button>
                                            </div>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $merchant['email'] }}</p>
                                                <button type="button" class="text-emerald-500 hover:text-emerald-600">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Payable Balance --}}
                                    <td class="px-4 py-4 text-right w-[20%]">
                                        <span class="text-sm font-semibold {{ $merchant['balance'] === '0' ? 'text-gray-500 dark:text-gray-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $merchant['balance'] }}</span>
                                    </td>
                                    {{-- Actions --}}
                                    <td class="px-4 py-4 text-center w-[20%]">
                                        <a href="#" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-500 px-3 py-1.5 text-xs font-medium text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:border-brand-400 dark:text-brand-400 dark:hover:bg-brand-500 dark:hover:text-white">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                            </svg>
                                            Beneficiaries
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
