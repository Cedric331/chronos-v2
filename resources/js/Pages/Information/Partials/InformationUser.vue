<template>
    <div class="dark:bg-gray-800 bg-gray-300 shadow rounded-lg p-4 2xl:col-span-2" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    Information de la team
                </h3>
            </div>
            <div class="flex-shrink-0">
<!--                <SecondaryButton @click="createUser()">-->
<!--                    {{ $t('team_user.buttonAdd') }}-->
<!--                </SecondaryButton>-->
            </div>
        </div>
        <div class="align-middle inline-block w-full overflow-x-auto">
        <div>
            <table class="divide-y divide-gray-200 w-full">
                <thead class="bg-white dark:bg-gray-800">
                <tr>
                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('name') }}
                    </th>
                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                        Téléphone
                    </th>
                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                        {{ $t('birthday') }}
                    </th>
                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-200">
                <tr v-for="(user, i) in users" class="hover:bg-gray-200 dark:hover:bg-gray-400">
                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                        {{ user.name }}
                    </td>
                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                        {{ user.email }}
                    </td>
                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                        {{ user.phone }}
                    </td>
                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        {{ dateFormatFr(user.birthday) }}
                    </td>
                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        <div v-if="user.account_active">
                            Compte activé
                        </div>
                        <div v-else class="flex justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Invitation envoyée
                        </div>
                    </td>
                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900 flex justify-between">
                        <svg v-if="user.id === $page.props.auth.user.id || $page.props.auth.isCoordinateur" @click="editUser(user)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#70a1ff" class="w-5 h-5 cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
        <ModalUser :showUser="showModalUser"
                   :user="this.user"
                   @update-users="(data) => { this.handleUpdateUsers(data) }"
                   @close="closeUser()">
        </ModalUser>
</div>
</template>

<script>
import ModalUser from "@/Pages/Team/Modal/ModalUser.vue";

export default {
    name: "InformationUser",
    components: {ModalUser},
    props: {
        users: {
            type: Object,
            required: true
        }
    },
    data () {
        return {
            showModalUser: false,
            user: null,
        }
    },
    methods: {
        closeUser() {
            this.showModalUser = false;
            this.user = null;
        },
        handleUpdateUsers (data) {
            this.users.find((user, index) => {
                if (user.id === data.id) {
                    this.users[index] = data
                }
            })
            this.$notify({
                type: 'success',
                title: 'Succès',
                text: 'Utilisateur modifié avec succès',
            });
            this.closeUser()
        },
        editUser(user) {
            this.user = user;
            this.showModalUser = true;
        },
        dateFormatFr (date) {
            if (date) {
                return this.$dateFormatFr(date);
            }
            return null
        },
    }
}
</script>

<style scoped>

</style>
