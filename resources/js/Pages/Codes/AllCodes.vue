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
                                            <button
                                                @click="showQrCode(code)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                View QR
                                            </button>
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
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
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
    router.get(
        route('codes.index'),
        { 
            search: search.value,
            file: selectedFileId.value
        },
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
</script> 