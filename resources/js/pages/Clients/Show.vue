<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money, shortDate } from '../EventSystem/_shared';
import { index, edit } from '@/routes/clients';
import { create as createEvent } from '@/routes/events';

defineOptions({ layout: { breadcrumbs: [{ title: 'Clients', href: index() }, { title: 'Client', href: '#' }] } });
defineProps<{ client: any }>();
</script>

<template>
    <Head :title="client.name" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader :title="client.name" :description="client.phone || client.email || 'Client profile'" :action-href="createEvent.url({ query: { client_id: client.id } })" action-label="Create Event" />
        <div class="grid gap-4 lg:grid-cols-[0.7fr_1.3fr]">
            <section class="rounded-lg border bg-card p-4 text-sm">
                <div class="flex justify-between"><h2 class="font-semibold">Contact</h2><Link :href="edit.url(client.id)" class="text-primary">Edit</Link></div>
                <p class="mt-4">{{ client.company || 'No company' }}</p>
                <p class="text-muted-foreground">{{ client.email || 'No email' }}</p>
                <p class="text-muted-foreground">{{ client.phone || 'No phone' }}</p>
                <p class="mt-3 text-muted-foreground">{{ client.address || 'No address' }}</p>
            </section>
            <section class="rounded-lg border bg-card">
                <h2 class="border-b p-4 font-semibold">Event History</h2>
                <div class="grid gap-3 p-4">
                    <div v-for="event in client.events" :key="event.id" class="rounded-md border p-3 text-sm">
                        <p class="font-medium">{{ event.name }} · {{ event.status }}</p>
                        <p class="text-muted-foreground">{{ shortDate(event.event_date) }} · {{ event.type }} · {{ event.venue || 'Venue pending' }}</p>
                        <p class="mt-2">Quoted {{ money(event.quotations.reduce((sum: number, quote: any) => sum + Number(quote.total_amount), 0)) }}</p>
                    </div>
                    <p v-if="client.events.length === 0" class="text-sm text-muted-foreground">No event history yet.</p>
                </div>
            </section>
        </div>
    </div>
</template>
