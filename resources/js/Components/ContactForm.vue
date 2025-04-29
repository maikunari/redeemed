<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Download Code (if applicable)</label>
            <div class="flex gap-1.5 sm:gap-3 justify-center">
                <template v-for="index in 6" :key="`contact-digit-${index}`">
                    <input
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        pattern="[2-9]"
                        v-model="codeDigits[index - 1]"
                        :ref="el => { if (el) codeInputs[index - 1] = el }"
                        @input="handleDigitInput(index - 1)"
                        @keydown="handleDigitKeydown($event, index - 1)"
                        @paste="handleDigitPaste"
                        class="w-10 h-10 sm:w-12 sm:h-12 text-center text-lg sm:text-2xl font-semibold border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </template>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :class="{'border-red-500': form.errors.name}" />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :class="{'border-red-500': form.errors.email}" />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Message</label>
            <textarea v-model="form.message" rows="4" class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :class="{'border-red-500': form.errors.message}"></textarea>
            <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</p>
        </div>
        <div class="flex justify-end">
            <button type="submit" :disabled="form.processing" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :class="{'opacity-75 cursor-not-allowed': form.processing}">
                {{ form.processing ? 'Sending...' : 'Send Message' }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const emit = defineEmits(['sent']);

const form = useForm({
    name: '',
    email: '',
    message: ''
});

const codeDigits = ref(Array(6).fill(''));
const codeInputs = ref([]);

const page = usePage();
const subtitle = computed(() => page.props.settings?.contact_subtitle || "Fill out the form below and we'll get back to you soon");
const thankyou = computed(() => page.props.settings?.contact_thankyou || 'Your message has been sent successfully.');

function handleDigitInput(index) {
    let digit = codeDigits.value[index];
    // Only allow digits 2-9
    digit = digit.replace(/[^2-9]/g, '');
    codeDigits.value[index] = digit;

    // Auto advance
    if (digit && index < 5 && codeInputs.value[index + 1]) {
        codeInputs.value[index + 1].focus();
    }
}

function handleDigitKeydown(event, index) {
    if (event.key === 'Backspace' && !codeDigits.value[index]) {
        if (index > 0 && codeInputs.value[index - 1]) {
            codeInputs.value[index - 1].focus();
        }
    } else if (event.key === 'ArrowLeft') {
        if (index > 0 && codeInputs.value[index - 1]) codeInputs.value[index - 1].focus();
    } else if (event.key === 'ArrowRight') {
        if (index < 5 && codeInputs.value[index + 1]) codeInputs.value[index + 1].focus();
    }
}

function handleDigitPaste(e) {
    e.preventDefault();
    const text = e.clipboardData.getData('text').replace(/[^2-9]/g, '').slice(0, 6);
    text.split('').forEach((d, i) => {
        codeDigits.value[i] = d;
    });
}

// Include code in form payload
form.defaults({ download_code: '' });

watch(codeDigits, (val) => {
    form.download_code = val.join('');
});

function submit() {
    form.post(route('support.send'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('sent');
        }
    });
}
</script>

<style scoped>
/* optional small tweaks */
</style>