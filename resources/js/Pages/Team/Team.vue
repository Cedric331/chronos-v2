<template>
    <AuthenticatedLayout>
        <div id="main-content" class="h-full relative overflow-y-auto"
             :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
            <div class="pt-6 px-4">
                <div class="inline-block border-b border-black dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="dark:text-gray-100" v-for="tab in tabs" :key="tab.id">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg"
                                    :id="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="{ 'border-transparent': activeTab !== tab.id, 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300': activeTab !== tab.id }">
                                {{ tab.name }}
                            </button>
                        </li>
                    </ul>
                </div>

                <div>
                    <div v-if="activeTab === 'team'" class="p-4 rounded-lg">
                        <div class="pt-6 px-4">
                            <div class="w-full grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-4 gap-4 mb-10">
                                <TeamGestion :team="team"></TeamGestion>
                                <TeamUser :users="team.users"></TeamUser>
                            </div>
                        </div>
                    </div>
                    <div v-if="activeTab === 'planning'" class="p-4 rounded-lg">
                        <div class="pt-6 px-4">
                            <div class="w-full grid grid-cols-1 gap-4">
                                <TeamRotation :team="team"></TeamRotation>
                            </div>
                        </div>
                    </div>
                    <div v-if="activeTab === 'module'" class="p-4 rounded-lg">
                        <div class="pt-6 px-4">
                            <div v-if="team.params.module_alert" class="w-full grid grid-cols-1 gap-4 mb-10">
                                <TeamHoraire :team="team" :schedules="schedules"></TeamHoraire>
                            </div>
                        </div>
                    </div>
                    <div v-if="activeTab === 'statistique'" class="p-4 rounded-lg">
                        <div class="pt-6 px-4">
                            <TeamStatistique :users="team.users"></TeamStatistique>
                        </div>
                    </div>
                    <div v-if="activeTab === 'log'" class="p-4 rounded-lg">
                        <div class="pt-6 px-4">
                            <Log :team="team"></Log>
                        </div>
                    </div>
                </div>
            </div>
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
import TeamStatistique from "@/Pages/Team/Components/TeamStatistique.vue";

export default {
    name: 'Team',
    components: {
        TeamStatistique,
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
    },
    data() {
        return {
            activeTab: 'team',
            baseTabs: [
                { id: 'team', name: 'Team' },
                { id: 'planning', name: 'Rotation & Planning' },
                { id: 'statistique', name: 'Statistique' },
                { id: 'log', name: 'Log de la team' }
            ]
        }
    },
    computed: {
        tabs() {
            if (this.team.params.module_alert) {
                return [
                    ...this.baseTabs.slice(0, 2),
                    { id: 'module', name: 'Module alerte' },
                    ...this.baseTabs.slice(2)
                ];
            }
            return this.baseTabs;
        }
    }
}
</script>
