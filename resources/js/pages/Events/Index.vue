<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { shortDate, type Paginated } from '../EventSystem/_shared';
import { index, create, show, edit } from '@/routes/events';

defineOptions({ layout: { breadcrumbs: [{ title: 'Events', href: index() }] } });
defineProps<{ events: Paginated<any> }>();
</script>

<template>
    <Head title="Events" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Events" description="Manage weddings, birthdays, corporate events, funerals, graduations, and more." :action-href="create.url()" action-label="Create Event" />
        <div class="overflow-x-auto rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-muted-foreground"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Event</th><th class="px-4 py-3">Client</th><th class="px-4 py-3">Guests</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
                <tbody>
                    <tr v-for="event in events.data" :key="event.id" class="border-t">
                        <td class="px-4 py-3">{{ shortDate(event.event_date) }}</td>
                        <td class="px-4 py-3 font-medium">{{ event.name }}<span class="block text-xs font-normal text-muted-foreground">{{ event.type }} · {{ event.venue || 'Venue pending' }}</span></td>
                        <td class="px-4 py-3">{{ event.client.name }}</td>
                        <td class="px-4 py-3">{{ event.guest_count }}</td>
                        <td class="px-4 py-3">{{ event.status }}</td>
                        <td class="px-4 py-3 text-right"><Link :href="show.url(event.id)" class="text-primary">View</Link><Link :href="edit.url(event.id)" class="ml-3 text-primary">Edit</Link></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
