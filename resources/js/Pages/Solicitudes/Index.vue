<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { 
    Plus, 
    Search, 
    Filter,
    Mail,
    MessageSquare,
    Eye,
    MoreHorizontal
} from 'lucide-vue-next';

const canCreate = computed(() => usePage().props.auth.user.role?.slug === 'analista_com');

const props = defineProps({
    solicitudes: Array
});

const getStatusConfig = (status) => {
    switch (status) {
        case 'creada': 
            return { class: 'bg-[#d7e2ff] text-[#2e4772]', label: 'Pendiente Prueba' };
        case 'prueba_enviada': 
            return { class: 'bg-[#ffdcc5] text-[#301400]', label: 'Prueba Enviada' };
        case 'borrador_infobip': 
            return { class: 'bg-[#ffdad6] text-[#93000a]', label: 'Borrador Infobip' };
        case 'lanzada': 
            return { class: 'bg-[#a3f69c] text-[#005312]', label: 'Lanzada' };
        default: 
            return { class: 'bg-[#e2e2e6] text-[#191c1e]', label: String(status).toUpperCase() };
    }
};
</script>

<template>
    <Head title="Solicitudes" />

    <AuthenticatedLayout>
        <template #header>
            Directorio de Solicitudes
        </template>

        <div class="px-8 py-10 bg-[#f9f9fd] min-h-screen font-sans">
            <!-- Header Text -->
            <header class="mb-10">
                <h1 class="text-3xl font-black text-[#002D57] tracking-tight mb-2">Solicitudes en Curso</h1>
                <p class="text-[#44474f] max-w-2xl text-sm leading-relaxed">Administra, filtra y consulta el listado completo de solicitudes de comunicaciones corporativas registradas en el sistema.</p>
            </header>

            <!-- Actions Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div class="relative w-full sm:w-96">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                    <input 
                        type="text" 
                        placeholder="Buscar ID, nombre o cliente..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-200 focus:border-[#1DB5E5] focus:ring-[#1DB5E5] text-sm shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all"
                    />
                </div>
                
                <div class="flex gap-3 w-full sm:w-auto">
                    <button class="flex items-center px-4 py-2.5 bg-white border border-[#edeef1] rounded-lg text-[#002D57] font-semibold text-sm hover:bg-[#f9f9fd] transition-all shadow-sm">
                        <Filter class="h-4 w-4 mr-2" />
                        Filtros
                    </button>
                    <Link 
                        v-if="canCreate"
                        :href="route('solicitudes.create')" 
                        class="flex-1 sm:flex-none flex items-center justify-center px-4 py-2.5 bg-[#002D57] text-white text-sm rounded-lg hover:bg-[#001735] transition-all shadow-lg shadow-[#002D57]/20 font-bold"
                    >
                        <Plus class="h-4 w-4 mr-2" />
                        Nueva Solicitud
                    </Link>
                </div>
            </div>

            <!-- Solicitudes Table -->
            <section class="bg-[#ffffff] rounded-xl shadow-[0_8px_40px_rgb(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f3f3f7] border-b border-[#edeef1]">
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-[#44474f]">Solicitud</th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-[#44474f]">Detalles</th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-[#44474f]">Cliente</th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-[#44474f] text-center">Tipo</th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-[#44474f]">Estado</th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-[#44474f] text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#edeef1]">
                            <tr v-for="solicitud in solicitudes" :key="solicitud.id" class="hover:bg-[#f9f9fd] transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-[#002D57] text-sm">{{ solicitud.numero_solicitud }}</div>
                                    <div class="text-xs text-[#44474f] mt-0.5">{{ solicitud.com_id }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-[#002D57] truncate max-w-xs">{{ solicitud.nombre_envio }}</div>
                                    <div class="text-xs text-[#44474f] line-clamp-1 mt-0.5">{{ solicitud.area_solicitante }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm text-[#002D57] font-medium">{{ solicitud.cliente_nombre }}</div>
                                    <div class="text-xs text-[#44474f] mt-0.5">{{ solicitud.cliente_email }}</div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <div class="flex justify-center">
                                        <div :title="solicitud.tipo" class="p-2 rounded-lg bg-[#f3f3f7] text-[#005082]">
                                            <Mail v-if="solicitud.tipo === 'Email'" class="h-4 w-4" />
                                            <MessageSquare v-else class="h-4 w-4" />
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span :class="['px-3 py-1.5 rounded-full text-[11px] font-bold', getStatusConfig(solicitud.estado).class]">
                                        {{ getStatusConfig(solicitud.estado).label }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-1">
                                        <Link :href="route('solicitudes.show', solicitud.id)" class="p-2.5 text-gray-400 hover:text-[#1DB5E5] hover:bg-[#f9f9fd] rounded-lg transition-all" title="Ver Solicitud">
                                            <Eye class="h-4 w-4" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="solicitudes.length === 0">
                                <td colspan="6" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-12 w-12 rounded-full bg-[#f3f3f7] flex items-center justify-center mb-3">
                                            <Search class="h-6 w-6 text-gray-400" />
                                        </div>
                                        <p class="text-[#002D57] font-bold text-sm">No hay solicitudes encontradas</p>
                                        <p class="text-[#44474f] text-xs mt-1">Intenta ajustando tus filtros o creando una nueva.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
