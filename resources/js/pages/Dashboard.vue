<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from './EventSystem/PageHeader.vue';
import { money, shortDate } from './EventSystem/_shared';
import { dashboard } from '@/routes';
import { create as createEvent } from '@/routes/events';
import { create as createQuote } from '@/routes/quotations';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});

defineProps<{
    stats: {
        upcoming_events: number;
        monthly_revenue: number;
        monthly_expenses: number;
        monthly_profit: number;
        outstanding_payments: number;
    };
    upcomingEvents: Array<any>;
    calendarEvents: Array<any>;
    popularServices: Array<any>;
    profitByEvent: Array<any>;
    monthLabel: string;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader
            title="Event Management"
            :description="`Revenue, bookings, resources, and profitability for ${monthLabel}.`"
            :action-href="createEvent.url()"
            action-label="Create Event"
        />

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Upcoming events</p>
                <p class="mt-2 text-2xl font-semibold">{{ stats.upcoming_events }}</p>
            </div>
            <div class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Monthly revenue</p>
                <p class="mt-2 text-2xl font-semibold">{{ money(stats.monthly_revenue) }}</p>
            </div>
            <div class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Monthly expenses</p>
                <p class="mt-2 text-2xl font-semibold">{{ money(stats.monthly_expenses) }}</p>
            </div>
            <div class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Monthly profit</p>
                <p class="mt-2 text-2xl font-semibold">{{ money(stats.monthly_profit) }}</p>
            </div>
            <div class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Outstanding</p>
                <p class="mt-2 text-2xl font-semibold">{{ money(stats.outstanding_payments) }}</p>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-[1.3fr_0.7fr]">
            <section class="rounded-lg border bg-card">
                <div class="flex items-center justify-between border-b p-4">
                    <h2 class="font-semibold">Upcoming Events</h2>
                    <Link :href="createQuote.url()" class="text-sm font-medium text-primary">New quotation</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 text-left text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3 font-medium">Date</th>
                                <th class="px-4 py-3 font-medium">Event</th>
                                <th class="px-4 py-3 font-medium">Client</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="event in upcomingEvents" :key="event.id" class="border-t">
                                <td class="px-4 py-3">{{ shortDate(event.event_date) }}</td>
                                <td class="px-4 py-3 font-medium">{{ event.name }}</td>
                                <td class="px-4 py-3">{{ event.client.name }}</td>
                                <td class="px-4 py-3">{{ event.status }}</td>
                            </tr>
                            <tr v-if="upcomingEvents.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">No upcoming events yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-lg border bg-card p-4">
                <h2 class="font-semibold">Calendar Snapshot</h2>
                <div class="mt-4 grid gap-3">
                    <div v-for="event in calendarEvents" :key="event.id" class="rounded-md border p-3">
                        <p class="text-sm font-medium">{{ event.name }}</p>
                        <p class="text-xs text-muted-foreground">{{ shortDate(event.date) }} · {{ event.client }} · {{ event.status }}</p>
                    </div>
                    <p v-if="calendarEvents.length === 0" class="text-sm text-muted-foreground">No events in the next 45 days.</p>
                </div>
            </section>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <section class="rounded-lg border bg-card p-4">
                <h2 class="font-semibold">Most Popular Services</h2>
                <div class="mt-4 grid gap-2">
                    <div v-for="service in popularServices" :key="service.id" class="flex items-center justify-between rounded-md bg-muted/40 px-3 py-2 text-sm">
                        <span>{{ service.name }}</span>
                        <span class="font-medium">{{ service.usage_count }} uses</span>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border bg-card p-4">
                <h2 class="font-semibold">Profit Per Event</h2>
                <div class="mt-4 grid gap-2">
                    <div v-for="event in profitByEvent" :key="event.id" class="flex items-center justify-between rounded-md bg-muted/40 px-3 py-2 text-sm">
                        <span>{{ event.name }}</span>
                        <span class="font-medium">{{ money(event.profit) }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
