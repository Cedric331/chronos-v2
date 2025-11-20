<template>
    <div 
        class="user-card dark:bg-gray-800 bg-white shadow-lg rounded-xl p-6 border border-gray-200 dark:border-gray-700" 
        :class="$attrs.class"
        :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }"
    >
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="icon-container mr-4 hidden sm:flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Informations de l'équipe
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Consultez les informations des membres de votre équipe</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Avatar
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ $t('name') }}
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Téléphone
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ $t('birthday') }}
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                        <tr v-for="(user, i) in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
                            <td class="p-4 whitespace-nowrap">
                                <img :src="user.avatar" class="h-10 w-10 rounded-full overflow-hidden shadow-sm" alt="Avatar">
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ user.name }}
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ user.email }}
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ user.phone || '-' }}
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ dateFormatFr(user.birthday) || '-' }}
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm">
                                <span v-if="user.account_active" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Compte activé
                                </span>
                                <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Invitation envoyée
                                </span>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm">
                                <button 
                                    v-if="user.id === $page.props.auth.user.id || $page.props.auth.isCoordinateur" 
                                    @click="editUser(user)" 
                                    class="p-2 rounded-full text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200"
                                    aria-label="Modifier l'utilisateur"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="7" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="text-lg font-medium">Aucun membre</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <ModalUser 
            :showUser="showModalUser"
            :user="this.user"
            @update-users="(data) => { this.handleUpdateUsers(data) }"
            @close="closeUser()"
        />
    </div>
</template>

<script>
import ModalUser from "@/Pages/Team/Modal/ModalUser.vue";

export default {
    name: "InformationUser",
    inheritAttrs: false,
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
.user-card {
    transition: all 0.3s ease;
}

.user-card:hover {
    transform: translateY(-2px);
}

.icon-container {
    @apply p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300;
}
</style>
