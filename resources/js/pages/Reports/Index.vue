<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money } from '../EventSystem/_shared';
import { index } from '@/routes/reports';

defineOptions({ layout: { breadcrumbs: [{ title: 'Reports', href: index() }] } });
defineProps<{
    revenueByMonth: any[];
    expensesByMonth: any[];
    profitByEvent: any[];
    clientSummary: any[];
    servicePerformance: any[];
}>();
</script>

<template>
    <Head title="Reports" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Reports" description="Revenue, profit, event, client, and service performance summaries." />

        <div class="grid gap-4 xl:grid-cols-2">
            <section class="rounded-lg border bg-card p-4">
                <h2 class="font-semibold">Monthly Revenue</h2>
                <div class="mt-4 grid gap-2 text-sm">
                    <div v-for="row in revenueByMonth" :key="row.month" class="flex justify-between rounded-md bg-muted/40 px-3 py-2"><span>{{ row.month }}</span><span>{{ money(row.total) }}</span></div>
                </div>
            </section>
            <section class="rounded-lg border bg-card p-4">
                <h2 class="font-semibold">Monthly Expenses</h2>
                <div class="mt-4 grid gap-2 text-sm">
                    <div v-for="row in expensesByMonth" :key="row.month" class="flex justify-between rounded-md bg-muted/40 px-3 py-2"><span>{{ row.month }}</span><span>{{ money(row.total) }}</span></div>
                </div>
            </section>
        </div>

        <section class="rounded-lg border bg-card">
            <h2 class="border-b p-4 font-semibold">Profit By Event</h2>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-muted/50 text-left text-muted-foreground"><tr><th class="px-4 py-3">Event</th><th class="px-4 py-3">Client</th><th class="px-4 py-3">Revenue</th><th class="px-4 py-3">Expenses</th><th class="px-4 py-3">Profit</th></tr></thead><tbody><tr v-for="event in profitByEvent" :key="event.id" class="border-t"><td class="px-4 py-3">{{ event.name }}</td><td class="px-4 py-3">{{ event.client }}</td><td class="px-4 py-3">{{ money(event.revenue) }}</td><td class="px-4 py-3">{{ money(event.expenses) }}</td><td class="px-4 py-3 font-medium">{{ money(event.profit) }}</td></tr></tbody></table></div>
        </section>

        <div class="grid gap-4 xl:grid-cols-2">
            <section class="rounded-lg border bg-card p-4">
                <h2 class="font-semibold">Clients</h2>
                <div class="mt-4 grid gap-2 text-sm"><div v-for="client in clientSummary" :key="client.id" class="flex justify-between rounded-md bg-muted/40 px-3 py-2"><span>{{ client.name }}</span><span>{{ client.events_count }} events</span></div></div>
            </section>
            <section class="rounded-lg border bg-card p-4">
                <h2 class="font-semibold">Service Performance</h2>
                <div class="mt-4 grid gap-2 text-sm"><div v-for="service in servicePerformance" :key="service.name" class="flex justify-between rounded-md bg-muted/40 px-3 py-2"><span>{{ service.name }} · {{ service.usage_count }} uses</span><span>{{ money(service.revenue) }}</span></div></div>
            </section>
        </div>
    </div>
</template>
