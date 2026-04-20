<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { 
    Users, 
    Upload, 
    Search, 
    Filter,
    Download,
    Mail,
    Phone,
    MoreVertical
} from 'lucide-vue-next';

const props = defineProps({
    contactos: Object
});

const form = useForm({
    archivo: null,
});

const uploadFile = () => {
    form.post(route('contactos.upload'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Base de Datos General" />

    <AuthenticatedLayout>
        <template #header>
            Base de Datos Maestra
        </template>

        <div class="space-y-6">
            <!-- Header Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Stats Summary -->
                <div class="lg:col-span-2 bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-poli-dark rounded-xl text-white">
                            <Users class="h-8 w-8" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Contactos</p>
                            <p class="text-3xl font-black text-poli-dark">{{ contactos.total.toLocaleString() }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="p-2 text-gray-400 hover:text-poli-light transition-colors"><Download class="h-6 w-6" /></button>
                    </div>
                </div>

                <!-- Upload Section -->
                <div class="bg-poli-medium/10 p-6 rounded-xl border border-poli-medium/20 shadow-sm">
                    <h4 class="text-sm font-bold text-poli-dark mb-4 flex items-center">
                        <Upload class="h-4 w-4 mr-2" />
                        Actualización Mensual
                    </h4>
                    <form @submit.prevent="uploadFile" class="flex items-center space-x-2">
                        <input 
                            type="file" 
                            @input="form.archivo = $event.target.files[0]" 
                            class="hidden" 
                            id="file-upload" 
                            accept=".csv"
                        />
                        <label 
                            for="file-upload" 
                            class="flex-1 bg-white border border-dashed border-poli-medium/50 rounded-lg px-3 py-2 text-xs text-poli-medium font-bold cursor-pointer hover:bg-white/50 transition-all truncate"
                        >
                            {{ form.archivo ? form.archivo.name : 'Seleccionar CSV...' }}
                        </label>
                        <button 
                            type="submit" 
                            :disabled="!form.archivo || form.processing" 
                            class="bg-poli-dark text-white p-2 rounded-lg hover:bg-poli-medium disabled:opacity-50 transition-all"
                        >
                            <Upload v-if="!form.processing" class="h-4 w-4" />
                            <span v-else class="h-4 w-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="relative w-full sm:w-96">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                    <input 
                        type="text" 
                        placeholder="Buscar por nombre, email o teléfono..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 focus:border-poli-light focus:ring-poli-light shadow-sm"
                    />
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 flex items-center hover:bg-gray-50">
                        <Filter class="h-4 w-4 mr-2" />
                        Segmento
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden text-sm">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 font-bold text-gray-500 uppercase tracking-widest text-xs">Contacto</th>
                            <th class="px-6 py-4 font-bold text-gray-500 uppercase tracking-widest text-xs">Comunicación</th>
                            <th class="px-6 py-4 font-bold text-gray-500 uppercase tracking-widest text-xs">Segmento</th>
                            <th class="px-6 py-4 font-bold text-gray-500 uppercase tracking-widest text-xs text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="contacto in contactos.data" :key="contacto.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-poli-dark italic">
                                {{ contacto.nombre }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center text-gray-600"><Mail class="h-3 w-3 mr-2 opacity-40" /> {{ contacto.email }}</div>
                                    <div class="flex items-center text-gray-500 text-xs"><Phone class="h-3 w-3 mr-2 opacity-40" /> {{ contacto.telefono }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded bg-blue-50 text-poli-medium font-bold text-[10px] uppercase border border-blue-100">
                                    {{ contacto.segmento }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="p-2 text-gray-400 hover:text-poli-medium transition-colors">
                                    <MoreVertical class="h-5 w-5" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Simple Pagination Placeholder -->
            <div class="flex items-center justify-between text-xs text-gray-400 pb-12">
                <p>Mostrando {{ contactos.from }}-{{ contactos.to }} de {{ contactos.total }}</p>
                <div class="flex gap-2">
                    <button class="px-3 py-1 bg-white border border-gray-200 rounded disabled:opacity-50" :disabled="!contactos.prev_page_url" @click="router.get(contactos.prev_page_url)">Anterior</button>
                    <button class="px-3 py-1 bg-white border border-gray-200 rounded disabled:opacity-50" :disabled="!contactos.next_page_url" @click="router.get(contactos.next_page_url)">Siguiente</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
