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
                    <button
                        @click="exportCards"
                        :disabled="exportingCards"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                    >
                        <svg v-if="exportingCards" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ exportingCards ? 'Generating...' : 'Export Cards' }}
                    </button>
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
                            <div class="flex justify-between items-center">
                                <div class="max-w-lg">
                                    <input
                                        type="text"
                                        v-model="search"
                                        placeholder="Search codes..."
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        @input="debouncedSearch"
                                    >
                                </div>
                                <div v-if="selectedCodes.length > 0" class="text-sm text-gray-600">
                                    {{ selectedCodes.length }} code{{ selectedCodes.length === 1 ? '' : 's' }} selected
                                </div>
                            </div>
                        </div>

                        <!-- Codes Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left">
                                            <input
                                                type="checkbox"
                                                :checked="allSelected"
                                                @change="toggleSelectAll"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            >
                                        </th>
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
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <input
                                                type="checkbox"
                                                :value="code.id"
                                                v-model="selectedCodes"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            >
                                        </td>
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
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
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
const selectedCodes = ref([]);
const exportingCards = ref(false);

// Computed property for "Select All" checkbox
const allSelected = computed(() => {
    return props.codes.data.length > 0 && selectedCodes.value.length === props.codes.data.length;
});

// Form for exporting cards
const exportForm = useForm({
    codes: []
});

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

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedCodes.value = [];
    } else {
        selectedCodes.value = props.codes.data.map(code => code.id);
    }
};

const exportCards = () => {
    if (selectedCodes.value.length === 0) {
        alert('Please select at least one code to export.');
        return;
    }

    exportingCards.value = true;
    
    // Create a form and submit it to trigger PDF download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route('codes.export-cards', props.file.id);
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);
    
    // Add selected codes
    selectedCodes.value.forEach(codeId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'codes[]';
        input.value = codeId;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset loading state after a short delay
    setTimeout(() => {
        exportingCards.value = false;
    }, 2000);
};
</script> 