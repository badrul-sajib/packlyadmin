@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Sliders" />

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Sliders</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">12</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">9</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Inactive</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">3</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sections</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white/90 mt-1">3</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25h2.25A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Slider Cards --}}
    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6" x-data="{ activeType: 'all' }">
        {{-- Filters --}}
        <div class="flex items-center gap-3 mb-5 px-5 sm:px-6">
            {{-- Type Tabs --}}
            <div class="flex items-center gap-2">
                <button @click="activeType = 'all'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeType === 'all' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    All
                </button>
                <button @click="activeType = 'home1'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeType === 'home1' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    Home Banner 1
                </button>
                <button @click="activeType = 'hero'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeType === 'hero' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    Hero Section
                </button>
                <button @click="activeType = 'home2'" type="button"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeType === 'home2' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                    Home Banner 2
                </button>
            </div>
            <div class="flex-1"></div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Slider
            </button>
        </div>

        {{-- Slider Grid --}}
        <div class="px-5 sm:px-6">
            @php
                $sliders = [
                    ['title' => 'Home Banner', 'type' => 'Home Banner 1', 'type_color' => 'bg-emerald-500', 'sub_title' => 'Main promotional banner for homepage', 'status' => 'Active', 'order' => 1, 'clicks' => 1240, 'views' => 18500],
                    ['title' => 'Baisakhi Blast', 'type' => 'Hero Section', 'type_color' => 'bg-orange-500', 'sub_title' => 'Seasonal Baisakhi sale campaign', 'status' => 'Active', 'order' => 2, 'clicks' => 890, 'views' => 12300],
                    ['title' => 'Boishakh-1', 'type' => 'Hero Section', 'type_color' => 'bg-orange-500', 'sub_title' => 'Bengali new year celebration', 'status' => 'Active', 'order' => 3, 'clicks' => 720, 'views' => 9800],
                    ['title' => 'baishaki B2', 'type' => 'Hero Section', 'type_color' => 'bg-orange-500', 'sub_title' => 'Baishakhi flash deals banner', 'status' => 'Active', 'order' => 4, 'clicks' => 560, 'views' => 7600],
                    ['title' => 'blast', 'type' => 'Home Banner 2', 'type_color' => 'bg-yellow-500', 'sub_title' => 'Secondary homepage promotion', 'status' => 'Inactive', 'order' => 5, 'clicks' => 0, 'views' => 0],
                ];
            @endphp

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                @foreach ($sliders as $index => $slider)
                    <div class="rounded-xl border {{ $slider['status'] === 'Inactive' ? 'border-red-200 dark:border-red-500/20 opacity-75' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-white/[0.02] overflow-hidden group hover:shadow-md transition-shadow">
                        {{-- Preview Area --}}
                        <div class="relative">
                            {{-- Desktop Preview --}}
                            <div class="w-full h-40 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-500 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Desktop Preview</p>
                                </div>
                            </div>

                            {{-- Order Badge --}}
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/90 dark:bg-gray-900/90 text-xs font-bold text-gray-700 dark:text-gray-300 shadow-sm border border-gray-200 dark:border-gray-700">{{ $slider['order'] }}</span>
                            </div>

                            {{-- Status + Type Badges --}}
                            <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                <span class="rounded px-2 py-0.5 text-[10px] font-bold text-white {{ $slider['type_color'] }}">{{ $slider['type'] }}</span>
                                @if($slider['status'] === 'Active')
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-500 text-white">Live</span>
                                @else
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-medium bg-red-500 text-white">Off</span>
                                @endif
                            </div>

                            {{-- Hover Actions Overlay --}}
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100">
                                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                    Edit
                                </button>
                                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-xs font-medium text-red-600 shadow-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    Delete
                                </button>
                            </div>
                        </div>

                        {{-- Info Area --}}
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $slider['title'] }}</h4>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $slider['sub_title'] }}</p>
                                </div>
                                {{-- Toggle --}}
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" class="sr-only peer" {{ $slider['status'] === 'Active' ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>

                            {{-- Stats --}}
                            @if($slider['status'] === 'Active')
                                <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($slider['views']) }} views</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243l-1.59-1.59"/></svg>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($slider['clicks']) }} clicks</span>
                                    </div>
                                    @if($slider['views'] > 0)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ round(($slider['clicks'] / $slider['views']) * 100, 1) }}% CTR</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Slider is currently disabled</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
