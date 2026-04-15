@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="SFC Payments" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Refresh & Date Filter --}}
        <div class="flex items-center justify-between gap-3 mb-5 px-5 sm:px-6">
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors">
                Refresh
            </button>
            <x-common.date-range-picker id="sfcDateRange" />
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar px-5 sm:px-6">
            <div class="min-w-[1200px]">
                <table class="w-full">
                    <thead>
                        <tr class="bg-emerald-500 text-white text-sm">
                            <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[4%]">SL</th>
                            <th class="px-4 py-3 text-left font-medium w-[10%]">Payment ID</th>
                            <th class="px-4 py-3 text-left font-medium w-[8%]">Method</th>
                            <th class="px-4 py-3 text-right font-medium w-[8%]">Due Bills</th>
                            <th class="px-4 py-3 text-right font-medium w-[8%]">Paid Bills</th>
                            <th class="px-4 py-3 text-right font-medium w-[8%]">Charges</th>
                            <th class="px-4 py-3 text-right font-medium w-[9%]">Total</th>
                            <th class="px-4 py-3 text-center font-medium w-[7%]">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-[13%]">Created At</th>
                            <th class="px-4 py-3 text-left font-medium w-[13%]">Ready At</th>
                            <th class="px-4 py-3 text-left font-medium w-[7%]">Paid At</th>
                            <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[5%]">Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $payments = [
                                    ['id' => 'SFC-27292356', 'method' => 'Cash', 'due_bills' => '5950.00', 'paid_bills' => '0.00', 'charges' => '465.00', 'total' => '46060.00', 'status' => 'ready', 'created_at' => '18/12/2025 03:08 AM', 'ready_at' => '18/12/2025 10:20 AM', 'paid_at' => null],
                                    ['id' => 'SFC-27254058', 'method' => 'Cash', 'due_bills' => '8921.00', 'paid_bills' => '0.00', 'charges' => '439.00', 'total' => '43487.00', 'status' => 'ready', 'created_at' => '15/12/2025 04:41 PM', 'ready_at' => '15/12/2025 04:43 PM', 'paid_at' => null],
                                    ['id' => 'SFC-27173159', 'method' => 'Cash', 'due_bills' => '2920.00', 'paid_bills' => '0.00', 'charges' => '186.00', 'total' => '18376.00', 'status' => 'ready', 'created_at' => '10/12/2025 07:07 PM', 'ready_at' => '10/12/2025 07:49 PM', 'paid_at' => null],
                                    ['id' => 'SFC-27154260', 'method' => 'Cash', 'due_bills' => '2820.00', 'paid_bills' => '0.00', 'charges' => '116.00', 'total' => '11480.00', 'status' => 'ready', 'created_at' => '09/12/2025 04:04 PM', 'ready_at' => '09/12/2025 04:05 PM', 'paid_at' => null],
                                    ['id' => 'SFC-27135584', 'method' => 'Cash', 'due_bills' => '2240.00', 'paid_bills' => '0.00', 'charges' => '159.00', 'total' => '15730.00', 'status' => 'ready', 'created_at' => '08/12/2025 12:38 PM', 'ready_at' => '08/12/2025 12:38 PM', 'paid_at' => null],
                                    ['id' => 'SFC-27122582', 'method' => 'Cash', 'due_bills' => '11201.00', 'paid_bills' => '0.00', 'charges' => '522.00', 'total' => '51694.00', 'status' => 'ready', 'created_at' => '07/12/2025 09:31 PM', 'ready_at' => '08/12/2025 10:37 AM', 'paid_at' => null],
                                    ['id' => 'SFC-27060375', 'method' => 'Cash', 'due_bills' => '1680.00', 'paid_bills' => '0.00', 'charges' => '63.00', 'total' => '6264.00', 'status' => 'ready', 'created_at' => '03/12/2025 08:46 PM', 'ready_at' => '04/12/2025 09:59 AM', 'paid_at' => null],
                                    ['id' => 'SFC-27044230', 'method' => 'Cash', 'due_bills' => '880.00', 'paid_bills' => '0.00', 'charges' => '51.00', 'total' => '4983.00', 'status' => 'ready', 'created_at' => '02/12/2025 09:11 PM', 'ready_at' => '03/12/2025 09:43 AM', 'paid_at' => null],
                                    ['id' => 'SFC-27026556', 'method' => 'Cash', 'due_bills' => '11350.00', 'paid_bills' => '0.00', 'charges' => '381.00', 'total' => '37709.00', 'status' => 'ready', 'created_at' => '01/12/2025 03:12 PM', 'ready_at' => '01/12/2025 03:16 PM', 'paid_at' => null],
                                ];
                            @endphp

                            @foreach ($payments as $index => $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    {{-- SL --}}
                                    <td class="px-4 py-4 w-[4%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                    </td>
                                    {{-- Payment ID --}}
                                    <td class="px-4 py-4 w-[10%]">
                                        <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment['id'] }}</span>
                                    </td>
                                    {{-- Method --}}
                                    <td class="px-4 py-4 w-[8%]">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $payment['method'] }}</span>
                                    </td>
                                    {{-- Due Bills --}}
                                    <td class="px-4 py-4 text-right w-[8%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $payment['due_bills'] }}</span>
                                    </td>
                                    {{-- Paid Bills --}}
                                    <td class="px-4 py-4 text-right w-[8%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $payment['paid_bills'] }}</span>
                                    </td>
                                    {{-- Charges --}}
                                    <td class="px-4 py-4 text-right w-[8%]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $payment['charges'] }}</span>
                                    </td>
                                    {{-- Total --}}
                                    <td class="px-4 py-4 text-right w-[9%]">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $payment['total'] }}</span>
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-4 py-4 text-center w-[7%]">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $payment['status'] === 'ready' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}
                                            {{ $payment['status'] === 'paid' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : '' }}
                                            {{ $payment['status'] === 'pending' ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' : '' }}
                                        ">
                                            {{ $payment['status'] }}
                                        </span>
                                    </td>
                                    {{-- Created At --}}
                                    <td class="px-4 py-4 w-[13%]">
                                        @if($payment['created_at'])
                                            @php
                                                $createdParts = explode(' ', $payment['created_at'], 2);
                                            @endphp
                                            <div>
                                                <p class="text-sm text-gray-800 dark:text-white/90">{{ $createdParts[0] ?? '' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $createdParts[1] ?? '' }}</p>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    {{-- Ready At --}}
                                    <td class="px-4 py-4 w-[13%]">
                                        @if($payment['ready_at'])
                                            @php
                                                $readyParts = explode(' ', $payment['ready_at'], 2);
                                            @endphp
                                            <div>
                                                <p class="text-sm text-gray-800 dark:text-white/90">{{ $readyParts[0] ?? '' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $readyParts[1] ?? '' }}</p>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    {{-- Paid At --}}
                                    <td class="px-4 py-4 w-[7%]">
                                        @if($payment['paid_at'])
                                            @php
                                                $paidParts = explode(' ', $payment['paid_at'], 2);
                                            @endphp
                                            <div>
                                                <p class="text-sm text-gray-800 dark:text-white/90">{{ $paidParts[0] ?? '' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $paidParts[1] ?? '' }}</p>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    {{-- Action --}}
                                    <td class="px-4 py-4 text-center w-[5%]">
                                        <a href="#" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300">
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
                Showing <span class="font-medium text-gray-700 dark:text-gray-300">1</span> to <span class="font-medium text-gray-700 dark:text-gray-300">9</span> of <span class="font-medium text-gray-700 dark:text-gray-300">142</span> results
            </p>
            <nav class="flex items-center gap-1">
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium bg-brand-500 text-white">1</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">2</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">3</button>
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.05]">15</button>
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.05]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div>
@endsection
