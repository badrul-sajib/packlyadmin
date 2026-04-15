@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="SLA Rules" />

    @php
        $rules = [
            ['id' => 1, 'category' => 'Help Request',   'priority' => 'Low',      'target_hours' => 8,  'escalation_hours' => 12, 'assignee' => 'Any Agent',    'active' => true],
            ['id' => 2, 'category' => 'Help Request',   'priority' => 'High',     'target_hours' => 2,  'escalation_hours' => 4,  'assignee' => 'Senior Agent', 'active' => true],
            ['id' => 3, 'category' => 'Order Issue',    'priority' => 'Low',      'target_hours' => 6,  'escalation_hours' => 10, 'assignee' => 'Any Agent',    'active' => true],
            ['id' => 4, 'category' => 'Order Issue',    'priority' => 'Critical', 'target_hours' => 1,  'escalation_hours' => 2,  'assignee' => 'Team Lead',    'active' => true],
            ['id' => 5, 'category' => 'Merchant Issue', 'priority' => 'Low',      'target_hours' => 12, 'escalation_hours' => 24, 'assignee' => 'KAM Agent',    'active' => true],
            ['id' => 6, 'category' => 'Merchant Issue', 'priority' => 'High',     'target_hours' => 4,  'escalation_hours' => 8,  'assignee' => 'KAM Lead',     'active' => false],
            ['id' => 7, 'category' => 'Payment',        'priority' => 'High',     'target_hours' => 4,  'escalation_hours' => 6,  'assignee' => 'Finance Team', 'active' => true],
            ['id' => 8, 'category' => 'Returns',        'priority' => 'Low',      'target_hours' => 24, 'escalation_hours' => 48, 'assignee' => 'Any Agent',    'active' => true],
        ];

        $priorityColors = [
            'Low'      => 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
            'High'     => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/30',
            'Critical' => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30',
        ];
    @endphp

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
         x-data="{ showAddModal: false }">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Define response time targets and escalation thresholds per ticket category and priority.
            </p>
            <button type="button" @click="showAddModal = true"
                class="ml-auto inline-flex items-center gap-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-4 py-2 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New Rule
            </button>
        </div>

        {{-- Rules table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-left">Priority</th>
                        <th class="px-5 py-3 text-center">Target Response</th>
                        <th class="px-5 py-3 text-center">Escalation At</th>
                        <th class="px-5 py-3 text-left">Assignee</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @foreach ($rules as $rule)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $rule['category'] }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $priorityColors[$rule['priority']] }}">
                                    {{ $rule['priority'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <svg class="w-3.5 h-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $rule['target_hours'] }}h
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-amber-600 dark:text-amber-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    {{ $rule['escalation_hours'] }}h
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $rule['assignee'] }}</td>
                            <td class="px-5 py-4 text-center">
                                <button type="button"
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none
                                        {{ $rule['active'] ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-700' }}"
                                    role="switch" aria-checked="{{ $rule['active'] ? 'true' : 'false' }}">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200
                                        {{ $rule['active'] ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" title="Edit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-brand-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </button>
                                    <button type="button" title="Delete"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ count($rules) }} rules configured — changes apply to new tickets immediately</p>
        </div>

        {{-- Add Rule Modal --}}
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center" x-transition.opacity style="display:none;">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showAddModal = false"></div>
            <div class="relative w-full max-w-md mx-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">New SLA Rule</h3>
                    <button @click="showAddModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Category</label>
                        <select class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500">
                            <option>Help Request</option>
                            <option>Order Issue</option>
                            <option>Merchant Issue</option>
                            <option>Payment</option>
                            <option>Returns</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Priority</label>
                        <select class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500">
                            <option>Low</option>
                            <option>High</option>
                            <option>Critical</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Target Response (hours)</label>
                            <input type="number" min="1" placeholder="e.g. 4"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Escalation At (hours)</label>
                            <input type="number" min="1" placeholder="e.g. 8"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Assignee</label>
                        <select class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 outline-none focus:border-brand-500">
                            <option>Any Agent</option>
                            <option>Senior Agent</option>
                            <option>Team Lead</option>
                            <option>KAM Agent</option>
                            <option>KAM Lead</option>
                            <option>Finance Team</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="showAddModal = false"
                        class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="showAddModal = false"
                        class="flex-1 rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors">
                        Save Rule
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
