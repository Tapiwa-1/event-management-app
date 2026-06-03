<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title: string;
    description?: string;
    actionHref?: string;
    actionLabel?: string;
}>();
</script>

<template>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-normal text-foreground">{{ title }}</h1>
            <p v-if="description" class="mt-1 max-w-3xl text-sm text-muted-foreground">
                {{ description }}
            </p>
        </div>
        <Link
            v-if="actionHref && actionLabel"
            :href="actionHref"
            class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground shadow-xs transition hover:bg-primary/90"
        >
            {{ actionLabel }}
        </Link>
    </div>
</template>
