<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money } from '../EventSystem/_shared';
import { index } from '@/routes/services';
defineOptions({ layout: { breadcrumbs: [{ title: 'Services', href: index() }, { title: 'Service', href: '#' }] } });
defineProps<{ service: any }>();
</script>
<template><Head :title="service.name" /><div class="p-4"><PageHeader :title="service.name" :description="`${service.category || 'General'} · ${money(service.default_price)}`" /></div></template>
