@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Phone Lookup" />

    @php
        $allAccounts = [
            // merchants
            ['type' => 'Merchant', 'name' => 'StyleNest BD',    'phone' => '+880 1711-001122', 'email' => 'style@nestbd.com',    'status' => 'Active',   'joined' => '12 Jan 2025', 'link' => '/merchants/1'],
            ['type' => 'Merchant', 'name' => 'TechZone Shop',   'phone' => '+880 1812-223344', 'email' => 'info@techzone.bd',    'status' => 'Active',   'joined' => '03 Feb 2025', 'link' => '/merchants/2'],
            ['type' => 'Merchant', 'name' => 'FreshMart',       'phone' => '+880 1711-001122', 'email' => 'fresh@mart.bd',       'status' => 'Active',   'joined' => '20 Feb 2025', 'link' => '/merchants/3'],
            ['type' => 'Merchant', 'name' => 'GreenLeaf Store', 'phone' => '+880 1913-334455', 'email' => 'green@leaf.bd',       'status' => 'Active',   'joined' => '01 Mar 2025', 'link' => '/merchants/4'],
            ['type' => 'Merchant', 'name' => 'Electro Point',   'phone' => '+880 1614-445566', 'email' => 'electro@point.bd',   'status' => 'Inactive', 'joined' => '15 Mar 2025', 'link' => '/merchants/5'],
            // customers
            ['type' => 'Customer', 'name' => 'Rahim Ahmed',     'phone' => '+880 1711-001122', 'email' => 'rahim@gmail.com',     'status' => 'Active',   'joined' => '05 Apr 2025', 'link' => '/customers/1'],
            ['type' => 'Customer', 'name' => 'Fatima Khanom',   'phone' => '+880 1812-223344', 'email' => 'fatima@mail.com',     'status' => 'Active',   'joined' => '20 Apr 2025', 'link' => '/customers/2'],
            ['type' => 'Customer', 'name' => 'Karim Uddin',     'phone' => '+880 1913-334455', 'email' => 'karim@example.com',   'status' => 'Blocked',  'joined' => '10 May 2025', 'link' => '/customers/3'],
            ['type' => 'Customer', 'name' => 'Nila Begum',      'phone' => '+880 1614-445566', 'email' => 'nila@webmail.net',    'status' => 'Active',   'joined' => '02 Jun 2025', 'link' => '/customers/4'],
            ['type' => 'Customer', 'name' => 'Sohel Rana',      'phone' => '+880 1711-001122', 'email' => 'sohel@test.com',      'status' => 'Active',   'joined' => '14 Jun 2025', 'link' => '/customers/5'],
        ];
    @endphp

    <div x-data="{
        query: '',
        results: [],
        searched: false,
        allAccounts: {{ json_encode($allAccounts) }},
        search() {
            this.searched = true;
            const q = this.query.trim().replace(/\s+/g, '');
            if (!q) { this.results = []; return; }
            this.results = this.allAccounts.filter(a =>
                a.phone.replace(/\s+/g, '').replace(/-/g, '').includes(q.replace(/-/g, ''))
            );
        }
    }">

        {{-- Search panel --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 mb-5">
            <div class="max-w-xl mx-auto text-center">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                </div>
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-1">Phone Number Lookup</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Enter a phone number to find all merchants and customers linked to it. Useful for detecting duplicate or suspicious accounts.</p>

                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        </div>
                        <input type="text" x-model="query" @keydown.enter="search()"
                            placeholder="+880 17XX-XXXXXX or 017XXXXXXXX"
                            class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                    </div>
                    <button type="button" @click="search()"
                        class="rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-5 py-2.5 transition-colors">
                        Search
                    </button>
                </div>

                {{-- Quick examples --}}
                <div class="flex items-center gap-2 justify-center mt-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500">Try:</span>
                    @foreach (['+880 1711-001122', '+880 1812-223344'] as $eg)
                        <button type="button" @click="query = '{{ $eg }}'; search()"
                            class="text-xs text-brand-600 dark:text-brand-400 hover:underline">{{ $eg }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Results --}}
        <div x-show="searched" style="display:none;">

            {{-- Duplicate warning --}}
            <div x-show="results.length > 1"
                class="mb-4 flex items-start gap-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 px-4 py-3">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <div>
                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-400" x-text="results.length + ' accounts found with this phone number'"></p>
                    <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">This may indicate duplicate or shared accounts. Review carefully before taking action.</p>
                </div>
            </div>

            {{-- No results --}}
            <div x-show="results.length === 0"
                class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] px-5 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No accounts found for this number</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Try a different format or check the number again</p>
            </div>

            {{-- Results table --}}
            <div x-show="results.length > 0"
                class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-x-auto">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Matching Accounts <span class="font-normal text-gray-400 dark:text-gray-500" x-text="'(' + results.length + ' found)'"></span>
                    </h3>
                </div>
                <table class="w-full">
                    <thead>
                        <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left">Type</th>
                            <th class="px-5 py-3 text-left">Name</th>
                            <th class="px-5 py-3 text-left">Phone</th>
                            <th class="px-5 py-3 text-left">Email</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-left">Joined</th>
                            <th class="px-5 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                        <template x-for="(acc, i) in results" :key="i">
                            <tr class="hover:bg-amber-50/50 dark:hover:bg-amber-500/5 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium"
                                        :class="acc.type === 'Merchant'
                                            ? 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/30'
                                            : 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30'"
                                        x-text="acc.type">
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-800 dark:text-white/90" x-text="acc.name"></td>
                                <td class="px-5 py-4 text-sm font-mono text-brand-600 dark:text-brand-400" x-text="acc.phone"></td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="acc.email"></td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium border"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30': acc.status === 'Active',
                                            'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30': acc.status === 'Blocked',
                                            'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700': acc.status === 'Inactive'
                                        }">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                            :class="{
                                                'bg-emerald-500': acc.status === 'Active',
                                                'bg-red-500': acc.status === 'Blocked',
                                                'bg-gray-400': acc.status === 'Inactive'
                                            }"></span>
                                        <span x-text="acc.status"></span>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500 whitespace-nowrap" x-text="acc.joined"></td>
                                <td class="px-5 py-4 text-center">
                                    <a :href="acc.link" class="inline-flex items-center gap-1 text-xs text-brand-600 dark:text-brand-400 hover:underline font-medium">
                                        View
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
