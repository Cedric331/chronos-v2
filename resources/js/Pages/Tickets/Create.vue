<template>
    <Head title="Nouveau ticket" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nouveau ticket
                </h2>
                <Link :href="route('tickets.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Titre -->
                                <div class="col-span-2">
                                    <InputLabel for="title" value="Titre" />
                                    <TextInput
                                        id="title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.title" />
                                </div>

                                <!-- Catégorie -->
                                <div>
                                    <InputLabel for="category_id" value="Catégorie" />
                                    <select
                                        id="category_id"
                                        v-model="form.category_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="" disabled>Sélectionnez une catégorie</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.category_id" />
                                </div>

                                <!-- Priorité -->
                                <div>
                                    <InputLabel for="priority_id" value="Priorité" />
                                    <select
                                        id="priority_id"
                                        v-model="form.priority_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="" disabled>Sélectionnez une priorité</option>
                                        <option v-for="priority in priorities" :key="priority.id" :value="priority.id">
                                            {{ priority.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.priority_id" />
                                </div>

                                <!-- Pas d'assignation ni de tags -->

                                <!-- Description -->
                                <div class="col-span-2">
                                    <InputLabel for="description" value="Description" />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        class="mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-md shadow-sm"
                                        rows="6"
                                        required
                                    ></textarea>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Décrivez votre problème ou votre demande en détail. Incluez toutes les informations pertinentes.
                                    </p>
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <!-- Pas de pièces jointes -->
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <i class="fas fa-save mr-2"></i> Créer le ticket
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

export default {
    components: {
        Head,
        Link,
        AuthenticatedLayout,
        InputError,
        InputLabel,
        PrimaryButton,
        TextInput,
    },
    props: {
        statuses: Array,
        categories: Array,
        priorities: Array,
        can_manage: Boolean,
    },
    setup(props) {
        const form = useForm({
            title: '',
            description: '',
            category_id: '',
            priority_id: '',
        });

        const submit = () => {
            form.post(route('tickets.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                },
            });
        };

        return {
            form,
            submit,
        };
    },
};
</script>
