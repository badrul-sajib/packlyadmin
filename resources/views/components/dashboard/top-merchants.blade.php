<div class="rounded-2xl border border-gray-200 bg-white px-5 pt-5 pb-3 sm:px-6 sm:pt-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            <span class="mr-1">👑</span> Top Merchants
        </h3>
        <a href="/merchants/all" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">
            View All
        </a>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <div class="min-w-[800px]">
            {{-- Header --}}
            <table class="w-full">
                <thead>
                    <tr class="bg-emerald-500 text-white text-sm">
                        <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[24%]">Merchant Info</th>
                        <th class="px-4 py-3 text-center font-medium w-[13%]">Total Orders</th>
                        <th class="px-4 py-3 text-center font-medium w-[13%]">Delivered</th>
                        <th class="px-4 py-3 text-center font-medium w-[13%]">Cancelled</th>
                        <th class="px-4 py-3 text-right font-medium w-[18%]">Total Amount</th>
                        <th class="px-4 py-3 text-right font-medium rounded-r-lg w-[19%]">Revenue</th>
                    </tr>
                </thead>
            </table>

            {{-- Scrollable Body --}}
            <div class="max-h-[540px] overflow-y-auto custom-scrollbar">
                <table class="w-full">
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @php
                            $merchants = [
                                ['shop' => "Anaya's Collection", 'owner' => 'Mukta Akter', 'phone' => '01681850732', 'orders' => '1,114', 'delivered' => '492', 'cancelled' => '162', 'amount' => '237,138.00', 'revenue' => '10,082.90'],
                                ['shop' => 'Gretees', 'owner' => 'Rajib Hossen', 'phone' => '01608387271', 'orders' => '1,162', 'delivered' => '469', 'cancelled' => '154', 'amount' => '168,016.00', 'revenue' => '6,668.80'],
                                ['shop' => 'Abdullah MotoGadget', 'owner' => 'Abdullah Al Emu', 'phone' => '01319810041', 'orders' => '251', 'delivered' => '182', 'cancelled' => '22', 'amount' => '166,150.00', 'revenue' => '7,539.00'],
                                ['shop' => 'White Horse Shop', 'owner' => 'Ziaul Hoque (Jobayer)', 'phone' => '01609450703', 'orders' => '465', 'delivered' => '337', 'cancelled' => '83', 'amount' => '149,436.00', 'revenue' => '6,318.30'],
                                ['shop' => 'Fashion World BD', 'owner' => 'Shamim Ahmed', 'phone' => '01712890456', 'orders' => '890', 'delivered' => '410', 'cancelled' => '120', 'amount' => '142,580.00', 'revenue' => '5,920.50'],
                                ['shop' => 'TechZone Store', 'owner' => 'Rafiq Uddin', 'phone' => '01812345987', 'orders' => '678', 'delivered' => '356', 'cancelled' => '95', 'amount' => '135,200.00', 'revenue' => '5,480.00'],
                                ['shop' => 'Gadget Hub', 'owner' => 'Sohel Rana', 'phone' => '01912876540', 'orders' => '534', 'delivered' => '298', 'cancelled' => '78', 'amount' => '128,750.00', 'revenue' => '4,950.20'],
                                ['shop' => 'Style Studio', 'owner' => 'Nasima Begum', 'phone' => '01556782345', 'orders' => '412', 'delivered' => '245', 'cancelled' => '55', 'amount' => '118,400.00', 'revenue' => '4,720.80'],
                                ['shop' => 'Home Essentials', 'owner' => 'Faruk Mia', 'phone' => '01612345678', 'orders' => '380', 'delivered' => '220', 'cancelled' => '48', 'amount' => '105,300.00', 'revenue' => '4,210.50'],
                                ['shop' => 'Beauty Palace', 'owner' => 'Ruma Khatun', 'phone' => '01712345000', 'orders' => '325', 'delivered' => '198', 'cancelled' => '42', 'amount' => '98,650.00', 'revenue' => '3,890.00'],
                            ];
                        @endphp

                        @foreach ($merchants as $merchant)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-4 w-[24%]">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $merchant['shop'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $merchant['owner'] }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                                            </svg>
                                            {{ $merchant['phone'] }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center w-[13%]">
                                    <span class="inline-flex items-center rounded border border-gray-300 dark:border-gray-600 px-2.5 py-0.5 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $merchant['orders'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center w-[13%]">
                                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ $merchant['delivered'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center w-[13%]">
                                    <span class="text-sm font-medium text-red-500 dark:text-red-400">{{ $merchant['cancelled'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-right w-[18%]">
                                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $merchant['amount'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-right w-[19%]">
                                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $merchant['revenue'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
