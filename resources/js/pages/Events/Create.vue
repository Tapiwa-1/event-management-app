<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { eventStatuses, eventTypes } from '../EventSystem/_shared';
import { index, store } from '@/routes/events';

const props = defineProps<{ clients: any[] }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Events', href: index() }, { title: 'New event', href: '#' }] } });
const form = useForm({ client_id: props.clients[0]?.id ?? '', name: '', type: 'Wedding', event_date: '', venue: '', guest_count: 0, status: 'Inquiry', special_requirements: '' });
const submit = () => form.post(store.url());
</script>

<template>
    <Head title="New Event" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="New Event" />
        <form class="grid max-w-4xl gap-4 rounded-lg border bg-card p-4" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-2">
                <FormField label="Client" :error="form.errors.client_id"><select v-model="form.client_id" class="h-10 rounded-md border bg-background px-3"><option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option></select></FormField>
                <FormField label="Event Name" :error="form.errors.name"><input v-model="form.name" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Type" :error="form.errors.type"><select v-model="form.type" class="h-10 rounded-md border bg-background px-3"><option v-for="type in eventTypes" :key="type">{{ type }}</option></select></FormField>
                <FormField label="Date" :error="form.errors.event_date"><input v-model="form.event_date" type="date" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Venue" :error="form.errors.venue"><input v-model="form.venue" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Guest Count" :error="form.errors.guest_count"><input v-model="form.guest_count" type="number" min="0" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Status" :error="form.errors.status"><select v-model="form.status" class="h-10 rounded-md border bg-background px-3"><option v-for="status in eventStatuses" :key="status">{{ status }}</option></select></FormField>
            </div>
            <FormField label="Special Requirements" :error="form.errors.special_requirements"><textarea v-model="form.special_requirements" class="min-h-24 rounded-md border bg-background px-3 py-2" /></FormField>
            <button class="h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground" :disabled="form.processing">Save Event</button>
        </form>
    </div>
</template>
