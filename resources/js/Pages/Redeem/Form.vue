<template>
    <GuestLayout>
        <Head title="Download Code Redemption" />

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
                                {{ siteName || 'redeem' }}
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
                                {{ showContactForm ? 'Contact Support' : 'Enter Your Download Code' }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-600">
                                {{ showContactForm ? contactSubtitle : 'Type or paste your code below' }}
                            </p>
                        </div>

                        <!-- Success state -->
                        <div v-if="downloadStarted && !showContactForm" class="space-y-6">
                            <div class="p-6 bg-green-50 rounded-lg text-center">
                                <template v-if="downloadProgress < 100">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 relative">
                                            <svg class="animate-spin w-16 h-16 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <!-- Removed percentage display -->
                                            </div>
                                        </div>
                                        <p class="mt-3 text-sm text-gray-600">Preparing your download. This may take 2 - 3 minutes for large files. Your download will start automatically.</p>
                                    </div>
                                </template>
                                <template v-else>
                                    <svg class="mx-auto h-12 w-12 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-green-800">Thank you!</h3>
                                    <p class="mt-2 text-sm text-green-600">
                                        Your download has completed successfully.
                                    </p>
                                </template>
                                
                                <button 
                                    v-if="!showTroubleshooting && downloadProgress === 100"
                                    @click="showTroubleshooting = true"
                                    class="mt-4 text-sm text-indigo-600 hover:text-indigo-800 underline"
                                >
                                    Having trouble with your download?
                                </button>
                                <div v-if="showTroubleshooting" class="mt-4 text-sm text-gray-600 space-y-2">
                                    <ul class="list-disc text-left pl-4 space-y-1">
                                        <li>Check your browser's download settings</li>
                                        <li>Ensure you have enough storage space</li>
                                        <li>
                                            <form @submit.prevent="handleBackupDownload" class="inline">
                                                <button type="submit" class="underline hover:text-gray-900">Try backup download method</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex flex-col items-center gap-4">
                                <button
                                    @click="resetForm"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Enter Another Code
                                </button>
                            </div>
                        </div>

                        <!-- Code input form and Contact Form -->
                        <div class="relative">
                            <div v-show="!showContactForm">
                                <form v-if="!downloadStarted" @submit.prevent="submit" class="space-y-8">
                                    <div>
                                        <label class="sr-only">Download Code</label>
                                        <div class="flex justify-center gap-1.5 sm:gap-3 items-center">
                                            <div class="flex gap-1.5 sm:gap-3">
                                                <template v-for="index in 6" :key="`digit-${index}`">
                                                    <input
                                                        type="text"
                                                        inputmode="numeric"
                                                        maxlength="1"
                                                        pattern="[2-9]"
                                                        v-model="codeDigits[index - 1]"
                                                        :ref="el => { if (el) codeInputs[index - 1] = el }"
                                                        @input="handleInput(index - 1)"
                                                        @keydown="handleKeydown($event, index - 1)"
                                                        @paste="handlePaste"
                                                        class="w-[2.75rem] h-[2.75rem] sm:w-12 sm:h-14 text-center text-lg sm:text-2xl font-semibold border-2 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        :class="{
                                                            'border-red-300 text-red-900': form.errors.code,
                                                            'border-gray-300': !form.errors.code
                                                        }"
                                                        :disabled="form.processing"
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <p v-if="form.errors.code" class="mt-3 text-sm text-center text-red-600">
                                            {{ form.errors.code }}
                                        </p>
                                        <div v-if="form.errors.code" class="mt-3 flex justify-center gap-4 text-sm">
                                            <button type="button" @click="resetForm" class="text-indigo-600 hover:text-indigo-800 underline">
                                                Try again
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <button
                                            type="submit"
                                            :disabled="!isCodeComplete || form.processing || form.errors.code"
                                            class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-lg shadow-sm text-base sm:text-sm font-medium text-white bg-indigo-600 transition-all duration-150"
                                            :class="{
                                                'opacity-75 cursor-not-allowed': !isCodeComplete || form.processing || form.errors.code,
                                                'hover:bg-indigo-700': isCodeComplete && !form.processing
                                            }"
                                        >
                                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ form.processing ? 'Processing...' : 'Download File' }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div v-show="showContactForm">
                                <template v-if="!contactSubmitted">
                                    <ContactForm @sent="handleContactSent" />
                                </template>
                                <template v-else>
                                    <div class="animate-fade-in text-center space-y-4 py-6">
                                        <svg class="mx-auto h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-800">Thank you!</h3>
                                        <p class="text-sm text-gray-600">{{ contactThankyou }}</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="relative mt-8">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <button
                                    type="button"
                                    @click="showContactForm = !showContactForm"
                                    class="px-4 bg-white text-gray-500 hover:text-gray-700"
                                >
                                    {{ showContactForm ? 'Return to Home' : 'Need help? Contact support' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
const page = usePage();
const siteName = computed(() => page.props.settings?.site_name || 'redeem');
const logo     = computed(() => page.props.settings?.logo      || '');
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ContactForm from '@/Components/ContactForm.vue';

const props = defineProps({
    code: {
        type: String,
        default: '',
    },
    siteName: {
        type: String,
        default: '',
    },
    logo: {
        type: String,
        default: '',
    },
});

const showSuccess = ref(false);
const showError = ref(false);
const errorMessage = ref('');
const codeDigits = ref(Array(6).fill(''));
const codeInputs = ref([]);
const downloadStarted = ref(false);
const showTroubleshooting = ref(false);
const downloadProgress = ref(0);
const isLoading = ref(false);
const error = ref(null);
const downloadUrl = ref(null);
const showContactForm = ref(false);
const contactSubmitted = ref(false);

const form = useForm({
    code: '',
    validate: false
});

const isCodeComplete = computed(() => {
    return codeDigits.value.every(digit => digit !== '' && /[2-9]/.test(digit));
});

const fluidAnimation = ref(null);

const contactSubtitle = computed(() => page.props.settings?.contact_subtitle || "Fill out the form below and we'll get back to you soon");
const contactThankyou = computed(() => page.props.settings?.contact_thankyou || 'Your message has been sent successfully.');

onMounted(() => {
    // Focus first input on mount
    if (codeInputs.value[0]) {
        codeInputs.value[0].focus();
    }
    
    // If code is provided, fill the digits
    if (props.code) {
        const digits = props.code.split('');
        digits.forEach((digit, index) => {
            if (index < 6 && /[2-9]/.test(digit)) codeDigits.value[index] = digit;
        });
    }

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

const handleInput = (index) => {
    let digit = codeDigits.value[index];
    
    // Ensure single character and is numeric 2-9
    if (digit.length >= 1) {
        // Filter out 0, 1, and non-numeric characters
        digit = digit.replace(/[^2-9]/g, '');
        codeDigits.value[index] = digit;
        
        if (form.errors.code) {
            form.clearErrors('code');
        }
        if (digit && index < 5 && codeInputs.value[index + 1]) {
            codeInputs.value[index + 1].focus();
        }
    }
};

const handleKeydown = (event, index) => {
    // Handle backspace
    if (event.key === 'Backspace' && !codeDigits.value[index]) {
        if (index > 0 && codeInputs.value[index - 1]) {
            codeInputs.value[index - 1].focus();
        }
    }
    // Handle left arrow
    else if (event.key === 'ArrowLeft') {
        if (index > 0 && codeInputs.value[index - 1]) {
            codeInputs.value[index - 1].focus();
        }
    }
    // Handle right arrow
    else if (event.key === 'ArrowRight') {
        if (index < 5 && codeInputs.value[index + 1]) {
            codeInputs.value[index + 1].focus();
        }
    }
};

const handlePaste = (event) => {
    event.preventDefault();
    const pastedText = event.clipboardData.getData('text')
        .replace(/[^2-9]/g, '') // Only keep digits 2-9
        .slice(0, 6); // Take only first 6 digits
    
    const digits = pastedText.split('');
    digits.forEach((digit, index) => {
        if (index < 6) codeDigits.value[index] = digit;
    });
    
    // Focus the next empty input or the last input
    for (let i = 0; i < 6; i++) {
        if (!codeDigits.value[i]) {
            codeInputs.value[i].focus();
            break;
        }
        if (i === 5) {
            codeInputs.value[i].focus();
        }
    }
};

const resetForm = () => {
    codeDigits.value = Array(6).fill('');
    if (codeInputs.value[0]) {
        codeInputs.value[0].focus();
    }
    downloadStarted.value = false;
    showSuccess.value = false;
    showError.value = false;
    showTroubleshooting.value = false;
    form.clearErrors('code');
    form.code = '';
    form.validate = false;
};

const submit = () => {
    // Reset states
    downloadStarted.value = true;
    downloadProgress.value = 0;
    form.code = codeDigits.value.join('');
    
    // Generate a unique request ID for this submission
    const requestId = Date.now().toString(36) + Math.random().toString(36).substr(2);
    
    // Use axios for the download request directly, handling both validation and download in one request
    axios.post(route('codes.redeem'), 
        { code: form.code, requestId: requestId },
        { 
            responseType: 'blob',
            headers: {
                'X-XSRF-TOKEN': usePage().props.csrf_token,
            }
        }
    ).then(response => {
        // Get filename from the Content-Disposition header
        const contentDisposition = response.headers['content-disposition'];
        const filename = contentDisposition
            ? contentDisposition.split('filename=')[1].replace(/["']/g, '')
            : 'download';

        // Create a blob URL and trigger download
        const blob = new Blob([response.data], { type: response.headers['content-type'] });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(link);
        downloadProgress.value = 100;
    }).catch(error => {
        downloadStarted.value = false;
        if (error.response && error.response.data) {
            // Log the raw error response for debugging
            console.error('Raw error response:', error.response);
            // Try to parse the error message from the response
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const json = JSON.parse(e.target.result);
                    console.error('Parsed error JSON:', json);
                    let errorMessage = json.errors?.code || json.message || 'An error occurred during redemption.';
                    // Ensure errorMessage is a string before calling replace
                    if (typeof errorMessage !== 'string') {
                        errorMessage = String(errorMessage);
                    }
                    // Remove square brackets from the error message
                    errorMessage = errorMessage.replace(/\[|\]/g, '');
                    form.errors.code = errorMessage;
                } catch (e) {
                    console.error('Failed to parse error JSON:', e);
                    form.errors.code = 'An error occurred during redemption.';
                }
            };
            reader.readAsText(error.response.data);
        } else {
            console.error('No error response data:', error);
            form.errors.code = 'Failed to download file. Please try again.';
        }
    });
};

const handleBackupDownload = () => {
    form.validate = false;
    
    // Use axios for the backup download request
    axios.post(route('codes.redeem'),
        { code: form.code },
        { 
            responseType: 'blob',
            headers: {
                'X-XSRF-TOKEN': usePage().props.csrf_token,
            }
        }
    ).then(response => {
        // Create a blob URL and open in new tab
        const blob = new Blob([response.data], { type: response.headers['content-type'] });
        const url = window.URL.createObjectURL(blob);
        window.open(url, '_blank');
        
        // Cleanup
        setTimeout(() => {
            window.URL.revokeObjectURL(url);
        }, 1000);
    }).catch(error => {
        error.value = 'Failed to initiate backup download';
    });
};

const handleContactSent = () => {
    contactSubmitted.value = true;
};
</script>

<style scoped>
/* Add subtle gradient animation */
.bg-gradient-to-br {
    background-size: 400% 400%;
    animation: gradient 45s ease infinite;
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Hide number input spinners */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type=number] {
    -moz-appearance: textfield;
}

#fluid-canvas {
    width: 100%;
    height: 100%;
    opacity: 0.9;
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- Add hidden iframe for download -->
<iframe name="download_frame" class="hidden"></iframe> 