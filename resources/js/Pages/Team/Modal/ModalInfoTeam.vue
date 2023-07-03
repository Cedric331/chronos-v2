<template>
    <Modal :show="showInfo" :max-width="'3xl'" @close="this.$emit('close')">
    <div class="bg-gray-300 dark:bg-gray-800  2xl:col-span-2 rounded-lg">
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
                        <tr v-for="(user, i) in team.users" class="hover:bg-gray-200 dark:hover:bg-gray-400">
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
        </div>
    </div>
        <ModalUser :showUser="showUser"
                   :user="this.user"
                   @update-users="(data) => { this.handleUpdateUsers(data) }"
                   @close="closeUser()">
        </ModalUser>
    </Modal>
</template>

<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import ModalUser from "@/Pages/Team/Modal/ModalUser.vue";

export default {
    name: "ModalInfoTeam",
    components: {
        ModalUser,
        Modal,
        PrimaryButton,
        SecondaryButton
    },
    props: {
        showInfo: Boolean
    },
    data () {
        return {
            showUser: false,
            user: null,
            team: {
                users: []
            }
        }
    },
    methods: {
        editUser (user) {
            this.user = user
            this.showUser = true
        },
        getData () {
          axios.get('/information')
              .then(response => {
                  this.team = response.data
              })
              .catch(error => {
                  console.log(error)
              })
        },
        handleUpdateUsers (data) {
            this.team.users.find((user, index) => {
                if (user.id === data.id) {
                    this.team.users[index] = data
                }
            })
            this.closeUser()
        },
        closeUser () {
            this.user = null
            this.showUser = false
        },
        dateFormatFr (date) {
            if (date) {
                return this.$dateFormatFr(date);
            }
            return null
        },
    },
    mounted() {
        this.getData()
    }
}
</script>

<style scoped>

</style>
