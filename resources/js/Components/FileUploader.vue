<template>
    <div class="w-full">
        <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="[
                'border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-all',
                isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-indigo-400',
                uploading ? 'pointer-events-none opacity-50' : ''
            ]"
            @click="$refs.fileInput.click()"
        >
            <div v-if="!selectedFile && !uploading">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-1 text-sm text-gray-600">Drag and drop your file here or click to browse</p>
                <p class="mt-1 text-xs text-gray-500">MP3 or ZIP files only (max 20MB)</p>
            </div>

            <div v-else-if="selectedFile && !uploading" class="text-left">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</div>
                            <div class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</div>
                        </div>
                    </div>
                    <button @click.stop="removeFile" class="text-red-500 hover:text-red-700">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-if="uploading" class="mt-4">
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block text-indigo-600">
                                Uploading...
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-indigo-600">
                                {{ Math.round(uploadProgress) }}%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-200">
                        <div :style="{ width: uploadProgress + '%' }" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500 transition-all duration-300"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thumbnail uploader -->
        <div v-if="selectedFile && !uploading" class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Thumbnail (optional)</label>
            <div class="mt-1 flex items-center">
                <div v-if="thumbnailPreview" class="relative">
                    <img :src="thumbnailPreview" class="h-20 w-20 object-cover rounded">
                    <button @click="removeThumbnail" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <button v-else @click="$refs.thumbnailInput.click()" type="button" class="ml-0 flex justify-center items-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="text-sm text-gray-600">
                            <span>Add thumbnail</span>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <input
            ref="fileInput"
            type="file"
            class="hidden"
            accept=".mp3,.zip"
            @change="handleFileSelect"
        >

        <input
            ref="thumbnailInput"
            type="file"
            class="hidden"
            accept="image/*"
            @change="handleThumbnailSelect"
        >
    </div>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['fileSelected', 'fileRemoved', 'uploadProgress', 'thumbnailSelected', 'thumbnailRemoved', 'error']);

const isDragging = ref(false);
const selectedFile = ref(null);
const thumbnailPreview = ref(null);
const uploading = ref(false);
const uploadProgress = ref(0);

const handleDrop = (e) => {
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    if (isValidFile(file)) {
        selectedFile.value = file;
        emit('fileSelected', file);
    }
};

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (file && isValidFile(file)) {
        selectedFile.value = file;
        emit('fileSelected', file);
    }
    e.target.value = ''; // Reset input
};

const handleThumbnailSelect = (e) => {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            thumbnailPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
        emit('thumbnailSelected', file);
    }
    e.target.value = ''; // Reset input
};

const isValidFile = (file) => {
    if (!file) return false;
    
    const validTypes = ['.mp3', '.zip'];
    const extension = '.' + file.name.split('.').pop().toLowerCase();
    
    if (!validTypes.includes(extension)) {
        emit('error', `Invalid file type. Please select an MP3 or ZIP file. You uploaded a ${extension} file.`);
        return false;
    }
    
    const maxSize = 20 * 1024 * 1024; // 20MB
    if (file.size > maxSize) {
        emit('error', `File size must be less than 20MB. Your file is ${formatFileSize(file.size)}.`);
        return false;
    }
    
    return true;
};

const removeFile = () => {
    selectedFile.value = null;
    thumbnailPreview.value = null;
    emit('fileRemoved');
    emit('thumbnailRemoved');
};

const removeThumbnail = () => {
    thumbnailPreview.value = null;
    emit('thumbnailRemoved');
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const reset = () => {
    selectedFile.value = null;
    thumbnailPreview.value = null;
    uploading.value = false;
    uploadProgress.value = 0;
};

defineExpose({
    setUploading(value) {
        uploading.value = value;
    },
    setProgress(value) {
        uploadProgress.value = value;
        emit('uploadProgress', { loaded: value, total: 100 });
    },
    reset
});
</script> 