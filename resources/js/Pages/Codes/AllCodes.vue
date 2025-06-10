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
                <!-- Control Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <!-- Main Control Row -->
                        <div class="grid grid-cols-2 gap-8 mb-6 items-center">
                            <!-- Left Side: File Selection and Search -->
                            <div class="space-y-4">
                                <!-- File Selection -->
                                <div>
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
                                
                                <!-- Search -->
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Codes</label>
                                    <input
                                        id="search"
                                        type="text"
                                        v-model="search"
                                        placeholder="Search codes..."
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        @input="debouncedSearch"
                                    >
                                </div>
                            </div>

                            <!-- Right Side: Action Buttons -->
                            <div class="space-y-4">
                                <!-- Generate Codes -->
                                <button
                                    type="button"
                                    @click="openGenerateModal"
                                    :disabled="!selectedFileId"
                                    class="w-full inline-flex justify-center items-center py-2.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Generate Codes
                                </button>

                                <!-- Export Buttons Row -->
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Export Cards -->
                                    <button
                                        @click="exportCards"
                                        :disabled="!selectedFileId || exportingCards"
                                        :class="[
                                            'inline-flex items-center justify-center px-3 py-2.5 rounded-md font-medium text-sm transition ease-in-out duration-150',
                                            selectedFileId ? 'bg-indigo-600 border border-transparent text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2' : 'bg-gray-300 text-gray-500 cursor-not-allowed',
                                            'disabled:opacity-50'
                                        ]"
                                    >
                                        <svg v-if="exportingCards" class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ exportingCards ? 'Generating...' : 'Export Cards' }}
                                    </button>

                                    <!-- Export CSV -->
                                    <a
                                        :href="route('codes.export', selectedFileId)"
                                        :class="[
                                            'inline-flex items-center justify-center px-3 py-2.5 rounded-md font-medium text-sm transition ease-in-out duration-150',
                                            selectedFileId ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                        ]"
                                        :disabled="!selectedFileId"
                                    >
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Export CSV
                                    </a>
                                </div>

                                <!-- Print Shop Instructions Link -->
                                <div class="text-center pt-1">
                                    <a
                                        href="/print-shop-instructions.html"
                                        target="_blank"
                                        class="inline-flex items-center px-3 py-2 text-sm text-indigo-600 hover:text-indigo-500 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors duration-150"
                                    >
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Print Shop Instructions
                                        <svg class="w-3 h-3 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Codes Table Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Tabs for Active and Expired Codes -->
                        <div class="mb-4">
                            <nav class="flex space-x-2" aria-label="Tabs">
                                <button
                                    @click="activeTab = 'active'"
                                    :class="{
                                        'bg-indigo-100 text-indigo-700': activeTab === 'active',
                                        'bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700': activeTab !== 'active'
                                    }"
                                    class="py-2 px-4 rounded-md font-medium text-sm transition-colors duration-200"
                                >
                                    Active Codes
                                </button>
                                <button
                                    @click="activeTab = 'expired'"
                                    :class="{
                                        'bg-indigo-100 text-indigo-700': activeTab === 'expired',
                                        'bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700': activeTab !== 'expired'
                                    }"
                                    class="py-2 px-4 rounded-md font-medium text-sm transition-colors duration-200"
                                >
                                    Expired Codes
                                </button>
                            </nav>
                        </div>

                        <!-- Codes Table -->
                        <div class="overflow-x-auto" style="min-height: 400px;">
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
                                    <tr v-for="code in filteredCodes" :key="code.id">
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
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 6a2 2 0 100-4 2 2 0 000 4zM10 12a2 2 0 100-4 2 2 0 000 4zM10 18a2 2 0 100-4 2 2 0 000 4z" />
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
                                                            @click="openManageRenewModal(code)"
                                                            class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900"
                                                            role="menuitem"
                                                            tabindex="-1"
                                                        >
                                                            Manage / Renew
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
                <img v-if="selectedCode" :src="route('codes.qr', selectedCode.id)" alt="QR Code" class="w-64 h-64" @error="console.error('Failed to load QR code image for code ID: ' + selectedCode.id + '. URL: ' + route('codes.qr', selectedCode.id))">
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
        :file-id="Number(selectedFileId)"
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

    <!-- Manage / Renew Modal -->
    <Modal :show="showManageRenewModal" @close="closeManageRenewModal" maxWidth="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Manage / Renew Code
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Current usage for code <strong>{{ codeToManage?.code }}</strong>: {{ codeToManage?.usage_count }}/{{ codeToManage?.usage_limit }}.
            </p>
            <div class="mt-4">
                <label for="newUsageLimit" class="block text-sm font-medium text-gray-700">New Usage Limit</label>
                <input
                    id="newUsageLimit"
                    type="number"
                    v-model="newUsageLimit"
                    min="1"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="closeManageRenewModal">
                    Cancel
                </SecondaryButton>
                <button
                    :class="{ 'opacity-25': updating, 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500': true }"
                    :disabled="updating || newUsageLimit < 1"
                    @click="confirmManageRenew"
                >
                    Update Limit
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
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
const showManageRenewModal = ref(false);
const codeToManage = ref(null);
const newUsageLimit = ref(1);
const updating = ref(false);
const activeTab = ref('active');
const exportingCards = ref(false);

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

const openManageRenewModal = (code) => {
    codeToManage.value = code;
    newUsageLimit.value = code.usage_limit;
    showManageRenewModal.value = true;
    openActionMenu.value = null; // Close the dropdown
};

const closeManageRenewModal = () => {
    showManageRenewModal.value = false;
    codeToManage.value = null;
    newUsageLimit.value = 1;
};

const confirmManageRenew = () => {
    if (!codeToManage.value || newUsageLimit.value < 1) return;
    
    updating.value = true;
    router.patch(route('codes.renew', codeToManage.value.id), {
        usage_limit: newUsageLimit.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            closeManageRenewModal();
        },
        onError: () => {
            // Error will be shown via Inertia's default error handling
        },
        onFinish: () => {
            updating.value = false;
        }
    });
};

const filteredCodes = computed(() => {
    if (activeTab.value === 'active') {
        return props.codes.data.filter(code => {
            return code.usage_count < code.usage_limit;
        });
    } else if (activeTab.value === 'expired') {
        return props.codes.data.filter(code => {
            return code.usage_count >= code.usage_limit || !code.expires_at;
        });
    }
    return props.codes.data;
});

const exportCards = () => {
    if (!selectedFileId.value) {
        alert('Please select a file to export cards for.');
        return;
    }

    exportingCards.value = true;
    
    // Create a form and submit it to trigger PDF download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route('codes.export-cards', selectedFileId.value);
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);
    
    // Note: For AllCodes page, we export all codes for the selected file
    // Individual code selection will be available on the specific file's codes page
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset loading state after a short delay
    setTimeout(() => {
        exportingCards.value = false;
    }, 2000);
};
</script> 