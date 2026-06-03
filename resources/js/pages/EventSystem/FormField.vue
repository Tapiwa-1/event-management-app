<script setup lang="ts">
defineProps<{
    label: string;
    error?: string;
}>();
</script>

<template>
    <label class="grid gap-2 text-sm font-medium text-foreground">
        <span>{{ label }}</span>
        <slot />
        <span v-if="error" class="text-xs font-normal text-destructive">{{ error }}</span>
    </label>
</template>
