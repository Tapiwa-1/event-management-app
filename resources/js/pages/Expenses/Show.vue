<script setup lang="ts">import { Head } from '@inertiajs/vue3'; defineProps<{ expense: any }>();</script>
<template><Head title="Expense" /><div class="p-4">{{ expense.description }}</div></template>
