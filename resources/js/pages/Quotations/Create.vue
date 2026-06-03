<script setup lang="ts">
import { computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { money } from '../EventSystem/_shared';
import { index, store } from '@/routes/quotations';

const props = defineProps<{ events: any[]; services: any[] }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Quotations', href: index() }, { title: 'New quotation', href: '#' }] } });
const today = new Date().toISOString().slice(0, 10);
const form = useForm({ event_id: props.events[0]?.id ?? '', issued_at: today, valid_until: '', status: 'Sent', terms: '50% deposit confirms booking. Balance due before the event date.', items: [{ service_id: props.services[0]?.id ?? '', description: props.services[0]?.name ?? '', quantity: 1, unit_price: Number(props.services[0]?.default_price ?? 0) }] });
const total = computed(() => form.items.reduce((sum, item) => sum + Number(item.quantity) * Number(item.unit_price), 0));
const selectedEvent = computed(() => props.events.find((event) => event.id === form.event_id));
const priceForGuestCount = (service: any) => {
    const guestCount = Number(selectedEvent.value?.guest_count ?? 0);
    const tier = service.price_tiers?.find((candidate: any) => {
        return guestCount >= Number(candidate.min_guests) && (candidate.max_guests === null || guestCount <= Number(candidate.max_guests));
    });

    return Number(tier?.price ?? service.default_price ?? 0);
};
const addItem = () => {
    const service = props.services[0];
    form.items.push({ service_id: service?.id ?? '', description: service?.name ?? '', quantity: 1, unit_price: service ? priceForGuestCount(service) : 0 });
};
const syncService = (item: any) => {
    const service = props.services.find((candidate) => candidate.id === item.service_id);
    if (service) {
        item.description = service.name;
        item.unit_price = priceForGuestCount(service);
    }
};
watch(() => form.event_id, () => {
    form.items.forEach((item: any) => syncService(item));
});
const submit = () => form.post(store.url());
</script>

<template>
    <Head title="New Quotation" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="New Quotation" :description="`Total: ${money(total)}`" />
        <form class="grid max-w-6xl gap-4 rounded-lg border bg-card p-4" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-4">
                <FormField label="Event"><select v-model="form.event_id" class="h-10 rounded-md border bg-background px-3"><option v-for="event in events" :key="event.id" :value="event.id">{{ event.name }} · {{ event.client.name }}</option></select></FormField>
                <FormField label="Issued"><input v-model="form.issued_at" type="date" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Valid Until"><input v-model="form.valid_until" type="date" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Status"><select v-model="form.status" class="h-10 rounded-md border bg-background px-3"><option>Draft</option><option>Sent</option><option>Accepted</option><option>Declined</option><option>Expired</option></select></FormField>
            </div>
            <div class="grid gap-3">
                <div v-for="(item, index) in form.items" :key="index" class="grid gap-3 rounded-md border p-3 md:grid-cols-[1fr_1fr_120px_160px]">
                    <FormField label="Service"><select v-model="item.service_id" class="h-10 rounded-md border bg-background px-3" @change="syncService(item)"><option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option></select><span v-if="services.find((service) => service.id === item.service_id)?.price_tiers?.length" class="text-xs font-normal text-muted-foreground">Priced by {{ selectedEvent?.guest_count || 0 }} guests</span></FormField>
                    <FormField label="Description"><input v-model="item.description" class="h-10 rounded-md border bg-background px-3" /></FormField>
                    <FormField label="Qty"><input v-model="item.quantity" type="number" step="0.01" min="0.01" class="h-10 rounded-md border bg-background px-3" /></FormField>
                    <FormField label="Unit Price"><input v-model="item.unit_price" type="number" step="0.01" min="0" class="h-10 rounded-md border bg-background px-3" /></FormField>
                </div>
            </div>
            <button type="button" class="h-9 rounded-md border px-3 text-sm" @click="addItem">Add Service</button>
            <FormField label="Terms"><textarea v-model="form.terms" class="min-h-20 rounded-md border bg-background px-3 py-2" /></FormField>
            <button class="h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground" :disabled="form.processing">Save Quotation</button>
        </form>
    </div>
</template>
