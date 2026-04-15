@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Locations" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeTab: 'districts' }">
        {{-- Tabs --}}
        <div class="flex items-center gap-2 mb-5 px-5 sm:px-6">
            <button @click="activeTab = 'districts'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeTab === 'districts' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Districts
            </button>
            <button @click="activeTab = 'areas'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeTab === 'areas' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Areas
            </button>
            <button @click="activeTab = 'zones'" type="button"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="activeTab === 'zones' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                Zones
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
                <input type="text" placeholder="Search by name" class="w-full rounded-lg border border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
            </div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span x-text="activeTab === 'districts' ? 'Add District' : activeTab === 'areas' ? 'Add Area' : 'Add Zone'"></span>
            </button>
        </div>

        {{-- Districts Table --}}
        <div x-show="activeTab === 'districts'">
            <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
                <div class="min-w-[800px]">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-emerald-500 text-white text-sm">
                                <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[5%]">SL</th>
                                <th class="px-4 py-3 text-left font-medium w-[25%]">District Name</th>
                                <th class="px-4 py-3 text-left font-medium w-[15%]">Division</th>
                                <th class="px-4 py-3 text-center font-medium w-[10%]">Areas</th>
                                <th class="px-4 py-3 text-right font-medium w-[15%] whitespace-nowrap">Delivery Charge</th>
                                <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                                <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @php
                                    $districts = [
                                        ['name' => 'Dhaka', 'division' => 'Dhaka', 'areas' => 45, 'charge' => '60.00', 'status' => 'Active'],
                                        ['name' => 'Gazipur', 'division' => 'Dhaka', 'areas' => 12, 'charge' => '80.00', 'status' => 'Active'],
                                        ['name' => 'Narayanganj', 'division' => 'Dhaka', 'areas' => 8, 'charge' => '80.00', 'status' => 'Active'],
                                        ['name' => 'Chattogram', 'division' => 'Chattogram', 'areas' => 32, 'charge' => '120.00', 'status' => 'Active'],
                                        ['name' => 'Comilla', 'division' => 'Chattogram', 'areas' => 14, 'charge' => '120.00', 'status' => 'Active'],
                                        ['name' => 'Rajshahi', 'division' => 'Rajshahi', 'areas' => 18, 'charge' => '130.00', 'status' => 'Active'],
                                        ['name' => 'Khulna', 'division' => 'Khulna', 'areas' => 15, 'charge' => '130.00', 'status' => 'Active'],
                                        ['name' => 'Sylhet', 'division' => 'Sylhet', 'areas' => 10, 'charge' => '150.00', 'status' => 'Active'],
                                        ['name' => 'Rangpur', 'division' => 'Rangpur', 'areas' => 9, 'charge' => '140.00', 'status' => 'Active'],
                                        ['name' => 'Barishal', 'division' => 'Barishal', 'areas' => 7, 'charge' => '150.00', 'status' => 'Inactive'],
                                    ];
                                @endphp
                                @foreach ($districts as $index => $district)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                        <td class="px-4 py-4 w-[5%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                        <td class="px-4 py-4 w-[25%]"><span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $district['name'] }}</span></td>
                                        <td class="px-4 py-4 w-[15%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $district['division'] }}</span></td>
                                        <td class="px-4 py-4 text-center w-[10%]"><span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $district['areas'] }}</span></td>
                                        <td class="px-4 py-4 text-right w-[15%]"><span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">৳{{ $district['charge'] }}</span></td>
                                        <td class="px-4 py-4 text-center w-[10%]">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $district['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : 'bg-gray-100 text-gray-500 border border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $district['status'] === 'Active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                {{ $district['status'] }}
                                            </span>
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

        {{-- Areas Table --}}
        <div x-show="activeTab === 'areas'" style="display:none;">
            <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
                <div class="min-w-[800px]">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-emerald-500 text-white text-sm">
                                <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[5%]">SL</th>
                                <th class="px-4 py-3 text-left font-medium w-[25%]">Area Name</th>
                                <th class="px-4 py-3 text-left font-medium w-[15%]">District</th>
                                <th class="px-4 py-3 text-left font-medium w-[12%]">Post Code</th>
                                <th class="px-4 py-3 text-right font-medium w-[13%] whitespace-nowrap">Delivery Charge</th>
                                <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                                <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @php
                                    $areas = [
                                        ['name' => 'Mirpur', 'district' => 'Dhaka', 'post_code' => '1216', 'charge' => '60.00', 'status' => 'Active'],
                                        ['name' => 'Dhanmondi', 'district' => 'Dhaka', 'post_code' => '1205', 'charge' => '60.00', 'status' => 'Active'],
                                        ['name' => 'Uttara', 'district' => 'Dhaka', 'post_code' => '1230', 'charge' => '60.00', 'status' => 'Active'],
                                        ['name' => 'Gulshan', 'district' => 'Dhaka', 'post_code' => '1212', 'charge' => '60.00', 'status' => 'Active'],
                                        ['name' => 'Tongi', 'district' => 'Gazipur', 'post_code' => '1710', 'charge' => '80.00', 'status' => 'Active'],
                                        ['name' => 'Board Bazar', 'district' => 'Gazipur', 'post_code' => '1704', 'charge' => '80.00', 'status' => 'Active'],
                                        ['name' => 'Agrabad', 'district' => 'Chattogram', 'post_code' => '4100', 'charge' => '120.00', 'status' => 'Active'],
                                        ['name' => 'Nasirabad', 'district' => 'Chattogram', 'post_code' => '4219', 'charge' => '120.00', 'status' => 'Inactive'],
                                    ];
                                @endphp
                                @foreach ($areas as $index => $area)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                        <td class="px-4 py-4 w-[5%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                        <td class="px-4 py-4 w-[25%]"><span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $area['name'] }}</span></td>
                                        <td class="px-4 py-4 w-[15%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $area['district'] }}</span></td>
                                        <td class="px-4 py-4 w-[12%]"><span class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $area['post_code'] }}</span></td>
                                        <td class="px-4 py-4 text-right w-[13%]"><span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">৳{{ $area['charge'] }}</span></td>
                                        <td class="px-4 py-4 text-center w-[10%]">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $area['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30' : 'bg-gray-100 text-gray-500 border border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $area['status'] === 'Active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>{{ $area['status'] }}
                                            </span>
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

        {{-- Zones Table --}}
        <div x-show="activeTab === 'zones'" style="display:none;">
            <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
                <div class="min-w-[800px]">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-emerald-500 text-white text-sm">
                                <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[5%]">SL</th>
                                <th class="px-4 py-3 text-left font-medium w-[25%]">Zone Name</th>
                                <th class="px-4 py-3 text-center font-medium w-[12%]">Districts</th>
                                <th class="px-4 py-3 text-right font-medium w-[15%] whitespace-nowrap">Base Charge</th>
                                <th class="px-4 py-3 text-right font-medium w-[13%] whitespace-nowrap">Extra KG</th>
                                <th class="px-4 py-3 text-center font-medium w-[10%]">Status</th>
                                <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[10%]">Action</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @php
                                    $zones = [
                                        ['name' => 'Inside Dhaka', 'districts' => 3, 'charge' => '60.00', 'extra' => '15.00', 'status' => 'Active'],
                                        ['name' => 'Dhaka Suburb', 'districts' => 5, 'charge' => '80.00', 'extra' => '20.00', 'status' => 'Active'],
                                        ['name' => 'Outside Dhaka', 'districts' => 56, 'charge' => '120.00', 'extra' => '25.00', 'status' => 'Active'],
                                    ];
                                @endphp
                                @foreach ($zones as $index => $zone)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                        <td class="px-4 py-4 w-[5%]"><span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span></td>
                                        <td class="px-4 py-4 w-[25%]"><span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $zone['name'] }}</span></td>
                                        <td class="px-4 py-4 text-center w-[12%]"><span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $zone['districts'] }}</span></td>
                                        <td class="px-4 py-4 text-right w-[15%]"><span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">৳{{ $zone['charge'] }}</span></td>
                                        <td class="px-4 py-4 text-right w-[13%]"><span class="text-sm text-gray-600 dark:text-gray-400">৳{{ $zone['extra'] }}/kg</span></td>
                                        <td class="px-4 py-4 text-center w-[10%]">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $zone['status'] }}
                                            </span>
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

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 sm:px-6 pt-4 pb-2 border-t border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">10</span> of <span class="font-medium text-gray-700 dark:text-gray-300">64</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">7</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
            </nav>
        </div>
    </div>
@endsection
