<template>
    <div class="bg-gray-300 dark:bg-gray-800 shadow p-4 2xl:col-span-2" :style="{ backgroundColor: this.$store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
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
                                <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $t('name') }}
                                </th>
                                <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th v-if="$page.props.auth.isAdmin" scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                    Rôle
                                </th>
                                <th v-else scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $t('birthday') }}
                                </th>
                                <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
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
                                <td v-if="$page.props.auth.isAdmin" class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ user.role }}
                                </td>
                                <td v-else class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
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
                                    <svg @click="editUser(user)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#70a1ff" class="w-5 h-5 cursor-pointer">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    <svg v-if="$page.props.auth.user.id !== user.id" @click="confirmDelete(user)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b81" class="w-5 h-5 cursor-pointer">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                </div>
            </div>
        </div>
        <ModalUser :showUser="showUser"
                     :user="this.user"
                     @store-users="(data) => { this.handleStoreUsers(data) }"
                     @update-users="(data) => { this.handleUpdateUsers(data) }"
                     @close="closeUser()">
        </ModalUser>
        <ModalConfirm v-if="confirmDeleteUser" :title="title" :message="message" @delete-confirm="this.deleteUser()" @close="this.closeConfirm()"></ModalConfirm>
    </div>
</template>

<script>
import ModalUser from "@/Pages/Team/Modal/ModalUser.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ModalConfirm from "@/Components/Modal/ModalConfirm.vue";

export default {
    name: "TeamUser",
    components: {
        ModalConfirm,
        ModalUser,
        SecondaryButton,
        PrimaryButton,
        AuthenticatedLayout,
    },
    props: {
        users: Object
    },
    data() {
        return {
            confirmDeleteUser: false,
            showUser: false,
            title: '',
            message: '',
            user: null
        }
    },
    methods: {
        handleStoreUsers (newUser) {
            this.users.push(newUser)
            this.$emit('update:users', this.users)
            this.closeUser()
            this.$notify({
                type: 'success',
                title: 'Succès',
                text: 'Invitation envoyée avec succès'
            })
        },
        handleUpdateUsers(updatedUser) {
            if (!updatedUser.id) {
                const index = this.users.findIndex((user) => user.id === this.user.id)
                this.users.splice(index, 1)
                this.$emit('update:users', this.users)
            } else {
                const index = this.users.findIndex((user) => user.id === updatedUser.id)
                if (index !== -1) {
                    this.users.splice(index, 1, updatedUser)
                    this.$emit('update:users', this.users)
                }
            }
            this.closeUser()
            this.$notify({
                type: 'success',
                title: 'Succès',
                text: 'Utilisateur modifié avec succès'
            })
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
        confirmDelete (user) {
            this.user = user
            this.title = 'Suppression d\'un utilisateur'
            this.message = 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?'
            this.confirmDeleteUser = true
        },
        deleteUser () {
          axios.delete('/user/' + this.user.id)
              .then(() => {
                  const index = this.users.findIndex((user) => user.id === this.user.id)
                  this.users.splice(index, 1)
                  this.$notify({
                      type: 'success',
                      title: 'Succès',
                      text: 'Utilisateur supprimé avec succès'
                  })
              })
              .catch(error =>  {
                  console.log(error)
              })
              .finally(() => {
                  this.closeConfirm()
              })
        },
        closeConfirm () {
            this.user = null
            this.title = ''
            this.message = ''
            this.confirmDeleteUser = false
        },
        editUser (user) {
            if (!user.account_active) {
                this.$notify({
                    duration: 6000,
                    type: 'error',
                    title: 'Erreur',
                    text: 'Vous ne pouvez pas modifier un utilisateur qui n\'a pas activé son compte. En cas d\'erreur, veuillez supprimer l\'utilisateur et le recréer.'
                })
                return
            }
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
