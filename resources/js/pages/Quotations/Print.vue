<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { money, shortDate } from '../EventSystem/_shared';
defineProps<{ quotation: any }>();
</script>
<template>
    <Head :title="`Print ${quotation.quotation_number}`" />
    <main class="mx-auto max-w-4xl bg-white p-8 text-black print:p-0">
        <header class="flex justify-between border-b pb-6">
            <div><h1 class="text-3xl font-bold">Quotation</h1><p>{{ quotation.quotation_number }}</p></div>
            <div class="text-right"><p class="font-semibold">Event Organizer</p><p>Professional Event Packages</p></div>
        </header>
        <section class="grid grid-cols-2 gap-6 py-6 text-sm">
            <div><h2 class="font-semibold">Client</h2><p>{{ quotation.event.client.name }}</p><p>{{ quotation.event.client.email }}</p><p>{{ quotation.event.client.phone }}</p></div>
            <div><h2 class="font-semibold">Event</h2><p>{{ quotation.event.name }}</p><p>{{ shortDate(quotation.event.event_date) }}</p><p>{{ quotation.event.venue }}</p></div>
        </section>
        <table class="w-full text-sm"><thead><tr class="border-b text-left"><th class="py-2">Service</th><th class="py-2">Qty</th><th class="py-2 text-right">Amount</th></tr></thead><tbody><tr v-for="item in quotation.items" :key="item.id" class="border-b"><td class="py-3">{{ item.description }}</td><td class="py-3">{{ item.quantity }}</td><td class="py-3 text-right">{{ money(item.line_total) }}</td></tr></tbody></table>
        <p class="mt-6 text-right text-2xl font-bold">{{ money(quotation.total_amount) }}</p>
        <section class="mt-8 border-t pt-4 text-sm"><h2 class="font-semibold">Terms</h2><p>{{ quotation.terms }}</p></section>
    </main>
</template>
