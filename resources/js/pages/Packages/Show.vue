<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money } from '../EventSystem/_shared';
import { index } from '@/routes/packages';
defineOptions({ layout: { breadcrumbs: [{ title: 'Packages', href: index() }, { title: 'Package', href: '#' }] } });
defineProps<{ packageTemplate: any }>();
</script>
<template>
    <Head :title="packageTemplate.name" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader :title="packageTemplate.name" :description="`${packageTemplate.event_type || 'Any event'} · ${money(packageTemplate.total_amount)}`" />
        <div class="rounded-lg border bg-card p-4">
            <div v-for="item in packageTemplate.items" :key="item.id" class="flex justify-between border-b py-3 text-sm last:border-b-0">
                <span>{{ item.service.name }} x {{ item.quantity }}</span><span>{{ money(item.line_total) }}</span>
            </div>
        </div>
    </div>
</template>
