<template>
    <Head :title="`Codes - ${file.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Download Codes for "{{ file.title }}"
                </h2>
                <div class="flex space-x-4">
                    <a
                        :href="route('files.index')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Back to Files
                    </a>
                    <a
                        :href="route('codes.export', file.id)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Export CSV
                    </a>
                    <PrimaryButton @click="openGenerateModal">
                        Generate Codes
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- File Info -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">File Details</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p><span class="font-medium">Filename:</span> {{ file.filename }}</p>
                                    <p><span class="font-medium">Size:</span> {{ file.size }}</p>
                                </div>
                                <div>
                                    <p><span class="font-medium">Type:</span> {{ file.type }}</p>
                                    <p><span class="font-medium">Downloads:</span> {{ file.download_count }}</p>
                                </div>
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

    <!-- Generate Codes Modal -->
    <GenerateCodeModal
        :show="showGenerateModal"
        :file-id="file.id"
        @close="closeGenerateModal"
        @generated="handleCodeGenerated"
    />

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
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import GenerateCodeModal from '@/Components/GenerateCodeModal.vue';

const props = defineProps({
    file: Object,
    codes: Object,
});

const search = ref('');
const showGenerateModal = ref(false);
const showQrModal = ref(false);
const selectedCode = ref(null);

const debouncedSearch = debounce(() => {
    router.get(
        route('codes.index', props.file.id),
        { search: search.value },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

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