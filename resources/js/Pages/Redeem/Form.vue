<template>
    <Head title="Redeem Download Code" />

    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Redeem Download Code
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">
                            Download Code
                        </label>
                        <div class="mt-1">
                            <input
                                id="code"
                                v-model="form.code"
                                type="text"
                                required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                :class="{ 'border-red-500': form.errors.code }"
                            >
                        </div>
                        <p v-if="form.errors.code" class="mt-2 text-sm text-red-600">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        >
                            {{ form.processing ? 'Processing...' : 'Download File' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    code: {
        type: String,
        default: '',
    },
});

const form = useForm({
    code: props.code || '',
});

const submit = () => {
    form.post(route('codes.redeem'), {
        preserveScroll: true,
        onSuccess: () => {
            // The response will be handled by the browser as a file download
        },
        onError: () => {
            form.code = '';
        },
    });
};
</script> 