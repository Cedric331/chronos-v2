<template>
    <div class="bg-gray-300 dark:bg-gray-800 shadow rounded-lg p-4 2xl:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $t('team_user.titre') }}
                </h3>
            </div>
            <div class="flex-shrink-0">
                <SecondaryButton @click="createUser()">
                    {{ $t('team_user.buttonAdd') }}
                </SecondaryButton>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto rounded-lg">
                <div class="align-middle inline-block min-w-full">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $t('name') }}
                                </th>
                                <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $t('birthday') }}
                                </th>
                                <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-200">
                            <tr v-for="(user, i) in users">
                                <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                    {{ user.name }}
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                    {{ user.email }}
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ dateFormatFr(user.birthday) }}
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    <svg @click="editUser(user)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cursor-pointer text-blue-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                </div>
            </div>
        </div>
        <ModalUserUpdate :showUser="showUser"
                         :user="this.user"
                         @update-users="(data) => { this.handleUpdateUsers(data) }"
                         @close="closeUser()">
        </ModalUserUpdate>
    </div>
</template>

<script>
import ModalUserUpdate from "@/Pages/Team/Modal/ModalUserUpdate.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "TeamUser",
    components: {
        ModalUserUpdate,
        SecondaryButton,
        PrimaryButton,
        AuthenticatedLayout,
    },
    props: {
        users: Object
    },
    data() {
        return {
            showUser: false,
            user: null
        }
    },
    methods: {
        handleUpdateUsers(updatedUser) {
            const index = this.users.findIndex((user) => user.id === updatedUser.id)
            if (index !== -1) {
                this.users.splice(index, 1, updatedUser)
                this.$emit('update:users', this.users)
            }
            this.closeUser()
        },
        dateFormatFr (date) {
            if (date) {
                return this.$dateFormatFr(date);
            }
            return null
        },
        createUser () {
            this.user = null
            this.showUser = true
        },
        editUser (user) {
            this.user = user
            this.showUser = true
        },
        closeUser () {
            this.user = null
            this.showUser = false
        }
    }
}
</script>

<style scoped>

</style>
