@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Settings" />

    <div class="flex gap-6" x-data="{ activeTab: 'general' }">
        {{-- Sidebar Navigation --}}
        <div class="w-56 shrink-0 hidden xl:block">
            <div class="sticky top-24 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                <nav class="py-2">
                    @php
                        $tabs = [
                            ['key' => 'general', 'label' => 'General', 'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                            ['key' => 'delivery', 'label' => 'Delivery', 'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12'],
                            ['key' => 'merchant', 'label' => 'Merchant', 'icon' => 'M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72'],
                            ['key' => 'google', 'label' => 'Google oAuth', 'icon' => 'M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z'],
                            ['key' => 'payout', 'label' => 'Payout', 'icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z'],
                            ['key' => 'website', 'label' => 'Website', 'icon' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418'],
                            ['key' => 'order', 'label' => 'Order', 'icon' => 'M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z'],
                            ['key' => 'contact', 'label' => 'Contact Info', 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75'],
                            ['key' => 'help', 'label' => 'Help Center', 'icon' => 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z'],
                            ['key' => 'social', 'label' => 'Social Links', 'icon' => 'M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z'],
                        ];
                    @endphp
                    @foreach ($tabs as $tab)
                        <button @click="activeTab = '{{ $tab['key'] }}'" type="button"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors"
                            :class="activeTab === '{{ $tab['key'] }}'
                                ? 'text-brand-600 bg-brand-50 font-medium border-r-2 border-brand-500 dark:text-brand-400 dark:bg-brand-500/10'
                                : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]'">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}"/></svg>
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="flex-1 min-w-0">
            {{-- Mobile Tab Selector --}}
            <div class="xl:hidden mb-6">
                <select @change="activeTab = $event.target.value" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300">
                    <option value="general">General</option>
                    <option value="delivery">Delivery</option>
                    <option value="merchant">Merchant Settings</option>
                    <option value="google">Google oAuth</option>
                    <option value="payout">Payout Settings</option>
                    <option value="website">Website</option>
                    <option value="order">Order Settings</option>
                    <option value="contact">Contact Information</option>
                    <option value="help">Help Center</option>
                    <option value="social">Social Links</option>
                </select>
            </div>

            {{-- General Tab --}}
            <div x-show="activeTab === 'general'" class="space-y-6">
                {{-- Site Identity --}}
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Site Identity</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Basic information about your platform</p>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Feed URL</label>
                            <div class="flex items-center gap-2">
                                <input type="text" value="https://packly-local.s3.ap-southeast-1.amazonaws.com/sitemaps/product_feed.xml" readonly class="flex-1 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 font-mono dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                                <button type="button" class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-400 hover:text-emerald-500 hover:border-emerald-300 transition-colors dark:border-gray-700" title="Copy">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Site Name</label>
                            <input type="text" value="Packly Admin" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Copyright Text</label>
                            <input type="text" value="&copy; 2025 Packly All rights reserved." class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Cache Time <span class="text-xs text-gray-400 font-normal">(minutes)</span></label>
                                <input type="number" value="" placeholder="e.g. 60" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nearby Shop Radius <span class="text-xs text-gray-400 font-normal">(KM)</span></label>
                                <input type="number" value="1" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Branding --}}
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Branding</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Logo, favicon and visual identity</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Site Logo</label>
                                <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-6 text-center hover:border-brand-300 transition-colors">
                                    <div class="w-20 h-20 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3 overflow-hidden">
                                        <img src="/images/logo/packly-logo.svg" alt="Logo" class="w-16 h-auto">
                                    </div>
                                    <label class="inline-flex items-center gap-2 rounded-lg bg-brand-50 px-4 py-2 text-xs font-medium text-brand-600 hover:bg-brand-100 cursor-pointer transition-colors dark:bg-brand-500/10 dark:text-brand-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        Upload Logo
                                        <input type="file" class="hidden" accept="image/*" />
                                    </label>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2">PNG, JPG, SVG. Max 2MB</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Site Favicon</label>
                                <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-6 text-center hover:border-brand-300 transition-colors">
                                    <div class="w-16 h-16 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3 overflow-hidden">
                                        <img src="/images/logo/packly-icon.svg" alt="Favicon" class="w-8 h-8">
                                    </div>
                                    <label class="inline-flex items-center gap-2 rounded-lg bg-brand-50 px-4 py-2 text-xs font-medium text-brand-600 hover:bg-brand-100 cursor-pointer transition-colors dark:bg-brand-500/10 dark:text-brand-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        Upload Favicon
                                        <input type="file" class="hidden" accept="image/*" />
                                    </label>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2">ICO, PNG. 32x32 recommended</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- App Store Settings --}}
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">App Store Settings</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Mobile app links and stats</p>
                    </div>
                    <div class="p-6">
                        {{-- Play Store --}}
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02] mb-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M3 20.5V3.5c0-.65.35-1.22.88-1.52L12 8l-8.12 6.02A1.745 1.745 0 003 15.5v5zm17-8.5L15 8l5-4-2.12 4.02L20 12zm-5 4l5-4-5-4v8zM3.88 2C3.35 2.28 3 2.85 3 3.5l8.88 5.5L3.88 2zM12 8L3.88 2l8.12 6z"/></svg>
                            </div>
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Play Store Link</label>
                                    <input type="url" value="https://play.google.com/store/apps/details?id=com.packly.app&pcampaignid=web_share" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Downloads</label>
                                    <input type="number" value="10000" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rating</label>
                                    <input type="number" step="0.1" value="4.8" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">QR Scanner</label>
                                    <label class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-500 hover:bg-gray-50 cursor-pointer transition-colors dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        Upload
                                        <input type="file" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- App Store --}}
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02]">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                            </div>
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">App Store Link</label>
                                    <input type="url" value="https://apps.apple.com/us/app/packly/id6754810452" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Downloads</label>
                                    <input type="number" value="10000" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rating</label>
                                    <input type="number" step="0.1" value="4.8" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Save --}}
                <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Save your changes before switching tabs.</p>
                    <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Save Changes
                    </button>
                </div>
            </div>

            {{-- Other tabs --}}
            <template x-for="tab in ['delivery','merchant','google','payout','website','order','contact','help','social']" :key="tab">
                <div x-show="activeTab === tab" style="display:none;">
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="text-center py-16 px-6">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.204-.107-.397.165-.71.505-.78.929l-.15.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 mb-1" x-text="tab.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + ' Settings'"></h4>
                            <p class="text-xs text-gray-400 dark:text-gray-500">This section will be configured here.</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection
