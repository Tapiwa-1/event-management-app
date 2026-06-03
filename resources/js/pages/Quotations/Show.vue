<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money, shortDate } from '../EventSystem/_shared';
import { index, confirm, print } from '@/routes/quotations';
import { create as createPayment } from '@/routes/payments';

const props = defineProps<{ quotation: any; paymentStatus: string; balanceDue: number }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Quotations', href: index() }, { title: 'Quotation', href: '#' }] } });
const accept = () => router.post(confirm.url(props.quotation.id));
</script>

<template>
    <Head :title="quotation.quotation_number" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader :title="quotation.quotation_number" :description="`${quotation.event.name} · ${quotation.status} · ${paymentStatus}`" :action-href="print.url(quotation.id)" action-label="Print Quote" />
        <div class="grid gap-4 lg:grid-cols-[1fr_320px]">
            <section class="rounded-lg border bg-card">
                <div class="border-b p-4"><h2 class="font-semibold">Package Items</h2></div>
                <div class="p-4">
                    <div v-for="item in quotation.items" :key="item.id" class="grid grid-cols-[1fr_80px_120px] gap-3 border-b py-3 text-sm last:border-b-0">
                        <span>{{ item.description }}</span><span>{{ item.quantity }}</span><span class="text-right">{{ money(item.line_total) }}</span>
                    </div>
                </div>
            </section>
            <aside class="rounded-lg border bg-card p-4 text-sm">
                <h2 class="font-semibold">Summary</h2>
                <p class="mt-4 flex justify-between"><span>Total</span><span>{{ money(quotation.total_amount) }}</span></p>
                <p class="mt-2 flex justify-between"><span>Paid</span><span>{{ money(Number(quotation.total_amount) - Number(balanceDue)) }}</span></p>
                <p class="mt-2 flex justify-between font-semibold"><span>Balance</span><span>{{ money(balanceDue) }}</span></p>
                <p class="mt-4 text-muted-foreground">Issued {{ shortDate(quotation.issued_at) }}</p>
                <button class="mt-4 h-9 w-full rounded-md bg-primary text-sm font-medium text-primary-foreground" @click="accept">Confirm Booking</button>
                <Link :href="createPayment.url({ query: { quotation_id: quotation.id } })" class="mt-2 inline-flex h-9 w-full items-center justify-center rounded-md border text-sm">Record Payment</Link>
            </aside>
        </div>
    </div>
</template>
