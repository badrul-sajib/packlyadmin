@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Call Configurator" />

    @php
        $callLogs = [
            ['id' => 1, 'agent' => 'Sabbir Ahmed', 'merchant' => 'StyleNest BD',    'phone' => '+880 1711-001122', 'duration' => '3m 42s', 'status' => 'Answered',  'date' => '10 Apr 2026, 11:24'],
            ['id' => 2, 'agent' => 'Roni Islam',   'merchant' => 'TechZone Shop',   'phone' => '+880 1812-223344', 'duration' => '1m 08s', 'status' => 'Answered',  'date' => '10 Apr 2026, 10:52'],
            ['id' => 3, 'agent' => 'Mim Akter',    'merchant' => 'FreshMart',       'phone' => '+880 1913-334455', 'duration' => '—',      'status' => 'Missed',    'date' => '09 Apr 2026, 16:30'],
            ['id' => 4, 'agent' => 'Sabbir Ahmed', 'merchant' => 'GreenLeaf Store', 'phone' => '+880 1614-445566', 'duration' => '7m 15s', 'status' => 'Answered',  'date' => '09 Apr 2026, 14:10'],
            ['id' => 5, 'agent' => 'Tanvir Hasan', 'merchant' => 'Electro Point',   'phone' => '+880 1515-556677', 'duration' => '—',      'status' => 'No Answer', 'date' => '08 Apr 2026, 09:45'],
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: config panels --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- SIP / VoIP Settings --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">SIP / VoIP Integration</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Connect your call center SIP server or VoIP provider</p>
                    </div>
                    <div class="ml-auto">
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Not Connected
                        </span>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">SIP Server / Host</label>
                        <input type="text" placeholder="sip.yourprovider.com"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">SIP Port</label>
                        <input type="text" placeholder="5060"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">SIP Username</label>
                        <input type="text" placeholder="sip_username"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">SIP Password</label>
                        <input type="password" placeholder="••••••••"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Caller ID (Outbound Number)</label>
                        <input type="text" placeholder="+880 9678 XXXXXX"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                    </div>
                    <div class="sm:col-span-2 flex justify-end gap-2">
                        <button type="button" class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                            Test Connection
                        </button>
                        <button type="button" class="rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-4 py-2 transition-colors">
                            Save Settings
                        </button>
                    </div>
                </div>
            </div>

            {{-- Auto-dial rules --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Auto-Dial Rules</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Configure automatic call triggers based on order or merchant events</p>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    @php
                        $rules = [
                            ['label' => 'Call merchant on new order above ৳5,000', 'enabled' => true],
                            ['label' => 'Auto-call on first order from new merchant', 'enabled' => false],
                            ['label' => 'Call on failed payout (3 retries)', 'enabled' => true],
                            ['label' => 'Escalation call if ticket is unresolved > 8h', 'enabled' => false],
                        ];
                    @endphp
                    @foreach ($rules as $rule)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $rule['label'] }}</span>
                            <button type="button"
                                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 {{ $rule['enabled'] ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-700' }}"
                                role="switch" aria-checked="{{ $rule['enabled'] ? 'true' : 'false' }}">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ $rule['enabled'] ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- WhatsApp / SMS --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">WhatsApp Business API</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Send automated and manual messages to merchants</p>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Business Account ID</label>
                        <input type="text" placeholder="WABA ID"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Access Token</label>
                        <input type="password" placeholder="••••••••••••"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button type="button" class="rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium px-4 py-2 transition-colors">
                            Save WhatsApp Config
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: call log --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent Call Log</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/60">
                @foreach ($callLogs as $log)
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 leading-tight">{{ $log['merchant'] }}</p>
                            <span class="flex-shrink-0 text-xs rounded-full px-2 py-0.5 font-medium
                                {{ $log['status'] === 'Answered'  ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}
                                {{ $log['status'] === 'Missed'    ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : '' }}
                                {{ $log['status'] === 'No Answer' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : '' }}">
                                {{ $log['status'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $log['phone'] }}</p>
                        <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                            <span>{{ $log['agent'] }}</span>
                            <span>·</span>
                            <span>{{ $log['duration'] }}</span>
                        </div>
                        <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">{{ $log['date'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                <a href="#" class="text-xs text-brand-600 dark:text-brand-400 hover:underline">View all call logs →</a>
            </div>
        </div>
    </div>
@endsection
