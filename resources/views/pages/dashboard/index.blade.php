@extends('layouts.app')

@section('content')
  <div class="grid grid-cols-12 gap-4 md:gap-6">

    {{-- Welcome + Metric Cards --}}
    <div class="col-span-12">
      <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
          <div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">
              Welcome back, Sajib! <span>👋</span>
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Here's what's happening with Packly today.</p>
          </div>
          <x-common.date-range-picker id="dashboardRange" />
        </div>

        {{-- Metric Cards Row 1 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
      {{-- Merchant Orders --}}
      <div class="rounded-xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
            <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
          </div>
          <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
            +12.5%
          </span>
        </div>
        <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">18,449</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Orders</p>
      </div>

      {{-- Total Revenue --}}
      <div class="rounded-xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
            +8.2%
          </span>
        </div>
        <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">৳1,24,580</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Revenue</p>
      </div>

      {{-- Pending Payouts --}}
      <div class="rounded-xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-yellow-50 dark:bg-yellow-500/10">
            <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <span class="inline-flex items-center gap-1 text-xs font-medium text-yellow-600 dark:text-yellow-400">
            6 pending
          </span>
        </div>
        <h4 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">৳4,008</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Pending Payouts</p>
      </div>

      {{-- Total Payable --}}
      <div class="rounded-xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10">
            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
          </div>
          <span class="inline-flex items-center gap-1 text-xs font-medium text-red-500 dark:text-red-400">
            15 requests
          </span>
        </div>
        <h4 class="text-2xl font-bold text-red-600 dark:text-red-400">৳5,96,207</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Payable</p>
      </div>
        </div>

        {{-- Metric Cards Row 2 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      {{-- Total Merchants --}}
      <div class="rounded-xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10">
            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z"/></svg>
          </div>
        </div>
        <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">7,013</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Merchants</p>
      </div>

      {{-- Active Products --}}
      <div class="rounded-xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21"/></svg>
          </div>
        </div>
        <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">44,856</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Active Products</p>
      </div>

      {{-- Total Customers --}}
      <div class="rounded-xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10">
            <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
          </div>
        </div>
        <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">43,159</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Customers</p>
      </div>

      {{-- Request Pending --}}
      <div class="rounded-xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10">
            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
          </div>
          <a href="{{ url('/merchants/update-requests') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">View all</a>
        </div>
        <h4 class="text-2xl font-bold text-amber-600 dark:text-amber-400">924</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Requests Pending</p>
      </div>
        </div>
      </div>
    </div>

    {{-- Quick Search --}}
    <div class="col-span-12">
      <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">
              Search Everything Here
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Here's what's happening with Packly today.</p>
          </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
          <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02]">
            <span class="flex items-center justify-center w-10 h-10 shrink-0 text-gray-400 border-r border-gray-200 dark:border-gray-700">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"/></svg>
            </span>
            <input type="text" placeholder="Consignment ID" class="flex-1 bg-transparent px-3 py-2 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 outline-none border-none focus:ring-0" />
            <button class="shrink-0 px-3 py-2 text-sm font-medium text-brand-500 hover:text-brand-600 border-l border-gray-200 dark:border-gray-700">Go</button>
          </div>
          <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02]">
            <span class="flex items-center justify-center w-10 h-10 shrink-0 text-gray-400 border-r border-gray-200 dark:border-gray-700">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </span>
            <input type="text" placeholder="Invoice ID" class="flex-1 bg-transparent px-3 py-2 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 outline-none border-none focus:ring-0" />
            <button class="shrink-0 px-3 py-2 text-sm font-medium text-brand-500 hover:text-brand-600 border-l border-gray-200 dark:border-gray-700">Go</button>
          </div>
          <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02]">
            <span class="flex items-center justify-center w-10 h-10 shrink-0 text-gray-400 border-r border-gray-200 dark:border-gray-700">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72"/></svg>
            </span>
            <input type="text" placeholder="Merchant ID" class="flex-1 bg-transparent px-3 py-2 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 outline-none border-none focus:ring-0" />
            <button class="shrink-0 px-3 py-2 text-sm font-medium text-brand-500 hover:text-brand-600 border-l border-gray-200 dark:border-gray-700">Go</button>
          </div>
          <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02]">
            <span class="flex items-center justify-center w-10 h-10 shrink-0 text-gray-400 border-r border-gray-200 dark:border-gray-700">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            </span>
            <input type="text" placeholder="Payment ID" class="flex-1 bg-transparent px-3 py-2 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 outline-none border-none focus:ring-0" />
            <button class="shrink-0 px-3 py-2 text-sm font-medium text-brand-500 hover:text-brand-600 border-l border-gray-200 dark:border-gray-700">Go</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Order Status Chart + Today's Snapshot --}}
    <div class="col-span-12 xl:col-span-8">
      <x-dashboard.order-status-chart />
    </div>

    {{-- Today's Snapshot --}}
    <div class="col-span-12 xl:col-span-4">
      <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 h-full">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Today's Snapshot</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-500 text-white">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Delivered</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">42 orders</p>
              </div>
            </div>
            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">৳28,450</span>
          </div>

          <div class="flex items-center justify-between p-3 rounded-xl bg-yellow-50 dark:bg-yellow-500/10">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-500 text-white">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Pending</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">86 orders</p>
              </div>
            </div>
            <span class="text-sm font-bold text-yellow-600 dark:text-yellow-400">৳51,899</span>
          </div>

          <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50 dark:bg-blue-500/10">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-500 text-white">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Processing</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">23 orders</p>
              </div>
            </div>
            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">৳12,340</span>
          </div>

          <div class="flex items-center justify-between p-3 rounded-xl bg-red-50 dark:bg-red-500/10">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-red-500 text-white">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Cancelled</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">8 orders</p>
              </div>
            </div>
            <span class="text-sm font-bold text-red-600 dark:text-red-400">৳4,120</span>
          </div>

          <div class="flex items-center justify-between p-3 rounded-xl bg-orange-50 dark:bg-orange-500/10">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-orange-500 text-white">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Returned</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">5 orders</p>
              </div>
            </div>
            <span class="text-sm font-bold text-orange-600 dark:text-orange-400">৳3,200</span>
          </div>

          <div class="flex items-center justify-between p-3 rounded-xl bg-violet-50 dark:bg-violet-500/10">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-violet-500 text-white">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Spam Flagged</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">3 orders</p>
              </div>
            </div>
            <span class="text-sm font-bold text-violet-600 dark:text-violet-400">৳1,850</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Recent Orders --}}
    <div class="col-span-12">
      <x-dashboard.recent-orders />
    </div>

    {{-- Top Merchants --}}
    <div class="col-span-12">
      <x-dashboard.top-merchants />
    </div>
  </div>
@endsection
