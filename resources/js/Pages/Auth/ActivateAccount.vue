<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";


const props =  defineProps({
    urlSigned: String,
    name: String,
    email: String
})


const form = useForm({
    email: props.email,
    name: props.name,
    password: '',
    password_confirmation: ''
});


const submit = () => {
    form.post(`${props.urlSigned}`, {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};



</script>

<template>
    <GuestLayout>
        <Head><title>Activation du compte</title></Head>

        <div class="min-h-screen w-full max-w-screen flex justify-center items-center">

            <div class="py-12 px-12 bg-white rounded-2xl shadow-xl z-20 md:w-auto xl:w-4/12 bg-opacity-80">
                <div class="flex justify-center">
                    <Link href="#">
                        <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
                    </Link>
                </div>
                <div>
                    <h1 class="flex justify-center my-4 text-lg">Bienvenue sur Chronos {{ props.name }} !</h1>
                    <p class="flex justify-center text-sm text-gray-600">Avant de commencer à utiliser Chronos, vous devez définir votre mot de passe.</p>
                </div>
            <form @submit.prevent="submit">

                <div class="mt-4">
                    <InputLabel for="password" value="Mot de passe" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirmer votre mot de passe" />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="flex items-center justify-end mt-4">

                    <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Terminer l'inscription
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
    </GuestLayout>
</template>
