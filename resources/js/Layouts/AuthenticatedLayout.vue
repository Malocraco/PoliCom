<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { 
    LayoutDashboard, 
    FileText, 
    Calendar, 
    Users, 
    LogOut,
    Menu,
    X,
    Bell
} from 'lucide-vue-next';

const showingSidebar = ref(true);

const navItems = computed(() => {
    return [
        { name: 'Dashboard', href: route('dashboard'), icon: LayoutDashboard, active: route().current('dashboard') },
        { name: 'Solicitudes', href: route('solicitudes.index'), icon: FileText, active: route().current('solicitudes.*') },
        { name: 'Calendario', href: route('calendar.index'), icon: Calendar, active: route().current('calendar.index') },
    ];
});
</script>

<template>
    <div class="flex h-screen bg-[#f9f9fd] font-sans antialiased overflow-hidden">
        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-white border-r border-[#edeef1] transition-transform duration-300 ease-in-out lg:static lg:translate-x-0',
                showingSidebar ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Brand / Logo Area -->
            <div class="flex h-16 items-center px-6 border-b border-[#edeef1]">
                <button @click="showingSidebar = !showingSidebar" class="mr-3 text-[#44474f] hover:text-[#002D57] lg:hidden">
                    <X class="h-6 w-6" />
                </button>
                <ApplicationLogo class="h-7 w-auto flex-shrink-0 mr-3 object-contain" />
                <h1 class="text-xl font-bold text-[#002D57] tracking-tight hidden lg:block truncate">PoliCom</h1>
            </div>
            
            <!-- User Role / Profile -->
            <div class="flex items-center justify-center py-4 border-b border-[#edeef1] px-6">
                 <span class="text-sm font-bold tracking-tight text-[#002D57] uppercase text-center leading-tight">
                    {{ $page.props.auth.user.role?.name || 'Analista de Canales' }}
                 </span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 px-3 py-6 overflow-y-auto">
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'group flex items-center rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200',
                        item.active 
                            ? 'bg-[#002D57]/5 text-[#002D57] font-semibold shadow-sm' 
                            : 'text-gray-500 hover:bg-gray-50 hover:text-[#005082]'
                    ]"
                >
                    <component 
                        :is="item.icon" 
                        :class="[
                            'mr-3 h-5 w-5',
                            item.active ? 'text-[#002D57]' : 'text-gray-400 group-hover:text-[#005082]'
                        ]" 
                    />
                    {{ item.name }}
                </Link>
            </nav>

            <!-- Logout -->
            <div class="border-t border-gray-100 p-3">
                <Link :href="route('logout')" method="post" as="button" class="group flex w-full items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-500 hover:bg-red-50 hover:text-[#ba1a1a] transition-all duration-200">
                    <LogOut class="mr-3 h-5 w-5 text-gray-400 group-hover:text-[#ba1a1a]" />
                    Cerrar Sesión
                </Link>
            </div>
        </aside>

        <!-- Right Side (Header + Content) -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="flex h-16 shrink-0 items-center justify-between border-b border-[#edeef1] bg-white px-8 z-40">
                <div class="flex items-center">
                    <button @click="showingSidebar = !showingSidebar" class="mr-4 text-[#44474f] hover:text-[#002D57] lg:hidden">
                        <Menu class="h-6 w-6" />
                    </button>
                    <h2 class="text-lg font-medium text-[#44474f] hidden md:block">
                        <slot name="header" />
                    </h2>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto w-full relative">
                <slot />
            </main>
        </div>

        <!-- Overlay for mobile -->
        <div v-if="showingSidebar" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden" @click="showingSidebar = false"></div>
    </div>
</template>
