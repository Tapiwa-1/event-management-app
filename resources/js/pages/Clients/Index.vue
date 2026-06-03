<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../EventSystem/PageHeader.vue';
import type { Paginated } from '../EventSystem/_shared';
import { index, create, show, edit } from '@/routes/clients';

defineOptions({ layout: { breadcrumbs: [{ title: 'Clients', href: index() }] } });
defineProps<{ clients: Paginated<any> }>();
</script>

<template>
    <Head title="Clients" />
    <div class="flex flex-1 flex-col gap-6 p-4">
        <PageHeader title="Clients" description="Register clients and track their inquiry, quotation, and booking history." :action-href="create.url()" action-label="Add Client" />
        <div class="overflow-x-auto rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-muted-foreground">
                    <tr><th class="px-4 py-3">Client</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Events</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody>
                    <tr v-for="client in clients.data" :key="client.id" class="border-t">
                        <td class="px-4 py-3 font-medium">{{ client.name }}<span v-if="client.company" class="block text-xs font-normal text-muted-foreground">{{ client.company }}</span></td>
                        <td class="px-4 py-3">{{ client.phone || 'No phone' }}<span class="block text-xs text-muted-foreground">{{ client.email || 'No email' }}</span></td>
                        <td class="px-4 py-3">{{ client.events_count }}</td>
                        <td class="px-4 py-3 text-right"><Link :href="show.url(client.id)" class="text-primary">View</Link><Link :href="edit.url(client.id)" class="ml-3 text-primary">Edit</Link></td>
                    </tr>
                    <tr v-if="clients.data.length === 0"><td colspan="4" class="px-4 py-8 text-center text-muted-foreground">No clients yet.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
