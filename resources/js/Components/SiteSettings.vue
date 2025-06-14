<template>
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <section>
            <header>
                <h2 class="text-lg font-medium text-gray-900">Site Settings</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Update your site's name and logo.
                </p>
            </header>

            <form @submit.prevent="submitSite" class="mt-6 space-y-6">
                <div>
                    <InputLabel for="site_name" value="Site Name" />
                    <TextInput
                        id="site_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.site_name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.site_name" />
                </div>

                <div>
                    <InputLabel for="logo" value="Site Logo" />
                    <div class="mt-2 flex items-center gap-x-3">
                        <img v-if="logoPreview" :src="logoPreview" class="h-12 w-12 object-cover rounded" />
                        <img v-else-if="settings?.logo_url" :src="settings.logo_url" class="h-12 w-12 object-cover rounded" />
                        <div v-else class="h-12 w-12 rounded bg-gray-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <SecondaryButton type="button" @click="selectNewLogo">
                            Change Logo
                        </SecondaryButton>
                    </div>
                    <input
                        ref="logoInput"
                        type="file"
                        class="hidden"
                        @change="updateLogo"
                        accept="image/*"
                    />
                    <InputError class="mt-2" :message="form.errors.logo" />
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                    </Transition>
                </div>
            </form>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({
            site_name: '',
            logo_url: null
        })
    }
});

const form = useForm({
    site_name: props.settings?.site_name || '',
    logo: null,
});

const logoInput = ref(null);
const logoPreview = ref(null);

const selectNewLogo = () => {
    logoInput.value.click();
};

const updateLogo = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    form.logo = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        logoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

function submitSite() {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            if (!form.hasErrors) {
                logoPreview.value = null;
                form.logo = null;
            }
        },
    });
}
</script> 