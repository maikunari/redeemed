<template>
    <Head title="Download Code Redemption" />

    <div class="min-h-screen bg-white flex flex-col">
        <!-- Header -->
        <div class="border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
                <div class="flex flex-col items-center gap-2 sm:gap-3">
                    <img class="h-12 w-12 sm:h-20 sm:w-20 object-contain" :src="logo || '/logo.png'" alt="Site Logo" />
                    <span v-if="siteName" class="text-lg sm:text-2xl font-semibold text-gray-900">{{ siteName }}</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col items-center justify-center min-h-[calc(100vh-4rem)] sm:min-h-[calc(100vh-5rem)] py-6 sm:py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-6 sm:space-y-8">
                <div class="text-center">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Enter Your Download Code
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Type or paste your code below
                    </p>
                </div>

                <!-- Success message -->
                <div v-if="showSuccess" class="p-4 bg-green-50 rounded-lg relative">
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

                <form @submit.prevent="submit" class="space-y-8">
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
const codeDigits = ref(Array(6).fill(''));
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
        const digits = props.code.split('');
        digits.forEach((digit, index) => {
            if (index < 6 && /[2-9]/.test(digit)) codeDigits.value[index] = digit;
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

const submit = () => {
    form.code = codeDigits.value.join('');
    
    form.post(route('codes.redeem'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess.value = true;
            setTimeout(() => {
                showSuccess.value = false;
            }, 5000);
        },
        onError: () => {
            codeDigits.value = Array(6).fill('');
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