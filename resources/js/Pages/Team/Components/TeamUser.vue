<template>
    <Loading v-if="isLoading" :show="isLoading" :messageLoading="'Envoi de l\'invitation en cours'" />
    <div class="p-6">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 flex items-center">
                    <i class="fas fa-users text-indigo-500 dark:text-indigo-400 mr-2"></i>
                    {{ $t('team_user.titre') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400" v-if="users">
                    {{ users.length }} membre<span v-if="users.length !== 1">s</span> dans l'équipe
                </p>
            </div>
            <div>
                <SecondaryButton
                    @click="createUser()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-sm transition-all duration-200 transform hover:scale-105 flex items-center"
                >
                    <i class="fas fa-user-plus mr-2"></i> {{ $t('team_user.buttonAdd') }}
                </SecondaryButton>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-sm transition-all duration-300 hover:shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
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
                                Anniversaire
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Rôle
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>

                        <tbody v-if="users" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="(user, i) in users">
                                <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center justify-center">
                                        <img :src="user.avatar" class="h-10 w-10 rounded-full object-cover border-2 border-indigo-100 dark:border-indigo-900 shadow-sm" alt="Avatar">
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                                        {{ user.name }}
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        {{ user.email }}
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-birthday-cake text-pink-500 mr-2"></i>
                                        <span v-if="user.birthday">{{ dateFormatFr(user.birthday) }}</span>
                                        <span v-else class="text-gray-400 dark:text-gray-200 italic">Non défini</span>
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-normal">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                                        'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300': user.role === 'Administrateur',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': user.role === 'Coordinateur',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': user.role === 'Conseiller'
                                    }">
                                        <i class="mr-1" :class="{
                                            'fas fa-crown': user.role === 'Administrateur',
                                            'fas fa-user-tie': user.role === 'Coordinateur',
                                            'fas fa-user': user.role === 'Conseiller'
                                        }"></i>
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-normal">
                                    <div v-if="user.account_active" class="flex items-center justify-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                    <div v-else>
                                        <div v-if="user.CanResendInvitation">
                                            <button @click.prevent="sendInvitation(user)" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700 transition-colors duration-200">
                                                <i class="fas fa-paper-plane mr-1.5"></i> Renvoyer
                                            </button>
                                        </div>
                                        <div v-else class="flex items-center justify-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm font-normal">
                                    <div class="flex items-center space-x-3 justify-center">
                                        <button @click="editUser(user)" class="p-1.5 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button v-if="$page.props.auth.user.id !== user.id" @click="confirmDelete(user)" class="p-1.5 rounded-full bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
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
import Loading from "@/Components/Loading.vue";

export default {
    name: "TeamUser",
    components: {
        Loading,
        ModalConfirm,
        ModalUser,
        SecondaryButton,
        PrimaryButton,
        AuthenticatedLayout,
    },
    props: {
        usersProps: Object
    },
    data() {
        return {
            isLoading: false,
            confirmDeleteUser: false,
            showUser: false,
            title: '',
            message: '',
            users: null,
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
                const index = Object.keys(this.users).find(key => this.users[key].id === this.user.id);

                if (index) {
                    delete this.users[index];
                    this.$emit('update:users', this.users);
                }

            } else {
                const index = Object.keys(this.users).find(key => this.users[key].id === this.user.id);

                if (index) {
                    this.users[index] = updatedUser;
                    this.$emit('update:users', this.users);
                }
            }
            this.closeUser()
            this.$notify({
                type: 'success',
                title: 'Succès',
                text: 'Utilisateur modifié avec succès'
            })
        },
        sendInvitation (user) {
            this.isLoading = true
            axios.post('/user/invitation', {
                id: user.id
            })
            .then((response) => {
                const updatedUser = response.data;
                const userIndex = this.users.findIndex((item) => item.id === user.id);
                if (userIndex !== -1) {
                    // Mettre à jour l'utilisateur avec les données retournées par le backend
                    this.users[userIndex] = { ...this.users[userIndex], ...updatedUser };
                }
                this.$notify({
                    type: 'success',
                    title: 'Succès',
                    text: 'Invitation envoyée avec succès'
                })
            })
            .catch(error =>  {
                console.log(error)
            })
            .finally(() => {
                this.isLoading = false
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
                  let errorMessage = 'Erreur lors de l\'envoi de l\'invitation.';
                  if (error.response && error.response.data && error.response.data.message) {
                      errorMessage = error.response.data.message;
                  } else if (error.message) {
                      errorMessage += ` Détail : ${error.message}`;
                  }
                  this.$notify({
                      type: 'error',
                      title: 'Échec',
                      text: errorMessage
                  });
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
    },
    mounted() {
        this.users = this.usersProps
    }
}
</script>

<style scoped>
/* Table hover effects */
.hover\:bg-gray-50:hover {
    background-color: rgba(249, 250, 251, 1);
}

.dark .dark\:hover\:bg-gray-700:hover {
    background-color: rgba(55, 65, 81, 1);
}

/* Button hover effects */
.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Avatar styling */
.rounded-full {
    border-radius: 9999px;
}

.object-cover {
    object-fit: cover;
}

/* Badge styling */
.inline-flex {
    display: inline-flex;
}

.items-center {
    align-items: center;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .p-6 {
        padding: 1rem;
    }

    table {
        display: block;
        overflow-x: auto;
    }
}
</style>
