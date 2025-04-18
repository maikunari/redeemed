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
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    codes: Object,
});

const search = ref('');
const showQrModal = ref(false);
const selectedCode = ref(null);

const debouncedSearch = debounce(() => {
    router.get(
        route('codes.index'),
        { search: search.value },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

const showQrCode = (code) => {
    selectedCode.value = code;
    showQrModal.value = true;
};

const closeQrModal = () => {
    showQrModal.value = false;
    selectedCode.value = null;
};
</script> 