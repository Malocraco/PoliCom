<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import 'dayjs/locale/es';

dayjs.extend(utc);
dayjs.locale('es');
import { 
    ChevronLeft, 
    Send, 
    Mail, 
    MessageSquare, 
    Calendar, 
    User, 
    FileText,
    CheckCircle2,
    Users,
    Download,
    Eye,
    RefreshCw,
    UserCircle
} from 'lucide-vue-next';

const props = defineProps({
    solicitud: {
        type: Object,
        required: true
    }
});

const user = computed(() => usePage().props.auth.user);
const isAnalistaCan = computed(() => user.value?.role?.slug === 'analista_can');

const showTestInput = ref(false);
const form = useForm({
    email_prueba: ''
});

const fileInput = ref(null);
const updateBaseForm = useForm({
    base_datos: null
});

const viewFile = (path) => {
    if (!path) return;
    window.open(`/storage/${path}`, '_blank');
};

const downloadFile = (type) => {
    window.location.href = route('solicitudes.download', { solicitud: props.solicitud.id, type });
};

const triggerBaseUpdate = () => {
    fileInput.value.click();
};

const onBaseSelected = (event) => {
    const file = event.target.files[0];
    if (file) {
        updateBaseForm.base_datos = file;
        updateBaseForm.post(route('solicitudes.update-base', props.solicitud.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                // Notificar éxito si quieres
            }
        });
    }
};

const canTest = computed(() => isAnalistaCan.value);

const prepareForm = useForm({});
const prepareForInfobip = () => {
    prepareForm.post(route('solicitudes.prepare', props.solicitud.id), {
        preserveScroll: true
    });
};

const sendTest = () => {
    if (!props.solicitud?.id) return;
    
    if (!showTestInput.value) {
        showTestInput.value = true;
        // Pre-llenar con el email del usuario actual
        form.email_prueba = usePage().props.auth.user.email;
        return;
    }

    form.post(route('solicitudes.send-test', props.solicitud.id), {
        onSuccess: () => {
            showTestInput.value = false;
        }
    });
};

const launchForm = useForm({});
const launchCampaign = () => {
    if (confirm('¿Estás seguro de lanzar la campaña masiva? (Nota: Por limitaciones de la cuenta actual, solo se enviará al primer contacto de la base de datos)')) {
        launchForm.post(route('solicitudes.launch', props.solicitud.id), {
            preserveScroll: true
        });
    }
};

const getStatusLabel = (status) => {
    if (!status) return 'PENDIENTE';
    if (status === 'creada') return 'PENDIENTE';
    if (status === 'borrador_infobip') return 'EN INFOBIP (BORRADOR)';
    if (status === 'prueba_enviada') return 'PRUEBA EXITOSA';
    if (status === 'lanzada') return 'CAMPAÑA LANZADA';
    return String(status).replace('_', ' ').toUpperCase();
};
</script>

<template>
    <Head :title="`Detalle - ${props.solicitud?.numero_solicitud || 'Cargando...'}`" />

    <AuthenticatedLayout v-if="props.solicitud">
        <template #header>
            <div class="flex items-center">
                <Link :href="route('solicitudes.index')" class="mr-4 p-2 rounded-xl hover:bg-[#f3f3f7] transition-all text-[#44474f]">
                    <ChevronLeft class="h-5 w-5" />
                </Link>
                Detalle de Solicitud
            </div>
        </template>

        <div class="px-8 py-10 bg-[#f9f9fd] min-h-screen font-sans">
            <div class="max-w-6xl mx-auto space-y-8">
                <!-- Actions Bar -->
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-black text-[#002D57] tracking-tight">Supervisión de Campaña</h1>
                        <p class="text-[#44474f] text-sm mt-1">Revisa los detalles, prueba el envío y lanza la campaña.</p>
                    </div>

                    <div v-if="canTest" class="flex items-center gap-3">
                        <!-- Paso 1: Cargar en Infobip -->
                        <button 
                            v-if="props.solicitud.estado === 'creada'"
                            @click="prepareForInfobip"
                            class="flex items-center px-4 py-2.5 bg-[#1DB5E5] text-white rounded-xl hover:bg-[#005082] transition-all shadow-lg font-bold"
                        >
                            <RefreshCw class="h-4 w-4 mr-2" :class="{'animate-spin': prepareForm.processing}" />
                            Cargar en Infobip
                        </button>

                        <!-- Paso 2: Enviar Prueba -->
                        <div v-if="['borrador_infobip', 'prueba_enviada'].includes(props.solicitud.estado)" class="flex items-center gap-2">
                            <input 
                                v-if="showTestInput"
                                v-model="form.email_prueba"
                                :placeholder="props.solicitud.tipo === 'Email' ? 'Email de destino' : 'Número de celular'" 
                                class="rounded-xl border-[#edeef1] text-sm focus:border-[#1DB5E5] focus:ring-[#1DB5E5] h-10 w-56 transition-all shadow-[0_2px_10px_rgb(0,0,0,0.02)]"
                            />
                            <button 
                                @click="sendTest"
                                class="flex items-center h-10 px-5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-all font-bold shadow-md"
                                :disabled="form.processing"
                            >
                                <Send v-if="!form.processing" class="h-4 w-4 mr-2" />
                                <span v-else class="mr-2 animate-spin border-2 border-white/20 border-t-white rounded-full h-4 w-4"></span>
                                {{ showTestInput ? 'Confirmar' : 'Enviar Prueba' }}
                            </button>
                            <button v-if="showTestInput" @click="showTestInput = false" class="text-xs font-bold text-[#44474f] hover:text-red-500 px-2 transition-colors">
                                Cancelar
                            </button>
                        </div>

                        <!-- Paso 3: Lanzar Envío Masivo -->
                        <button 
                            v-if="['prueba_enviada', 'lanzada'].includes(props.solicitud.estado)"
                            @click="launchCampaign"
                            class="flex items-center px-5 py-2.5 bg-[#002D57] text-white rounded-xl hover:bg-[#001735] transition-all shadow-lg shadow-[#002D57]/20 font-bold disabled:opacity-75 disabled:cursor-not-allowed"
                            :disabled="launchForm.processing || props.solicitud.estado === 'lanzada'"
                        >
                            <component :is="props.solicitud.estado === 'lanzada' ? CheckCircle2 : Send" class="h-4 w-4 mr-2" />
                            {{ props.solicitud.estado === 'lanzada' ? 'Campaña Lanzada' : 'Lanzar Envío Masivo' }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Info Card -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-[#ffffff] rounded-xl shadow-[0_8px_40px_rgb(0,0,0,0.03)] overflow-hidden relative">
                            <div class="absolute top-0 right-0 p-8">
                                 <span :class="['px-5 py-2 rounded-xl text-xs font-black', props.solicitud.estado === 'lanzada' ? 'bg-[#e5f5fb] text-[#005082]' : 'bg-[#f3f3f7] text-[#44474f]']">
                                    {{ getStatusLabel(props.solicitud.estado) }}
                                </span>
                            </div>

                            <div class="p-8 pb-0 flex items-start space-x-6">
                                <div class="p-4 bg-[#f3f3f7] rounded-xl">
                                    <component :is="props.solicitud.tipo === 'Email' ? Mail : MessageSquare" class="h-10 w-10 text-[#005082]" />
                                </div>
                                <div class="space-y-1">
                                    <h1 class="text-2xl font-black text-[#002D57]">{{ props.solicitud.nombre_envio }}</h1>
                                    <p class="text-sm text-[#44474f] font-mono tracking-widest">{{ props.solicitud.numero_solicitud }}</p>
                                    <div class="flex flex-wrap items-center mt-4 gap-4">
                                        <p class="text-sm font-medium text-[#44474f] flex items-center bg-[#f9f9fd] px-3 py-1.5 rounded-lg border border-[#edeef1]">
                                            <Calendar class="h-4 w-4 mr-2 text-[#005082]" />
                                            Programado: <span class="ml-1 font-bold text-[#002D57]">{{ dayjs.utc(props.solicitud.fecha_requerida).format('DD/MM/YYYY') }}</span>
                                        </p>
                                        <div class="flex items-center text-xs text-[#44474f] bg-[#f9f9fd] px-3 py-1.5 rounded-lg border border-[#edeef1]">
                                            <User class="h-4 w-4 mr-2 text-[#1DB5E5]" />
                                            Solicitante: <span class="ml-1 font-bold text-[#002D57]">{{ props.solicitud.cliente_nombre }}</span>
                                        </div>
                                        <div class="flex items-center text-xs text-[#44474f] bg-[#f9f9fd] px-3 py-1.5 rounded-lg border border-[#edeef1]">
                                            <UserCircle class="h-4 w-4 mr-2 text-[#1DB5E5]" />
                                            Analista: <span class="ml-1 font-bold text-[#002D57]">{{ props.solicitud.analista?.name || 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="m-8 p-8 bg-[#f9f9fd] rounded-xl border border-[#edeef1] grid grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="text-[10px] uppercase font-black text-[#44474f] tracking-wider mb-2">Área Solicitante</h4>
                                        <p class="text-sm font-bold text-[#002D57]">{{ props.solicitud.area_solicitante }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] uppercase font-black text-[#44474f] tracking-wider mb-2">Canal de Envío</h4>
                                        <p class="text-sm font-bold text-[#002D57]">{{ props.solicitud.tipo }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] uppercase font-black text-[#44474f] tracking-wider mb-2">Remitente (Mask)</h4>
                                        <p class="text-sm font-bold text-[#002D57]">{{ props.solicitud.mask }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] uppercase font-black text-[#44474f] tracking-wider mb-2">Analista Creador</h4>
                                        <p class="text-sm font-bold text-[#002D57]">{{ props.solicitud.analista?.name || 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="text-[10px] uppercase font-black text-[#44474f] tracking-wider mb-2">Tipo de Target</h4>
                                        <p class="text-sm font-bold text-[#002D57]">{{ props.solicitud.tipo_target }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] uppercase font-black text-[#44474f] tracking-wider mb-2">Detalle del target</h4>
                                        <p class="text-sm font-bold text-[#002D57]">{{ props.solicitud.detalle_target || 'Sin detalle adicional' }}</p>
                                    </div>
                                    <div v-if="props.solicitud.tipo === 'Email'">
                                        <h4 class="text-[10px] uppercase font-black text-[#44474f] tracking-wider mb-2">Asunto (Subject)</h4>
                                        <p class="text-sm font-bold text-[#002D57]">{{ props.solicitud.subject }}</p>
                                    </div>
                                    <div v-else>
                                        <h4 class="text-[10px] uppercase font-black text-[#44474f] tracking-wider mb-2">Contenido SMS</h4>
                                        <p class="text-sm font-bold text-[#002D57] bg-white p-3 rounded-lg border border-[#edeef1] italic">"{{ props.solicitud.sms_copy }}"</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assets Card -->
                        <div class="bg-[#ffffff] rounded-xl shadow-[0_8px_40px_rgb(0,0,0,0.03)] overflow-hidden">
                            <div class="p-6 border-b border-[#edeef1] flex items-center">
                                <FileText class="h-5 w-5 mr-3 text-[#1DB5E5]" />
                                <h3 class="font-bold text-[#002D57]">Archivos Adjuntos</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Pieza Card -->
                                <div class="p-5 border-2 border-dashed border-[#edeef1] rounded-xl flex items-center justify-between group hover:border-[#1DB5E5] hover:bg-[#f9f9fd] transition-all">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-[#ffffff] rounded-xl text-[#005082] shadow-sm mr-4 border border-[#edeef1] group-hover:text-[#1DB5E5]">
                                            <Mail v-if="props.solicitud.tipo === 'Email'" class="h-6 w-6" />
                                            <MessageSquare v-else class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-[#002D57]">Pieza Creativa</p>
                                            <p class="text-xs text-[#44474f] truncate max-w-[150px]" :title="props.solicitud.pieza_creativa_nombre">
                                                {{ props.solicitud.pieza_creativa_nombre || 'Diseño institucional' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button @click="viewFile(props.solicitud.pieza_creativa_path)" class="p-2 text-[#44474f] bg-white border border-[#edeef1] rounded-lg hover:text-[#002D57] hover:border-[#002D57] shadow-sm transition-all" title="Ver">
                                            <Eye class="h-4 w-4" />
                                        </button>
                                        <button @click="downloadFile('pieza')" class="p-2 text-[#44474f] bg-white border border-[#edeef1] rounded-lg hover:text-[#002D57] hover:border-[#002D57] shadow-sm transition-all" title="Descargar">
                                            <Download class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Base de Datos Card -->
                                <div class="p-5 border-2 border-dashed border-[#edeef1] rounded-xl flex items-center justify-between group hover:border-[#1DB5E5] hover:bg-[#f9f9fd] transition-all">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-[#ffffff] rounded-xl text-[#005082] shadow-sm mr-4 border border-[#edeef1] group-hover:text-[#1DB5E5]">
                                            <Users class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-[#002D57]">BBDD (Excel/CSV)</p>
                                            <p class="text-xs text-[#44474f] truncate max-w-[150px]" :title="props.solicitud.base_datos_nombre">
                                                {{ props.solicitud.base_datos_nombre || (props.solicitud.base_datos_path ? 'Listado de contactos' : 'BBDD General (No adjunto)') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button v-if="props.solicitud.base_datos_path" @click="downloadFile('base')" class="p-2 text-[#44474f] bg-white border border-[#edeef1] rounded-lg hover:text-[#002D57] hover:border-[#002D57] shadow-sm transition-all" title="Descargar">
                                            <Download class="h-4 w-4" />
                                        </button>
                                        <button v-if="isAnalistaCan" @click="triggerBaseUpdate" class="p-2 text-[#44474f] bg-white border border-[#edeef1] rounded-lg hover:text-orange-500 hover:border-orange-500 shadow-sm transition-all" :title="props.solicitud.base_datos_path ? 'Cambiar Base' : 'Subir Base'">
                                            <RefreshCw class="h-4 w-4" :class="{'animate-spin': updateBaseForm.processing}" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input oculto para cambiar la base -->
                    <input type="file" ref="fileInput" class="hidden" @change="onBaseSelected" accept=".csv,.xlsx,.xls" />

                    <!-- History / Timeline Side -->
                    <div class="space-y-8">
                        <div class="bg-[#ffffff] rounded-xl shadow-[0_8px_40px_rgb(0,0,0,0.03)] h-full overflow-hidden">
                            <div class="p-6 border-b border-[#edeef1] flex items-center">
                                <Calendar class="h-5 w-5 mr-3 text-[#1DB5E5]" />
                                <h3 class="font-bold text-[#002D57]">Línea de Tiempo</h3>
                            </div>
                            <div class="p-8">
                                <div class="space-y-10 relative">
                                    <div class="absolute left-5 top-2 bottom-2 w-0.5 bg-[#edeef1]"></div>
                                    
                                    <div class="relative flex items-start space-x-5">
                                        <div class="z-10 h-10 w-10 mt-[-4px] rounded-full bg-[#e5f5fb] border-4 border-white flex items-center justify-center text-[#005082] shadow-sm">
                                            <FileText class="h-4 w-4" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-[#002D57] uppercase tracking-wide">Solicitud Creada</p>
                                            <p class="text-xs font-medium text-[#44474f] mt-1">
                                                {{ dayjs(props.solicitud.created_at).format('DD/MM/YYYY [-] hh:mm A') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div v-if="props.solicitud.estado === 'prueba_enviada' || props.solicitud.estado === 'lanzada'" class="relative flex items-start space-x-5">
                                        <div class="z-10 h-10 w-10 mt-[-4px] rounded-full bg-orange-100 border-4 border-white flex items-center justify-center text-orange-500 shadow-sm">
                                            <Send class="h-4 w-4" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-[#002D57] uppercase tracking-wide">Prueba Enviada</p>
                                            <p class="text-xs font-medium text-[#44474f] mt-1">
                                                {{ dayjs(props.solicitud.updated_at).format('DD/MM/YYYY [-] hh:mm A') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div v-if="props.solicitud.estado === 'lanzada'" class="relative flex items-start space-x-5">
                                        <div class="z-10 h-10 w-10 mt-[-4px] rounded-full bg-green-100 border-4 border-white flex items-center justify-center text-green-600 shadow-sm">
                                            <CheckCircle2 class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-[#002D57] uppercase tracking-wide">Envío Completado</p>
                                            <p class="text-xs font-medium text-[#44474f] mt-1">
                                                {{ dayjs(props.solicitud.updated_at).format('DD/MM/YYYY [-] hh:mm A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
