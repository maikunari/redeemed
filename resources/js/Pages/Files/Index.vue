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

                <!-- File Upload Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Upload New File</h3>
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
                    </div>
                </div>

                <!-- FTP Management Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">FTP Bulk Upload</h3>
                                <p class="text-sm text-gray-600">Process files from your FTP staging directory</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span v-if="ftpFileCount !== null" class="text-sm text-gray-500">
                                    {{ ftpFileCount }} file{{ ftpFileCount !== 1 ? 's' : '' }} available
                                </span>
                                <button
                                    @click="scanFtpStaging"
                                    :disabled="scanning"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    <svg v-if="scanning" class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    {{ scanning ? 'Scanning...' : 'Scan FTP Directory' }}
                                </button>
                                <button
                                    v-if="ftpFiles.length > 0"
                                    @click="showFtpModal = true"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Process Files
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="ftpScanError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline">{{ ftpScanError }}</span>
                            <button @click="ftpScanError = null" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <title>Close</title>
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                </svg>
                            </button>
                        </div>

                        <div v-if="ftpFileCount === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V5a2 2 0 012-2h14a2 2 0 012 2v2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No FTP files found</h3>
                            <p class="mt-1 text-sm text-gray-500">Upload files to your FTP staging directory to get started.</p>
                        </div>
                        
                        <div class="mt-4 text-right">
                            <button
                                @click="openHistoryModal"
                                class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                View Processing History
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Files List Card -->
                <div ref="filesListSection" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
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

    <!-- FTP Processing Modal -->
    <Modal :show="showFtpModal" @close="closeFtpModal" maxWidth="4xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Process FTP Files
                </h2>
                <button @click="closeFtpModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Processing State -->
            <div v-if="processing" class="text-center py-12">
                <svg class="animate-spin mx-auto h-12 w-12 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Processing Files...</h3>
                <p class="mt-2 text-sm text-gray-600">Please wait while we process the selected files.</p>
            </div>

            <!-- Results State -->
            <div v-else-if="processingResults" class="space-y-6">
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Processing Complete</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Successfully processed {{ processingResults.processed }} file(s)</p>
                                <p v-if="processingResults.invalid > 0">Deleted {{ processingResults.invalid }} invalid file(s)</p>
                                <p v-if="processingResults.conflicts > 0">Resolved {{ processingResults.conflicts }} naming conflict(s)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="processingResults.errors && processingResults.errors.length > 0" class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Errors Occurred</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li v-for="error in processingResults.errors" :key="error">{{ error }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="closeFtpModal">
                        Close
                    </SecondaryButton>
                    <button
                        @click="redirectToNewFiles"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        View New Files
                    </button>
                </div>
            </div>

            <!-- File Selection State -->
            <div v-else class="space-y-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Select files to process from your FTP staging directory:
                    </p>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                :checked="allSelected"
                                @change="toggleSelectAll"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-sm text-gray-700">Select All</span>
                        </label>
                        <span class="text-sm text-gray-500">
                            {{ selectedFtpFiles.length }} of {{ ftpFiles.length }} selected
                        </span>
                    </div>
                </div>

                <div v-if="ftpFiles.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V5a2 2 0 012-2h14a2 2 0 012 2v2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No files available</h3>
                    <p class="mt-1 text-sm text-gray-500">Scan the FTP directory first to see available files.</p>
                </div>

                <div v-else class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                    <div class="divide-y divide-gray-200">
                        <div
                            v-for="file in ftpFiles"
                            :key="file.filename"
                            class="flex items-center justify-between p-4 hover:bg-gray-50"
                        >
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    :value="file.filename"
                                    v-model="selectedFtpFiles"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <svg v-if="file.type === 'audio'" class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                        </svg>
                                        <svg v-else class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-900">{{ file.title }}</p>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ file.filename }} • {{ file.size }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    file.valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                ]">
                                    {{ file.valid ? 'Valid' : 'Invalid' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="closeFtpModal">
                        Cancel
                    </SecondaryButton>
                    <button
                        @click="processFtpFiles"
                        :disabled="selectedFtpFiles.length === 0"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Process {{ selectedFtpFiles.length }} File{{ selectedFtpFiles.length !== 1 ? 's' : '' }}
                    </button>
                </div>
            </div>
        </div>
    </Modal>

    <!-- FTP Processing History Modal -->
    <Modal :show="showHistoryModal" @close="closeHistoryModal" maxWidth="6xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-medium text-gray-900">
                    FTP Processing History
                </h2>
                <button @click="closeHistoryModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Loading State -->
            <div v-if="loadingHistory" class="text-center py-12">
                <svg class="animate-spin mx-auto h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-600">Loading processing history...</p>
            </div>

            <!-- History List -->
            <div v-else-if="processingHistory.length > 0" class="space-y-4">
                <div v-for="log in processingHistory" :key="log.id" class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    log.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                ]">
                                    {{ log.success ? 'Success' : 'Failed' }}
                                </span>
                                <span class="text-sm text-gray-500">{{ log.processed_at }}</span>
                                <span class="text-sm text-gray-500">by {{ log.user_name }}</span>
                                <span v-if="log.processing_time" class="text-sm text-gray-500">({{ log.processing_time }})</span>
                            </div>
                            <p class="text-sm text-gray-900 mb-2">{{ log.summary }}</p>
                            <div v-if="log.errors && log.errors.length > 0" class="text-sm text-red-600">
                                <p class="font-medium">Errors:</p>
                                <ul class="list-disc list-inside ml-2">
                                    <li v-for="error in log.errors" :key="error">{{ error }}</li>
                                </ul>
                            </div>
                        </div>
                        <button
                            @click="toggleLogDetails(log.id)"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Expanded Details -->
                    <div v-if="expandedLogs.includes(log.id)" class="mt-4 pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Processing Statistics</h4>
                                <ul class="space-y-1 text-gray-600">
                                    <li>Total files: {{ log.total_files }}</li>
                                    <li>Successfully processed: {{ log.files_processed }}</li>
                                    <li>Invalid files deleted: {{ log.files_invalid }}</li>
                                    <li>Naming conflicts resolved: {{ log.files_conflicts }}</li>
                                    <li>Failed: {{ log.files_failed }}</li>
                                </ul>
                            </div>
                            <div v-if="log.processing_details">
                                <h4 class="font-medium text-gray-900 mb-2">File Details</h4>
                                <div class="max-h-32 overflow-y-auto">
                                    <div v-if="log.processing_details.processed_files && log.processing_details.processed_files.length > 0" class="mb-2">
                                        <p class="text-xs font-medium text-green-700">Processed Files:</p>
                                        <ul class="text-xs text-gray-600">
                                            <li v-for="file in log.processing_details.processed_files" :key="file.filename">
                                                {{ file.title }} ({{ file.filename }})
                                            </li>
                                        </ul>
                                    </div>
                                    <div v-if="log.processing_details.deleted_files && log.processing_details.deleted_files.length > 0" class="mb-2">
                                        <p class="text-xs font-medium text-red-700">Deleted Files:</p>
                                        <ul class="text-xs text-gray-600">
                                            <li v-for="file in log.processing_details.deleted_files" :key="file.filename">
                                                {{ file.filename }} ({{ file.reason }})
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No History State -->
            <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No processing history</h3>
                <p class="mt-1 text-sm text-gray-500">Process some FTP files to see the history here.</p>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeHistoryModal">
                    Close
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FileUploader from '@/Components/FileUploader.vue';
import GenerateCodeModal from '@/Components/GenerateCodeModal.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import axios from 'axios';

const props = defineProps({
    files: {
        type: Array,
        default: () => [],
    },
});

const fileUploader = ref(null);
const filesListSection = ref(null);
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

// FTP Management State
const ftpFiles = ref([]);
const ftpFileCount = ref(null);
const selectedFtpFiles = ref([]);
const showFtpModal = ref(false);
const scanning = ref(false);
const processing = ref(false);
const processingResults = ref(null);
const ftpScanError = ref(null);
const showHistoryModal = ref(false);
const loadingHistory = ref(false);
const processingHistory = ref([]);
const expandedLogs = ref([]);

// Computed Properties
const allSelected = computed(() => {
    return ftpFiles.value.length > 0 && selectedFtpFiles.value.length === ftpFiles.value.length;
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

// FTP Management Methods
const scanFtpStaging = async () => {
    scanning.value = true;
    ftpScanError.value = null;
    
    try {
        const response = await axios.get(route('files.scan-ftp'));
        ftpFiles.value = response.data.files || [];
        ftpFileCount.value = ftpFiles.value.length;
        selectedFtpFiles.value = [];
    } catch (error) {
        ftpScanError.value = error.response?.data?.message || 'Failed to scan FTP directory';
        ftpFiles.value = [];
        ftpFileCount.value = 0;
    } finally {
        scanning.value = false;
    }
};

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedFtpFiles.value = [];
    } else {
        selectedFtpFiles.value = ftpFiles.value.map(file => file.filename);
    }
};

const closeFtpModal = () => {
    showFtpModal.value = false;
    processing.value = false;
    processingResults.value = null;
    selectedFtpFiles.value = [];
};

const processFtpFiles = async () => {
    if (selectedFtpFiles.value.length === 0) return;
    
    processing.value = true;
    
    try {
        const response = await axios.post(route('files.process-ftp'), {
            files: selectedFtpFiles.value
        });
        
        processingResults.value = response.data;
        
        // Refresh FTP file list after processing
        await scanFtpStaging();
        
    } catch (error) {
        processingResults.value = {
            processed: 0,
            invalid: 0,
            conflicts: 0,
            errors: [error.response?.data?.message || 'Failed to process files']
        };
    } finally {
        processing.value = false;
    }
};

const redirectToNewFiles = async () => {
    closeFtpModal();
    
    // Refresh the page data
    await router.reload({ only: ['files'] });
    
    // Scroll to the files section after a brief delay to allow the DOM to update
    setTimeout(() => {
        if (filesListSection.value) {
            // Get the position of the files section
            const rect = filesListSection.value.getBoundingClientRect();
            const offset = window.pageYOffset + rect.top - 200; // 200px offset to show more of the card
            
            // Smooth scroll to the calculated position
            window.scrollTo({
                top: offset,
                behavior: 'smooth'
            });
        }
    }, 200);
};

const openHistoryModal = async () => {
    showHistoryModal.value = true;
    await loadProcessingHistory();
};

const closeHistoryModal = () => {
    showHistoryModal.value = false;
    expandedLogs.value = [];
};

const loadProcessingHistory = async () => {
    loadingHistory.value = true;
    
    try {
        const response = await axios.get(route('files.ftp-history'));
        processingHistory.value = response.data.logs || [];
    } catch (error) {
        console.error('Failed to load processing history:', error);
        processingHistory.value = [];
    } finally {
        loadingHistory.value = false;
    }
};

const toggleLogDetails = (logId) => {
    const index = expandedLogs.value.indexOf(logId);
    if (index > -1) {
        expandedLogs.value.splice(index, 1);
    } else {
        expandedLogs.value.push(logId);
    }
};

// Auto-scan FTP directory on page load
onMounted(() => {
    scanFtpStaging();
});
</script> 