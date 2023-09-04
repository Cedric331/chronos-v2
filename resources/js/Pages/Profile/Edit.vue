<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import UpdateLinkShare from "@/Pages/Profile/Partials/UpdateLinkShare.vue";

defineProps({
    links: Object,
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>

        <div class="py-12" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div v-if="$page.props.auth.team.params.share_link_planning" class="p-4 sm:p-8 bg-gray-300 dark:bg-gray-800 shadow sm:rounded-lg" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
                    <UpdateLinkShare :links="links" class="max-w-7xl" />
                </div>

                <div class="p-4 sm:p-8 dark:bg-gray-800 bg-gray-300 shadow sm:rounded-lg" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 dark:bg-gray-800 bg-gray-300 shadow sm:rounded-lg" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 dark:bg-gray-800 bg-gray-300 shadow sm:rounded-lg" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
