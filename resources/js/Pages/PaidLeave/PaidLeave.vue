<template>
    <AuthenticatedLayout>
        <Head title="Gestion des congés payés" />
        <section class="p-6">
            <div class="bg-gray-300 dark:bg-gray-800 shadow p-4 2xl:col-span-2" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
                <div class="mb-4  flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            Gestion des congés
                        </h3>
                    </div>
                    <div class="flex-shrink-0">
                        <SecondaryButton>
                            Exporter les données
                        </SecondaryButton>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="align-middle inline-block min-w-full">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Demandeur
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Commentaire
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Période
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre de jours
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                </tr>
                                </thead>

                                <tbody class="bg-white dark:bg-gray-200 table-auto">
                                <tr v-for="(paidLeave, i) in paidLeaves" :class="[i % 2 === 0 ? 'bg-gray-100' : 'bg-gray-200']">
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                        {{ paidLeave.user.name }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                        {{ paidLeave.type }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        <div :id="'comment-' + i" v-if="paidLeave.comment">
                                            {{ paidLeave.comment.slice(0, 60) }} {{ paidLeave.comment.length > 60 ? '...' : '' }}
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        <div v-for="date in paidLeave.dates">
                                            {{ date }}
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ paidLeave.number_days }} {{ paidLeave.number_days > 1 ? 'jours' : 'jour' }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ paidLeave.status }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <!--            <ModalConfirm v-if="confirmDeleteUser" :title="title" :message="message" @delete-confirm="this.deleteUser()" @close="this.closeConfirm()"></ModalConfirm>-->
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
// import ModalConfirm from "@/Components/Modal/ModalConfirm.vue";
import tippy from "tippy.js";

export default {
    name: "PaidLeave",
    components: {
        SecondaryButton,
        Head,
        AuthenticatedLayout
    },
    props: {
        leavesProps: Object
    },
    data () {
      return {
        paidLeaves: null,
      }
    },
    mounted() {
        this.paidLeaves = this.$page.props.leavesProps
        setTimeout(() => {
            this.paidLeaves.forEach((paidLeave, i) => {
                tippy('#comment-' + i, {
                    placement: 'left',
                    content: paidLeave.comment,
                });
            })
        }, 100)
    }
}
</script>

<style scoped>

</style>
