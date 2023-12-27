<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Link, router, useForm, usePage} from '@inertiajs/vue3';
import {ref} from "vue";

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    avatar: user.avatar,
});

const imagePreview = ref(user.avatar);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.avatar = file;

        imagePreview.value = URL.createObjectURL(file);
    }
};

function submitForm () {
    router.post(route('profile.update'),{
        _method: 'patch',
        name: form.name,
        email: form.email,
        avatar: form.avatar,
    });
}


</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Information sur le profil</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Mettez à jour les informations de votre profil et l'adresse email de votre compte.
            </p>
        </header>

        <form @submit.prevent="submitForm()" class="mt-6 space-y-6">
            <div class="flex justify-start">
                <div class="w-1/6">
                    <div v-if="imagePreview" tabindex="0" class="focus:outline-none h-14 w-14 mb-4 lg:mb-0 mr-4">
                        <img :src="imagePreview" class="h-full w-full rounded-full overflow-hidden shadow" alt="Avatar preview">
                    </div>
                    <div v-else tabindex="0" class="focus:outline-none h-14 w-14 mb-4 lg:mb-0 mr-4">
                        <img :src="form.avatar" class="h-full w-full rounded-full overflow-hidden shadow" alt="Avatar">
                    </div>
                </div>
                <div class="w-5/6">
                    <InputLabel for="avatar" value="Avatar" />

                    <input @change="handleFileChange"
                           class="mt-1 block w-full border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                           id="file_input"
                           accept="image/png, image/jpeg, image/jpg"
                           type="file">

                    <InputError class="mt-2" :message="form.errors.avatar" />
                </div>
            </div>

            <div>
                <InputLabel for="name" value="Nom" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="props.mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >
                        Cliquez ici pour renvoyer l'e-mail de vérification.
                    </Link>
                </p>

                <div
                    v-show="props.status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600 dark:text-green-400"
                >
                    Un nouveau lien de vérification a été envoyé à votre adresse électronique.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600">Enregistrement effectué</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
