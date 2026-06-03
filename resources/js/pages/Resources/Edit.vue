<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import FormField from '../EventSystem/FormField.vue';
import { index, update } from '@/routes/resources';
const props = defineProps<{ resource: any }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Resources', href: index() }, { title: 'Edit resource', href: '#' }] } });
const form = useForm({ ...props.resource });
const submit = () => form.patch(update.url(props.resource.id));
</script>
<template>
    <Head title="Edit Resource" />
    <div class="flex flex-1 flex-col gap-6 p-4"><PageHeader title="Edit Resource" /><form class="grid max-w-3xl gap-4 rounded-lg border bg-card p-4" @submit.prevent="submit">
        <FormField label="Name"><input v-model="form.name" class="h-10 rounded-md border bg-background px-3" /></FormField>
        <div class="grid gap-4 md:grid-cols-3"><FormField label="Type"><select v-model="form.type" class="h-10 rounded-md border bg-background px-3"><option>PA System</option><option>Camera</option><option>Decor Inventory</option><option>Staff</option><option>Vehicle</option><option>Other</option></select></FormField><FormField label="Quantity"><input v-model="form.quantity" type="number" min="1" class="h-10 rounded-md border bg-background px-3" /></FormField><FormField label="Status"><select v-model="form.status" class="h-10 rounded-md border bg-background px-3"><option>Available</option><option>Maintenance</option><option>Unavailable</option></select></FormField></div>
        <FormField label="Notes"><textarea v-model="form.notes" class="min-h-20 rounded-md border bg-background px-3 py-2" /></FormField><button class="h-10 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground" :disabled="form.processing">Update Resource</button>
    </form></div>
</template>
