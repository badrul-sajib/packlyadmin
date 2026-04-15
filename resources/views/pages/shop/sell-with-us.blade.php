@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Sell With Us Page Manager" />

    @php
        $tabs = [
            ['key' => 'header', 'label' => 'Header', 'icon' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12'],
            ['key' => 'hero', 'label' => 'Hero', 'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z'],
            ['key' => 'features', 'label' => 'Features', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25h2.25A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z'],
            ['key' => 'steps', 'label' => 'Steps', 'icon' => 'M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25'],
            ['key' => 'banner', 'label' => 'Banner', 'icon' => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z'],
            ['key' => 'testimonials', 'label' => 'Testimonials', 'icon' => 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z'],
            ['key' => 'faq', 'label' => 'FAQ', 'icon' => 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z'],
        ];
    @endphp

    <div class="flex gap-6" x-data="{ activeTab: 'header' }">
        {{-- Sidebar Nav --}}
        <div class="w-52 shrink-0 hidden xl:block">
            <div class="sticky top-24 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Page Sections</p>
                </div>
                <nav class="py-1">
                    @foreach ($tabs as $tab)
                        <button @click="activeTab = '{{ $tab['key'] }}'" type="button"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm transition-colors"
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

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            {{-- Mobile selector --}}
            <div class="xl:hidden mb-6">
                <select @change="activeTab = $event.target.value" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300">
                    @foreach ($tabs as $tab)
                        <option value="{{ $tab['key'] }}">{{ $tab['label'] }}</option>
                    @endforeach
                </select>
            </div>

            @php
                $sections = [
                    'header' => [
                        'title' => 'Header Section',
                        'desc' => 'Navigation bar content and contact info',
                        'groups' => [
                            ['heading' => 'Section Info', 'fields' => [
                                ['label' => 'Title', 'value' => 'Header Section', 'col' => 1],
                                ['label' => 'Subtitle', 'value' => '', 'placeholder' => 'Enter subtitle', 'col' => 1],
                            ]],
                            ['heading' => 'Contact', 'fields' => [
                                ['label' => 'Call Text', 'value' => '+8809678045555', 'col' => 1],
                                ['label' => 'Phone Number', 'value' => '+8809678045555', 'col' => 1],
                            ]],
                        ],
                    ],
                    'hero' => [
                        'title' => 'Hero Section',
                        'desc' => 'Main landing area with headline and CTA',
                        'groups' => [
                            ['heading' => 'Content', 'fields' => [
                                ['label' => 'Headline', 'value' => 'Start Selling Online Today', 'col' => 2],
                                ['label' => 'Subtitle', 'value' => 'Join thousands of merchants on Packly', 'col' => 2],
                                ['label' => 'Description', 'value' => "Reach millions of customers and grow your business with Packly's powerful e-commerce platform.", 'type' => 'textarea', 'col' => 2],
                            ]],
                            ['heading' => 'Call to Action', 'fields' => [
                                ['label' => 'Button Text', 'value' => 'Register Now', 'col' => 1],
                                ['label' => 'Button Link', 'value' => '/merchant/register', 'col' => 1],
                            ]],
                            ['heading' => 'Media', 'fields' => [
                                ['label' => 'Hero Image', 'type' => 'image', 'col' => 2],
                            ]],
                        ],
                    ],
                    'features' => [
                        'title' => 'Features Section',
                        'desc' => 'Highlight key selling points',
                        'groups' => [
                            ['heading' => 'Section Info', 'fields' => [
                                ['label' => 'Title', 'value' => 'Why Sell on Packly?', 'col' => 1],
                                ['label' => 'Subtitle', 'value' => 'Benefits of joining our marketplace', 'col' => 1],
                            ]],
                            ['heading' => 'Feature 1', 'fields' => [
                                ['label' => 'Title', 'value' => 'Zero Setup Cost', 'col' => 1],
                                ['label' => 'Icon', 'type' => 'image', 'col' => 1],
                                ['label' => 'Description', 'value' => 'Start selling without any upfront fees or hidden charges.', 'type' => 'textarea', 'col' => 2],
                            ]],
                            ['heading' => 'Feature 2', 'fields' => [
                                ['label' => 'Title', 'value' => 'Wide Customer Base', 'col' => 1],
                                ['label' => 'Icon', 'type' => 'image', 'col' => 1],
                                ['label' => 'Description', 'value' => 'Access millions of active shoppers across Bangladesh.', 'type' => 'textarea', 'col' => 2],
                            ]],
                            ['heading' => 'Feature 3', 'fields' => [
                                ['label' => 'Title', 'value' => 'Fast Payouts', 'col' => 1],
                                ['label' => 'Icon', 'type' => 'image', 'col' => 1],
                                ['label' => 'Description', 'value' => 'Get paid quickly through bKash, Nagad, or bank transfer.', 'type' => 'textarea', 'col' => 2],
                            ]],
                        ],
                    ],
                    'steps' => [
                        'title' => 'How It Works',
                        'desc' => 'Step-by-step process for merchants',
                        'groups' => [
                            ['heading' => 'Section Info', 'fields' => [
                                ['label' => 'Title', 'value' => 'How It Works', 'col' => 1],
                                ['label' => 'Subtitle', 'value' => '3 simple steps to start selling', 'col' => 1],
                            ]],
                            ['heading' => 'Step 1', 'fields' => [['label' => 'Title', 'value' => 'Create Account', 'col' => 1], ['label' => 'Description', 'value' => 'Sign up and verify your identity in minutes.', 'type' => 'textarea', 'col' => 1]]],
                            ['heading' => 'Step 2', 'fields' => [['label' => 'Title', 'value' => 'List Products', 'col' => 1], ['label' => 'Description', 'value' => 'Add your products with images, prices and descriptions.', 'type' => 'textarea', 'col' => 1]]],
                            ['heading' => 'Step 3', 'fields' => [['label' => 'Title', 'value' => 'Start Earning', 'col' => 1], ['label' => 'Description', 'value' => 'Receive orders and get paid directly to your account.', 'type' => 'textarea', 'col' => 1]]],
                        ],
                    ],
                    'banner' => [
                        'title' => 'Promotional Banner',
                        'desc' => 'Mid-page call-to-action banner',
                        'groups' => [
                            ['heading' => 'Content', 'fields' => [
                                ['label' => 'Title', 'value' => 'Grow Your Business with Packly', 'col' => 1],
                                ['label' => 'Subtitle', 'value' => 'Join 7,000+ merchants already selling', 'col' => 1],
                                ['label' => 'CTA Text', 'value' => 'Get Started Free', 'col' => 1],
                                ['label' => 'CTA Link', 'value' => '/merchant/register', 'col' => 1],
                            ]],
                            ['heading' => 'Media', 'fields' => [['label' => 'Banner Image', 'type' => 'image', 'col' => 2]]],
                        ],
                    ],
                    'testimonials' => [
                        'title' => 'Testimonials',
                        'desc' => 'Merchant success stories',
                        'groups' => [
                            ['heading' => 'Testimonial 1', 'fields' => [
                                ['label' => 'Name', 'value' => 'Rakib Hasan', 'col' => 1],
                                ['label' => 'Shop', 'value' => 'Home Shop BD.com', 'col' => 1],
                                ['label' => 'Photo', 'type' => 'image', 'col' => 2],
                                ['label' => 'Quote', 'value' => 'Packly helped me grow my business 5x in just 6 months.', 'type' => 'textarea', 'col' => 2],
                            ]],
                            ['heading' => 'Testimonial 2', 'fields' => [
                                ['label' => 'Name', 'value' => 'Kamrul Islam', 'col' => 1],
                                ['label' => 'Shop', 'value' => 'LUXURY VIP', 'col' => 1],
                                ['label' => 'Photo', 'type' => 'image', 'col' => 2],
                                ['label' => 'Quote', 'value' => 'Best marketplace for Bangladeshi sellers. Support is excellent.', 'type' => 'textarea', 'col' => 2],
                            ]],
                        ],
                    ],
                    'faq' => [
                        'title' => 'FAQ Section',
                        'desc' => 'Common questions from potential sellers',
                        'groups' => [
                            ['heading' => 'Section Info', 'fields' => [
                                ['label' => 'Title', 'value' => 'Frequently Asked Questions', 'col' => 1],
                                ['label' => 'Subtitle', 'value' => 'Everything you need to know', 'col' => 1],
                            ]],
                            ['heading' => 'FAQ 1', 'fields' => [['label' => 'Question', 'value' => 'How much does it cost to sell?', 'col' => 2], ['label' => 'Answer', 'value' => 'Signing up is free. We charge a small commission on each sale.', 'type' => 'textarea', 'col' => 2]]],
                            ['heading' => 'FAQ 2', 'fields' => [['label' => 'Question', 'value' => 'How do I receive payments?', 'col' => 2], ['label' => 'Answer', 'value' => 'Payouts weekly via bKash, Nagad, Rocket, or bank transfer.', 'type' => 'textarea', 'col' => 2]]],
                            ['heading' => 'FAQ 3', 'fields' => [['label' => 'Question', 'value' => 'What products can I sell?', 'col' => 2], ['label' => 'Answer', 'value' => 'Almost any category. Check merchant guidelines for restrictions.', 'type' => 'textarea', 'col' => 2]]],
                        ],
                    ],
                ];
            @endphp

            @foreach ($sections as $key => $section)
                <div x-show="activeTab === '{{ $key }}'" {{ $key !== 'header' ? 'style=display:none;' : '' }} class="space-y-5">
                    {{-- Section Header --}}
                    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-white/[0.03]">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $section['title'] }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $section['desc'] }}</p>
                    </div>

                    {{-- Field Groups --}}
                    @foreach ($section['groups'] as $group)
                        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $group['heading'] }}</h4>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-5">
                                    @foreach ($group['fields'] as $field)
                                        <div class="{{ $field['col'] === 2 ? 'col-span-2' : 'col-span-2 sm:col-span-1' }}">
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">{{ $field['label'] }}</label>
                                            @if(isset($field['type']) && $field['type'] === 'textarea')
                                                <textarea rows="2" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 resize-none dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300">{{ $field['value'] ?? '' }}</textarea>
                                            @elseif(isset($field['type']) && $field['type'] === 'image')
                                                <div class="flex items-center gap-4">
                                                    <div class="w-16 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                                        <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                                    </div>
                                                    <label class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 cursor-pointer transition-colors dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                        Upload
                                                        <input type="file" class="hidden" accept="image/*" />
                                                    </label>
                                                </div>
                                            @else
                                                <input type="text" value="{{ $field['value'] ?? '' }}" placeholder="{{ $field['placeholder'] ?? '' }}" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300" />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Save Bar --}}
                    <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-xs text-gray-400 dark:text-gray-500">Changes are saved per section.</p>
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
