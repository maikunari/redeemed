<template>
    <Head title="Files" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Files</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FileUploader
                            ref="fileUploader"
                            @fileSelected="handleFileSelected"
                            @fileRemoved="handleFileRemoved"
                            @thumbnailSelected="handleThumbnailSelected"
                            @thumbnailRemoved="handleThumbnailRemoved"
                        />

                        <form v-if="selectedFile" @submit.prevent="uploadFile" class="mt-4">
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input
                                    type="text"
                                    id="title"
                                    v-model="title"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                >
                            </div>

                            <button
                                type="submit"
                                :disabled="uploading"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                :class="{ 'opacity-50 cursor-not-allowed': uploading }"
                            >
                                {{ uploading ? 'Uploading...' : 'Upload' }}
                            </button>
                        </form>

                        <!-- Files List -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Uploaded Files</h3>
                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div v-for="file in files" :key="file.id" class="relative bg-white border rounded-lg shadow-sm">
                                    <div class="p-4">
                                        <div class="aspect-w-3 aspect-h-2 mb-4">
                                            <img
                                                v-if="file.thumbnail"
                                                :src="file.thumbnail"
                                                :alt="file.title"
                                                class="object-cover rounded-lg"
                                            >
                                            <div v-else class="flex items-center justify-center bg-gray-100 rounded-lg">
                                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ file.title }}</h4>
                                        <p class="mt-1 text-sm text-gray-500">{{ file.filename }}</p>
                                        <div class="mt-4 flex justify-between">
                                            <a
                                                :href="route('files.download', file.id)"
                                                class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                            >
                                                Download
                                            </a>
                                            <button
                                                @click="deleteFile(file.id)"
                                                class="text-sm font-medium text-red-600 hover:text-red-500"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FileUploader from '@/Components/FileUploader.vue';

const props = defineProps({
    files: {
        type: Array,
        default: () => [],
    },
});

const fileUploader = ref(null);
const selectedFile = ref(null);
const selectedThumbnail = ref(null);
const title = ref('');
const uploading = ref(false);

const handleFileSelected = (file) => {
    selectedFile.value = file;
};

const handleFileRemoved = () => {
    selectedFile.value = null;
    title.value = '';
};

const handleThumbnailSelected = (file) => {
    selectedThumbnail.value = file;
};

const handleThumbnailRemoved = () => {
    selectedThumbnail.value = null;
};

const uploadFile = async () => {
    if (!selectedFile.value) return;

    const formData = new FormData();
    formData.append('title', title.value);
    formData.append('file', selectedFile.value);
    if (selectedThumbnail.value) {
        formData.append('thumbnail', selectedThumbnail.value);
    }

    uploading.value = true;
    fileUploader.value.setUploading(true);

    try {
        const response = await axios.post(route('files.store'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress: (progressEvent) => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                fileUploader.value.setProgress(percentCompleted);
            },
        });

        // Redirect to refresh the page with new data
        window.location.href = route('files.index');
    } catch (error) {
        alert(error.response?.data?.message || 'An error occurred while uploading the file');
    } finally {
        uploading.value = false;
        fileUploader.value.setUploading(false);
        fileUploader.value.setProgress(0);
    }
};

const deleteFile = async (fileId) => {
    if (!confirm('Are you sure you want to delete this file?')) return;

    try {
        await axios.delete(route('files.destroy', fileId));
        window.location.href = route('files.index');
    } catch (error) {
        alert('An error occurred while deleting the file');
    }
};
</script> 