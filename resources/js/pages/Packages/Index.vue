<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money, type Paginated } from '../EventSystem/_shared';
import { index, create, show, edit } from '@/routes/packages';

defineOptions({ layout: { breadcrumbs: [{ title: 'Packages', href: index() }] } });
defineProps<{ packages: Paginated<any> }>();
</script>

<template>
    <Head title="Packages" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Saved Packages" description="Reusable bundles for common event types." :action-href="create.url()" action-label="Create Package" />
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            <div v-for="pkg in packages.data" :key="pkg.id" class="rounded-lg border bg-card p-4">
                <div class="flex justify-between gap-3"><h2 class="font-semibold">{{ pkg.name }}</h2><Link :href="edit.url(pkg.id)" class="text-sm text-primary">Edit</Link></div>
                <p class="text-sm text-muted-foreground">{{ pkg.event_type || 'Any event' }}</p>
                <p class="mt-4 text-2xl font-semibold">{{ money(pkg.total_amount) }}</p>
                <p class="mt-2 text-sm text-muted-foreground">{{ pkg.items.length }} services</p>
                <Link :href="show.url(pkg.id)" class="mt-4 inline-flex text-sm font-medium text-primary">View package</Link>
            </div>
        </div>
    </div>
</template>
