@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="File Manager" />

    @php
        $folders = [
            ['name' => 'All Files',   'icon' => 'all',       'count' => 142, 'active' => true],
            ['name' => 'Images',      'icon' => 'images',    'count' => 87],
            ['name' => 'Videos',      'icon' => 'videos',    'count' => 12],
            ['name' => 'Documents',   'icon' => 'docs',      'count' => 31],
            ['name' => 'Products',    'icon' => 'folder',    'count' => 64],
            ['name' => 'Banners',     'icon' => 'folder',    'count' => 18],
            ['name' => 'Categories',  'icon' => 'folder',    'count' => 22],
            ['name' => 'Merchants',   'icon' => 'folder',    'count' => 45],
        ];

        $files = [
            ['name' => 'hero-banner-summer.jpg',   'type' => 'image',    'size' => '248 KB', 'folder' => 'Banners',    'date' => '12 Apr 2026', 'url' => '/images/sample/hero-banner.jpg',   'thumb' => 'https://placehold.co/160x100/e0f2fe/0284c7?text=JPG'],
            ['name' => 'product-shoes-01.png',     'type' => 'image',    'size' => '312 KB', 'folder' => 'Products',   'date' => '11 Apr 2026', 'url' => '/images/sample/product-shoes.png', 'thumb' => 'https://placehold.co/160x100/f0fdf4/15803d?text=PNG'],
            ['name' => 'category-electronics.jpg', 'type' => 'image',    'size' => '185 KB', 'folder' => 'Categories', 'date' => '10 Apr 2026', 'url' => '/images/sample/cat-electronics.jpg','thumb' => 'https://placehold.co/160x100/fef9c3/854d0e?text=JPG'],
            ['name' => 'merchant-logo-abc.png',    'type' => 'image',    'size' => '54 KB',  'folder' => 'Merchants',  'date' => '10 Apr 2026', 'url' => '/images/sample/merchant-abc.png',  'thumb' => 'https://placehold.co/160x100/fce7f3/9d174d?text=PNG'],
            ['name' => 'promo-reel-april.mp4',     'type' => 'video',    'size' => '8.4 MB', 'folder' => 'Banners',    'date' => '09 Apr 2026', 'url' => '/videos/sample/promo-reel.mp4',    'thumb' => null],
            ['name' => 'product-tshirt-blue.jpg',  'type' => 'image',    'size' => '202 KB', 'folder' => 'Products',   'date' => '09 Apr 2026', 'url' => '/images/sample/tshirt-blue.jpg',   'thumb' => 'https://placehold.co/160x100/ede9fe/5b21b6?text=JPG'],
            ['name' => 'tos-document-v2.pdf',      'type' => 'document', 'size' => '1.1 MB', 'folder' => 'Documents',  'date' => '08 Apr 2026', 'url' => '/docs/tos-v2.pdf',                 'thumb' => null],
            ['name' => 'slider-eid-offer.jpg',     'type' => 'image',    'size' => '390 KB', 'folder' => 'Banners',    'date' => '07 Apr 2026', 'url' => '/images/sample/slider-eid.jpg',    'thumb' => 'https://placehold.co/160x100/ffedd5/c2410c?text=JPG'],
            ['name' => 'invoice-template.xlsx',    'type' => 'document', 'size' => '64 KB',  'folder' => 'Documents',  'date' => '06 Apr 2026', 'url' => '/docs/invoice-template.xlsx',      'thumb' => null],
            ['name' => 'category-fashion.jpg',     'type' => 'image',    'size' => '220 KB', 'folder' => 'Categories', 'date' => '05 Apr 2026', 'url' => '/images/sample/cat-fashion.jpg',   'thumb' => 'https://placehold.co/160x100/ecfdf5/065f46?text=JPG'],
            ['name' => 'intro-video.mp4',          'type' => 'video',    'size' => '14.2 MB','folder' => 'Banners',    'date' => '04 Apr 2026', 'url' => '/videos/sample/intro.mp4',         'thumb' => null],
            ['name' => 'brand-guidelines.pdf',     'type' => 'document', 'size' => '3.2 MB', 'folder' => 'Documents',  'date' => '03 Apr 2026', 'url' => '/docs/brand-guidelines.pdf',       'thumb' => null],
        ];
    @endphp

    <div
        x-data="{
            activeFolder: 'All Files',
            viewMode: 'grid',
            searchQuery: '',
            selectedFiles: [],
            showUploadModal: false,
            showFolderModal: false,
            newFolderName: '',
            copiedUrl: null,
            files: {{ json_encode($files) }},
            get filteredFiles() {
                return this.files.filter(f => {
                    const matchFolder = this.activeFolder === 'All Files' ||
                        f.folder === this.activeFolder ||
                        (this.activeFolder === 'Images' && f.type === 'image') ||
                        (this.activeFolder === 'Videos' && f.type === 'video') ||
                        (this.activeFolder === 'Documents' && f.type === 'document');
                    const matchSearch = this.searchQuery === '' ||
                        f.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    return matchFolder && matchSearch;
                });
            },
            toggleSelect(name) {
                if (this.selectedFiles.includes(name)) {
                    this.selectedFiles = this.selectedFiles.filter(n => n !== name);
                } else {
                    this.selectedFiles.push(name);
                }
            },
            isSelected(name) {
                return this.selectedFiles.includes(name);
            },
            selectAll() {
                if (this.selectedFiles.length === this.filteredFiles.length) {
                    this.selectedFiles = [];
                } else {
                    this.selectedFiles = this.filteredFiles.map(f => f.name);
                }
            },
            copyUrl(url) {
                navigator.clipboard.writeText(window.location.origin + url).catch(() => {});
                this.copiedUrl = url;
                setTimeout(() => { this.copiedUrl = null; }, 2000);
            },
            typeIcon(type) {
                if (type === 'image') return 'image';
                if (type === 'video') return 'video';
                return 'document';
            },
            typeBadgeClass(type) {
                if (type === 'image')    return 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30';
                if (type === 'video')    return 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/30';
                return 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/30';
            },
        }"
        class="flex gap-5">

        {{-- ── Sidebar / Folder Tree ───────────────────────────────────────── --}}
        <div class="w-56 flex-shrink-0">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-4">

                {{-- Upload button --}}
                <button @click="showUploadModal = true" type="button"
                    class="flex items-center justify-center gap-2 w-full rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium py-2.5 px-4 transition-colors mb-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Files
                </button>

                {{-- New Folder --}}
                <button @click="showFolderModal = true" type="button"
                    class="flex items-center justify-center gap-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-sm font-medium py-2.5 px-4 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors mb-5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    New Folder
                </button>

                {{-- Folder list --}}
                <p class="text-[11px] uppercase font-semibold text-gray-400 dark:text-gray-500 mb-2 tracking-wide px-1">Folders</p>
                <ul class="space-y-0.5">
                    @foreach ($folders as $folder)
                        <li>
                            <button @click="activeFolder = '{{ $folder['name'] }}'" type="button"
                                class="flex items-center gap-2.5 w-full rounded-lg px-2.5 py-2 text-sm transition-colors"
                                :class="activeFolder === '{{ $folder['name'] }}'
                                    ? 'bg-brand-50 text-brand-600 font-medium dark:bg-brand-500/10 dark:text-brand-400'
                                    : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]'">

                                {{-- Icon --}}
                                @if ($folder['icon'] === 'all')
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7C3 5.89543 3.89543 5 5 5H9.58579C9.851 5 10.1054 5.10536 10.2929 5.29289L11.7071 6.70711C11.8946 6.89464 12.149 7 12.4142 7H19C20.1046 7 21 7.89543 21 9V17C21 18.1046 20.1046 19 19 19H5C3.89543 19 3 18.1046 3 17V7Z"/></svg>
                                @elseif ($folder['icon'] === 'images')
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                @elseif ($folder['icon'] === 'videos')
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/></svg>
                                @elseif ($folder['icon'] === 'docs')
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                @else
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7C3 5.89543 3.89543 5 5 5H9.58579C9.851 5 10.1054 5.10536 10.2929 5.29289L11.7071 6.70711C11.8946 6.89464 12.149 7 12.4142 7H19C20.1046 7 21 7.89543 21 9V17C21 18.1046 20.1046 19 19 19H5C3.89543 19 3 18.1046 3 17V7Z"/></svg>
                                @endif

                                <span class="flex-1 truncate text-left">{{ $folder['name'] }}</span>
                                <span class="text-xs tabular-nums"
                                    :class="activeFolder === '{{ $folder['name'] }}' ? 'text-brand-500' : 'text-gray-400 dark:text-gray-600'">
                                    {{ $folder['count'] }}
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                {{-- Storage usage --}}
                <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">Storage Used</p>
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                        <div class="bg-brand-500 h-1.5 rounded-full" style="width: 38%"></div>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">3.8 GB of 10 GB</p>
                </div>
            </div>
        </div>

        {{-- ── Main Content ─────────────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                {{-- Toolbar --}}
                <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">

                    {{-- Breadcrumb / Folder title --}}
                    <div class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 mr-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7C3 5.89543 3.89543 5 5 5H9.58579C9.851 5 10.1054 5.10536 10.2929 5.29289L11.7071 6.70711C11.8946 6.89464 12.149 7 12.4142 7H19C20.1046 7 21 7.89543 21 9V17C21 18.1046 20.1046 19 19 19H5C3.89543 19 3 18.1046 3 17V7Z"/></svg>
                        <span x-text="activeFolder" class="font-medium text-gray-800 dark:text-white/90"></span>
                        <span class="text-gray-400 dark:text-gray-600">(<span x-text="filteredFiles.length"></span> files)</span>
                    </div>

                    {{-- Search --}}
                    <div class="relative flex-1 min-w-[180px] max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        </div>
                        <input x-model="searchQuery" type="text" placeholder="Search files…"
                            class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-4 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:placeholder-gray-500" />
                    </div>

                    <div class="flex items-center gap-2 ml-auto">
                        {{-- Delete selected --}}
                        <template x-if="selectedFiles.length > 0">
                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600 hover:bg-red-100 transition-colors dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                Delete (<span x-text="selectedFiles.length"></span>)
                            </button>
                        </template>

                        {{-- View toggle --}}
                        <div class="flex items-center rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <button @click="viewMode = 'grid'" type="button"
                                class="p-2 transition-colors"
                                :class="viewMode === 'grid' ? 'bg-brand-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.07]'">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            </button>
                            <button @click="viewMode = 'list'" type="button"
                                class="p-2 transition-colors"
                                :class="viewMode === 'list' ? 'bg-brand-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.07]'">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            </button>
                        </div>

                        {{-- Upload shortcut --}}
                        <button @click="showUploadModal = true" type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 px-3 py-2 text-sm font-medium text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Upload
                        </button>
                    </div>
                </div>

                {{-- ── Grid View ─────────────────────────────────────────────── --}}
                <div x-show="viewMode === 'grid'" class="p-5">
                    {{-- Select all row --}}
                    <div class="flex items-center gap-2 mb-4">
                        <input type="checkbox" id="selectAll"
                            @click="selectAll()"
                            :checked="selectedFiles.length > 0 && selectedFiles.length === filteredFiles.length"
                            class="w-4 h-4 rounded border-gray-300 text-brand-500 cursor-pointer dark:border-gray-600">
                        <label for="selectAll" class="text-sm text-gray-500 dark:text-gray-400 cursor-pointer select-none">Select all</label>
                    </div>

                    <template x-if="filteredFiles.length === 0">
                        <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-600">
                            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7C3 5.89543 3.89543 5 5 5H9.58579C9.851 5 10.1054 5.10536 10.2929 5.29289L11.7071 6.70711C11.8946 6.89464 12.149 7 12.4142 7H19C20.1046 7 21 7.89543 21 9V17C21 18.1046 20.1046 19 19 19H5C3.89543 19 3 18.1046 3 17V7Z"/></svg>
                            <p class="text-sm">No files found</p>
                        </div>
                    </template>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4">
                        <template x-for="file in filteredFiles" :key="file.name">
                            <div class="group relative rounded-xl border transition-all cursor-pointer"
                                :class="isSelected(file.name)
                                    ? 'border-brand-400 bg-brand-50 dark:bg-brand-500/10 dark:border-brand-500/50'
                                    : 'border-gray-200 bg-gray-50 hover:border-brand-200 hover:bg-brand-50/30 dark:border-gray-700 dark:bg-white/[0.03] dark:hover:border-brand-500/30'">

                                {{-- Checkbox --}}
                                <div class="absolute top-2 left-2 z-10"
                                    :class="isSelected(file.name) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 transition-opacity'">
                                    <input type="checkbox"
                                        :checked="isSelected(file.name)"
                                        @click.stop="toggleSelect(file.name)"
                                        class="w-4 h-4 rounded border-gray-300 text-brand-500 cursor-pointer dark:border-gray-600">
                                </div>

                                {{-- Thumbnail / Icon --}}
                                <div @click="toggleSelect(file.name)" class="p-3 pb-0">
                                    <div class="w-full aspect-[16/10] rounded-lg overflow-hidden flex items-center justify-center bg-white dark:bg-gray-800">
                                        <template x-if="file.thumb && file.type === 'image'">
                                            <img :src="file.thumb" :alt="file.name" class="w-full h-full object-cover" />
                                        </template>
                                        <template x-if="!file.thumb && file.type === 'video'">
                                            <svg class="w-10 h-10 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/></svg>
                                        </template>
                                        <template x-if="!file.thumb && file.type === 'document'">
                                            <svg class="w-10 h-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                        </template>
                                    </div>
                                </div>

                                {{-- File info --}}
                                <div class="p-3">
                                    <p class="text-xs font-medium text-gray-800 dark:text-white/90 truncate" x-text="file.name"></p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500" x-text="file.size"></span>
                                        <span class="inline-flex items-center rounded border px-1.5 py-0.5 text-[10px] font-medium uppercase"
                                            :class="typeBadgeClass(file.type)"
                                            x-text="file.type"></span>
                                    </div>
                                </div>

                                {{-- Hover action bar --}}
                                <div class="absolute bottom-0 left-0 right-0 flex items-center justify-end gap-1 p-2 opacity-0 group-hover:opacity-100 transition-opacity bg-gradient-to-t from-white dark:from-gray-900 rounded-b-xl">
                                    {{-- Copy URL --}}
                                    <button @click.stop="copyUrl(file.url)" type="button"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg transition-colors"
                                        :class="copiedUrl === file.url
                                            ? 'bg-emerald-500 text-white'
                                            : 'bg-gray-100 text-gray-500 hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-400'"
                                        :title="copiedUrl === file.url ? 'Copied!' : 'Copy URL'">
                                        <template x-if="copiedUrl !== file.url">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </template>
                                        <template x-if="copiedUrl === file.url">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        </template>
                                    </button>
                                    {{-- Rename --}}
                                    <button type="button" title="Rename"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-gray-800 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </button>
                                    {{-- Delete --}}
                                    <button type="button" title="Delete"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-gray-800 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── List View ──────────────────────────────────────────────── --}}
                <div x-show="viewMode === 'list'" style="display:none;">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left font-medium w-8">
                                        <input type="checkbox" @click="selectAll()"
                                            :checked="selectedFiles.length > 0 && selectedFiles.length === filteredFiles.length"
                                            class="w-4 h-4 rounded border-gray-300 text-brand-500 dark:border-gray-600">
                                    </th>
                                    <th class="px-5 py-3 text-left font-medium">File Name</th>
                                    <th class="px-5 py-3 text-left font-medium">Folder</th>
                                    <th class="px-5 py-3 text-left font-medium">Type</th>
                                    <th class="px-5 py-3 text-right font-medium">Size</th>
                                    <th class="px-5 py-3 text-left font-medium">Date</th>
                                    <th class="px-5 py-3 text-center font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="filteredFiles.length === 0">
                                    <tr>
                                        <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400 dark:text-gray-600">No files found</td>
                                    </tr>
                                </template>
                                <template x-for="file in filteredFiles" :key="file.name">
                                    <tr class="border-b border-gray-50 dark:border-gray-800/60 hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors"
                                        :class="isSelected(file.name) ? 'bg-brand-50/50 dark:bg-brand-500/5' : ''">
                                        <td class="px-5 py-3">
                                            <input type="checkbox"
                                                :checked="isSelected(file.name)"
                                                @click="toggleSelect(file.name)"
                                                class="w-4 h-4 rounded border-gray-300 text-brand-500 cursor-pointer dark:border-gray-600">
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                {{-- mini icon --}}
                                                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                                    :class="file.type === 'image' ? 'bg-blue-50 dark:bg-blue-500/10' : file.type === 'video' ? 'bg-purple-50 dark:bg-purple-500/10' : 'bg-amber-50 dark:bg-amber-500/10'">
                                                    <template x-if="file.type === 'image'">
                                                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                                    </template>
                                                    <template x-if="file.type === 'video'">
                                                        <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/></svg>
                                                    </template>
                                                    <template x-if="file.type === 'document'">
                                                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                    </template>
                                                </div>
                                                <span class="text-sm font-medium text-gray-800 dark:text-white/90 truncate max-w-[200px]" x-text="file.name"></span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="file.folder"></td>
                                        <td class="px-5 py-3">
                                            <span class="inline-flex items-center rounded border px-2 py-0.5 text-xs font-medium uppercase"
                                                :class="typeBadgeClass(file.type)"
                                                x-text="file.type"></span>
                                        </td>
                                        <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400 text-right tabular-nums" x-text="file.size"></td>
                                        <td class="px-5 py-3 text-sm text-gray-400 dark:text-gray-500 whitespace-nowrap" x-text="file.date"></td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button @click="copyUrl(file.url)" type="button"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                                    :class="copiedUrl === file.url
                                                        ? 'bg-emerald-500 text-white'
                                                        : 'bg-gray-100 text-gray-500 hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-400'"
                                                    :title="copiedUrl === file.url ? 'Copied!' : 'Copy URL'">
                                                    <template x-if="copiedUrl !== file.url">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    </template>
                                                    <template x-if="copiedUrl === file.url">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    </template>
                                                </button>
                                                <button type="button" title="Rename"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-brand-500 hover:text-white transition-colors dark:bg-gray-800 dark:text-gray-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                                </button>
                                                <button type="button" title="Delete"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-500 hover:text-white transition-colors dark:bg-gray-800 dark:text-gray-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>{{-- end main card --}}
        </div>{{-- end main content --}}

        {{-- ── Upload Modal ──────────────────────────────────────────────────── --}}
        <template x-teleport="body">
            <div x-show="showUploadModal" x-transition.opacity
                class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60"
                @click.self="showUploadModal = false"
                style="display:none;">
                <div @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">

                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Upload Files</h3>
                        <button @click="showUploadModal = false" type="button"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Drop zone --}}
                    <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-8 flex flex-col items-center justify-center gap-3 text-center hover:border-brand-300 dark:hover:border-brand-500/50 transition-colors cursor-pointer">
                        <div class="w-12 h-12 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Drag & drop files here</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">or click to browse from your computer</p>
                        </div>
                        <input type="file" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                        <p class="text-xs text-gray-400 dark:text-gray-500">Supports: JPG, PNG, GIF, SVG, MP4, PDF, XLSX (max 20 MB)</p>
                    </div>

                    {{-- Folder select --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Upload to folder</label>
                        <select class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            @foreach ($folders as $folder)
                                @if (!in_array($folder['name'], ['All Files', 'Images', 'Videos', 'Documents']))
                                    <option>{{ $folder['name'] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 mt-5">
                        <button @click="showUploadModal = false" type="button"
                            class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                            Cancel
                        </button>
                        <button type="button"
                            class="rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-sm font-medium text-white transition-colors">
                            Upload Files
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ── New Folder Modal ─────────────────────────────────────────────── --}}
        <template x-teleport="body">
            <div x-show="showFolderModal" x-transition.opacity
                class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60"
                @click.self="showFolderModal = false"
                style="display:none;">
                <div @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">

                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Create New Folder</h3>
                        <button @click="showFolderModal = false" type="button"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Folder Name</label>
                        <input x-model="newFolderName" type="text" placeholder="e.g. Campaign Assets"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 placeholder-gray-400 dark:placeholder-gray-600" />
                    </div>

                    <div class="flex justify-end gap-3 mt-5">
                        <button @click="showFolderModal = false" type="button"
                            class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                            Cancel
                        </button>
                        <button @click="showFolderModal = false" type="button"
                            class="rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-sm font-medium text-white transition-colors">
                            Create Folder
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>{{-- end x-data wrapper --}}
@endsection
