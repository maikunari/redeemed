<template>
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <section>
            <header>
                <h2 class="text-lg font-medium text-gray-900">Redeem Card Settings</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Customize the design and content of your PDF redeem cards.
                </p>
            </header>

            <form @submit.prevent="submitCards" class="mt-6 space-y-6">
                <div>
                    <InputLabel for="card_brand_name" value="Brand Name (appears at top of card)" />
                    <TextInput 
                        id="card_brand_name" 
                        type="text" 
                        class="mt-1 block w-full" 
                        v-model="form.card_brand_name" 
                        placeholder="Your Company Name" 
                    />
                    <InputError class="mt-2" :message="form.errors.card_brand_name" />
                </div>

                <div>
                    <InputLabel for="card_website_url" value="Website URL (displayed on cards)" />
                    <TextInput 
                        id="card_website_url" 
                        type="url" 
                        class="mt-1 block w-full" 
                        v-model="form.card_website_url" 
                        placeholder="https://yoursite.com/redeem" 
                    />
                    <InputError class="mt-2" :message="form.errors.card_website_url" />
                </div>

                <div>
                    <InputLabel for="card_instructions" value="Usage Instructions" />
                    <textarea 
                        id="card_instructions" 
                        rows="3" 
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                        v-model="form.card_instructions" 
                        placeholder="Enter your download code at the website above to access your music."
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.card_instructions" />
                </div>

                <div>
                    <InputLabel for="card_qr_instruction" value="QR Code Instructions" />
                    <TextInput 
                        id="card_qr_instruction" 
                        type="text" 
                        class="mt-1 block w-full" 
                        v-model="form.card_qr_instruction" 
                        placeholder="Scan to Download" 
                    />
                    <InputError class="mt-2" :message="form.errors.card_qr_instruction" />
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
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
});

const form = useForm({
    site_name: props.settings?.site_name || '',
    logo: null,
    support_email: props.settings?.support_email || '',
    contact_subtitle: props.settings?.contact_subtitle || '',
    contact_thankyou: props.settings?.contact_thankyou || '',
    card_website_url: props.settings?.card_website_url || '',
    card_brand_name: props.settings?.card_brand_name || '',
    card_instructions: props.settings?.card_instructions || '',
    card_qr_instruction: props.settings?.card_qr_instruction || 'Scan to Download',
});

function submitCards() {
    form.post(route('settings.update'), {
        preserveScroll: true,
    });
}
</script> 