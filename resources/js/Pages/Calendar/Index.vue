<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { 
    ChevronLeft, 
    ChevronRight, 
    Calendar as CalendarIcon,
    Clock,
    MoreVertical,
    Mail,
    MessageSquare,
    Lock,
    Unlock,
    Plus
} from 'lucide-vue-next';
import dayjs from 'dayjs';
import 'dayjs/locale/es';

dayjs.locale('es');

const props = defineProps({
    solicitudes: Array,
    startOfWeek: String,
    endOfWeek: String,
    currentDate: String
});

const isEditorActive = ref(false);
const dragOverDay = ref(null);

const weekDays = computed(() => {
    const days = [];
    let current = dayjs(props.startOfWeek);
    for (let i = 0; i < 7; i++) {
        days.push({
            date: current.format('YYYY-MM-DD'),
            name: current.format('ddd').replace('.', ''), 
            day: current.format('D'),
            month: current.format('MMM'),
            fullDate: current.format('dddd D [de] MMMM'),
            isWeekend: current.day() === 0 || current.day() === 6
        });
        current = current.add(1, 'day');
    }
    return days;
});

const getSolicitudesByDay = (date) => {
    return props.solicitudes.filter(s => {
        const solDate = s.fecha_requerida.split('T')[0];
        return solDate === date;
    });
};

const getStatusBorderColor = (status) => {
    switch (status) {
        case 'lanzada': return 'border-l-[#002D57]';
        case 'prueba_enviada': return 'border-l-[#d96606]';
        case 'borrador_infobip': return 'border-l-[#1DB5E5]';
        case 'vencida': return 'border-l-[#ba1a1a]';
        default: return 'border-l-[#2e7d32]'; 
    }
};

const getStatusIconColor = (status) => {
    switch (status) {
        case 'lanzada': return 'text-[#002D57]';
        case 'prueba_enviada': return 'text-[#d96606]';
        case 'borrador_infobip': return 'text-[#1DB5E5]';
        case 'vencida': return 'text-[#ba1a1a]';
        default: return 'text-[#2e7d32]'; 
    }
};

const goToSolicitud = (id) => {
    if (isEditorActive.value) return; 
    router.get(route('solicitudes.show', id));
};

const navigateWeek = (direction) => {
    const newDate = dayjs(props.currentDate).add(direction * 7, 'day').format('YYYY-MM-DD');
    router.get(route('calendar.index'), { date: newDate }, { preserveState: true });
};

const navigateToday = () => {
    const newDate = dayjs().format('YYYY-MM-DD');
    router.get(route('calendar.index'), { date: newDate }, { preserveState: true });
};

const canManage = computed(() => usePage().props.auth.user.role?.slug === 'analista_can');

const onDragStart = (event, solicitudId) => {
    if (!canManage.value || !isEditorActive.value) return;
    event.dataTransfer.setData('solicitudId', solicitudId);
    event.dataTransfer.effectAllowed = 'move';
    
    if (event.currentTarget) {
        event.dataTransfer.setDragImage(event.currentTarget, 20, 20);
    }
};

const onDragEnd = (event) => {
    dragOverDay.value = null;
};

const onDragOver = (event, date) => {
    if (!canManage.value || !isEditorActive.value) return;
    dragOverDay.value = date;
};

const onDragLeave = (event, date) => {
    // Evitar parpadeo del DropZone si el mouse interseca elementos hijos (como otras tarjetas)
    const currentTarget = event.currentTarget;
    const relatedTarget = event.relatedTarget;
    if (currentTarget && relatedTarget && currentTarget.contains(relatedTarget)) {
        return;
    }
    
    if (dragOverDay.value === date) {
        dragOverDay.value = null;
    }
};

const onDrop = (event, newDate) => {
    if (!canManage.value || !isEditorActive.value) return;
    dragOverDay.value = null;
    let solicitudId = event.dataTransfer.getData('solicitudId');
    
    const dbId = props.solicitudes.find(s => s.numero_solicitud === solicitudId || s.id == solicitudId)?.id || solicitudId;

    router.patch(route('calendar.move', dbId), {
        fecha_requerida: newDate
    }, {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Calendario" />

    <AuthenticatedLayout>
        <div class="flex-1 p-4 md:p-8 space-y-8 max-w-[1600px] w-full mx-auto">
            
            <!-- Content Header -->
            <section class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Title and Navigation -->
                <div class="flex items-center gap-6">
                    <h2 class="text-3xl font-black font-headline text-[#002D57] tracking-tight capitalize">
                        {{ dayjs(currentDate).format('MMMM YYYY') }}
                    </h2>
                    <div class="flex items-center bg-[#f3f3f7] p-1 rounded-lg">
                        <button @click="navigateWeek(-1)" class="p-2 hover:bg-[#e2e2e6] rounded-lg transition-colors group">
                            <ChevronLeft class="text-[#44474f] group-hover:text-[#002D57] w-5 h-5" />
                        </button>
                        <button @click="navigateToday" class="px-4 py-1.5 text-sm font-bold text-[#002D57] hover:bg-[#e2e2e6] rounded-lg transition-colors">
                            Hoy
                        </button>
                        <button @click="navigateWeek(1)" class="p-2 hover:bg-[#e2e2e6] rounded-lg transition-colors group">
                            <ChevronRight class="text-[#44474f] group-hover:text-[#002D57] w-5 h-5" />
                        </button>
                    </div>
                </div>

                <!-- Editor Mode Toggle -->
                <div v-if="canManage" class="flex items-center gap-3 bg-[#1DB5E5]/10 p-3 rounded-xl ring-1 ring-[#1DB5E5]/20">
                    <span class="text-xs font-bold text-[#005082] tracking-wider uppercase">Modo Editor</span>
                    
                    <div class="relative inline-flex items-center cursor-pointer" @click="isEditorActive = !isEditorActive">
                        <div :class="[
                            'w-14 h-7 rounded-full transition-colors duration-300 relative',
                            isEditorActive ? 'bg-[#1DB5E5]' : 'bg-[#d9dadd]'
                        ]">
                            <div :class="[
                                'absolute top-[2px] left-[2px] bg-white rounded-full h-6 w-6 transition-transform duration-300',
                                isEditorActive ? 'translate-x-7' : 'translate-x-0'
                            ]"></div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5 ml-1">
                        <component 
                            :is="isEditorActive ? Unlock : Lock" 
                            :class="['w-5 h-5', isEditorActive ? 'text-[#1DB5E5]' : 'text-[#8c929d]']" 
                        />
                        <span :class="['text-xs font-semibold', isEditorActive ? 'text-[#1DB5E5]' : 'text-[#8c929d]']">
                            {{ isEditorActive ? 'EDITABLE' : 'BLOQUEADO' }}
                        </span>
                    </div>
                </div>
            </section>

            <!-- Weekly Grid -->
            <section class="bg-[#f3f3f7] rounded-2xl p-1 overflow-hidden">
                <div class="calendar-grid bg-[#c4c6d0]/10">
                    
                    <!-- Days of Week Headers -->
                    <div v-for="day in weekDays" :key="'header-'+day.date" 
                        :class="[
                            'bg-white p-4 text-center', 
                            day.isWeekend ? 'bg-[#f3f3f7]' : ''
                        ]">
                        <span :class="[
                            'block text-xs font-bold uppercase tracking-[0.2em] mb-1',
                            dayjs().isSame(day.date, 'day') ? 'text-[#1DB5E5]' : 'text-[#44474f]'
                        ]">
                            {{ day.name }}
                        </span>
                        <span :class="[
                            'text-2xl font-black font-headline',
                            dayjs().isSame(day.date, 'day') ? 'text-[#1DB5E5]' : (day.isWeekend ? 'text-[#002D57]/60' : 'text-[#002D57]')
                        ]">
                            {{ day.day }}
                        </span>
                    </div>

                    <!-- Column Contents -->
                    <div v-for="day in weekDays" :key="'col-'+day.date"
                        :class="[
                            'min-h-[700px] p-3 space-y-4 relative transition-colors',
                            day.isWeekend ? 'bg-[#d9dadd]/5' : (dayjs().isSame(day.date, 'day') ? 'bg-[#f3f3f7]/30' : 'bg-[#f9f9fd]'),
                            dragOverDay === day.date && isEditorActive ? 'bg-[#1DB5E5]/10' : ''
                        ]"
                        @dragover.prevent="onDragOver($event, day.date)"
                        @dragleave="onDragLeave($event, day.date)"
                        @drop="onDrop($event, day.date)"
                    >
                        <!-- Current Time Indicator Indicator -->
                        <div v-if="dayjs().isSame(day.date, 'day')" class="absolute inset-x-0 top-1/4 border-t-2 border-[#1DB5E5]/30 z-0"></div>

                        <!-- Drop indicator -->
                        <div v-if="dragOverDay === day.date && isEditorActive" 
                            class="h-24 border-2 border-dashed border-[#1DB5E5]/50 rounded-xl flex items-center justify-center group bg-[#1DB5E5]/5 relative z-10 pointer-events-none">
                            <div class="text-center pointer-events-none">
                                <Plus class="w-6 h-6 text-[#1DB5E5] mx-auto mb-1 animate-bounce" />
                                <span class="text-[10px] font-bold text-[#1DB5E5] uppercase tracking-wider">Nueva Tarea</span>
                            </div>
                        </div>

                        <!-- Events -->
                        <article 
                            v-for="solicitud in getSolicitudesByDay(day.date)" 
                            :key="solicitud.id"
                            :draggable="isEditorActive"
                            @dragstart="onDragStart($event, solicitud.numero_solicitud || solicitud.id)"
                            @dragend="onDragEnd($event)"
                            @click="goToSolicitud(solicitud.id)"
                            :class="[
                                'p-4 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border-l-4 cursor-pointer relative z-10 transition-all bg-white hover:translate-y-[-2px]',
                                isEditorActive ? 'cursor-grab active:cursor-grabbing hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)]' : 'hover:shadow-md',
                                getStatusBorderColor(solicitud.estado)
                            ]"
                        >
                            <header class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-mono font-bold text-[#44474f]">
                                    {{ solicitud.numero_solicitud || 'SOL-NUEVA' }}
                                </span>
                                <component 
                                    :is="solicitud.tipo === 'Email' ? Mail : MessageSquare" 
                                    :class="['w-4 h-4', getStatusIconColor(solicitud.estado)]" 
                                />
                            </header>
                            
                            <h3 class="text-sm font-bold text-[#000a1f] mb-3 leading-tight">
                                {{ solicitud.nombre_envio }}
                            </h3>
                            
                            <footer class="flex items-center gap-1.5">
                                <Clock class="w-3.5 h-3.5 text-[#44474f]" />
                                <span class="text-[11px] font-bold text-[#44474f]">
                                    {{ solicitud.hora_programada ? solicitud.hora_programada.substring(0,5) : '--:--' }}
                                </span>
                            </footer>
                        </article>

                    </div>
                </div>
            </section>

            <!-- Bottom Dashboard Summary -->
            <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Semanal -->
                <div class="bg-[#002D57] p-6 rounded-2xl flex items-center justify-between overflow-hidden relative group shadow-sm">
                    <div class="relative z-10">
                        <span class="text-xs font-bold text-[#7189b8] uppercase tracking-widest block mb-1">Total Semanal</span>
                        <span class="text-3xl font-black text-white">{{ solicitudes.length }}</span>
                    </div>
                </div>

                <!-- Email Mkt -->
                <div class="bg-white p-6 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-[#44474f] uppercase tracking-widest block mb-1">Email Mkt</span>
                        <span class="text-3xl font-black text-[#002D57]">
                            {{ solicitudes.filter(s => s.tipo === 'Email').length }}
                        </span>
                    </div>
                    <div class="w-12 h-12 bg-[#d7e2ff] rounded-xl flex items-center justify-center">
                        <Mail class="w-6 h-6 text-[#001b3f]" />
                    </div>
                </div>

                <!-- SMS Push -->
                <div class="bg-white p-6 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-[#44474f] uppercase tracking-widest block mb-1">SMS Push</span>
                        <span class="text-3xl font-black text-[#002D57]">
                            {{ solicitudes.filter(s => s.tipo === 'SMS').length }}
                        </span>
                    </div>
                    <div class="w-12 h-12 bg-[#ffdcc5] rounded-xl flex items-center justify-center">
                        <MessageSquare class="w-6 h-6 text-[#301400]" />
                    </div>
                </div>

                <!-- Promedio Aprobadas -->
                <div class="bg-white p-6 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-[#44474f] uppercase tracking-widest block mb-1">Aprobación</span>
                        <span class="text-3xl font-black text-[#002805]">
                            {{ Math.round((solicitudes.filter(s => s.estado === 'lanzada').length / Math.max(1, solicitudes.length)) * 100) }}%
                        </span>
                    </div>
                    <div class="w-12 h-12 bg-[#88d982] rounded-xl flex items-center justify-center">
                        <!-- 'verified' or checkmark icon instead -->
                        <svg class="w-6 h-6 text-[#002204]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </section>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
}
</style>
