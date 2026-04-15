<div class="rounded-2xl border border-gray-200 bg-white px-5 pt-5 pb-3 sm:px-6 sm:pt-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Recent Orders
        </h3>
        <a href="/orders/all" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">
            View All
        </a>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <div class="min-w-[800px]">
            {{-- Sticky Header --}}
            <table class="w-full">
                <thead>
                    <tr class="bg-emerald-500 text-white text-sm">
                        <th class="px-4 py-3 text-left font-medium rounded-l-lg w-[14%]">Invoice</th>
                        <th class="px-4 py-3 text-left font-medium w-[22%]">Customer</th>
                        <th class="px-4 py-3 text-left font-medium w-[12%]">Amount</th>
                        <th class="px-4 py-3 text-center font-medium w-[8%]">Items</th>
                        <th class="px-4 py-3 text-center font-medium w-[12%]">Status</th>
                        <th class="px-4 py-3 text-left font-medium w-[18%]">Date</th>
                        <th class="px-4 py-3 text-center font-medium rounded-r-lg w-[14%]">Action</th>
                    </tr>
                </thead>
            </table>

            {{-- Scrollable Body --}}
            <div class="max-h-[540px] overflow-y-auto custom-scrollbar">
                <table class="w-full">
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @php
                            $orders = [
                                ['invoice' => '#INV6C6A593', 'name' => 'Rahim Uddin', 'phone' => '01712345678', 'amount' => '৳399.00', 'items' => 1, 'status' => 'Pending', 'date' => '2026-04-10 17:28:46'],
                                ['invoice' => '#INV3BA17B8', 'name' => 'Fatema Akter', 'phone' => '01898765432', 'amount' => '৳280.00', 'items' => 1, 'status' => 'Pending', 'date' => '2026-04-10 17:26:21'],
                                ['invoice' => '#INV0D5E9CB', 'name' => 'Kamal Hossain', 'phone' => '01612349876', 'amount' => '৳280.00', 'items' => 1, 'status' => 'Delivered', 'date' => '2026-04-10 17:22:07'],
                                ['invoice' => '#INV7F8A2D1', 'name' => 'Nusrat Jahan', 'phone' => '01556781234', 'amount' => '৳550.00', 'items' => 3, 'status' => 'Processing', 'date' => '2026-04-10 16:45:12'],
                                ['invoice' => '#INV2E4C8F0', 'name' => 'Ariful Islam', 'phone' => '01912348765', 'amount' => '৳1,250.00', 'items' => 2, 'status' => 'Cancelled', 'date' => '2026-04-10 15:30:55'],
                                ['invoice' => '#INV9A3D7E2', 'name' => 'Mitu Rahman', 'phone' => '01812345690', 'amount' => '৳720.00', 'items' => 4, 'status' => 'Delivered', 'date' => '2026-04-10 14:18:33'],
                                ['invoice' => '#INV5B2F1C4', 'name' => 'Shakib Ahmed', 'phone' => '01712340987', 'amount' => '৳185.00', 'items' => 1, 'status' => 'Pending', 'date' => '2026-04-10 13:55:20'],
                                ['invoice' => '#INV8D6E3A9', 'name' => 'Rashida Begum', 'phone' => '01612987654', 'amount' => '৳960.00', 'items' => 2, 'status' => 'Delivered', 'date' => '2026-04-10 12:40:15'],
                                ['invoice' => '#INV1C7B4D5', 'name' => 'Tanvir Hasan', 'phone' => '01912876543', 'amount' => '৳430.00', 'items' => 1, 'status' => 'Processing', 'date' => '2026-04-10 11:22:08'],
                                ['invoice' => '#INV4E9F2A8', 'name' => 'Sadia Islam', 'phone' => '01556789012', 'amount' => '৳1,100.00', 'items' => 5, 'status' => 'Pending', 'date' => '2026-04-10 10:15:44'],
                                ['invoice' => '#INV6F1A3B7', 'name' => 'Mizanur Rahman', 'phone' => '01812345111', 'amount' => '৳340.00', 'items' => 2, 'status' => 'Delivered', 'date' => '2026-04-10 09:50:30'],
                                ['invoice' => '#INV3C8D5E1', 'name' => 'Anika Sultana', 'phone' => '01712340222', 'amount' => '৳890.00', 'items' => 3, 'status' => 'Cancelled', 'date' => '2026-04-10 08:35:18'],
                                ['invoice' => '#INV7A2B9C4', 'name' => 'Imran Chowdhury', 'phone' => '01612345333', 'amount' => '৳210.00', 'items' => 1, 'status' => 'Delivered', 'date' => '2026-04-10 07:20:55'],
                                ['invoice' => '#INV9E4F1D6', 'name' => 'Laboni Akter', 'phone' => '01912345444', 'amount' => '৳1,450.00', 'items' => 4, 'status' => 'Pending', 'date' => '2026-04-09 23:48:10'],
                                ['invoice' => '#INV2D7A8B3', 'name' => 'Sumon Mia', 'phone' => '01556780555', 'amount' => '৳575.00', 'items' => 2, 'status' => 'Processing', 'date' => '2026-04-09 22:15:42'],
                            ];

                            $statusClasses = [
                                'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-500',
                                'Processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500',
                                'Delivered' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-500',
                                'Cancelled' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-500',
                            ];
                        @endphp

                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-4 w-[14%]">
                                    <span class="text-sm font-medium text-brand-500">{{ $order['invoice'] }}</span>
                                </td>
                                <td class="px-4 py-4 w-[22%]">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $order['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order['phone'] }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-4 w-[12%]">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['amount'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center w-[8%]">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $order['items'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center w-[12%]">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClasses[$order['status']] }}">
                                        {{ $order['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 w-[18%]">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $order['date'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center w-[14%]">
                                    <a href="{{ route('orders.detail', $order['invoice']) }}" class="inline-flex items-center gap-1 rounded-lg border border-brand-500 px-3 py-1.5 text-xs font-medium text-brand-500 hover:bg-brand-500 hover:text-white transition-colors dark:border-brand-400 dark:text-brand-400 dark:hover:bg-brand-500 dark:hover:text-white">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
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
</div>
