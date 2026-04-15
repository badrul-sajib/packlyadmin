@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="No Activity Orders" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Filters Row --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 px-5 sm:px-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Showing merchant orders in Pending, Approved, Processing, or Ready to Ship status with no status update for at least <strong class="text-gray-800 dark:text-white/90">3 days</strong>.</p>
                <p class="text-sm text-red-500 dark:text-red-400">Orders with payout are locked and cannot be edited or status-updated.</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Cutoff date: 07/04/2026 09:47 PM</p>
            </div>
            <div class="ml-auto">
                <div class="relative">
                    <input type="text" placeholder="Search by invoice/tracking/CN/merchant" class="rounded-lg border border-gray-200 bg-white px-4 py-2 pl-9 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 w-72" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1100px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-3 py-3 text-left font-medium rounded-l-lg w-[12%]">TrkID</th>
                            <th class="px-3 py-3 text-left font-medium w-[12%]">Merchant</th>
                            <th class="px-3 py-3 text-right font-medium w-[10%]">Total Amount</th>
                            <th class="px-3 py-3 text-right font-medium w-[10%]">Discount Amount</th>
                            <th class="px-3 py-3 text-right font-medium w-[10%]">Shipping Charge</th>
                            <th class="px-3 py-3 text-right font-medium w-[10%]">Total Price</th>
                            <th class="px-3 py-3 text-center font-medium w-[7%]">Total Items</th>
                            <th class="px-3 py-3 text-left font-medium w-[10%]">Date</th>
                            <th class="px-3 py-3 text-center font-medium w-[8%]">Status</th>
                            <th class="px-3 py-3 text-left font-medium w-[7%]">Note</th>
                            <th class="px-3 py-3 text-center font-medium rounded-r-lg w-[8%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $orders = [
                                    ['trkId' => 'TRKC388BD6', 'invoice' => 'INVB16C154', 'shop' => 'Venus Leather', 'phone' => '01329683111', 'amount' => '1,520.00', 'discount' => '0.00', 'charge' => '120.00', 'price' => '1,400.00', 'items' => 1, 'date' => '2025-07-22', 'time' => '12:15:09', 'status' => 'Pending', 'note' => 'No Note'],
                                    ['trkId' => 'TRKBF8599E', 'invoice' => 'INVC784014', 'shop' => 'Venus Leather', 'phone' => '01329683111', 'amount' => '970.00', 'discount' => '0.00', 'charge' => '120.00', 'price' => '850.00', 'items' => 1, 'date' => '2025-07-28', 'time' => '04:59:20', 'status' => 'Pending', 'note' => 'No Note'],
                                    ['trkId' => 'TRK63F3779', 'invoice' => 'INV7C5AC54', 'shop' => 'Venus Leather', 'phone' => '01329683111', 'amount' => '1,719.00', 'discount' => '0.00', 'charge' => '120.00', 'price' => '1,599.00', 'items' => 1, 'date' => '2025-07-30', 'time' => '15:04:46', 'status' => 'Pending', 'note' => 'No Note'],
                                    ['trkId' => 'TRKD07C140', 'invoice' => 'INV0437E23', 'shop' => 'Venus Leather', 'phone' => '01329683111', 'amount' => '970.00', 'discount' => '0.00', 'charge' => '120.00', 'price' => '850.00', 'items' => 1, 'date' => '2025-08-05', 'time' => '18:31:02', 'status' => 'Pending', 'note' => 'No Note'],
                                    ['trkId' => 'TRK1BF903D', 'invoice' => 'INV3IA8EAA', 'shop' => 'Venus Leather', 'phone' => '01329683111', 'amount' => '2,619.00', 'discount' => '0.00', 'charge' => '120.00', 'price' => '2,499.00', 'items' => 1, 'date' => '2025-09-11', 'time' => '11:22:17', 'status' => 'Pending', 'note' => 'No Note'],
                                    ['trkId' => 'TRK8BA3110', 'invoice' => 'INV7C77484', 'shop' => 'Venus Leather', 'phone' => '01329683111', 'amount' => '959.00', 'discount' => '0.00', 'charge' => '60.00', 'price' => '899.00', 'items' => 1, 'date' => '2025-09-15', 'time' => '02:16:03', 'status' => 'Pending', 'note' => 'No Note'],
                                    ['trkId' => 'TRK4F2A8C1', 'invoice' => 'INV9D3E5B7', 'shop' => 'Gadget World', 'phone' => '01712340987', 'amount' => '1,250.00', 'discount' => '50.00', 'charge' => '80.00', 'price' => '1,120.00', 'items' => 2, 'date' => '2025-09-20', 'time' => '09:45:30', 'status' => 'Processing', 'note' => 'No Note'],
                                    ['trkId' => 'TRK7C9D3E5', 'invoice' => 'INV2A8F1B4', 'shop' => 'Fashion Hub BD', 'phone' => '01912345678', 'amount' => '680.00', 'discount' => '0.00', 'charge' => '80.00', 'price' => '600.00', 'items' => 1, 'date' => '2025-10-02', 'time' => '14:20:55', 'status' => 'Approved', 'note' => 'No Note'],
                                    ['trkId' => 'TRK1E5B7D9', 'invoice' => 'INV6C4A2F8', 'shop' => 'TechZone Store', 'phone' => '01812345999', 'amount' => '445.00', 'discount' => '0.00', 'charge' => '50.00', 'price' => '395.00', 'items' => 1, 'date' => '2025-10-08', 'time' => '11:10:42', 'status' => 'Ready to Ship', 'note' => 'No Note'],
                                    ['trkId' => 'TRK3B8D2F6', 'invoice' => 'INV5E7C9A1', 'shop' => 'Style Studio', 'phone' => '01556782345', 'amount' => '890.00', 'discount' => '20.00', 'charge' => '80.00', 'price' => '790.00', 'items' => 3, 'date' => '2025-10-15', 'time' => '08:35:18', 'status' => 'Pending', 'note' => 'No Note'],
                                ];

                                $statusClasses = [
                                    'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-500',
                                    'Processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500',
                                    'Approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-500',
                                    'Ready to Ship' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-400',
                                ];
                            @endphp

                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- TrkID --}}
                                    <td class="px-3 py-3.5 w-[12%]">
                                        <div>
                                            <div class="flex items-center gap-1">
                                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $order['trkId'] }}</p>
                                                <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </button>
                                            </div>
                                            <a href="#" class="text-xs text-brand-500 hover:underline">{{ $order['invoice'] }}</a>
                                        </div>
                                    </td>
                                    {{-- Merchant --}}
                                    <td class="px-3 py-3.5 w-[12%]">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $order['shop'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['phone'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Total Amount --}}
                                    <td class="px-3 py-3.5 text-right w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['amount'] }}</span>
                                    </td>
                                    {{-- Discount Amount --}}
                                    <td class="px-3 py-3.5 text-right w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['discount'] }}</span>
                                    </td>
                                    {{-- Shipping Charge --}}
                                    <td class="px-3 py-3.5 text-right w-[10%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['charge'] }}</span>
                                    </td>
                                    {{-- Total Price --}}
                                    <td class="px-3 py-3.5 text-right w-[10%]">
                                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $order['price'] }}</span>
                                    </td>
                                    {{-- Total Items --}}
                                    <td class="px-3 py-3.5 text-center w-[7%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['items'] }}</span>
                                    </td>
                                    {{-- Date --}}
                                    <td class="px-3 py-3.5 w-[10%]">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order['date'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order['time'] }}</p>
                                        </div>
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-3 py-3.5 text-center w-[8%]">
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $order['status'] }}
                                        </span>
                                    </td>
                                    {{-- Note --}}
                                    <td class="px-3 py-3.5 w-[7%]">
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $order['note'] }}</span>
                                            <button type="button" class="text-amber-500 hover:text-amber-600">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    {{-- Action --}}
                                    <td class="px-3 py-3.5 text-center w-[8%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('orders.detail', $order['invoice']) }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 hover:underline dark:text-brand-400">View</a>
                                            <a href="#" class="text-sm font-medium text-amber-500 hover:text-amber-600 hover:underline dark:text-amber-400">Edit</a>
                                        </div>
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">10</span> of <span class="font-medium text-gray-700 dark:text-gray-300">156</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">16</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
