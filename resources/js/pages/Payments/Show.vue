<script setup lang="ts">import { Head } from '@inertiajs/vue3'; defineProps<{ payment: any }>();</script>
<template><Head title="Payment" /><div class="p-4">{{ payment.reference || payment.id }}</div></template>
