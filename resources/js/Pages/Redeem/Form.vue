<template>
    <Head title="Download Code Redemption" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo area - replace src with your actual logo -->
            <img class="mx-auto h-24 w-auto" src="/logo.png" alt="Your Logo" />
            
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Enter Your Download Code
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Type or paste your code below
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl shadow-indigo-50/20 sm:rounded-xl sm:px-10 relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-purple-50/50 opacity-50"></div>
                
                <!-- Success message -->
                <div v-if="showSuccess" class="mb-6 p-4 bg-green-50 rounded-lg relative">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                Your download will begin automatically
                            </p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="relative space-y-6">
                    <div>
                        <label class="sr-only">Download Code</label>
                        <div class="flex justify-center gap-2 items-center">
                            <!-- First 4 digits -->
                            <div class="flex gap-2">
                                <template v-for="index in 4" :key="`first-${index}`">
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
                                        class="w-12 h-14 text-center text-2xl font-semibold border-2 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'border-red-300 text-red-900': form.errors.code,
                                            'border-gray-300': !form.errors.code
                                        }"
                                        :disabled="form.processing"
                                    >
                                </template>
                            </div>

                            <!-- Dash separator -->
                            <div class="w-6 flex items-center justify-center">
                                <div class="h-0.5 w-4 bg-gray-400"></div>
                            </div>

                            <!-- Last 4 digits -->
                            <div class="flex gap-2">
                                <template v-for="index in 4" :key="`second-${index}`">
                                    <input
                                        type="text"
                                        inputmode="numeric"
                                        maxlength="1"
                                        pattern="[2-9]"
                                        v-model="codeDigits[index + 3]"
                                        :ref="el => { if (el) codeInputs[index + 3] = el }"
                                        @input="handleInput(index + 3)"
                                        @keydown="handleKeydown($event, index + 3)"
                                        @paste="handlePaste"
                                        class="w-12 h-14 text-center text-2xl font-semibold border-2 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{
                                            'border-red-300 text-red-900': form.errors.code,
                                            'border-gray-300': !form.errors.code
                                        }"
                                        :disabled="form.processing"
                                    >
                                </template>
                            </div>
                        </div>
                        <p v-if="form.errors.code" class="mt-2 text-sm text-center text-red-600">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="!isCodeComplete || form.processing"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 transition-all duration-150"
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

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
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
});

const showSuccess = ref(false);
const codeDigits = ref(Array(8).fill(''));
const codeInputs = ref([]);

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
        const digits = props.code.replace('-', '').split('');
        digits.forEach((digit, index) => {
            if (index < 8 && /[2-9]/.test(digit)) codeDigits.value[index] = digit;
        });
    }
});

const handleInput = (index) => {
    let digit = codeDigits.value[index];
    
    // Ensure single character and is numeric 2-9
    if (digit.length >= 1) {
        // Filter out 0, 1, and non-numeric characters
        digit = digit.replace(/[^2-9]/g, '');
        codeDigits.value[index] = digit;
        
        if (digit && index < 7 && codeInputs.value[index + 1]) {
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
        if (index < 7 && codeInputs.value[index + 1]) {
            codeInputs.value[index + 1].focus();
        }
    }
};

const handlePaste = (event) => {
    event.preventDefault();
    const pastedText = event.clipboardData.getData('text')
        .replace(/[^2-9]/g, '') // Only keep digits 2-9
        .slice(0, 8); // Take only first 8 digits
    
    const digits = pastedText.split('');
    digits.forEach((digit, index) => {
        if (index < 8) codeDigits.value[index] = digit;
    });
    
    // Focus the next empty input or the last input
    for (let i = 0; i < 8; i++) {
        if (!codeDigits.value[i]) {
            codeInputs.value[i].focus();
            break;
        }
        if (i === 7) {
            codeInputs.value[i].focus();
        }
    }
};

const submit = () => {
    // Format code as XXXX-XXXX
    const formattedCode = `${codeDigits.value.slice(0, 4).join('')}-${codeDigits.value.slice(4).join('')}`;
    form.code = formattedCode;
    
    form.post(route('codes.redeem'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess.value = true;
            setTimeout(() => {
                showSuccess.value = false;
            }, 5000);
        },
        onError: () => {
            codeDigits.value = Array(8).fill('');
            if (codeInputs.value[0]) {
                codeInputs.value[0].focus();
            }
        },
    });
};
</script>

<style scoped>
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

/* Hide number input spinners */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type=number] {
    -moz-appearance: textfield;
}
</style> 