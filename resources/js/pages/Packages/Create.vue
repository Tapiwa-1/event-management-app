<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { money } from '../EventSystem/_shared';
import { index, store } from '@/routes/packages';

const props = defineProps<{ services: any[] }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Packages', href: index() }, { title: 'New package', href: '#' }] } });
const form = useForm({ name: '', event_type: '', description: '', is_active: true, items: [{ service_id: props.services[0]?.id ?? '', quantity: 1, unit_price: Number(props.services[0]?.default_price ?? 0) }] });
const total = computed(() => form.items.reduce((sum, item) => sum + Number(item.quantity) * Number(item.unit_price), 0));
const addItem = () => form.items.push({ service_id: props.services[0]?.id ?? '', quantity: 1, unit_price: Number(props.services[0]?.default_price ?? 0) });
const submit = () => form.post(store.url());
</script>

<template>
    <Head title="New Package" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="New Saved Package" :description="`Package total: ${money(total)}`" />
        <form class="grid max-w-5xl gap-4 rounded-lg border bg-card p-4" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-2">
                <FormField label="Name"><input v-model="form.name" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Event Type"><input v-model="form.event_type" class="h-10 rounded-md border bg-background px-3" /></FormField>
            </div>
            <FormField label="Description"><textarea v-model="form.description" class="min-h-20 rounded-md border bg-background px-3 py-2" /></FormField>
            <div class="grid gap-3">
                <div v-for="(item, index) in form.items" :key="index" class="grid gap-3 rounded-md border p-3 md:grid-cols-[1fr_120px_160px]">
                    <FormField label="Service"><select v-model="item.service_id" class="h-10 rounded-md border bg-background px-3"><option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option></select></FormField>
                    <FormField label="Qty"><input v-model="item.quantity" type="number" step="0.01" min="0.01" class="h-10 rounded-md border bg-background px-3" /></FormField>
                    <FormField label="Unit Price"><input v-model="item.unit_price" type="number" step="0.01" min="0" class="h-10 rounded-md border bg-background px-3" /></FormField>
                </div>
            </div>
            <button type="button" class="h-9 rounded-md border px-3 text-sm" @click="addItem">Add Service</button>
            <button class="h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground" :disabled="form.processing">Save Package</button>
        </form>
    </div>
</template>
