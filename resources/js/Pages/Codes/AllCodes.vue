<template>
    <Head title="Manage Codes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manage Codes
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- File Selection and Generate Button -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-1/2">
                                <label for="file-select" class="block text-sm font-medium text-gray-700 mb-1">Select File</label>
                                <select
                                    id="file-select"
                                    v-model="selectedFileId"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">All Files</option>
                                    <option v-for="file in files" :key="file.id" :value="file.id">
                                        {{ file.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="flex space-x-4">
                                <button
                                    type="button"
                                    @click="openGenerateModal"
                                    :disabled="!selectedFileId"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Generate Codes
                                </button>
                                <a
                                    :href="route('codes.export', selectedFileId)"
                                    :class="[
                                        'inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150',
                                        selectedFileId ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    ]"
                                    :disabled="!selectedFileId"
                                >
                                    Export CSV
                                </a>
                            </div>
                        </div>

                        <!-- Search and Filters -->
                        <div class="mb-6">
                            <div class="max-w-lg">
                                <input
                                    type="text"
                                    v-model="search"
                                    placeholder="Search codes..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    @input="debouncedSearch"
                                >
                            </div>
                        </div>

                        <!-- Codes Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Code
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            File
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Usage
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Expires
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="code in codes.data" :key="code.id">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono">
                                            {{ code.code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="route('codes.file', code.file.id)" class="text-indigo-600 hover:text-indigo-900">
                                                {{ code.file.title }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ code.usage_count }}/{{ code.usage_limit }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ code.expires_at || 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ code.created_at }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="relative inline-block text-left">
                                                <button
                                                    @click="toggleActionsMenu(code.id)"
                                                    class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                >
                                                    Actions
                                                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <div
                                                    v-if="openActionMenu === code.id"
                                                    class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                                                    role="menu"
                                                    aria-orientation="vertical"
                                                    aria-labelledby="menu-button"
                                                    tabindex="-1"
                                                >
                                                    <div class="py-1" role="none">
                                                        <button
                                                            @click="showQrCode(code)"
                                                            class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900"
                                                            role="menuitem"
                                                            tabindex="-1"
                                                        >
                                                            View QR
                                                        </button>
                                                        <button
                                                            @click="openDeleteModal(code)"
                                                            class="text-red-600 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 hover:text-red-700"
                                                            role="menuitem"
                                                            tabindex="-1"
                                                        >
                                                            Delete Code
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="codes.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- QR Code Modal -->
    <Modal :show="showQrModal" @close="closeQrModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                QR Code for {{ selectedCode?.code }}
            </h2>
            <div class="flex justify-center">
                <img v-if="selectedCode" :src="route('codes.qr', selectedCode.id)" alt="QR Code" class="w-64 h-64">
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeQrModal">
                    Close
                </SecondaryButton>
            </div>
        </div>
    </Modal>

    <!-- Generate Codes Modal -->
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
                Delete Code
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to delete the code <strong>{{ codeToDelete?.code }}</strong>? This action cannot be undone.
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
                    Delete Code
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import GenerateCodeModal from '@/Components/GenerateCodeModal.vue';

const props = defineProps({
    codes: Object,
    files: {
        type: Array,
        required: true,
    }
});

const search = ref('');
const showQrModal = ref(false);
const selectedCode = ref(null);
const selectedFileId = ref('');
const showGenerateModal = ref(false);
const openActionMenu = ref(null);
const showDeleteModal = ref(false);
const codeToDelete = ref(null);
const deleting = ref(false);

// Safely initialize selectedFileId from the URL query string
try {
    const url = new URL(window.location.href);
    const fileParam = url.searchParams.get('file');
    if (fileParam && props.files && props.files.length > 0) {
        const fileIdStr = fileParam.toString();
        if (props.files.some(f => f && f.id && f.id.toString() === fileIdStr)) {
            selectedFileId.value = fileIdStr;
        }
    }
} catch (e) {
    console.error('Error parsing URL for file parameter:', e);
}

const debouncedSearch = debounce(() => {
    router.get(
        route('codes.index'),
        { 
            search: search.value,
            file: selectedFileId.value
        },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

watch(selectedFileId, () => {
    const queryParams = { search: search.value };
    if (selectedFileId.value) {
        queryParams.file = selectedFileId.value;
    }
    router.get(
        route('codes.index'),
        queryParams,
        { preserveState: true, preserveScroll: true }
    );
});

const openGenerateModal = () => {
    showGenerateModal.value = true;
};

const closeGenerateModal = () => {
    showGenerateModal.value = false;
};

const handleCodeGenerated = () => {
    router.reload({ only: ['codes'] });
};

const showQrCode = (code) => {
    selectedCode.value = code;
    showQrModal.value = true;
};

const closeQrModal = () => {
    showQrModal.value = false;
    selectedCode.value = null;
};

const toggleActionsMenu = (codeId) => {
    if (openActionMenu.value === codeId) {
        openActionMenu.value = null;
    } else {
        openActionMenu.value = codeId;
    }
};

const openDeleteModal = (code) => {
    codeToDelete.value = code;
    showDeleteModal.value = true;
    openActionMenu.value = null; // Close the dropdown
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    codeToDelete.value = null;
};

const confirmDelete = () => {
    if (!codeToDelete.value) return;
    
    deleting.value = true;
    router.delete(route('codes.destroy', codeToDelete.value.id), {
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
</script> 