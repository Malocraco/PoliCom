<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { 
    ArrowLeft, 
    Save, 
    Upload, 
    Info,
    Mail,
    MessageSquare,
    Globe,
    UserCircle,
    FileText
} from 'lucide-vue-next';

import { computed, ref, onMounted, onUnmounted } from 'vue';

const form = useForm({
    area_solicitante: '',
    cliente_nombre: '',
    cliente_email: '',
    com_id: '',
    nombre_envio: '',
    tipo: 'Email',
    tipo_target: '',
    detalle_target: '',
    tiene_evento_noticia: false,
    link_url: '',
    subject: '',
    mask: '',
    sms_copy: '',
    fecha_requerida: '',
    hora_programada: '',
    pieza_creativa: null,
    base_datos: null,
});

const departamentos = [
    'TH',
    'Gerencia de Operaciones Académicas',
    'SST',
    'Huella Grancolombiana',
    'Prácticas',
    'Data y analítica',
    'ORII',
    'Permanencia',
    'Dirección Nacional de Registro y Control',
    'Gerencia de Servicio y Permanencia',
    'CONTRABILIDAD CXP',
    'Infra estructura',
    'Oficina de Graduados',
    'FSCC',
    'Comunicaciones',
    'Ruta Formativa',
    'Dirección Académica y de Docencia',
    'Academico',
    'Gobierno Digital',
    'FSCC / Jefatura de Prensa',
    'Vicerrectoría financiera',
    'FNGS',
    'Centro de Idiomas',
    'Gerencia de Talento Humano y Comunicaciones',
    'FIDI',
    'Trompo',
    'SISNAB',
    'Direccion de Experiencia E Inclusion',
    'BIENESTAR',
    'Psicologia'
];

const mascaras = [
    'GUARDIANES POLI',
    'POLI SST',
    'COMUNICACIONES POLI',
    'SOY GRADUADO POLI',
    'ÁREA DE PRÁCTICAS',
    'PRÁCTICAS POLI',
    'PRÁCTICAS Y EMPLEABILIDAD POLI',
    'EXPERIENCIA POLI',
    'INVESTIGACIÓN POLI',
    'TU POLIGUÍA',
    'PLANEACIÓN POLI',
    'CRECIENDO CON BIENESTAR',
    'POLI DIGITAL',
    'FNGS EN MOVIMIENTO',
    'FIDI INNOVA',
    'FSCC EN ACCIÓN',
    'CONEXIÓN POLI FSCC',
    'CONEXIÓN POLI FNGS',
    'CONEXIÓN POLI FIDI',
    'EDITORIAL POLI',
    'HUELLA GRANCOLOMBIANA',
    'PLANEACIÓN ESTRATEGICA',
    'BIENESTAR POLI',
    'DIRECCIÓN DE DOCENCIA',
    'PROCESOS Y SERVICIOS POLI',
    'VICERRECTORÍA ESTUDIANTE',
    'VICERRECTORÍA FINANCIERA',
    'ENTERATE GRADUADO POLI',
    'VICERRECTORÍA ACADÉMICA',
    'TALENTO HUMANO',
    'TECNOLOGÍA',
    'RECTORÍA',
    'BECADOS POLI'
];

const showDepartamentos = ref(false);
const showMascaras = ref(false);

const filteredDepartamentos = computed(() => {
    if (!form.area_solicitante) return departamentos;
    return departamentos.filter(d => 
        d.toLowerCase().includes(form.area_solicitante.toLowerCase())
    );
});

const minDate = new Date().toISOString().split('T')[0];

const filteredMascaras = computed(() => {
    if (!form.mask) return mascaras;
    return mascaras.filter(m => 
        m.toLowerCase().includes(form.mask.toLowerCase())
    );
});

const dropdownContainer = ref(null);
const dropdownMaskContainer = ref(null);

const selectDepto = (depto) => {
    form.area_solicitante = depto;
    showDepartamentos.value = false;
};

const selectMask = (mask) => {
    form.mask = mask;
    showMascaras.value = false;
};

const handleClickOutside = (event) => {
    if (dropdownContainer.value && !dropdownContainer.value.contains(event.target)) {
        showDepartamentos.value = false;
    }
    if (dropdownMaskContainer.value && !dropdownMaskContainer.value.contains(event.target)) {
        showMascaras.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const submit = () => {
    form.post(route('solicitudes.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Nueva Solicitud" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <Link :href="route('solicitudes.index')" class="mr-4 p-2 rounded-xl hover:bg-[#f3f3f7] transition-all text-[#44474f]">
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                Crear Nueva Solicitud
            </div>
        </template>

        <div class="px-8 py-10 bg-[#f9f9fd] min-h-screen font-sans">
            <header class="mb-10 max-w-5xl mx-auto">
                <h1 class="text-3xl font-black text-[#002D57] tracking-tight mb-2">Nueva Solicitud de Envío</h1>
                <p class="text-[#44474f] max-w-2xl text-sm leading-relaxed">Completa el formulario para registrar una nueva solicitud de comunicación. Asegúrate de adjuntar la pieza creativa y la base de datos si aplica.</p>
            </header>

            <div class="max-w-5xl mx-auto pb-12">
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Informacion del Cliente -->
                    <div class="bg-[#ffffff] rounded-xl shadow-[0_8px_40px_rgb(0,0,0,0.03)]">
                        <div class="p-6 border-b border-[#edeef1] flex items-center">
                            <UserCircle class="h-5 w-5 mr-3 text-[#1DB5E5]" />
                            <h3 class="font-bold text-[#002D57]">Información del Solicitante</h3>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2 relative" ref="dropdownContainer">
                                <label class="text-sm font-bold text-[#44474f]">Departamento / Área</label>
                                <input 
                                    v-model="form.area_solicitante" 
                                    type="text" 
                                    placeholder="Escriba o busque..." 
                                    class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all"
                                    @focus="showDepartamentos = true"
                                    required 
                                />
                                
                                <div v-if="showDepartamentos && filteredDepartamentos.length > 0" 
                                     class="absolute z-[100] w-full mt-1 bg-white border border-[#edeef1] rounded-lg shadow-2xl max-h-60 overflow-y-auto">
                                    <div 
                                        v-for="depto in filteredDepartamentos" 
                                        :key="depto"
                                        @mousedown="selectDepto(depto)"
                                        class="px-5 py-3 hover:bg-[#f3f3f7] hover:text-[#002D57] cursor-pointer text-sm font-medium text-[#44474f] transition-colors border-b border-[#edeef1] last:border-0"
                                    >
                                        {{ depto }}
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#44474f]">Nombre del Cliente</label>
                                <input v-model="form.cliente_nombre" type="text" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" required />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#44474f]">Correo del Cliente</label>
                                <input v-model="form.cliente_email" type="email" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" required />
                            </div>
                        </div>
                    </div>

                    <!-- Detalles de la Campaña -->
                    <div class="bg-[#ffffff] rounded-xl shadow-[0_8px_40px_rgb(0,0,0,0.03)]">
                        <div class="p-6 border-b border-[#edeef1] flex items-center">
                            <FileText class="h-5 w-5 mr-3 text-[#1DB5E5]" />
                            <h3 class="font-bold text-[#002D57]">Detalles de la Campaña</h3>
                        </div>
                        <div class="p-8 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-[#44474f]">Nombre del Envío / Campaña</label>
                                    <input v-model="form.nombre_envio" type="text" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" required />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-[#44474f]">COM ID</label>
                                    <input v-model="form.com_id" type="text" placeholder="Ej: COM-001" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" required />
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label class="text-sm font-bold text-[#44474f]">Tipo de Comunicación</label>
                                <div class="flex gap-4">
                                    <button type="button" @click="form.tipo = 'Email'" :class="['flex-1 flex items-center justify-center p-4 rounded-xl border-2 transition-all font-bold', form.tipo === 'Email' ? 'border-[#002D57] bg-[#f9f9fd] text-[#002D57]' : 'border-[#edeef1] text-[#44474f] hover:border-gray-300']">
                                        <Mail class="h-5 w-5 mr-2" />
                                        Email
                                    </button>
                                    <button type="button" @click="form.tipo = 'SMS'" :class="['flex-1 flex items-center justify-center p-4 rounded-xl border-2 transition-all font-bold', form.tipo === 'SMS' ? 'border-[#002D57] bg-[#f9f9fd] text-[#002D57]' : 'border-[#edeef1] text-[#44474f] hover:border-gray-300']">
                                        <MessageSquare class="h-5 w-5 mr-2" />
                                        SMS
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-[#44474f]">Tipo de Target / Público</label>
                                    <input v-model="form.tipo_target" type="text" placeholder="Ej: Estudiantes, Colaboradores..." class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" required />
                                </div>
                                <div class="space-y-2 relative" ref="dropdownMaskContainer">
                                    <label class="text-sm font-bold text-[#44474f]">Máscara / Remitente</label>
                                    <input 
                                        v-model="form.mask" 
                                        type="text" 
                                        placeholder="Ej: Poli Grancolombiano" 
                                        class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" 
                                        @focus="showMascaras = true"
                                        required 
                                    />

                                    <div v-if="showMascaras && filteredMascaras.length > 0" 
                                         class="absolute z-[100] w-full mt-1 bg-white border border-[#edeef1] rounded-lg shadow-2xl max-h-60 overflow-y-auto">
                                        <div 
                                            v-for="mask in filteredMascaras" 
                                            :key="mask"
                                            @mousedown="selectMask(mask)"
                                            class="px-5 py-3 hover:bg-[#f3f3f7] hover:text-[#002D57] cursor-pointer text-sm font-medium text-[#44474f] transition-colors border-b border-[#edeef1] last:border-0"
                                        >
                                            {{ mask }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#44474f]">Detalle del Target (Programa, rol, etc.)</label>
                                <textarea v-model="form.detalle_target" rows="2" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" placeholder="Ej: Facultad de Ingeniería, Estudiantes de último semestre..."></textarea>
                            </div>

                            <div v-if="form.tipo === 'Email'" class="space-y-2 pt-4 border-t border-[#edeef1]">
                                <label class="text-sm font-bold text-[#44474f]">Asunto (Subject)</label>
                                <input v-model="form.subject" type="text" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" required />
                            </div>

                            <div v-if="form.tipo === 'SMS'" class="space-y-2 pt-4 border-t border-[#edeef1]">
                                <label class="text-sm font-bold text-[#44474f]">Contenido del SMS</label>
                                <textarea v-model="form.sms_copy" rows="3" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" placeholder="Máximo 160 caracteres..." required></textarea>
                                <p class="text-right text-xs font-bold text-[#44474f]">{{ form.sms_copy?.length || 0 }} / 160</p>
                            </div>

                            <div class="flex items-center space-x-3 pt-2">
                                <input v-model="form.tiene_evento_noticia" type="checkbox" class="h-5 w-5 rounded text-[#002D57] focus:ring-[#002D57] border-[#edeef1] transition-all" />
                                <label class="text-sm font-bold text-[#44474f] flex items-center">
                                    <Globe class="h-4 w-4 mr-2 text-[#44474f]" />
                                    Campaña vinculada a evento o noticia en página web
                                </label>
                            </div>

                            <div v-if="form.tiene_evento_noticia" class="space-y-2 animate-in fade-in duration-300">
                                <label class="text-sm font-bold text-[#44474f]">URL del Enlace Externo</label>
                                <input v-model="form.link_url" type="url" placeholder="https://..." class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" required />
                            </div>
                        </div>
                    </div>

                    <!-- Programación -->
                    <div class="bg-[#ffffff] rounded-xl shadow-[0_8px_40px_rgb(0,0,0,0.03)]">
                        <div class="p-6 border-b border-[#edeef1] flex items-center">
                            <Info class="h-5 w-5 mr-3 text-[#1DB5E5]" />
                            <h3 class="font-bold text-[#002D57]">Programación y Archivos</h3>
                        </div>
                        <div class="p-8 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-[#44474f]">Fecha Requerida</label>
                                    <input v-model="form.fecha_requerida" type="date" :min="minDate" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" required />
                                    <div v-if="form.errors.fecha_requerida" class="text-xs text-red-500 mt-1 font-medium">{{ form.errors.fecha_requerida }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-[#44474f]">Hora Sugerida (Opcional)</label>
                                    <input v-model="form.hora_programada" type="time" class="w-full rounded-lg border-[#edeef1] focus:border-[#1DB5E5] focus:ring-[#1DB5E5] shadow-[0_2px_10px_rgb(0,0,0,0.02)] transition-all" />
                                    <div v-if="form.errors.hora_programada" class="text-xs text-red-500 mt-1 font-medium">{{ form.errors.hora_programada }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-[#44474f]">Pieza Creativa (HTML/Imagen)</label>
                                    <div class="relative group">
                                        <div class="flex items-center justify-center w-full h-32 px-4 transition bg-[#f9f9fd] border-2 border-[#edeef1] border-dashed rounded-xl appearance-none cursor-pointer hover:border-[#002D57] focus:outline-none">
                                            <div class="flex flex-col items-center space-y-2">
                                                <Upload class="w-8 h-8 text-[#44474f] group-hover:text-[#002D57] transition-colors" />
                                                <span class="font-bold text-[#44474f] text-sm">
                                                    {{ form.pieza_creativa ? form.pieza_creativa.name : 'Haz clic para subir archivo' }}
                                                </span>
                                            </div>
                                            <input type="file" @input="form.pieza_creativa = $event.target.files[0]" class="absolute inset-0 z-50 w-full h-full p-0 m-0 outline-none opacity-0 cursor-pointer" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-[#44474f]">Base de Datos Segmentada (CSV/Excel)</label>
                                    <div class="relative group">
                                        <div class="flex items-center justify-center w-full h-32 px-4 transition bg-[#f9f9fd] border-2 border-[#edeef1] border-dashed rounded-xl appearance-none cursor-pointer hover:border-[#002D57] focus:outline-none">
                                            <div class="flex flex-col items-center space-y-2">
                                                <Upload class="w-8 h-8 text-[#44474f] group-hover:text-[#002D57] transition-colors" />
                                                <span class="font-bold text-[#44474f] text-sm text-center">
                                                    {{ form.base_datos ? form.base_datos.name : 'Subir BD segmentada (opcional)' }}
                                                </span>
                                            </div>
                                            <input type="file" @input="form.base_datos = $event.target.files[0]" class="absolute inset-0 z-50 w-full h-full p-0 m-0 outline-none opacity-0 cursor-pointer" />
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400">Si no se sube una base, se utilizará la BBDD general.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer with buttons -->
                    <div class="flex justify-end gap-4 pt-4 pb-8">
                        <Link :href="route('solicitudes.index')" class="px-6 py-3 rounded-xl border border-[#edeef1] text-[#44474f] font-bold hover:bg-[#edeef1] transition-all">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing" class="px-10 py-3 bg-[#002D57] text-white rounded-xl font-bold hover:bg-[#001735] transition-all shadow-xl shadow-[#002D57]/20 flex items-center">
                            <Save v-if="!form.processing" class="h-5 w-5 mr-3" />
                            <span v-else class="mr-3 animate-spin border-2 border-white/20 border-t-white rounded-full h-5 w-5"></span>
                            {{ form.processing ? 'Guardando...' : 'Crear Solicitud' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
