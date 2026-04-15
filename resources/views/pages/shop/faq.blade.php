@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="FAQ Management" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeCategory: 'all' }">
        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="activeCategory = 'all'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeCategory === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    All
                </button>
                <button @click="activeCategory = 'general'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeCategory === 'general' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    General
                </button>
                <button @click="activeCategory = 'orders'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeCategory === 'orders' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    Orders
                </button>
                <button @click="activeCategory = 'payments'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeCategory === 'payments' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    Payments
                </button>
                <button @click="activeCategory = 'merchants'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeCategory === 'merchants' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    Merchants
                </button>
            </div>
            <div class="flex-1"></div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add FAQ
            </button>
        </div>

        {{-- FAQ List --}}
        <div class="px-5 sm:px-6 space-y-3">
            @php
                $faqs = [
                    ['question' => 'How do I place an order on Packly?', 'answer' => 'You can place an order by browsing products, adding items to your cart, and proceeding to checkout. Select your preferred payment method and confirm your order.', 'category' => 'General', 'status' => 'Published', 'order' => 1],
                    ['question' => 'What payment methods are accepted?', 'answer' => 'We accept bKash, Nagad, Rocket, bank transfer, SSL Commerz online payment, and Cash on Delivery (COD) for eligible orders.', 'category' => 'Payments', 'status' => 'Published', 'order' => 2],
                    ['question' => 'How can I track my order?', 'answer' => 'After placing your order, you will receive a consignment ID. You can use this ID to track your order status from our website or by contacting customer support.', 'category' => 'Orders', 'status' => 'Published', 'order' => 3],
                    ['question' => 'What is the return policy?', 'answer' => 'Products can be returned within 7 days of delivery if they are damaged, defective, or not as described. Please contact our support team to initiate a return.', 'category' => 'Orders', 'status' => 'Published', 'order' => 4],
                    ['question' => 'How do I become a merchant on Packly?', 'answer' => 'Visit our Sell With Us page and fill out the registration form. Our team will review your application and get back to you within 24-48 hours.', 'category' => 'Merchants', 'status' => 'Published', 'order' => 5],
                    ['question' => 'How are merchant payouts processed?', 'answer' => 'Merchant payouts are processed weekly via bank transfer, bKash, Nagad, or Rocket based on your preferred payout method configured in your merchant dashboard.', 'category' => 'Merchants', 'status' => 'Published', 'order' => 6],
                    ['question' => 'Is Cash on Delivery available everywhere?', 'answer' => 'COD is available in most areas across Bangladesh. However, some remote locations may only support prepaid orders. Check availability during checkout.', 'category' => 'Payments', 'status' => 'Draft', 'order' => 7],
                    ['question' => 'How do I cancel my order?', 'answer' => 'You can cancel your order before it has been shipped by contacting our customer support. Once shipped, cancellation may not be possible.', 'category' => 'Orders', 'status' => 'Published', 'order' => 8],
                ];
            @endphp

            @foreach ($faqs as $index => $faq)
                <div x-data="{ expanded: false }" class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors" :class="expanded ? 'bg-gray-50 dark:bg-white/[0.02]' : 'bg-white dark:bg-white/[0.01]'">
                    <div class="flex items-center gap-3 px-5 py-4 cursor-pointer" @click="expanded = !expanded">
                        {{-- Order --}}
                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 w-6 shrink-0">{{ $faq['order'] }}</span>
                        {{-- Question --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $faq['question'] }}</p>
                        </div>
                        {{-- Category --}}
                        @php
                            $catColors = [
                                'General' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                'Orders' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                'Payments' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
                                'Merchants' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                            ];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-medium {{ $catColors[$faq['category']] ?? '' }} shrink-0">{{ $faq['category'] }}</span>
                        {{-- Status --}}
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium shrink-0
                            {{ $faq['status'] === 'Published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' }}">
                            <span class="w-1 h-1 rounded-full {{ $faq['status'] === 'Published' ? 'bg-emerald-500' : 'bg-yellow-500' }}"></span>
                            {{ $faq['status'] }}
                        </span>
                        {{-- Expand Icon --}}
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    {{-- Answer --}}
                    <div x-show="expanded" x-collapse>
                        <div class="px-5 pb-4">
                            <div class="pl-9 pr-24">
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $faq['answer'] }}</p>
                                <div class="flex items-center gap-2 mt-3">
                                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:text-brand-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                        Edit
                                    </button>
                                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors dark:bg-red-500/10 dark:text-red-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
