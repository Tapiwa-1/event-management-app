<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { index, update } from '@/routes/payments';
const props = defineProps<{ payment: any; quotations: any[] }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Payments', href: index() }, { title: 'Edit payment', href: '#' }] } });
const form = useForm({ ...props.payment });
const submit = () => form.patch(update.url(props.payment.id));
</script>
<template>
    <Head title="Edit Payment" />
    <div class="flex flex-1 flex-col gap-6 p-4"><PageHeader title="Edit Payment" /><form class="grid max-w-3xl gap-4 rounded-lg border bg-card p-4" @submit.prevent="submit">
        <FormField label="Quotation"><select v-model="form.quotation_id" class="h-10 rounded-md border bg-background px-3"><option v-for="quote in quotations" :key="quote.id" :value="quote.id">{{ quote.quotation_number }} · {{ quote.event.client.name }}</option></select></FormField>
        <div class="grid gap-4 md:grid-cols-3"><FormField label="Date"><input v-model="form.paid_at" type="date" class="h-10 rounded-md border bg-background px-3" /></FormField><FormField label="Type"><select v-model="form.type" class="h-10 rounded-md border bg-background px-3"><option>Deposit</option><option>Balance</option><option>Full Payment</option><option>Refund</option></select></FormField><FormField label="Amount"><input v-model="form.amount" type="number" step="0.01" min="0.01" class="h-10 rounded-md border bg-background px-3" /></FormField></div>
        <button class="h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground" :disabled="form.processing">Update Payment</button>
    </form></div>
</template>
