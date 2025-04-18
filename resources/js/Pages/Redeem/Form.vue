<template>
    <Head title="Redeem Download Code" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-8">
                Redeem Your Download Code
            </h2>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="code" value="Download Code" />
                    <TextInput
                        id="code"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.code"
                        required
                        autofocus
                        placeholder="Enter your code (e.g., XXXX-XXXX)"
                    />
                    <InputError :message="form.errors.code" class="mt-2" />
                </div>

                <div class="flex items-center justify-center mt-6">
                    <PrimaryButton class="w-full justify-center" :disabled="form.processing">
                        Download File
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    code: '',
});

const submit = () => {
    form.post(route('codes.redeem'), {
        preserveScroll: true,
    });
};
</script> 