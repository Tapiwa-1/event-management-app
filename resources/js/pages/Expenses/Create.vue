<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { index, store } from '@/routes/expenses';
const props = defineProps<{ events: any[] }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Expenses', href: index() }, { title: 'New expense', href: '#' }] } });
const form = useForm({ event_id: props.events[0]?.id ?? '', spent_at: new Date().toISOString().slice(0, 10), category: 'Fuel', description: '', amount: 0, supplier: '', notes: '' });
const submit = () => form.post(store.url());
</script>
<template>
    <Head title="New Expense" />
    <div class="flex flex-1 flex-col gap-6 p-4"><PageHeader title="New Expense" /><form class="grid max-w-3xl gap-4 rounded-lg border bg-card p-4" @submit.prevent="submit">
        <FormField label="Event"><select v-model="form.event_id" class="h-10 rounded-md border bg-background px-3"><option v-for="event in events" :key="event.id" :value="event.id">{{ event.name }} · {{ event.client.name }}</option></select></FormField>
        <div class="grid gap-4 md:grid-cols-3"><FormField label="Date"><input v-model="form.spent_at" type="date" class="h-10 rounded-md border bg-background px-3" /></FormField><FormField label="Category"><select v-model="form.category" class="h-10 rounded-md border bg-background px-3"><option>Fuel</option><option>Food Ingredients</option><option>Staff Wages</option><option>Rentals</option><option>Transport</option><option>Other</option></select></FormField><FormField label="Amount"><input v-model="form.amount" type="number" step="0.01" min="0.01" class="h-10 rounded-md border bg-background px-3" /></FormField></div>
        <FormField label="Description"><input v-model="form.description" class="h-10 rounded-md border bg-background px-3" /></FormField><FormField label="Supplier"><input v-model="form.supplier" class="h-10 rounded-md border bg-background px-3" /></FormField><button class="h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground" :disabled="form.processing">Save Expense</button>
    </form></div>
</template>
