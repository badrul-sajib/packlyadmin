<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Search Everythings Here</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Consignment ID --}}
        <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02]">
            <span class="flex items-center justify-center w-10 h-10 shrink-0 text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"/>
                </svg>
            </span>
            <input type="text" placeholder="Search With Consignment ID" class="flex-1 bg-transparent px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 outline-none border-none focus:ring-0" />
            <button class="shrink-0 px-3 py-2.5 text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300 border-l border-gray-200 dark:border-gray-700">
                Go
            </button>
        </div>

        {{-- Order Invoice ID --}}
        <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02]">
            <span class="flex items-center justify-center w-10 h-10 shrink-0 text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
            </span>
            <input type="text" placeholder="Search With Invoice ID" class="flex-1 bg-transparent px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 outline-none border-none focus:ring-0" />
            <button class="shrink-0 px-3 py-2.5 text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300 border-l border-gray-200 dark:border-gray-700">
                Go
            </button>
        </div>

        {{-- Merchant ID --}}
        <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02]">
            <span class="flex items-center justify-center w-10 h-10 shrink-0 text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72"/>
                </svg>
            </span>
            <input type="text" placeholder="Search With Merchant ID" class="flex-1 bg-transparent px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 outline-none border-none focus:ring-0" />
            <button class="shrink-0 px-3 py-2.5 text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300 border-l border-gray-200 dark:border-gray-700">
                Go
            </button>
        </div>

        {{-- Payment ID --}}
        <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.02]">
            <span class="flex items-center justify-center w-10 h-10 shrink-0 text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                </svg>
            </span>
            <input type="text" placeholder="Search With Payment ID" class="flex-1 bg-transparent px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 outline-none border-none focus:ring-0" />
            <button class="shrink-0 px-3 py-2.5 text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300 border-l border-gray-200 dark:border-gray-700">
                Go
            </button>
        </div>
    </div>
</div>
