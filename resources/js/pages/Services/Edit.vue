<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { index, update } from '@/routes/services';

const props = defineProps<{ service: any }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Services', href: index() }, { title: 'Edit', href: '#' }] } });
const form = useForm({ ...props.service });
const submit = () => form.patch(update.url(props.service.id));
</script>

<template>
    <Head title="Edit Service" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Edit Service" />
        <form class="grid max-w-3xl gap-4 rounded-lg border bg-card p-4" @submit.prevent="submit">
            <FormField label="Name" :error="form.errors.name"><input v-model="form.name" class="h-10 rounded-md border bg-background px-3" /></FormField>
            <div class="grid gap-4 md:grid-cols-3">
                <FormField label="Category"><input v-model="form.category" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Unit"><input v-model="form.unit" class="h-10 rounded-md border bg-background px-3" /></FormField>
                <FormField label="Default Price"><input v-model="form.default_price" type="number" step="0.01" min="0" class="h-10 rounded-md border bg-background px-3" /></FormField>
            </div>
            <FormField label="Description"><textarea v-model="form.description" class="min-h-24 rounded-md border bg-background px-3 py-2" /></FormField>
            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" /> Active</label>
            <button class="h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground" :disabled="form.processing">Update Service</button>
        </form>
    </div>
</template>
