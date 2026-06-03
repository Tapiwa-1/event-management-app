<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    CalendarDays,
    ChartColumn,
    ClipboardList,
    FolderGit2,
    HandCoins,
    LayoutGrid,
    Package,
    Receipt,
    Settings2,
    Users,
    Warehouse,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as clientsIndex } from '@/routes/clients';
import { index as eventsIndex } from '@/routes/events';
import { index as expensesIndex } from '@/routes/expenses';
import { index as packagesIndex } from '@/routes/packages';
import { index as paymentsIndex } from '@/routes/payments';
import { index as quotationsIndex } from '@/routes/quotations';
import { index as reportsIndex } from '@/routes/reports';
import { index as resourcesIndex } from '@/routes/resources';
import { index as servicesIndex } from '@/routes/services';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clients',
        href: clientsIndex(),
        icon: Users,
    },
    {
        title: 'Events',
        href: eventsIndex(),
        icon: CalendarDays,
    },
    {
        title: 'Services',
        href: servicesIndex(),
        icon: Settings2,
    },
    {
        title: 'Packages',
        href: packagesIndex(),
        icon: Package,
    },
    {
        title: 'Quotations',
        href: quotationsIndex(),
        icon: ClipboardList,
    },
    {
        title: 'Payments',
        href: paymentsIndex(),
        icon: HandCoins,
    },
    {
        title: 'Resources',
        href: resourcesIndex(),
        icon: Warehouse,
    },
    {
        title: 'Expenses',
        href: expensesIndex(),
        icon: Receipt,
    },
    {
        title: 'Reports',
        href: reportsIndex(),
        icon: ChartColumn,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
