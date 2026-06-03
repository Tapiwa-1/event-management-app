<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money, shortDate, type Paginated } from '../EventSystem/_shared';
import { index, create, edit } from '@/routes/payments';
defineOptions({ layout: { breadcrumbs: [{ title: 'Payments', href: index() }] } });
defineProps<{ payments: Paginated<any>; quotations: any[] }>();
</script>
<template>
    <Head title="Payments" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Payments" description="Record deposits, balance payments, full payments, and refunds." :action-href="create.url()" action-label="Record Payment" />
        <div class="overflow-x-auto rounded-lg border bg-card"><table class="w-full text-sm"><thead class="bg-muted/50 text-left text-muted-foreground"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Quote</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3"></th></tr></thead><tbody><tr v-for="payment in payments.data" :key="payment.id" class="border-t"><td class="px-4 py-3">{{ shortDate(payment.paid_at) }}</td><td class="px-4 py-3">{{ payment.quotation.quotation_number }}<span class="block text-xs text-muted-foreground">{{ payment.quotation.event.client.name }}</span></td><td class="px-4 py-3">{{ payment.type }}</td><td class="px-4 py-3">{{ money(payment.amount) }}</td><td class="px-4 py-3 text-right"><Link :href="edit.url(payment.id)" class="text-primary">Edit</Link></td></tr></tbody></table></div>
    </div>
</template>
