<template>
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <section>
            <header>
                <h2 class="text-lg font-medium text-gray-900">Contact Form Settings</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Customize the texts and support email used in the contact form.
                </p>
            </header>

            <form @submit.prevent="submitContact" class="mt-6 space-y-6">
                <div>
                    <InputLabel for="support_email" value="Support Email" />
                    <TextInput id="support_email" type="email" class="mt-1 block w-full" v-model="form.support_email" placeholder="Enter your email address" />
                    <InputError class="mt-2" :message="form.errors.support_email" />
                </div>

                <div>
                    <InputLabel for="contact_subtitle" value="Contact Form Subtitle" />
                    <TextInput id="contact_subtitle" type="text" class="mt-1 block w-full" v-model="form.contact_subtitle" placeholder="Fill out the form below and we'll get back to you soon" />
                    <InputError class="mt-2" :message="form.errors.contact_subtitle" />
                </div>

                <div>
                    <InputLabel for="contact_thankyou" value="Contact Form Thank-you Message" />
                    <TextInput id="contact_thankyou" type="text" class="mt-1 block w-full" v-model="form.contact_thankyou" placeholder="Your message has been sent successfully." />
                    <InputError class="mt-2" :message="form.errors.contact_thankyou" />
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

function submitContact() {
    form.post(route('settings.update'), {
        preserveScroll: true,
    });
}
</script> 