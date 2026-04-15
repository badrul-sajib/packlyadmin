@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Account Dashboard Overview" />

    {{-- Account Summary Header --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mb-6">
        <div class="flex items-center justify-between px-5 py-4 sm:px-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Account Summary</h2>
            <x-common.date-range-picker id="dashboardDateRange" />
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        {{-- Total Paid --}}
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Paid</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">0.00</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">0 Transactions</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/10">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Payable --}}
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Payable</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">596,207.60</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">15 Pending Requests</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-50 dark:bg-yellow-500/10">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
            </div>
        </div>

        {{-- Pending Orders --}}
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Orders</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">51,899.00</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">86 Orders</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-50 dark:bg-red-500/10">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Delivered/Corrected --}}
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Delivered/Corrected</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">0.00</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">0 Orders</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Second Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        {{-- Total Revenue --}}
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">0.00</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/10">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Charges --}}
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Charges</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">0.00</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-500/10">
                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                </div>
            </div>
        </div>

        {{-- Total COD --}}
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total COD Collection</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">0.00</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-500/10">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                </div>
            </div>
        </div>

        {{-- SFC Payments --}}
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">SFC Payments</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">0.00</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-violet-50 dark:bg-violet-500/10">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Quick Access</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 px-5 py-5 sm:px-6">
            <a href="{{ route('accounts.payables') }}" class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-center hover:bg-emerald-50 hover:border-emerald-200 transition-colors dark:border-gray-700 dark:bg-white/[0.02] dark:hover:bg-emerald-500/10 dark:hover:border-emerald-500/30">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Payables</span>
            </a>
            <a href="{{ route('accounts.payouts') }}" class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-center hover:bg-emerald-50 hover:border-emerald-200 transition-colors dark:border-gray-700 dark:bg-white/[0.02] dark:hover:bg-emerald-500/10 dark:hover:border-emerald-500/30">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-500/20">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Payouts</span>
            </a>
            <a href="{{ route('accounts.today-payments') }}" class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-center hover:bg-emerald-50 hover:border-emerald-200 transition-colors dark:border-gray-700 dark:bg-white/[0.02] dark:hover:bg-emerald-500/10 dark:hover:border-emerald-500/30">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-500/20">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Today's Payments</span>
            </a>
            <a href="{{ route('accounts.sfc-payments') }}" class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-center hover:bg-emerald-50 hover:border-emerald-200 transition-colors dark:border-gray-700 dark:bg-white/[0.02] dark:hover:bg-emerald-500/10 dark:hover:border-emerald-500/30">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-500/20">
                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">SFC Payments</span>
            </a>
            <a href="{{ route('accounts.ssl-payments') }}" class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-center hover:bg-emerald-50 hover:border-emerald-200 transition-colors dark:border-gray-700 dark:bg-white/[0.02] dark:hover:bg-emerald-500/10 dark:hover:border-emerald-500/30">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-500/20">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">SSL Payments</span>
            </a>
            <a href="{{ route('accounts.merchant-balances') }}" class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-center hover:bg-emerald-50 hover:border-emerald-200 transition-colors dark:border-gray-700 dark:bg-white/[0.02] dark:hover:bg-emerald-500/10 dark:hover:border-emerald-500/30">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Merchant Balances</span>
            </a>
        </div>
    </div>
@endsection
