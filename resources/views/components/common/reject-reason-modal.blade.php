{{--
    Reject Reason Modal — shared Alpine.js component
    Usage: include anywhere, then trigger with $dispatch('open-reject-modal', { label: 'Shop Name Change' })
--}}
<div
    x-data="{
        open: false,
        label: '',
        reason: '',
        submitted: false,
        init() {
            window.addEventListener('open-reject-modal', (e) => {
                this.label  = e.detail?.label ?? 'Request';
                this.reason = '';
                this.submitted = false;
                this.open = true;
            });
        },
        submit() {
            if (!this.reason.trim()) return;
            this.submitted = true;
            // In a real app: POST reason to backend here
            setTimeout(() => { this.open = false; }, 800);
        }
    }"
    x-show="open"
    x-transition.opacity
    class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60"
    @click.self="open = false"
    style="display:none;">

    <div @click.stop
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">

        {{-- Header --}}
        <div class="flex items-start gap-3 mb-5">
            <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Reject <span x-text="label"></span></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">This note is <span class="font-medium text-gray-700 dark:text-gray-300">private</span> — visible to admins only.</p>
            </div>
            <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Private badge --}}
        <div class="flex items-center gap-2 rounded-lg bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 px-3 py-2 mb-4">
            <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            <p class="text-xs text-amber-700 dark:text-amber-400">Stored as a private internal note. Not shown to the merchant.</p>
        </div>

        {{-- Reason input --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Rejection Reason <span class="text-red-500">*</span></label>
            <textarea
                x-model="reason"
                rows="4"
                placeholder="Explain why this is being rejected…"
                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] text-sm text-gray-700 dark:text-gray-300 px-3 py-2.5 outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 placeholder-gray-400 dark:placeholder-gray-600 resize-none"
                :class="!reason.trim() && submitted ? 'border-red-400 ring-1 ring-red-400' : ''">
            </textarea>
            <p x-show="!reason.trim() && submitted" class="text-xs text-red-500 mt-1">Rejection reason is required.</p>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 mt-5">
            <button @click="open = false" type="button"
                class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                Cancel
            </button>
            <button @click="submit()" type="button"
                class="rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors"
                :class="submitted && reason.trim() ? 'bg-emerald-500' : 'bg-red-500 hover:bg-red-600'">
                <template x-if="!(submitted && reason.trim())">
                    <span>Confirm Reject</span>
                </template>
                <template x-if="submitted && reason.trim()">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Rejected
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>
