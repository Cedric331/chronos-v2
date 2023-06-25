<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head><title>Mot de passe oublié</title></Head>
        <div class="min-h-screen w-full max-w-screen flex justify-center items-center">
            <div class="py-12 px-12 bg-white rounded-2xl shadow-xl z-20 md:w-auto xl:w-5/12 bg-opacity-80">
                <div class="flex justify-center">
                    <ApplicationLogo class="w-20 h-20 fill-current text-gray-500 mb-8" />
                </div>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Vous avez oublié votre mot de passe?
        </div>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Donnez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation de mot de passe qui vous permettra d'en choisir un nouveau.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="items-center flex justify-center mt-4">
                    <Link
                        :href="route('login')"
                        class="underline text-sm text-gray-600 mr-5 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Revenir à la page de connexion
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Envoyer le lien de réinitialisation
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
    </GuestLayout>
</template>


