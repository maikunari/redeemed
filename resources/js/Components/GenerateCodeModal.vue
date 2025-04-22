<template>
    <Modal :show="show" @close="closeModal" maxWidth="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Generate Download Code
            </h2>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="number_of_codes" class="block text-sm font-medium text-gray-700">
                        Number of Codes to Generate
                    </label>
                    <input
                        id="number_of_codes"
                        type="number"
                        v-model="form.number_of_codes"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required
                        min="1"
                    >
                    <p v-if="form.errors.number_of_codes" class="mt-2 text-sm text-red-600">
                        {{ form.errors.number_of_codes }}
                    </p>
                </div>

                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700">
                        Usage Limit (per code)
                    </label>
                    <input
                        id="usage_limit"
                        type="number"
                        v-model="form.usage_limit"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required
                        min="1"
                    >
                    <p v-if="form.errors.usage_limit" class="mt-2 text-sm text-red-600">
                        {{ form.errors.usage_limit }}
                    </p>
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700">
                        Expiration Date (Optional)
                    </label>
                    <input
                        id="expires_at"
                        type="datetime-local"
                        v-model="form.expires_at"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        :min="minDateTime"
                    >
                    <p v-if="form.errors.expires_at" class="mt-2 text-sm text-red-600">
                        {{ form.errors.expires_at }}
                    </p>
                </div>

                <div class="flex justify-end mt-6">
                    <button
                        type="button"
                        @click="closeModal"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed mr-3"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            v-if="form.processing"
                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
    fileId: Number,
});

const emit = defineEmits(['close', 'generated']);

const form = useForm({
    number_of_codes: 1,
    usage_limit: 1,
    expires_at: '',
});

const minDateTime = computed(() => {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0, 16);
});

const closeModal = () => {
    form.reset();
    emit('close');
};

const submit = () => {
    form.post(route('codes.store', props.fileId), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            emit('generated');
        },
    });
};
</script> 