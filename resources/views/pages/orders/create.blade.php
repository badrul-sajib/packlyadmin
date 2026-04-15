@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create Order" />

    @php
        $merchants = [
            ['id' => 1,  'name' => 'Rojgar Telecom'],
            ['id' => 2,  'name' => 'Borshon Shop'],
            ['id' => 3,  'name' => 'ROYAL BD SHOP'],
            ['id' => 4,  'name' => 'Express Gadgets'],
            ['id' => 5,  'name' => 'Fashion Hub BD'],
            ['id' => 6,  'name' => 'TechZone Store'],
            ['id' => 7,  'name' => 'Cosmetics World Bangladesh'],
            ['id' => 8,  'name' => 'StyleNest BD'],
        ];

        $divisions = ['Dhaka', 'Chittagong', 'Rajshahi', 'Khulna', 'Barishal', 'Sylhet', 'Rangpur', 'Mymensingh'];

        $districts = [
            'Dhaka'       => ['Dhaka', 'Gazipur', 'Narayanganj', 'Manikganj', 'Munshiganj', 'Narsingdi', 'Tangail'],
            'Chittagong'  => ['Chittagong', 'Cox\'s Bazar', 'Comilla', 'Noakhali', 'Feni', 'Lakshmipur', 'Chandpur'],
            'Rajshahi'    => ['Rajshahi', 'Bogura', 'Pabna', 'Natore', 'Naogaon', 'Sirajganj', 'Chapai Nawabganj'],
            'Khulna'      => ['Khulna', 'Jessore', 'Satkhira', 'Bagerhat', 'Chuadanga', 'Kushtia', 'Meherpur'],
            'Barishal'    => ['Barishal', 'Patuakhali', 'Bhola', 'Pirojpur', 'Jhalokati', 'Barguna'],
            'Sylhet'      => ['Sylhet', 'Moulvibazar', 'Habiganj', 'Sunamganj'],
            'Rangpur'     => ['Rangpur', 'Dinajpur', 'Kurigram', 'Gaibandha', 'Nilphamari', 'Lalmonirhat', 'Thakurgaon', 'Panchagarh'],
            'Mymensingh'  => ['Mymensingh', 'Netrokona', 'Jamalpur', 'Sherpur'],
        ];

        $customerDatabase = [
            ['phone' => '01631366798', 'name' => 'Shohan Ahmed Raj',    'alt_phone' => '',            'email' => 'shohan@example.com',  'address' => 'House 12, Road 4, Mirpur-1',      'division' => 'Dhaka',      'district' => 'Dhaka',      'city' => 'Mirpur',       'orders' => 7,  'last_order' => '10 Apr 2026'],
            ['phone' => '01890241069', 'name' => 'হিমাদ্রী কবির ঋষি',  'alt_phone' => '01712000001', 'email' => '',                    'address' => 'Flat 3B, Bashundhara R/A',        'division' => 'Dhaka',      'district' => 'Dhaka',      'city' => 'Bashundhara',  'orders' => 3,  'last_order' => '10 Apr 2026'],
            ['phone' => '01601603177', 'name' => 'Hridoy Islam',         'alt_phone' => '',            'email' => 'hridoy@gmail.com',    'address' => '45 Agrabad, CDA Avenue',         'division' => 'Chittagong', 'district' => 'Chittagong', 'city' => 'Agrabad',      'orders' => 12, 'last_order' => '10 Apr 2026'],
            ['phone' => '01724513237', 'name' => 'Bishal Hossain',       'alt_phone' => '01811234567', 'email' => 'bishal@yahoo.com',    'address' => 'Holding 7, Muradpur',            'division' => 'Chittagong', 'district' => 'Chittagong', 'city' => 'Muradpur',     'orders' => 5,  'last_order' => '09 Apr 2026'],
            ['phone' => '01903750809', 'name' => 'Limon Sarkar',         'alt_phone' => '',            'email' => '',                    'address' => 'Ward 3, Rajshahi Sadar',         'division' => 'Rajshahi',   'district' => 'Rajshahi',   'city' => 'Rajshahi Sadar','orders' => 2, 'last_order' => '08 Apr 2026'],
            ['phone' => '01874022686', 'name' => 'Sonamui Begum',        'alt_phone' => '',            'email' => 'sonamui@gmail.com',   'address' => 'Village: Sonamui, Barisal',      'division' => 'Barishal',   'district' => 'Barishal',   'city' => 'Barisal Sadar','orders' => 1,  'last_order' => '08 Apr 2026'],
            ['phone' => '01756234890', 'name' => 'Hridoy Hasan',         'alt_phone' => '01956234890', 'email' => 'hridoyhasan@mail.com','address' => 'Block C, Uttara Sector 10',      'division' => 'Dhaka',      'district' => 'Dhaka',      'city' => 'Uttara',       'orders' => 19, 'last_order' => '10 Apr 2026'],
            ['phone' => '01912345678', 'name' => 'Rafiq Ahmed',          'alt_phone' => '',            'email' => 'rafiq@example.com',   'address' => 'Kazipara, Mirpur-10',            'division' => 'Dhaka',      'district' => 'Dhaka',      'city' => 'Mirpur',       'orders' => 8,  'last_order' => '10 Apr 2026'],
        ];

        $productCatalog = [
            ['id' => 1, 'name' => 'Arche Pearl Cream - 3gm',                     'sku' => 'SKU-001', 'price' => 140,  'stock' => 48],
            ['id' => 2, 'name' => 'Goree Whitening Night Cream - 20g',            'sku' => 'SKU-002', 'price' => 620,  'stock' => 22],
            ['id' => 3, 'name' => 'Samsung Galaxy A15 (4/64GB)',                  'sku' => 'SKU-003', 'price' => 15500,'stock' => 10],
            ['id' => 4, 'name' => 'TP-Link TL-WR841N WiFi Router',                'sku' => 'SKU-004', 'price' => 1350, 'stock' => 35],
            ['id' => 5, 'name' => 'Men\'s Polo T-Shirt (Cotton, L)',              'sku' => 'SKU-005', 'price' => 450,  'stock' => 80],
            ['id' => 6, 'name' => 'Women\'s Kurti - Printed (M)',                 'sku' => 'SKU-006', 'price' => 550,  'stock' => 60],
            ['id' => 7, 'name' => 'Sony WH-1000XM4 Headphones',                  'sku' => 'SKU-007', 'price' => 18500,'stock' => 6],
            ['id' => 8, 'name' => 'Organic Aloe Vera Gel - 200ml',               'sku' => 'SKU-008', 'price' => 280,  'stock' => 55],
            ['id' => 9, 'name' => 'Rechargeable LED Desk Lamp',                  'sku' => 'SKU-009', 'price' => 890,  'stock' => 30],
            ['id' => 10,'name' => 'Non-stick Frying Pan - 26cm',                 'sku' => 'SKU-010', 'price' => 760,  'stock' => 20],
        ];
    @endphp

    <div
        x-data="{
            /* ── Merchant ── */
            merchantSearch: '',
            merchantOpen: false,
            selectedMerchant: null,
            merchants: {{ collect($merchants)->toJson() }},
            get filteredMerchants() {
                if (!this.merchantSearch) return this.merchants;
                return this.merchants.filter(m => m.name.toLowerCase().includes(this.merchantSearch.toLowerCase()));
            },
            selectMerchant(m) { this.selectedMerchant = m; this.merchantSearch = m.name; this.merchantOpen = false; },

            /* ── Product search / cart ── */
            productSearch: '',
            productOpen: false,
            catalog: {{ collect($productCatalog)->toJson() }},
            cart: [],
            get filteredProducts() {
                if (!this.productSearch) return this.catalog;
                return this.catalog.filter(p => p.name.toLowerCase().includes(this.productSearch.toLowerCase()) || p.sku.toLowerCase().includes(this.productSearch.toLowerCase()));
            },
            addProduct(p) {
                const existing = this.cart.find(c => c.id === p.id);
                if (existing) { existing.qty += 1; }
                else { this.cart.push({ ...p, qty: 1, unitPrice: p.price }); }
                this.productSearch = '';
                this.productOpen = false;
            },
            removeProduct(id) { this.cart = this.cart.filter(c => c.id !== id); },
            get subTotal() { return this.cart.reduce((s, c) => s + c.unitPrice * c.qty, 0); },

            /* ── Delivery ── */
            shippingType: 'OSD',
            get shippingCharge() { return this.shippingType === 'OSD' ? 80 : 50; },

            /* ── Payment ── */
            paymentMethod: 'COD',

            /* ── Discount ── */
            discount: 0,

            /* ── Source ── */
            orderSource: 'Admin Panel',

            /* ── Total ── */
            get total() { return this.subTotal + this.shippingCharge - Number(this.discount || 0); },

            /* ── Customer lookup ── */
            phoneQuery: '',
            customerSearchState: 'idle',   // idle | searching | found | not_found
            foundCustomer: null,
            customerDb: {{ collect($customerDatabase)->toJson() }},
            customer: { name: '', alt_phone: '', email: '', address: '', division: '', district: '', city: '' },
            searchCustomer() {
                const q = this.phoneQuery.trim().replace(/\s|-/g, '');
                if (!q) return;
                this.customerSearchState = 'searching';
                setTimeout(() => {
                    const match = this.customerDb.find(c => c.phone.replace(/\s|-/g, '') === q);
                    if (match) {
                        this.foundCustomer = match;
                        this.customer = { name: match.name, alt_phone: match.alt_phone, email: match.email, address: match.address, division: match.division, district: match.district, city: match.city };
                        this.selectedDivision = match.division;
                        this.customerSearchState = 'found';
                    } else {
                        this.foundCustomer = null;
                        this.customer = { name: '', alt_phone: '', email: '', address: '', division: '', district: '', city: '' };
                        this.selectedDivision = '';
                        this.customerSearchState = 'not_found';
                    }
                }, 600);
            },
            resetCustomer() {
                this.phoneQuery = '';
                this.customerSearchState = 'idle';
                this.foundCustomer = null;
                this.customer = { name: '', alt_phone: '', email: '', address: '', division: '', district: '', city: '' };
                this.selectedDivision = '';
            },

            /* ── Division → District ── */
            selectedDivision: '',
            districts: {{ json_encode($districts) }},
            get districtList() { return this.selectedDivision ? (this.districts[this.selectedDivision] || []) : []; },

            /* ── Submit ── */
            submitted: false,
            submit() { this.submitted = true; }
        }"
        class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══════════════════════════════════════════════
             LEFT COLUMN  (2/3 width)
        ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- ── Merchant ── --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold">1</span>
                    Merchant
                </h2>
                <div class="relative" @click.outside="merchantOpen = false">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Select Merchant <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <input type="text" x-model="merchantSearch" @focus="merchantOpen = true" @input="merchantOpen = true"
                            placeholder="Search merchant by name…"
                            class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                    </div>
                    <div x-show="merchantOpen && filteredMerchants.length > 0"
                        class="absolute z-50 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900 max-h-52 overflow-y-auto"
                        style="display:none;">
                        <template x-for="m in filteredMerchants" :key="m.id">
                            <button type="button" @click="selectMerchant(m)"
                                class="w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/[0.05] flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
                                <span x-text="m.name"></span>
                            </button>
                        </template>
                    </div>
                    <p x-show="selectedMerchant" class="mt-1.5 text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1" style="display:none;">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span x-text="'Merchant selected: ' + (selectedMerchant ? selectedMerchant.name : '')"></span>
                    </p>
                </div>
            </div>

            {{-- ── Customer Information ── --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold">2</span>
                    Customer Information
                </h2>

                {{-- ── Phone search (always visible) ── --}}
                <div class="mb-5">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                        Search by Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <div class="flex flex-1 rounded-lg border border-gray-200 overflow-hidden focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 dark:border-gray-700 transition-colors">
                            <span class="inline-flex items-center px-3 bg-gray-50 text-sm text-gray-500 border-r border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 select-none flex-shrink-0">+880</span>
                            <input type="tel" x-model="phoneQuery"
                                @keydown.enter.prevent="searchCustomer()"
                                placeholder="01XXXXXXXXX"
                                class="flex-1 bg-white px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                        </div>
                        <button type="button" @click="searchCustomer()"
                            :disabled="customerSearchState === 'searching'"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-brand-500 hover:bg-brand-600 disabled:opacity-60 text-white text-sm font-medium transition-colors flex-shrink-0">
                            {{-- spinner --}}
                            <svg x-show="customerSearchState === 'searching'" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            {{-- search icon --}}
                            <svg x-show="customerSearchState !== 'searching'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            <span x-show="customerSearchState !== 'searching'">Search</span>
                        </button>
                    </div>
                </div>

                {{-- ── FOUND: customer card ── --}}
                <div x-show="customerSearchState === 'found'" style="display:none;">
                    {{-- Found banner --}}
                    <div class="flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/30 px-4 py-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300" x-text="foundCustomer && foundCustomer.name"></p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-500 flex items-center gap-2 mt-0.5">
                                    <span x-text="'+880 ' + phoneQuery"></span>
                                    <span class="text-emerald-300 dark:text-emerald-700">•</span>
                                    <span x-text="(foundCustomer ? foundCustomer.orders : 0) + ' previous orders'"></span>
                                    <span class="text-emerald-300 dark:text-emerald-700">•</span>
                                    <span x-text="'Last order: ' + (foundCustomer ? foundCustomer.last_order : '')"></span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Existing Customer
                            </span>
                            <button type="button" @click="resetCustomer()" title="Clear & search again"
                                class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Auto-filled editable fields --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="customer.name"
                                class="w-full rounded-lg border border-emerald-200 bg-emerald-50/50 px-3.5 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-emerald-500/30 dark:bg-emerald-500/5 dark:text-gray-300" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Alternate Phone <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="tel" x-model="customer.alt_phone" placeholder="01XXXXXXXXX"
                                class="w-full rounded-lg border border-emerald-200 bg-emerald-50/50 px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-emerald-500/30 dark:bg-emerald-500/5 dark:text-gray-300" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Email <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="email" x-model="customer.email" placeholder="customer@example.com"
                                class="w-full rounded-lg border border-emerald-200 bg-emerald-50/50 px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-emerald-500/30 dark:bg-emerald-500/5 dark:text-gray-300" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Delivery Address <span class="text-red-500">*</span></label>
                            <textarea rows="2" x-model="customer.address"
                                class="w-full rounded-lg border border-emerald-200 bg-emerald-50/50 px-3.5 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-emerald-500/30 dark:bg-emerald-500/5 dark:text-gray-300 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Division <span class="text-red-500">*</span></label>
                            <select x-model="selectedDivision" @change="customer.division = selectedDivision; customer.district = ''"
                                class="w-full rounded-lg border border-emerald-200 bg-emerald-50/50 px-3.5 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-emerald-500/30 dark:bg-emerald-500/5 dark:text-gray-300">
                                <option value="">Select Division</option>
                                @foreach ($divisions as $div)
                                    <option value="{{ $div }}">{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">District <span class="text-red-500">*</span></label>
                            <select x-model="customer.district" :disabled="!selectedDivision"
                                class="w-full rounded-lg border border-emerald-200 bg-emerald-50/50 px-3.5 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-emerald-500/30 dark:bg-emerald-500/5 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">Select District</option>
                                <template x-for="d in districtList" :key="d">
                                    <option :value="d" x-text="d" :selected="d === customer.district"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">City / Upazila</label>
                            <input type="text" x-model="customer.city"
                                class="w-full rounded-lg border border-emerald-200 bg-emerald-50/50 px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-emerald-500/30 dark:bg-emerald-500/5 dark:text-gray-300" />
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-emerald-600 dark:text-emerald-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                        Fields are auto-filled from previous order. You can edit them for this order.
                    </p>
                </div>

                {{-- ── NOT FOUND: manual entry ── --}}
                <div x-show="customerSearchState === 'not_found'" style="display:none;">
                    {{-- Not found banner --}}
                    <div class="flex items-center justify-between rounded-xl bg-amber-50 border border-amber-200 dark:bg-amber-500/10 dark:border-amber-500/30 px-4 py-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">No customer found for <span x-text="'+880 ' + phoneQuery"></span></p>
                                <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">A new customer record will be created when this order is placed.</p>
                            </div>
                        </div>
                        <button type="button" @click="resetCustomer()" title="Search again"
                            class="text-amber-500 hover:text-amber-700 dark:hover:text-amber-300 transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Manual entry fields --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="customer.name" placeholder="e.g. Rahul Ahmed"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Alternate Phone <span class="text-gray-400 font-normal">(optional)</span></label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 select-none">+880</span>
                                <input type="tel" x-model="customer.alt_phone" placeholder="01XXXXXXXXX"
                                    class="flex-1 rounded-r-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Email <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="email" x-model="customer.email" placeholder="customer@example.com"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Delivery Address <span class="text-red-500">*</span></label>
                            <textarea rows="2" x-model="customer.address" placeholder="House no, road, area, landmark…"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Division <span class="text-red-500">*</span></label>
                            <select x-model="selectedDivision"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300">
                                <option value="">Select Division</option>
                                @foreach ($divisions as $div)
                                    <option value="{{ $div }}">{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">District <span class="text-red-500">*</span></label>
                            <select :disabled="!selectedDivision"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">Select District</option>
                                <template x-for="d in districtList" :key="d">
                                    <option :value="d" x-text="d"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">City / Upazila</label>
                            <input type="text" x-model="customer.city" placeholder="e.g. Mirpur"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                        </div>
                    </div>
                </div>

                {{-- ── IDLE: prompt hint ── --}}
                <div x-show="customerSearchState === 'idle'" class="flex items-center gap-3 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 px-4 py-4">
                    <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Enter phone number to look up existing customer</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">If found, all details will be auto-filled. If not, you can enter them manually.</p>
                    </div>
                </div>
            </div>

            {{-- ── Product Selection ── --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold">3</span>
                    Products
                </h2>

                {{-- Search & add product --}}
                <div class="relative mb-4" @click.outside="productOpen = false">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        </span>
                        <input type="text" x-model="productSearch" @focus="productOpen = true" @input="productOpen = true"
                            placeholder="Search product by name or SKU…"
                            class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                    </div>
                    <div x-show="productOpen && filteredProducts.length > 0"
                        class="absolute z-50 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900 max-h-60 overflow-y-auto"
                        style="display:none;">
                        <template x-for="p in filteredProducts" :key="p.id">
                            <button type="button" @click="addProduct(p)"
                                class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-white/[0.05] flex items-center justify-between gap-4 border-b border-gray-50 dark:border-gray-800 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="p.name"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="p.sku"></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400" x-text="'৳ ' + p.price.toLocaleString()"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500" x-text="'Stock: ' + p.stock"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Cart table --}}
                <div x-show="cart.length > 0" style="display:none;">
                    <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-white/[0.03] text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-4 py-3 text-left">Product</th>
                                    <th class="px-4 py-3 text-center w-28">Qty</th>
                                    <th class="px-4 py-3 text-right w-28">Unit Price</th>
                                    <th class="px-4 py-3 text-right w-28">Total</th>
                                    <th class="px-4 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <template x-for="item in cart" :key="item.id">
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-800 dark:text-white/90" x-text="item.name"></p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="item.sku"></p>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="inline-flex items-center rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <button type="button" @click="item.qty > 1 ? item.qty-- : removeProduct(item.id)"
                                                    class="flex items-center justify-center w-8 h-8 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/[0.07] transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                                </button>
                                                <span class="w-8 text-center text-sm font-medium text-gray-800 dark:text-white/90" x-text="item.qty"></span>
                                                <button type="button" @click="item.qty++"
                                                    class="flex items-center justify-center w-8 h-8 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/[0.07] transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="inline-flex items-center gap-1">
                                                <span class="text-xs text-gray-400">৳</span>
                                                <input type="number" x-model="item.unitPrice" min="0"
                                                    class="w-24 rounded border border-gray-200 bg-white px-2 py-1 text-sm text-right text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300" />
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-semibold text-gray-800 dark:text-white/90" x-text="'৳ ' + (item.unitPrice * item.qty).toLocaleString()"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" @click="removeProduct(item.id)"
                                                class="text-gray-300 hover:text-red-500 dark:text-gray-600 dark:hover:text-red-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Empty state --}}
                <div x-show="cart.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                    </div>
                    <p class="text-sm text-gray-400 dark:text-gray-500">No products added yet.</p>
                    <p class="text-xs text-gray-400 dark:text-gray-600 mt-0.5">Search and add products above.</p>
                </div>
            </div>

            {{-- ── Order Settings ── --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold">4</span>
                    Order Settings
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Shipping Type --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Shipping Type</label>
                        <div class="flex gap-2">
                            <button type="button" @click="shippingType = 'OSD'"
                                :class="shippingType === 'OSD' ? 'bg-brand-500 text-white border-brand-500' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-300 dark:bg-white/[0.03] dark:text-gray-300 dark:border-gray-700'"
                                class="flex-1 py-2.5 rounded-lg border text-sm font-medium transition-colors text-center">
                                OSD <span class="text-xs opacity-70">(৳80)</span>
                            </button>
                            <button type="button" @click="shippingType = 'ISD'"
                                :class="shippingType === 'ISD' ? 'bg-brand-500 text-white border-brand-500' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-300 dark:bg-white/[0.03] dark:text-gray-300 dark:border-gray-700'"
                                class="flex-1 py-2.5 rounded-lg border text-sm font-medium transition-colors text-center">
                                ISD <span class="text-xs opacity-70">(৳50)</span>
                            </button>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Payment Method</label>
                        <div class="flex gap-2">
                            @foreach (['COD', 'SSL', 'bKash', 'Nagad'] as $pm)
                            <button type="button" @click="paymentMethod = '{{ $pm }}'"
                                :class="paymentMethod === '{{ $pm }}' ? 'bg-brand-500 text-white border-brand-500' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-300 dark:bg-white/[0.03] dark:text-gray-300 dark:border-gray-700'"
                                class="flex-1 py-2.5 rounded-lg border text-xs font-medium transition-colors text-center">
                                {{ $pm }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Discount --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Discount Amount (৳)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-sm text-gray-400 pointer-events-none">৳</span>
                            <input type="number" x-model="discount" min="0" placeholder="0"
                                class="w-full rounded-lg border border-gray-200 bg-white pl-8 pr-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                        </div>
                    </div>

                    {{-- Order Source --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Order Source</label>
                        <select x-model="orderSource"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300">
                            <option>Admin Panel</option>
                            <option>Phone Call</option>
                            <option>WhatsApp</option>
                            <option>Facebook</option>
                            <option>Instagram</option>
                            <option>Walk-in</option>
                        </select>
                    </div>

                    {{-- Internal Note --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Internal Note <span class="text-gray-400 font-normal">(not visible to customer)</span></label>
                        <textarea rows="2" placeholder="e.g. Customer requested urgent delivery, please prioritise…"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500 resize-none"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             RIGHT COLUMN  (1/3 width) — sticky summary
        ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-5">

                {{-- Order Summary --}}
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4">Order Summary</h2>

                    <div class="space-y-3">
                        {{-- Items count --}}
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Items</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300" x-text="cart.reduce((s,c) => s + c.qty, 0) + ' item(s)'">0 item(s)</span>
                        </div>
                        {{-- Sub total --}}
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Sub Total</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300" x-text="'৳ ' + subTotal.toLocaleString()">৳ 0</span>
                        </div>
                        {{-- Shipping --}}
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                Shipping
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400" x-text="shippingType"></span>
                            </span>
                            <span class="font-medium text-gray-700 dark:text-gray-300" x-text="'৳ ' + shippingCharge">৳ 80</span>
                        </div>
                        {{-- Discount --}}
                        <div class="flex items-center justify-between text-sm" x-show="discount > 0" style="display:none;">
                            <span class="text-gray-500 dark:text-gray-400">Discount</span>
                            <span class="font-medium text-red-500" x-text="'- ৳ ' + Number(discount).toLocaleString()"></span>
                        </div>
                        {{-- Payment --}}
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Payment</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400" x-text="paymentMethod"></span>
                        </div>
                    </div>

                    <div class="my-4 border-t border-gray-100 dark:border-gray-800"></div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Total Amount</span>
                        <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400" x-text="'৳ ' + total.toLocaleString()">৳ 80</span>
                    </div>

                    {{-- Source badge --}}
                    <div class="mt-3 flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                        Source: <span x-text="orderSource" class="text-gray-600 dark:text-gray-400"></span>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="space-y-3">
                    <button type="button" @click="submit()"
                        :disabled="cart.length === 0 || !selectedMerchant || (customerSearchState !== 'found' && customerSearchState !== 'not_found') || !customer.name"
                        class="w-full py-3 rounded-xl bg-brand-500 hover:bg-brand-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Place Order
                    </button>
                    <a href="{{ route('orders.all') }}"
                        class="w-full py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:hover:bg-white/[0.06] text-sm font-medium text-gray-600 dark:text-gray-300 transition-colors flex items-center justify-center gap-2">
                        Cancel
                    </a>
                </div>

                {{-- Validation hint --}}
                <div x-show="cart.length === 0 || !selectedMerchant || customerSearchState === 'idle' || !customer.name"
                    class="rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-500/30 dark:bg-amber-500/10 px-4 py-3 text-xs text-amber-700 dark:text-amber-400 space-y-1"
                    style="display:none;">
                    <p class="font-semibold">Required before placing order:</p>
                    <p x-show="!selectedMerchant">• Select a merchant</p>
                    <p x-show="customerSearchState === 'idle'">• Search for a customer by phone</p>
                    <p x-show="(customerSearchState === 'found' || customerSearchState === 'not_found') && !customer.name">• Enter customer full name</p>
                    <p x-show="cart.length === 0">• Add at least one product</p>
                </div>

                {{-- Success state --}}
                <div x-show="submitted" class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-500/30 dark:bg-emerald-500/10 px-4 py-4 text-center" style="display:none;">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Order Placed!</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-0.5">The order has been created successfully.</p>
                    <a href="{{ route('orders.all') }}" class="inline-block mt-3 text-xs font-medium text-emerald-700 dark:text-emerald-400 hover:underline">View All Orders →</a>
                </div>
            </div>
        </div>
    </div>
@endsection
