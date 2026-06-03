<script setup lang="ts">import { Head } from '@inertiajs/vue3'; defineProps<{ resource: any; events: any[] }>();</script>
<template><Head :title="resource.name" /><div class="p-4">{{ resource.name }}</div></template>
