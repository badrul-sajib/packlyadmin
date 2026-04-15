@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Payment Methods" />

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @php
            $methods = [
                ['name' => 'Cash on Delivery', 'code' => 'COD', 'color' => 'emerald', 'enabled' => true, 'desc' => 'Accept payment when order is delivered', 'fields' => []],
                ['name' => 'bKash', 'code' => 'bKash', 'color' => 'pink', 'enabled' => true, 'desc' => 'Mobile payment via bKash', 'fields' => ['App Key', 'App Secret', 'Username', 'Password']],
                ['name' => 'Nagad', 'code' => 'Nagad', 'color' => 'orange', 'enabled' => true, 'desc' => 'Mobile payment via Nagad', 'fields' => ['Merchant ID', 'Merchant Key', 'API URL']],
                ['name' => 'Rocket', 'code' => 'Rocket', 'color' => 'violet', 'enabled' => false, 'desc' => 'Mobile payment via Rocket (DBBL)', 'fields' => ['Merchant ID', 'API Key']],
                ['name' => 'SSL Commerz', 'code' => 'SSL', 'color' => 'blue', 'enabled' => true, 'desc' => 'Online payment gateway (Card, MFS)', 'fields' => ['Store ID', 'Store Password', 'API URL']],
                ['name' => 'Bank Transfer', 'code' => 'Bank', 'color' => 'gray', 'enabled' => false, 'desc' => 'Manual bank transfer payment', 'fields' => ['Bank Name', 'Account Number', 'Routing Number']],
            ];
            $colorMap = [
                'emerald' => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400', 'border' => 'border-emerald-200 dark:border-emerald-500/20'],
                'pink' => ['bg' => 'bg-pink-500', 'light' => 'bg-pink-50 dark:bg-pink-500/10', 'text' => 'text-pink-600 dark:text-pink-400', 'border' => 'border-pink-200 dark:border-pink-500/20'],
                'orange' => ['bg' => 'bg-orange-500', 'light' => 'bg-orange-50 dark:bg-orange-500/10', 'text' => 'text-orange-600 dark:text-orange-400', 'border' => 'border-orange-200 dark:border-orange-500/20'],
                'violet' => ['bg' => 'bg-violet-500', 'light' => 'bg-violet-50 dark:bg-violet-500/10', 'text' => 'text-violet-600 dark:text-violet-400', 'border' => 'border-violet-200 dark:border-violet-500/20'],
                'blue' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-50 dark:bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400', 'border' => 'border-blue-200 dark:border-blue-500/20'],
                'gray' => ['bg' => 'bg-gray-500', 'light' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400', 'border' => 'border-gray-200 dark:border-gray-700'],
            ];
        @endphp

        @foreach ($methods as $method)
            @php $c = $colorMap[$method['color']]; @endphp
            <div class="rounded-2xl border {{ $method['enabled'] ? $c['border'] : 'border-gray-200 dark:border-gray-800 opacity-60' }} bg-white dark:bg-white/[0.03] overflow-hidden" x-data="{ expanded: false }">
                <div class="flex items-center justify-between px-5 py-4 {{ $c['light'] }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center">
                            <span class="text-xs font-bold text-white">{{ $method['code'] }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold {{ $c['text'] }}">{{ $method['name'] }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $method['desc'] }}</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" class="sr-only peer" {{ $method['enabled'] ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                @if(count($method['fields']) > 0)
                    <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">
                        <button @click="expanded = !expanded" type="button" class="flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 transition-colors">
                            <svg class="w-3.5 h-3.5 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            <span x-text="expanded ? 'Hide Credentials' : 'Configure Credentials'"></span>
                        </button>
                    </div>
                    <div x-show="expanded" x-collapse>
                        <div class="px-5 pb-5 space-y-3">
                            @foreach ($method['fields'] as $field)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $field }}</label>
                                    <input type="{{ str_contains(strtolower($field), 'password') || str_contains(strtolower($field), 'secret') || str_contains(strtolower($field), 'key') ? 'password' : 'text' }}" value="" placeholder="Enter {{ strtolower($field) }}" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                                </div>
                            @endforeach
                            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-xs font-medium text-white hover:bg-brand-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Save
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endsection
