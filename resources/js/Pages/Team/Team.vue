<template>
    <AuthenticatedLayout>
                <div id="main-content" class="h-full w-full relative overflow-y-auto" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
                    <main>
                        <div class="pt-6 px-4">
                            <div class="w-full grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-4 gap-4 mb-10">
                                <TeamGestion :team="team"></TeamGestion>
                                <TeamUser :users="team.users"></TeamUser>
                            </div>
                            <div class="w-full grid grid-cols-1 gap-4 mb-10">
                                <TeamRotation :team="team"></TeamRotation>
                            </div>
                            <div v-if="this.team.params.module_alert" class="w-full grid grid-cols-1 gap-4 mb-10">
                                <TeamHoraire :team="team" :schedules="schedules"></TeamHoraire>
                            </div>
                            <div>
                                <Log :team="this.team"></Log>
                            </div>
                        </div>
                    </main>
                </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Calendar from "@/Pages/Calendar/Calendar.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ModalUserUpdate from "@/Pages/Team/Modal/ModalUser.vue";
import TeamUser from "@/Pages/Team/Components/TeamUser.vue";
import TeamGestion from "@/Pages/Team/Components/TeamGestion.vue";
import TeamRotation from "@/Pages/Team/Components/TeamRotation.vue";
import TeamHoraire from "@/Pages/Team/Components/TeamHoraire.vue";
import Log from "@/Pages/Team/Components/Log.vue";

export default {
    name: 'Team',
    components: {
        Log,
        TeamHoraire,
        TeamRotation,
        TeamGestion,
        TeamUser,
        ModalUserUpdate,
        SecondaryButton,
        PrimaryButton,
        Calendar,
        AuthenticatedLayout,
        Head
    },
    props: {
        team: Object,
        schedules: Object
    }
}
</script>
