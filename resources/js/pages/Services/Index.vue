<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money, type Paginated } from '../EventSystem/_shared';
import { index, create, edit } from '@/routes/services';

defineOptions({ layout: { breadcrumbs: [{ title: 'Services', href: index() }] } });
defineProps<{ services: Paginated<any> }>();
</script>

<template>
    <Head title="Services" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Services" description="Decor, PA systems, photography, catering, cakes, tents, furniture, MCs, transport, and more." :action-href="create.url()" action-label="Add Service" />
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            <div v-for="service in services.data" :key="service.id" class="rounded-lg border bg-card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div><h2 class="font-semibold">{{ service.name }}</h2><p class="text-sm text-muted-foreground">{{ service.category || 'General' }} · {{ service.unit }}</p></div>
                    <Link :href="edit.url(service.id)" class="text-sm text-primary">Edit</Link>
                </div>
                <p class="mt-4 text-2xl font-semibold">{{ money(service.default_price) }}</p>
                <p class="mt-2 text-sm text-muted-foreground">{{ service.description || 'No description.' }}</p>
            </div>
        </div>
    </div>
</template>
