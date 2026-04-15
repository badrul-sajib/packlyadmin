@props(['id' => 'dateRangePicker'])

<div x-data="dateRangePicker('{{ $id }}')" class="relative" @click.outside="open = false">
    {{-- Trigger Button --}}
    <button @click="open = !open" type="button"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05] transition-colors">
        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
        </svg>
        <span x-text="displayLabel"></span>
        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-[99999] mt-2 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900 min-w-[640px]"
        style="display: none;">

        <div class="flex">
            {{-- Preset Sidebar --}}
            <div class="w-44 shrink-0 border-r border-gray-200 dark:border-gray-700 py-2">
                <template x-for="preset in presets" :key="preset.key">
                    <button @click="selectPreset(preset.key)" type="button"
                        class="w-full px-4 py-2 text-left text-sm whitespace-nowrap transition-colors"
                        :class="activePreset === preset.key
                            ? 'bg-brand-500 text-white font-medium'
                            : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.05]'"
                        x-text="preset.label">
                    </button>
                </template>
            </div>

            {{-- Calendar Area --}}
            <div class="p-5">
                <div class="flex gap-8">
                    {{-- Left Calendar (Current Month) --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <button @click="prevMonth('left')" type="button" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-white/[0.05] text-gray-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="leftMonthLabel"></span>
                            <div class="w-6"></div>
                        </div>
                        <div class="grid grid-cols-7 gap-0 text-center text-xs text-gray-500 dark:text-gray-400 mb-1">
                            <template x-for="d in dayHeaders" :key="d"><span class="w-10 py-1" x-text="d"></span></template>
                        </div>
                        <div class="grid grid-cols-7 gap-0 text-center text-sm">
                            <template x-for="(day, i) in leftDays" :key="'l'+i">
                                <button @click="day.date && pickDate(day.date)" type="button"
                                    class="w-10 h-10 rounded-full text-sm transition-colors"
                                    :class="getDayClass(day)"
                                    :disabled="!day.date"
                                    x-text="day.label">
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Right Calendar (Next Month) --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-6"></div>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="rightMonthLabel"></span>
                            <button @click="nextMonth('right')" type="button" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-white/[0.05] text-gray-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-7 gap-0 text-center text-xs text-gray-500 dark:text-gray-400 mb-1">
                            <template x-for="d in dayHeaders" :key="'r'+d"><span class="w-10 py-1" x-text="d"></span></template>
                        </div>
                        <div class="grid grid-cols-7 gap-0 text-center text-sm">
                            <template x-for="(day, i) in rightDays" :key="'r'+i">
                                <button @click="day.date && pickDate(day.date)" type="button"
                                    class="w-10 h-10 rounded-full text-sm transition-colors"
                                    :class="getDayClass(day)"
                                    :disabled="!day.date"
                                    x-text="day.label">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-200 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400" x-text="footerLabel"></span>
            <div class="flex items-center gap-2">
                <button @click="cancelPick()" type="button" class="px-4 py-1.5 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button @click="applyRange()" type="button" class="px-4 py-1.5 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">
                    Apply
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dateRangePicker', (id) => ({
        id: id,
        open: false,
        activePreset: 'last_7',
        startDate: null,
        endDate: null,
        tempStart: null,
        tempEnd: null,
        leftMonth: null,
        leftYear: null,
        displayLabel: '',
        dayHeaders: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],

        presets: [
            { key: 'today', label: 'Today' },
            { key: 'yesterday', label: 'Yesterday' },
            { key: 'last_7', label: 'Last 7 Days' },
            { key: 'last_15', label: 'Last 15 Days' },
            { key: 'last_30', label: 'Last 30 Days' },
            { key: 'this_month', label: 'This Month' },
            { key: 'last_month', label: 'Last Month' },
            { key: 'this_year', label: 'This Year' },
            { key: 'last_year', label: 'Last Year' },
            { key: 'custom', label: 'Custom Range' },
        ],

        init() {
            const today = new Date();
            this.leftMonth = today.getMonth();
            this.leftYear = today.getFullYear();
            this.selectPreset('last_7');
            this.applyRange();
        },

        get leftMonthLabel() {
            return new Date(this.leftYear, this.leftMonth).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        },

        get rightMonthLabel() {
            let m = this.leftMonth + 1, y = this.leftYear;
            if (m > 11) { m = 0; y++; }
            return new Date(y, m).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        },

        get leftDays() { return this.buildDays(this.leftYear, this.leftMonth); },

        get rightDays() {
            let m = this.leftMonth + 1, y = this.leftYear;
            if (m > 11) { m = 0; y++; }
            return this.buildDays(y, m);
        },

        get footerLabel() {
            if (this.tempStart && this.tempEnd) return this.fmt(this.tempStart) + ' - ' + this.fmt(this.tempEnd);
            if (this.tempStart) return this.fmt(this.tempStart) + ' - ...';
            return 'Select a range';
        },

        buildDays(year, month) {
            const days = [];
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const prevDays = new Date(year, month, 0).getDate();

            for (let i = firstDay - 1; i >= 0; i--) {
                days.push({ label: prevDays - i, date: null });
            }
            for (let d = 1; d <= daysInMonth; d++) {
                days.push({ label: d, date: new Date(year, month, d) });
            }
            const remaining = 42 - days.length;
            for (let d = 1; d <= remaining; d++) {
                days.push({ label: d, date: null });
            }
            return days;
        },

        getDayClass(day) {
            if (!day.date) return 'text-gray-300 dark:text-gray-600 cursor-default';

            const ts = day.date.getTime();
            const sTs = this.tempStart ? this.tempStart.getTime() : null;
            const eTs = this.tempEnd ? this.tempEnd.getTime() : null;

            if (sTs && ts === sTs) return 'bg-brand-500 text-white';
            if (eTs && ts === eTs) return 'bg-brand-500 text-white';
            if (sTs && eTs && ts > sTs && ts < eTs) return 'bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400';

            return 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/[0.05] cursor-pointer';
        },

        selectPreset(key) {
            this.activePreset = key;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            let start, end;

            switch (key) {
                case 'today':
                    start = end = new Date(today);
                    break;
                case 'yesterday':
                    start = end = new Date(today);
                    start.setDate(start.getDate() - 1);
                    end = new Date(start);
                    break;
                case 'last_7':
                    end = new Date(today);
                    start = new Date(today);
                    start.setDate(start.getDate() - 6);
                    break;
                case 'last_15':
                    end = new Date(today);
                    start = new Date(today);
                    start.setDate(start.getDate() - 14);
                    break;
                case 'last_30':
                    end = new Date(today);
                    start = new Date(today);
                    start.setDate(start.getDate() - 29);
                    break;
                case 'this_month':
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today);
                    break;
                case 'last_month':
                    start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    end = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
                case 'this_year':
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date(today);
                    break;
                case 'last_year':
                    start = new Date(today.getFullYear() - 1, 0, 1);
                    end = new Date(today.getFullYear() - 1, 11, 31);
                    break;
                case 'custom':
                    return;
            }

            this.tempStart = start;
            this.tempEnd = end;
            this.leftMonth = start.getMonth();
            this.leftYear = start.getFullYear();
        },

        pickDate(date) {
            this.activePreset = 'custom';

            if (!this.tempStart || (this.tempStart && this.tempEnd)) {
                this.tempStart = date;
                this.tempEnd = null;
            } else {
                if (date < this.tempStart) {
                    this.tempEnd = this.tempStart;
                    this.tempStart = date;
                } else {
                    this.tempEnd = date;
                }
            }
        },

        prevMonth() {
            this.leftMonth--;
            if (this.leftMonth < 0) { this.leftMonth = 11; this.leftYear--; }
        },

        nextMonth() {
            this.leftMonth++;
            if (this.leftMonth > 11) { this.leftMonth = 0; this.leftYear++; }
        },

        applyRange() {
            if (this.tempStart && this.tempEnd) {
                this.startDate = new Date(this.tempStart);
                this.endDate = new Date(this.tempEnd);
                this.displayLabel = this.fmt(this.startDate) + ' - ' + this.fmt(this.endDate);
                this.open = false;
                this.$dispatch('date-range-changed', { id: this.id, start: this.startDate, end: this.endDate, preset: this.activePreset });
            }
        },

        cancelPick() {
            if (this.startDate && this.endDate) {
                this.tempStart = new Date(this.startDate);
                this.tempEnd = new Date(this.endDate);
            }
            this.open = false;
        },

        fmt(date) {
            if (!date) return '';
            const dd = String(date.getDate()).padStart(2, '0');
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const yy = date.getFullYear();
            return dd + '/' + mm + '/' + yy;
        },
    }));
});
</script>
