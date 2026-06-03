<script setup lang="ts">
import CreateQuotation from './Create.vue';
defineProps<{ quotation: any; events: any[]; services: any[] }>();
</script>
<template><CreateQuotation :events="events" :services="services" /></template>
