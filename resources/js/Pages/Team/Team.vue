<template>
    <AuthenticatedLayout>
                <div id="main-content" class="h-full w-full relative overflow-y-auto" :style="{ backgroundColor: this.$store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
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
                            <div class="grid grid-cols-1 my-4">
                                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                    <h3 class="text-xl leading-none font-bold text-gray-900 mb-10">Log</h3>
                                    <div class="block w-full overflow-x-auto">
                                        <table class="items-center w-full bg-transparent border-collapse">
                                            <thead>
                                            <tr>
                                                <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Top Channels</th>
                                                <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Users</th>
                                                <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px"></th>
                                            </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                            <tr class="text-gray-500">
                                                <th class="border-t-0 px-4 align-middle text-sm font-normal whitespace-nowrap p-4 text-left">Organic Search</th>
                                                <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">5,649</td>
                                                <td class="border-t-0 px-4 align-middle text-xs whitespace-nowrap p-4">
                                                    <div class="flex items-center">
                                                        <span class="mr-2 text-xs font-medium">30%</span>
                                                        <div class="relative w-full">
                                                            <div class="w-full bg-gray-200 rounded-sm h-2">
                                                                <div class="bg-cyan-600 h-2 rounded-sm" style="width: 30%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-gray-500">
                                                <th class="border-t-0 px-4 align-middle text-sm font-normal whitespace-nowrap p-4 text-left">Referral</th>
                                                <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">4,025</td>
                                                <td class="border-t-0 px-4 align-middle text-xs whitespace-nowrap p-4">
                                                    <div class="flex items-center">
                                                        <span class="mr-2 text-xs font-medium">24%</span>
                                                        <div class="relative w-full">
                                                            <div class="w-full bg-gray-200 rounded-sm h-2">
                                                                <div class="bg-orange-300 h-2 rounded-sm" style="width: 24%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-gray-500">
                                                <th class="border-t-0 px-4 align-middle text-sm font-normal whitespace-nowrap p-4 text-left">Direct</th>
                                                <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">3,105</td>
                                                <td class="border-t-0 px-4 align-middle text-xs whitespace-nowrap p-4">
                                                    <div class="flex items-center">
                                                        <span class="mr-2 text-xs font-medium">18%</span>
                                                        <div class="relative w-full">
                                                            <div class="w-full bg-gray-200 rounded-sm h-2">
                                                                <div class="bg-teal-400 h-2 rounded-sm" style="width: 18%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-gray-500">
                                                <th class="border-t-0 px-4 align-middle text-sm font-normal whitespace-nowrap p-4 text-left">Social</th>
                                                <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">1251</td>
                                                <td class="border-t-0 px-4 align-middle text-xs whitespace-nowrap p-4">
                                                    <div class="flex items-center">
                                                        <span class="mr-2 text-xs font-medium">12%</span>
                                                        <div class="relative w-full">
                                                            <div class="w-full bg-gray-200 rounded-sm h-2">
                                                                <div class="bg-pink-600 h-2 rounded-sm" style="width: 12%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-gray-500">
                                                <th class="border-t-0 px-4 align-middle text-sm font-normal whitespace-nowrap p-4 text-left">Other</th>
                                                <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">734</td>
                                                <td class="border-t-0 px-4 align-middle text-xs whitespace-nowrap p-4">
                                                    <div class="flex items-center">
                                                        <span class="mr-2 text-xs font-medium">9%</span>
                                                        <div class="relative w-full">
                                                            <div class="w-full bg-gray-200 rounded-sm h-2">
                                                                <div class="bg-indigo-600 h-2 rounded-sm" style="width: 9%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-gray-500">
                                                <th class="border-t-0 align-middle text-sm font-normal whitespace-nowrap p-4 pb-0 text-left">Email</th>
                                                <td class="border-t-0 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4 pb-0">456</td>
                                                <td class="border-t-0 align-middle text-xs whitespace-nowrap p-4 pb-0">
                                                    <div class="flex items-center">
                                                        <span class="mr-2 text-xs font-medium">7%</span>
                                                        <div class="relative w-full">
                                                            <div class="w-full bg-gray-200 rounded-sm h-2">
                                                                <div class="bg-purple-500 h-2 rounded-sm" style="width: 7%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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

export default {
    name: 'Team',
    components: {
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
