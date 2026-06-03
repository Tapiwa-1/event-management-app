<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { shortDate, type Paginated } from '../EventSystem/_shared';
import { index, create, edit } from '@/routes/resources';
import { store as bookResource } from '@/routes/resource-bookings';

const props = defineProps<{ resources: Paginated<any>; events: any[] }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Resources', href: index() }] } });
const booking = useForm({ resource_id: '', event_id: props.events[0]?.id ?? '', booking_date: '', quantity: 1, status: 'Reserved', notes: '' });
const submitBooking = () => booking.post(bookResource.url(), { preserveScroll: true });
</script>

<template>
    <Head title="Resources" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Resources" description="Track PA systems, cameras, decor inventory, staff, and vehicles." :action-href="create.url()" action-label="Add Resource" />
        <form class="grid gap-3 rounded-lg border bg-card p-4 md:grid-cols-[1fr_1fr_150px_120px_auto]" @submit.prevent="submitBooking">
            <FormField label="Resource"><select v-model="booking.resource_id" class="h-10 rounded-md border bg-background px-3"><option value="">Choose resource</option><option v-for="resource in resources.data" :key="resource.id" :value="resource.id">{{ resource.name }} ({{ resource.quantity }})</option></select></FormField>
            <FormField label="Event"><select v-model="booking.event_id" class="h-10 rounded-md border bg-background px-3"><option v-for="event in events" :key="event.id" :value="event.id">{{ event.name }} · {{ event.client.name }}</option></select></FormField>
            <FormField label="Date"><input v-model="booking.booking_date" type="date" class="h-10 rounded-md border bg-background px-3" /></FormField>
            <FormField label="Qty" :error="booking.errors.quantity"><input v-model="booking.quantity" type="number" min="1" class="h-10 rounded-md border bg-background px-3" /></FormField>
            <button class="mt-7 h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground">Book</button>
        </form>
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            <div v-for="resource in resources.data" :key="resource.id" class="rounded-lg border bg-card p-4">
                <div class="flex justify-between gap-3"><div><h2 class="font-semibold">{{ resource.name }}</h2><p class="text-sm text-muted-foreground">{{ resource.type }} · {{ resource.status }}</p></div><Link :href="edit.url(resource.id)" class="text-sm text-primary">Edit</Link></div>
                <p class="mt-4 text-2xl font-semibold">{{ resource.quantity }}</p>
                <p class="text-sm text-muted-foreground">available units</p>
                <div class="mt-4 grid gap-2 text-sm">
                    <div v-for="bookingItem in resource.bookings.slice(0, 3)" :key="bookingItem.id" class="rounded-md bg-muted/40 px-3 py-2">
                        {{ shortDate(bookingItem.booking_date) }} · {{ bookingItem.event.name }} · {{ bookingItem.quantity }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
