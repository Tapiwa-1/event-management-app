<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import { money, shortDate, type Paginated } from '../EventSystem/_shared';
import { index, create, edit } from '@/routes/expenses';
defineOptions({ layout: { breadcrumbs: [{ title: 'Expenses', href: index() }] } });
defineProps<{ expenses: Paginated<any>; events: any[] }>();
</script>
<template>
    <Head title="Expenses" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Expenses" description="Categorize fuel, ingredients, wages, rentals, transport, and other event costs." :action-href="create.url()" action-label="Add Expense" />
        <div class="overflow-x-auto rounded-lg border bg-card"><table class="w-full text-sm"><thead class="bg-muted/50 text-left text-muted-foreground"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Event</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3"></th></tr></thead><tbody><tr v-for="expense in expenses.data" :key="expense.id" class="border-t"><td class="px-4 py-3">{{ shortDate(expense.spent_at) }}</td><td class="px-4 py-3">{{ expense.event.name }}</td><td class="px-4 py-3">{{ expense.category }}<span class="block text-xs text-muted-foreground">{{ expense.description }}</span></td><td class="px-4 py-3">{{ money(expense.amount) }}</td><td class="px-4 py-3 text-right"><Link :href="edit.url(expense.id)" class="text-primary">Edit</Link></td></tr></tbody></table></div>
    </div>
</template>
