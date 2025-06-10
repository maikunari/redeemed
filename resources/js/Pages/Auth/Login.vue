<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';

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

const page = usePage();
const logo = computed(() => page.props.logo || '');
const siteName = computed(() => page.props.siteName || 'redeem');

const fluidAnimation = ref(null);

onMounted(() => {
    // Initialize fluid animation
    const canvas = document.getElementById('fluid-canvas');
    const gl = canvas.getContext('webgl2');
    
    // Resize handler
    const handleResize = () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        gl.viewport(0, 0, canvas.width, canvas.height);
    };
    
    window.addEventListener('resize', handleResize);
    handleResize();

    // WebGL initialization
    const vertexShader = gl.createShader(gl.VERTEX_SHADER);
    gl.shaderSource(vertexShader, `#version 300 es
        in vec2 position;
        void main() {
            gl_Position = vec4(position, 0.0, 1.0);
        }
    `);
    gl.compileShader(vertexShader);

    const fragmentShader = gl.createShader(gl.FRAGMENT_SHADER);
    gl.shaderSource(fragmentShader, `#version 300 es
        precision highp float;
        uniform float time;
        uniform vec2 resolution;
        out vec4 fragColor;

        #define PI 3.14159265359

        float rand(vec2 n) { 
            return fract(sin(dot(n, vec2(12.9898, 4.1414))) * 43758.5453);
        }

        float noise(vec2 p) {
            vec2 ip = floor(p);
            vec2 u = fract(p);
            u = u*u*(3.0-2.0*u);
            
            float res = mix(
                mix(rand(ip), rand(ip+vec2(1.0,0.0)), u.x),
                mix(rand(ip+vec2(0.0,1.0)), rand(ip+vec2(1.0,1.0)), u.x), u.y);
            return res*res;
        }

        void main() {
            vec2 uv = gl_FragCoord.xy/resolution.xy;
            vec2 pos = uv * 2.0 - 1.0;
            pos.x *= resolution.x/resolution.y;
            
            float t = time * 0.2;
            
            // Create base colors
            vec3 color1 = vec3(0.5, 0.0, 1.0); // Purple
            vec3 color2 = vec3(0.0, 0.5, 1.0); // Blue
            
            // Generate fluid motion
            float n = noise(pos * 2.0 + t);
            n += 0.5 * noise(pos * 4.0 - t);
            n += 0.25 * noise(pos * 8.0 + t);
            
            // Create swirling effect
            float angle = atan(pos.y, pos.x);
            float dist = length(pos);
            float pattern = sin(dist * 5.0 - t * 2.0) * 0.5 + 0.5;
            
            // Combine effects
            vec3 color = mix(color1, color2, n * pattern);
            color += 0.1 * vec3(1.0) * (1.0 - dist);
            
            // Add brightness variations
            color *= 0.8 + 0.2 * noise(pos * 10.0 + t);
            
            fragColor = vec4(color, 1.0);
        }
    `);
    gl.compileShader(fragmentShader);

    const program = gl.createProgram();
    gl.attachShader(program, vertexShader);
    gl.attachShader(program, fragmentShader);
    gl.linkProgram(program);
    gl.useProgram(program);

    const buffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1,-1, 1,-1, -1,1, 1,1]), gl.STATIC_DRAW);

    const position = gl.getAttribLocation(program, 'position');
    gl.enableVertexAttribArray(position);
    gl.vertexAttribPointer(position, 2, gl.FLOAT, false, 0, 0);

    const timeLocation = gl.getUniformLocation(program, 'time');
    const resolutionLocation = gl.getUniformLocation(program, 'resolution');

    let startTime = Date.now();
    
    const animate = () => {
        const time = (Date.now() - startTime) * 0.001;
        gl.uniform1f(timeLocation, time);
        gl.uniform2f(resolutionLocation, canvas.width, canvas.height);
        gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
        fluidAnimation.value = requestAnimationFrame(animate);
    };
    
    animate();
});

onBeforeUnmount(() => {
    if (fluidAnimation.value) {
        cancelAnimationFrame(fluidAnimation.value);
    }
});
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="min-h-screen bg-[#000000] relative overflow-hidden">
            <!-- Fluid animation -->
            <canvas id="fluid-canvas" class="absolute inset-0 z-0"></canvas>
            
            <!-- Diagonal white overlay -->
            <div class="absolute inset-0 bg-white/95 transform -skew-y-12 origin-top-left translate-y-1/2"></div>
            
            <!-- Noise texture overlay -->
            <div class="absolute inset-0 opacity-50" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIiB4PSIwIiB5PSIwIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iLjc1IiBzdGl0Y2hUaWxlcz0ic3RpdGNoIi8+PC9maWx0ZXI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIzMDAiIGZpbHRlcj0idXJsKCNhKSIgb3BhY2l0eT0iLjA1Ii8+PC9zdmc+');"></div>

            <!-- Header -->
            <div class="relative z-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex justify-between items-center">
                        <!-- App name with logo -->
                        <div class="flex items-center gap-3">
                            <div v-if="logo" class="h-[55px] w-[55px] rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center p-1.5">
                                <img class="h-full w-full object-contain rounded-full" :src="logo" alt="Site Logo" />
                            </div>
                            <div class="text-white text-3xl font-black tracking-tight font-['Roboto'] italic drop-shadow-sm">
                                {{ siteName }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="relative z-10 flex-1 flex flex-col items-center justify-center min-h-[calc(100vh-4rem)] py-6 sm:py-12 px-4 sm:px-6 lg:px-8">
                <div class="w-full max-w-md">
                    <!-- Form Card -->
                    <div class="bg-white rounded-xl shadow-xl p-6 sm:p-12 space-y-6 sm:space-y-8">
                        <div class="text-center">
                            <h2 class="text-[1.7rem] font-bold text-gray-900">
                                Welcome Back
                            </h2>
                            <p class="mt-2 text-sm text-gray-600">
                                Sign in to your admin account
                            </p>
                        </div>

                        <div v-if="status" class="p-4 bg-green-50 rounded-lg text-center">
                            <p class="text-sm font-medium text-green-600">{{ status }}</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="email" value="Email" class="text-gray-700 font-medium" />

                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />

                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="password" value="Password" class="text-gray-700 font-medium" />

                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                />

                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <Checkbox name="remember" v-model:checked="form.remember" />
                                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                </label>

                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-sm text-indigo-600 hover:text-indigo-800 underline"
                                >
                                    Forgot password?
                                </Link>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-lg shadow-sm text-base sm:text-sm font-medium text-white bg-indigo-600 transition-all duration-150"
                                    :class="{
                                        'opacity-75 cursor-not-allowed': form.processing,
                                        'hover:bg-indigo-700': !form.processing
                                    }"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Signing in...' : 'Sign In' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
#fluid-canvas {
    width: 100%;
    height: 100%;
    opacity: 0.9;
}
</style>
