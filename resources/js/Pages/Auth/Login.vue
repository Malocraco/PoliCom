<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, Lock, LogIn, ChevronRight, BadgeCheck, Shield } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Acceso Institucional" />

    <div class="min-h-screen flex bg-white relative overflow-hidden">
        
        <!-- Left Side: Brand/Graphic -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-poli-dark overflow-hidden flex-col justify-center items-center">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('/images/policom-login-bg.png');"></div>
            <!-- Decorative gradient -->
            <div class="absolute inset-0 bg-[#00204a]/50 bg-gradient-to-br from-poli-dark/40 to-transparent z-0"></div>
            
            <div class="relative z-10 p-12 flex flex-col items-center text-center">
                <div class="bg-white p-6 rounded-3xl shadow-2xl mb-8 transform hover:scale-105 transition-transform duration-500">
                    <span class="text-6xl font-black text-poli-dark tracking-tighter">POLI</span>
                </div>
                <h1 class="text-4xl font-extrabold text-white mb-4 tracking-tight">POLICOM</h1>
                <p class="text-blue-100 text-lg font-medium opacity-90 max-w-md">Sistema Integrado de Comunicaciones Corporativas. Gestión, envío y trazabilidad académica.</p>
                
                <!-- Trust Badges -->
                <div class="flex gap-4 mt-10">
                    <div class="flex items-center gap-2.5 bg-[#1a365d]/80 backdrop-blur-sm px-4 py-2.5 rounded-lg border border-white/10 shadow-lg">
                        <BadgeCheck class="w-5 h-5 text-orange-400" />
                        <span class="text-white text-sm font-bold tracking-wide">Portal Oficial</span>
                    </div>
                    <div class="flex items-center gap-2.5 bg-[#1a365d]/80 backdrop-blur-sm px-4 py-2.5 rounded-lg border border-white/10 shadow-lg">
                        <Shield class="w-5 h-5 text-orange-400" />
                        <span class="text-white text-sm font-bold tracking-wide">Acceso Seguro</span>
                    </div>
                </div>
            </div>
            
            <!-- Abstract decorative elements -->
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob" style="animation-delay: 2s;"></div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 bg-gray-50/50">
            <div class="w-full max-w-md">
                <!-- Mobile only Header -->
                <div class="lg:hidden text-center mb-10">
                    <div class="inline-block bg-poli-dark p-4 rounded-2xl shadow-lg mb-4">
                        <span class="text-3xl font-black text-white tracking-tighter">POLI</span>
                    </div>
                    <h2 class="text-2xl font-black text-poli-dark uppercase tracking-tight">POLICOM</h2>
                </div>

                <div class="mb-10 text-left">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Ingresar a PoliCom</h2>
                    <p class="text-sm font-medium text-gray-500 mt-2">Por favor, ingresa tus credenciales institucionales.</p>
                </div>

                <div v-if="status" class="mb-6 p-4 rounded-xl bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold shadow-sm">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wider">Correo Electrónico</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-poli-dark text-gray-400">
                                <Mail class="h-5 w-5" />
                            </div>
                            <input 
                                v-model="form.email"
                                type="email" 
                                class="block w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-poli-dark/20 focus:border-poli-dark text-gray-900 font-medium transition-all shadow-sm hover:border-gray-300"
                                placeholder="usuario@poligran.edu.co"
                                required
                                autofocus
                            />
                        </div>
                        <p v-if="form.errors.email" class="text-xs text-red-500 mt-1.5 font-bold flex items-center">
                            <span class="inline-block w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wider">Contraseña</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-poli-dark text-gray-400">
                                <Lock class="h-5 w-5" />
                            </div>
                            <input 
                                v-model="form.password"
                                type="password" 
                                class="block w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-poli-dark/20 focus:border-poli-dark text-gray-900 font-medium transition-all shadow-sm hover:border-gray-300"
                                placeholder="••••••••"
                                required
                            />
                        </div>
                        <p v-if="form.errors.password" class="text-xs text-red-500 mt-1.5 font-bold flex items-center">
                            <span class="inline-block w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.remember" class="w-4 h-4 rounded text-poli-dark bg-white border-gray-300 focus:ring-poli-dark transition-all" />
                            <span class="ms-2 text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors">Recordarme</span>
                        </label>

                        <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm font-semibold text-gray-500 hover:text-poli-dark transition-colors">
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>

                    <div class="pt-4">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full flex items-center justify-center px-6 py-4 bg-poli-dark hover:bg-[#001530] text-white rounded-lg font-bold uppercase tracking-wide text-sm transition-all shadow-lg shadow-poli-dark/30 group disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <span v-if="!form.processing" class="flex items-center">
                                Iniciar Sesión
                                <LogIn class="h-5 w-5 ml-2 group-hover:translate-x-1 transition-transform" />
                            </span>
                            <span v-else class="h-5 w-5 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
                        </button>
                    </div>
                </form>

                <div class="mt-12 text-center text-xs font-semibold text-gray-400 uppercase tracking-widest">
                    Politécnico Grancolombiano © 2026
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
</style>
