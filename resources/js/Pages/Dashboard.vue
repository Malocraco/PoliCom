<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { 
    TrendingUp,
    AlertCircle,
    MoreVertical
} from 'lucide-vue-next';

const props = defineProps({
    solicitudesCreadasSemana: Number,
    campanasLanzadas: Number,
    pendientesPrueba: Number,
    recentRequests: Array,
});
</script>

<template>
    <Head title="Panel de Control" />

    <AuthenticatedLayout>
        <div class="px-8 py-10 bg-[#f9f9fd] min-h-screen font-sans">
            <!-- Greeting Header -->
            <header class="mb-12 flex flex-col md:flex-row justify-between md:items-end gap-6">
                <div>
                    <h1 class="text-4xl font-black text-[#000a1f] tracking-tight mb-2">Hola, {{ $page.props.auth.user.name.split(' ')[0] }}</h1>
                    <p class="text-[#44474f] max-w-2xl leading-relaxed">Bienvenido al archivo arquitectónico del conocimiento de PoliCom. Aquí tienes el resumen de tu actividad corporativa actual.</p>
                </div>
                <div class="flex gap-4">
                    <div class="text-left md:text-right">
                        <p class="text-xs font-bold uppercase tracking-widest text-[#944a00]">Estado del Sistema</p>
                        <p class="text-sm font-medium text-[#4a994a] flex items-center md:justify-end gap-2 mt-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#4a994a] animate-pulse"></span> Operativo
                        </p>
                    </div>
                </div>
            </header>

            <!-- Metrics Bento Grid -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- Card 1 -->
                <div class="p-8 bg-[#ffffff] rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] flex flex-col justify-between h-48 border-l-4 border-[#000a1f]">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[#44474f]">Solicitudes creadas esta semana</span>
                        <h2 class="text-5xl font-black text-[#000a1f] mt-2">{{ String(solicitudesCreadasSemana).padStart(2, '0') }}</h2>
                    </div>
                    <div class="flex items-center gap-2 text-[#4a994a] bg-[#a3f69c] p-2 px-3 rounded-lg w-fit">
                        <TrendingUp class="w-4 h-4" />
                        <span class="text-xs font-bold">+12% vs anterior</span>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="p-8 bg-[#ffffff] rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] flex flex-col justify-between h-48 border-l-4 border-[#fc8f34]">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[#44474f]">Campañas lanzadas</span>
                        <h2 class="text-5xl font-black text-[#000a1f] mt-2">{{ String(campanasLanzadas).padStart(2, '0') }}</h2>
                    </div>
                    <p class="text-xs text-[#944a00] font-semibold">3 en proceso de revisión</p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 bg-[#ffffff] rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] flex flex-col justify-between h-48 border-l-4 border-[#ba1a1a]">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[#44474f]">Pendientes de prueba</span>
                        <h2 class="text-5xl font-black text-[#000a1f] mt-2">{{ String(pendientesPrueba).padStart(2, '0') }}</h2>
                    </div>
                    <div class="flex items-center gap-2 text-[#ba1a1a] bg-[#ffdad6] p-2 px-3 rounded-lg w-fit">
                        <AlertCircle class="w-4 h-4" />
                        <span class="text-xs font-bold">Requiere acción inmediata</span>
                    </div>
                </div>
            </section>

            <!-- Main Table Section -->
            <section class="bg-[#ffffff] rounded-xl shadow-[0_8px_40px_rgb(0,0,0,0.03)] overflow-hidden">
                <div class="px-8 py-6 border-b border-[#edeef1] flex justify-between items-center bg-white">
                    <h3 class="text-xl font-bold text-[#000a1f]">Solicitudes Recientes</h3>
                    <button class="text-sm font-semibold text-[#00204a] hover:underline underline-offset-4">Ver todas las solicitudes</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f3f3f7]">
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest text-[#44474f]">ID</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest text-[#44474f]">Nombre de Proyecto</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest text-[#44474f]">Fecha de Creación</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest text-[#44474f]">Estado</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest text-[#44474f] text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#edeef1]">
                            <tr v-for="req in recentRequests" :key="req.id" class="hover:bg-[#f9f9fd] transition-colors group">
                                <td class="px-8 py-6 text-sm font-medium text-[#000a1f]">{{ req.id }}</td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-[#000a1f] truncate max-w-xs">{{ req.title }}</span>
                                        <span class="text-xs text-[#44474f] truncate max-w-xs">{{ req.subtitle }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm text-[#44474f]">{{ req.date }}</td>
                                <td class="px-8 py-6">
                                    <span :class="['px-3 py-1 rounded-full text-xs font-bold', req.statusBg, req.statusText]">
                                        {{ req.status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button class="p-2 hover:bg-[#edeef1] rounded-lg transition-all text-[#000a1f]">
                                        <MoreVertical class="w-5 h-5"/>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="recentRequests.length === 0">
                                <td colspan="5" class="px-8 py-8 text-center text-[#44474f] text-sm">No hay solicitudes recientes en el sistema.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
