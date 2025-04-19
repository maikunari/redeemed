<template>
    <Head title="Download Code Redemption" />

    <div class="min-h-screen bg-gradient-to-br from-violet-500 via-blue-500 to-indigo-600 relative overflow-hidden">
        <!-- Particles animation -->
        <div id="particles-js" class="absolute inset-0 z-0"></div>
        
        <!-- Diagonal white overlay -->
        <div class="absolute inset-0 bg-white transform -skew-y-12 origin-top-left translate-y-1/2"></div>
        
        <!-- Noise texture overlay -->
        <div class="absolute inset-0 opacity-50" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIiB4PSIwIiB5PSIwIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iLjc1IiBzdGl0Y2hUaWxlcz0ic3RpdGNoIi8+PC9maWx0ZXI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIzMDAiIGZpbHRlcj0idXJsKCNhKSIgb3BhY2l0eT0iLjA1Ii8+PC9zdmc+');"></div>

        <!-- Header -->
        <div class="relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <!-- App name -->
                    <div class="text-white text-3xl font-black tracking-tight font-['Roboto'] italic drop-shadow-sm">
                        redeem
                    </div>
                    
                    <!-- Site info -->
                    <div class="flex items-center gap-3">
                        <span v-if="siteName" class="text-base text-white font-medium">{{ siteName }}</span>
                        <div v-if="logo" class="h-[45px] w-[45px] rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center p-1">
                            <img class="h-full w-full object-contain rounded-full" :src="logo" alt="Site Logo" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 flex-1 flex flex-col items-center justify-center min-h-[calc(100vh-4rem)] py-6 sm:py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Form Card -->
                <div class="bg-white rounded-xl shadow-xl p-6 sm:p-8 space-y-6 sm:space-y-8">
                    <div class="text-center">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                            Enter Your Download Code
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Type or paste your code below
                        </p>
                    </div>

                    <!-- Success state -->
                    <div v-if="downloadStarted" class="space-y-6">
                        <div class="p-6 bg-green-50 rounded-lg text-center">
                            <svg class="mx-auto h-12 w-12 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-green-800">Thank you!</h3>
                            <p class="mt-2 text-sm text-green-600">
                                Your download should begin automatically.
                            </p>
                            <button 
                                v-if="!showTroubleshooting"
                                @click="showTroubleshooting = true"
                                class="mt-4 text-sm text-indigo-600 hover:text-indigo-800 underline"
                            >
                                Click here if your download doesn't start
                            </button>
                            <div v-if="showTroubleshooting" class="mt-4 text-sm text-gray-600 space-y-2">
                                <ul class="list-disc text-left pl-4 space-y-1">
                                    <li>Check your browser's download settings</li>
                                    <li>Ensure pop-ups aren't blocked for this site</li>
                                    <li>Try the <a :href="route('codes.redeem') + '?code=' + form.code" class="underline hover:text-gray-900">backup download link</a> (within 5 minutes)</li>
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
                            
                            <a
                                href="/"
                                class="text-sm text-gray-600 hover:text-gray-900"
                            >
                                Return to Home
                            </a>
                        </div>
                    </div>

                    <!-- Code input form -->
                    <form v-else @submit.prevent="submit" class="space-y-8">
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
                        </div>

                        <div>
                            <button
                                type="submit"
                                :disabled="!isCodeComplete || form.processing"
                                class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-lg shadow-sm text-base sm:text-sm font-medium text-white bg-indigo-600 transition-all duration-150"
                                :class="{
                                    'opacity-75 cursor-not-allowed': !isCodeComplete || form.processing,
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

                    <div class="relative mt-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">
                                Need help? Contact support
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

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

const form = useForm({
    code: '',
});

const isCodeComplete = computed(() => {
    return codeDigits.value.every(digit => digit !== '' && /[2-9]/.test(digit));
});

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

    // Load particles.js dynamically
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js';
    script.onload = () => {
        window.particlesJS('particles-js', {
            particles: {
                number: {
                    value: 40,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: ['#ffffff', '#e0e7ff', '#c7d2fe']
                },
                shape: {
                    type: ['circle', 'triangle', 'polygon'],
                    polygon: {
                        nb_sides: 6
                    }
                },
                opacity: {
                    value: 0.2,
                    random: true,
                    anim: {
                        enable: true,
                        speed: 0.5,
                        opacity_min: 0.1,
                        sync: false
                    }
                },
                size: {
                    value: 4,
                    random: true,
                    anim: {
                        enable: true,
                        speed: 1,
                        size_min: 2,
                        sync: false
                    }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#e0e7ff',
                    opacity: 0.15,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 0.8,
                    direction: 'none',
                    random: true,
                    straight: false,
                    out_mode: 'out',
                    bounce: false,
                    attract: {
                        enable: true,
                        rotateX: 600,
                        rotateY: 1200
                    }
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: {
                        enable: true,
                        mode: 'bubble'
                    },
                    resize: true
                },
                modes: {
                    bubble: {
                        distance: 200,
                        size: 6,
                        duration: 0.8,
                        opacity: 0.3,
                        speed: 2
                    }
                }
            },
            retina_detect: true
        });
    };
    document.body.appendChild(script);
});

const handleInput = (index) => {
    let digit = codeDigits.value[index];
    
    // Ensure single character and is numeric 2-9
    if (digit.length >= 1) {
        // Filter out 0, 1, and non-numeric characters
        digit = digit.replace(/[^2-9]/g, '');
        codeDigits.value[index] = digit;
        
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
};

const submit = async () => {
    form.code = codeDigits.value.join('');
    
    try {
        // First, validate the code
        const response = await fetch(route('codes.redeem') + '?validate=true&code=' + form.code);
        const data = await response.json();

        if (!response.ok || data.errors) {
            showError.value = true;
            errorMessage.value = data.errors?.code || 'An error occurred while processing your request.';
            showSuccess.value = false;
            resetForm();
            setTimeout(() => {
                showError.value = false;
            }, 5000);
            return;
        }

        // If validation passed, trigger download in a new window/tab
        const downloadUrl = route('codes.redeem') + '?code=' + form.code;
        const downloadFrame = document.createElement('iframe');
        downloadFrame.style.display = 'none';
        document.body.appendChild(downloadFrame);
        downloadFrame.src = downloadUrl;

        // Show success state
        showSuccess.value = true;
        showError.value = false;
        downloadStarted.value = true;

        // Clean up the iframe after a delay
        setTimeout(() => {
            document.body.removeChild(downloadFrame);
        }, 5000);

    } catch (error) {
        showError.value = true;
        errorMessage.value = 'An error occurred while processing your request.';
        showSuccess.value = false;
        resetForm();
        setTimeout(() => {
            showError.value = false;
        }, 5000);
    }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@1,900&display=swap');

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Add subtle gradient animation */
.bg-gradient-to-br {
    background-size: 400% 400%;
    animation: gradient 30s ease infinite;
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

#particles-js {
    pointer-events: none;
}

#particles-js canvas {
    opacity: 0.2;
}
</style>

<!-- Add hidden iframe for download -->
<iframe name="download_frame" class="hidden"></iframe> 