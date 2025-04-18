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
                                    :placeholder="selectedFile?.name || 'Enter title'"
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
                                        <div class="aspect-w-1 aspect-h-1 mb-4 relative w-full h-[180px] group">
                                            <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner py-4">
                                                <img
                                                    v-if="file.thumbnail"
                                                    :src="file.thumbnail"
                                                    :alt="file.title"
                                                    class="max-h-[150px] max-w-[150px] object-contain rounded-lg shadow-md"
                                                >
                                                <div v-else class="w-full h-full flex items-center justify-center">
                                                    <svg v-if="file.type === 'audio'" class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                    </svg>
                                                    <svg v-else class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <!-- Replace Thumbnail Button -->
                                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                                <input
                                                    type="file"
                                                    :id="'thumbnail-' + file.id"
                                                    class="hidden"
                                                    accept="image/*"
                                                    @change="(e) => replaceThumbnail(e, file.id)"
                                                >
                                                <label
                                                    :for="'thumbnail-' + file.id"
                                                    class="px-3 py-2 bg-white text-gray-700 rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                                >
                                                    Replace Thumbnail
                                                </label>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div v-if="editingFile === file.id">
                                                <input
                                                    type="text"
                                                    v-model="editTitle"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    @keyup.enter="updateFileTitle(file.id)"
                                                    @keyup.esc="cancelEdit"
                                                >
                                            </div>
                                            <h4 v-else class="text-lg font-medium text-gray-900" @dblclick="startEdit(file)">
                                                {{ file.title }}
                                            </h4>
                                            <button 
                                                v-if="!editingFile"
                                                @click="startEdit(file)" 
                                                class="text-gray-400 hover:text-gray-500"
                                                title="Edit title"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-500">
                                            <p>{{ file.filename }}</p>
                                            <p>Size: {{ file.size }}</p>
                                            <p>Uploaded: {{ file.created_at }}</p>
                                            <p>Downloads: {{ file.download_count }}</p>
                                        </div>
                                        <div class="mt-4 flex justify-between">
                                            <a
                                                :href="route('files.download', file.id)"
                                                class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                            >
                                                Download
                                            </a>
                                            <div class="flex space-x-3">
                                                <a
                                                    :href="route('codes.index', file.id)"
                                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                                >
                                                    Manage Codes
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
        </div>
    </AuthenticatedLayout>

    <GenerateCodeModal
        :show="showGenerateModal"
        :file-id="selectedFileId"
        @close="closeGenerateModal"
        @generated="handleCodeGenerated"
    />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FileUploader from '@/Components/FileUploader.vue';
import GenerateCodeModal from '@/Components/GenerateCodeModal.vue';

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
const editingFile = ref(null);
const editTitle = ref('');
const showGenerateModal = ref(false);
const selectedFileId = ref(null);

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

const startEdit = (file) => {
    editingFile.value = file.id;
    editTitle.value = file.title;
};

const cancelEdit = () => {
    editingFile.value = null;
    editTitle.value = '';
};

const updateFileTitle = async (fileId) => {
    try {
        await axios.patch(route('files.update', fileId), {
            title: editTitle.value
        });
        window.location.href = route('files.index');
    } catch (error) {
        alert('An error occurred while updating the file title');
    }
    cancelEdit();
};

const openGenerateCode = (file) => {
    selectedFileId.value = file.id;
    showGenerateModal.value = true;
};

const closeGenerateModal = () => {
    showGenerateModal.value = false;
    selectedFileId.value = null;
};

const handleCodeGenerated = () => {
    // Refresh the page to show the new code
    window.location.reload();
};

const replaceThumbnail = async (event, fileId) => {
    const thumbnail = event.target.files[0];
    if (!thumbnail) return;

    const formData = new FormData();
    formData.append('thumbnail', thumbnail);

    try {
        await axios.post(route('files.update-thumbnail', fileId), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        window.location.href = route('files.index');
    } catch (error) {
        alert(error.response?.data?.message || 'An error occurred while updating the thumbnail');
    }
};
</script> 