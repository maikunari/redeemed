<template>
    <Modal :show="show" @close="closeModal">
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
                    <SecondaryButton class="mr-3" @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        Generate
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

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