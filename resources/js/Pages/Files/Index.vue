<template>
    <Head title="File Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                File Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Upload Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Upload New File</h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <InputLabel for="title" value="Title" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="file" value="File" />
                                <input
                                    id="file"
                                    type="file"
                                    class="mt-1 block w-full"
                                    @input="form.file = $event.target.files[0]"
                                    required
                                />
                                <InputError :message="form.errors.file" class="mt-2" />
                            </div>
                            <PrimaryButton :disabled="form.processing">
                                Upload
                            </PrimaryButton>
                        </form>
                    </div>
                </div>

                <!-- Files List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Uploaded Files</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="file in files" :key="file.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ file.title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img v-if="file.thumbnail" :src="file.thumbnail" class="h-10 w-10 object-cover rounded" />
                                            <span v-else class="text-gray-400">No preview</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                            <button
                                                @click="showGenerateCode(file)"
                                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            >
                                                Generate Code
                                            </button>
                                            <Link
                                                :href="route('codes.export', file.id)"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            >
                                                Export Codes
                                            </Link>
                                            <button
                                                @click="deleteFile(file)"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generate Code Modal -->
        <Modal :show="codeModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Generate Download Code
                </h2>
                <form @submit.prevent="generateCode" class="space-y-4">
                    <div>
                        <InputLabel for="usage_limit" value="Usage Limit" />
                        <TextInput
                            id="usage_limit"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="codeForm.usage_limit"
                            required
                            min="1"
                        />
                        <InputError :message="codeForm.errors.usage_limit" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="expires_at" value="Expiration Date (Optional)" />
                        <TextInput
                            id="expires_at"
                            type="datetime-local"
                            class="mt-1 block w-full"
                            v-model="codeForm.expires_at"
                        />
                        <InputError :message="codeForm.errors.expires_at" class="mt-2" />
                    </div>
                    <div class="flex justify-end mt-6">
                        <SecondaryButton class="mr-3" @click="closeModal">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="codeForm.processing">
                            Generate
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
    files: Array,
});

const form = useForm({
    title: '',
    file: null,
});

const codeForm = useForm({
    usage_limit: 1,
    expires_at: '',
});

const codeModal = ref(false);
const selectedFile = ref(null);

const submit = () => {
    form.post(route('files.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const showGenerateCode = (file) => {
    selectedFile.value = file;
    codeModal.value = true;
};

const closeModal = () => {
    codeModal.value = false;
    selectedFile.value = null;
    codeForm.reset();
};

const generateCode = () => {
    codeForm.post(route('codes.store', selectedFile.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const deleteFile = (file) => {
    if (confirm('Are you sure you want to delete this file?')) {
        router.delete(route('files.destroy', file.id));
    }
};
</script> 