<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { index, update } from '@/routes/clients';

const props = defineProps<{ client: any }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Clients', href: index() }, { title: 'Edit', href: '#' }] } });
const form = useForm({ ...props.client });
const submit = () => form.patch(update.url(props.client.id));
</script>

<template>
    <Head title="Edit Client" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Edit Client" />
        <form class="grid max-w-3xl gap-4 rounded-lg border bg-card p-4" @submit.prevent="submit">
            <FormField label="Name" :error="form.errors.name"><input v-model="form.name" class="h-10 rounded-md border bg-background px-3" /></FormField>
            <div class="grid gap-4 md:grid-cols-2">
                <FormField label="Phone" :error="form.errors.phone"><input v-model="form.phone" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Email" :error="form.errors.email"><input v-model="form.email" type="email" class="h-10 rounded-md border bg-background px-3" /></FormField>
            </div>
            <FormField label="Company" :error="form.errors.company"><input v-model="form.company" class="h-10 rounded-md border bg-background px-3" /></FormField>
            <FormField label="Address" :error="form.errors.address"><input v-model="form.address" class="h-10 rounded-md border bg-background px-3" /></FormField>
            <FormField label="Notes" :error="form.errors.notes"><textarea v-model="form.notes" class="min-h-24 rounded-md border bg-background px-3 py-2" /></FormField>
            <button class="h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground" :disabled="form.processing">Update Client</button>
        </form>
    </div>
</template>
