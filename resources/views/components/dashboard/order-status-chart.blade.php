<div class="overflow-visible rounded-2xl border border-gray-200 bg-white px-5 pt-5 pb-3 sm:px-6 sm:pt-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Order Status Overview
        </h3>
        <x-common.date-range-picker id="orderStatusFilter" />
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <div id="chartOrderStatus" class="min-w-[690px] xl:min-w-full"></div>
    </div>
</div>
