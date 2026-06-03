<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money, shortDate, type Paginated } from '../EventSystem/_shared';
import { index, create, show } from '@/routes/quotations';

defineOptions({ layout: { breadcrumbs: [{ title: 'Quotations', href: index() }] } });
defineProps<{ quotations: Paginated<any> }>();
</script>

<template>
    <Head title="Quotations" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Quotations" description="Build bundled packages, track payment status, and convert accepted quotes into bookings." :action-href="create.url()" action-label="New Quotation" />
        <div class="overflow-x-auto rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-muted-foreground"><tr><th class="px-4 py-3">Quote</th><th class="px-4 py-3">Event</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Balance</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
                <tbody>
                    <tr v-for="quote in quotations.data" :key="quote.id" class="border-t">
                        <td class="px-4 py-3 font-medium">{{ quote.quotation_number }}<span class="block text-xs font-normal text-muted-foreground">{{ shortDate(quote.issued_at) }}</span></td>
                        <td class="px-4 py-3">{{ quote.event.name }}<span class="block text-xs text-muted-foreground">{{ quote.event.client.name }}</span></td>
                        <td class="px-4 py-3">{{ money(quote.total_amount) }}</td>
                        <td class="px-4 py-3">{{ money(quote.balance_due) }}<span class="block text-xs text-muted-foreground">{{ quote.payment_status }}</span></td>
                        <td class="px-4 py-3">{{ quote.status }}</td>
                        <td class="px-4 py-3 text-right"><Link :href="show.url(quote.id)" class="text-primary">View</Link></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
