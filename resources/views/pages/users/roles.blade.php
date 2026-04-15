@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Roles & Permissions" />

    <div class="rounded-2xl border border-gray-200 bg-white pt-5 pb-5 dark:border-gray-800 dark:bg-white/[0.03] sm:pt-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-5 px-5 sm:px-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage admin roles and their permissions</p>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Role
            </button>
        </div>

        {{-- Roles Grid --}}
        <div class="px-5 sm:px-6 grid grid-cols-1 xl:grid-cols-2 gap-5">
            @php
                $roles = [
                    ['name' => 'Super Admin', 'description' => 'Full access to all features and settings. Can manage other admins.', 'users' => 1, 'color' => 'red', 'permissions' => ['Dashboard', 'Orders', 'Products', 'Merchants', 'Accounts', 'Users', 'Settings', 'Reports']],
                    ['name' => 'Admin', 'description' => 'Access to most features except user management and critical settings.', 'users' => 1, 'color' => 'brand', 'permissions' => ['Dashboard', 'Orders', 'Products', 'Merchants', 'Accounts', 'Reports']],
                    ['name' => 'Manager', 'description' => 'Can manage orders, products and merchants. No access to finance.', 'users' => 2, 'color' => 'violet', 'permissions' => ['Dashboard', 'Orders', 'Products', 'Merchants']],
                    ['name' => 'Support', 'description' => 'Handle customer queries, help requests and order issues only.', 'users' => 3, 'color' => 'emerald', 'permissions' => ['Dashboard', 'Orders', 'Help Requests']],
                ];
            @endphp

            @foreach ($roles as $role)
                @php
                    $colors = [
                        'red' => ['bg' => 'bg-red-50 dark:bg-red-500/10', 'text' => 'text-red-600 dark:text-red-400', 'border' => 'border-red-200 dark:border-red-500/20', 'badge' => 'bg-red-500'],
                        'brand' => ['bg' => 'bg-brand-50 dark:bg-brand-500/10', 'text' => 'text-brand-600 dark:text-brand-400', 'border' => 'border-brand-200 dark:border-brand-500/20', 'badge' => 'bg-brand-500'],
                        'violet' => ['bg' => 'bg-violet-50 dark:bg-violet-500/10', 'text' => 'text-violet-600 dark:text-violet-400', 'border' => 'border-violet-200 dark:border-violet-500/20', 'badge' => 'bg-violet-500'],
                        'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400', 'border' => 'border-emerald-200 dark:border-emerald-500/20', 'badge' => 'bg-emerald-500'],
                    ];
                    $c = $colors[$role['color']];
                @endphp
                <div class="rounded-xl border {{ $c['border'] }} overflow-hidden">
                    {{-- Role Header --}}
                    <div class="flex items-center justify-between px-5 py-4 {{ $c['bg'] }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg {{ $c['badge'] }} flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold {{ $c['text'] }}">{{ $role['name'] }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $role['users'] }} {{ $role['users'] === 1 ? 'user' : 'users' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/80 text-gray-500 hover:text-brand-500 transition-colors dark:bg-gray-800/80 dark:text-gray-400" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            </button>
                            @if($role['name'] !== 'Super Admin')
                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/80 text-gray-500 hover:text-red-500 transition-colors dark:bg-gray-800/80 dark:text-gray-400" title="Delete">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Role Body --}}
                    <div class="px-5 py-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $role['description'] }}</p>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Permissions</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($role['permissions'] as $perm)
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $perm }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
