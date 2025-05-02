<template>
    <Head title="Manage Files" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Files</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Error Messages -->
                <div v-if="form.errors.file" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ form.errors.file }}</span>
                    <button @click="clearError" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="mb-4 text-sm text-gray-600">Upload your MP3 or ZIP file (max 20MB) and add a custom thumbnail to make it easily identifiable.</p>
                        
                        <FileUploader
                            ref="fileUploader"
                            @fileSelected="handleFileSelected"
                            @fileRemoved="handleFileRemoved"
                            @thumbnailSelected="handleThumbnailSelected"
                            @thumbnailRemoved="handleThumbnailRemoved"
                            @error="handleUploadError"
                        />

                        <form v-if="selectedFile" @submit.prevent="uploadFile" class="mt-4">
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input
                                    type="text"
                                    id="title"
                                    v-model="title"
                                    :placeholder="selectedFile?.name"
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
                            <div v-if="!files.length" class="mt-4 text-center py-12 bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No files uploaded</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by uploading your first file above.</p>
                            </div>
                            <div v-else class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                                            <div v-if="editingFile === file.id" class="flex items-center gap-2">
                                                <input
                                                    type="text"
                                                    v-model="editTitle"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    @keyup.enter="updateFileTitle(file.id)"
                                                    @keyup.esc="cancelEdit"
                                                >
                                                <button 
                                                    @click="updateFileTitle(file.id)"
                                                    class="text-green-600 hover:text-green-700"
                                                    title="Save"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button 
                                                    @click="cancelEdit"
                                                    class="text-gray-400 hover:text-gray-500"
                                                    title="Cancel"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
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
                                            <p><span class="text-gray-700">File Name:</span> {{ file.filename }}</p>
                                            <p>Size: {{ file.size }}</p>
                                            <p>Uploaded: {{ file.created_at }}</p>
                                            <p>Downloads: {{ file.download_count }}</p>
                                        </div>
                                        <div class="mt-4 -mx-4 -mb-4 flex divide-x border-t">
                                            <a
                                                :href="route('codes.index', { file: file.id })"
                                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-50 hover:bg-green-100 transition-colors text-sm font-medium text-green-700 hover:text-green-800 whitespace-nowrap"
                                            >
                                                Manage Codes
                                            </a>
                                            <button
                                                @click="deleteFile(file.id)"
                                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-rose-50 hover:bg-rose-100 transition-colors text-sm font-medium text-rose-700 hover:text-rose-800"
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

    <GenerateCodeModal
        :show="showGenerateModal"
        :file-id="selectedFileId"
        @close="closeGenerateModal"
        @generated="handleCodeGenerated"
    />

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal" maxWidth="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Delete File
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to delete this file? This action cannot be undone.
            </p>
            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="closeDeleteModal">
                    Cancel
                </SecondaryButton>
                <DangerButton
                    :class="{ 'opacity-25': deleting }"
                    :disabled="deleting"
                    @click="confirmDelete"
                >
                    Delete File
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FileUploader from '@/Components/FileUploader.vue';
import GenerateCodeModal from '@/Components/GenerateCodeModal.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

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
const uploadProgress = ref(0);
const showDeleteModal = ref(false);
const fileToDelete = ref(null);
const deleting = ref(false);
const form = useForm({
    title: '',
    file: null,
    thumbnail: null,
});

const handleFileSelected = (file) => {
    selectedFile.value = file;
    // Don't automatically set the title, let the user type it
    // The filename will show as placeholder
};

const handleFileRemoved = () => {
    selectedFile.value = null;
    title.value = '';
    form.clearErrors();
};

const handleThumbnailSelected = (file) => {
    selectedThumbnail.value = file;
};

const handleThumbnailRemoved = () => {
    selectedThumbnail.value = null;
};

const handleUploadError = (message) => {
    if (!form.errors) {
        form.errors = {};
    }
    form.errors.file = message;
};

const uploadFile = async () => {
    if (!selectedFile.value) return;

    uploading.value = true;
    fileUploader.value?.setUploading(true);

    try {
        form.clearErrors();
        form.title = title.value;
        form.file = selectedFile.value;
        form.thumbnail = selectedThumbnail.value;

        await form.post(route('files.store'), {
            preserveScroll: true,
            onProgress: (progress) => {
                const percentCompleted = Math.round((progress.loaded * 100) / progress.total);
                fileUploader.value?.setProgress(percentCompleted);
                uploadProgress.value = percentCompleted;
            },
            onError: (errors) => {
                // Handle Laravel validation errors
                if (errors.file) {
                    handleUploadError(errors.file);
                } else if (errors.message) {
                    handleUploadError(errors.message);
                } else {
                    handleUploadError('An error occurred during file upload');
                }
            },
            onSuccess: () => {
                selectedFile.value = null;
                selectedThumbnail.value = null;
                title.value = '';
                uploadProgress.value = 0;
                fileUploader.value?.reset();
            },
            onFinish: () => {
                uploading.value = false;
                fileUploader.value?.setUploading(false);
                fileUploader.value?.setProgress(0);
            }
        });
    } catch (error) {
        handleUploadError(error.message || 'An error occurred during file upload');
    }
};

const deleteFile = (fileId) => {
    fileToDelete.value = fileId;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    fileToDelete.value = null;
};

const confirmDelete = () => {
    if (!fileToDelete.value) return;
    
    deleting.value = true;
    router.delete(route('files.destroy', fileToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
        },
        onError: () => {
            // Error will be shown via Inertia's default error handling
        },
        onFinish: () => {
            deleting.value = false;
        }
    });
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
    if (!editTitle.value.trim()) {
        return;
    }

    try {
        await router.patch(route('files.update', fileId), {
            title: editTitle.value.trim()
        }, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                editingFile.value = null;
                editTitle.value = '';
            },
            onError: (errors) => {
                const message = errors.title || 'Failed to update title';
                form.setError('title', message);
            }
        });
    } catch (error) {
        form.setError('title', 'Failed to update title');
    }
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

const clearError = () => {
    form.clearErrors();
};
</script> 