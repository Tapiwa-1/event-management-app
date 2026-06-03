<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money, shortDate } from '../EventSystem/_shared';
import { index, edit } from '@/routes/events';
import { create as createQuote } from '@/routes/quotations';

defineOptions({ layout: { breadcrumbs: [{ title: 'Events', href: index() }, { title: 'Event', href: '#' }] } });
defineProps<{ event: any }>();
</script>

<template>
    <Head :title="event.name" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader :title="event.name" :description="`${event.client.name} · ${shortDate(event.event_date)} · ${event.status}`" :action-href="createQuote.url({ query: { event_id: event.id } })" action-label="Create Quotation" />
        <div class="grid gap-4 lg:grid-cols-3">
            <section class="rounded-lg border bg-card p-4 text-sm">
                <div class="flex justify-between"><h2 class="font-semibold">Details</h2><Link :href="edit.url(event.id)" class="text-primary">Edit</Link></div>
                <p class="mt-4">{{ event.type }} · {{ event.guest_count }} guests</p>
                <p class="text-muted-foreground">{{ event.venue || 'Venue pending' }}</p>
                <p class="mt-3 text-muted-foreground">{{ event.special_requirements || 'No special requirements recorded.' }}</p>
            </section>
            <section class="rounded-lg border bg-card p-4 text-sm">
                <h2 class="font-semibold">Quotations</h2>
                <div class="mt-3 grid gap-2"><div v-for="quote in event.quotations" :key="quote.id" class="rounded-md border p-3">{{ quote.quotation_number }} · {{ quote.status }}<span class="block text-muted-foreground">{{ money(quote.total_amount) }}</span></div></div>
            </section>
            <section class="rounded-lg border bg-card p-4 text-sm">
                <h2 class="font-semibold">Costs and Resources</h2>
                <p class="mt-3">Expenses: {{ money(event.expenses.reduce((sum: number, expense: any) => sum + Number(expense.amount), 0)) }}</p>
                <p class="mt-2">Booked resources: {{ event.resource_bookings.length }}</p>
            </section>
        </div>
    </div>
</template>
