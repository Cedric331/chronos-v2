<template>
    <Head :title="$t('nav.management')" />

    <AuthenticatedLayout>
        <div>
            <div>
                <div id="main-content" class="h-full w-full bg-gray-50 relative overflow-y-auto">
                    <main>
                        <div class="pt-6 px-4">
                            <div class="w-full grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-4 gap-4 mb-10">
                                <TeamGestion :team="team"></TeamGestion>
                                <TeamUser :users="team.users"></TeamUser>
                            </div>
                            <div class="w-full grid grid-cols-1 gap-4 mb-10">
                                <TeamRotation :team="team"></TeamRotation>
                            </div>
                            <div class="w-full grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-4">
                                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">2,340</span>
                                            <h3 class="text-base font-normal text-gray-500">New products this week</h3>
                                        </div>
                                        <div class="ml-5 w-0 flex items-center justify-end flex-1 text-green-500 text-base font-bold">
                                            14.6%
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">5,355</span>
                                            <h3 class="text-base font-normal text-gray-500">Visitors this week</h3>
                                        </div>
                                        <div class="ml-5 w-0 flex items-center justify-end flex-1 text-green-500 text-base font-bold">
                                            32.9%
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">385</span>
                                            <h3 class="text-base font-normal text-gray-500">User signups this week</h3>
                                        </div>
                                        <div class="ml-5 w-0 flex items-center justify-end flex-1 text-red-500 text-base font-bold">
                                            -2.7%
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 2xl:grid-cols-2 xl:gap-4 my-4">
                                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                    <h3 class="text-xl leading-none font-bold text-gray-900 mb-10">Acquisition Overview</h3>
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
import ModalUserUpdate from "@/Pages/Team/Modal/ModalUserUpdate.vue";
import TeamUser from "@/Pages/Team/Components/TeamUser.vue";
import TeamGestion from "@/Pages/Team/Components/TeamGestion.vue";
import TeamRotation from "@/Pages/Team/Components/TeamRotation.vue";

export default {
    name: 'Team',
    components: {
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
        team: Object
    }
}
</script>
