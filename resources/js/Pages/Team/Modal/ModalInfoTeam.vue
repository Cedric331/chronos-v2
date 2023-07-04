<template>
    <Modal :show="showInfo" :max-width="'3xl'" @close="this.$emit('close')">
    <div class="bg-gray-300 dark:bg-gray-800 w-full 2xl:col-span-2 rounded-lg p-5 ">
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
        </div>
        <div v-if="$page.props.auth.team.params.share_link" class="flex flex-col mt-5">
            <div class="overflow-x-auto rounded-lg">
                <div class="align-middle inline-block min-w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                Lien
                            </th>
                            <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                Ajouté par
                            </th>
                            <th scope="col" class="p-2 text-xs font-medium text-gray-500 w-1/5 tracking-wider">
                                <PrimaryButton class="flex justify-center">
                                    Ajouter un lien
                                </PrimaryButton>
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-200">
                        <tr v-for="(link, i) in links" class="hover:bg-gray-200 dark:hover:bg-gray-400">
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                <a :href="link.link" target="_blank" class="text-[#70a1ff]">Accéder au lien</a>
                            </td>
                            <td class="p-4 whitespace-normal text-sm font-normal text-gray-900">
                                <div class="w-full">
                                    {{ link.description }}
                                </div>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                {{ link.user.name }}
                            </td>
                            <td class="p-4 flex items-center justify-center">
                                <svg v-if="link.user.id === $page.props.auth.user.id || $page.props.auth.isCoordinateur" @click="editUser(link)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#70a1ff" class="w-5 h-5 mr-5 cursor-pointer">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                <svg v-if="link.user.id === $page.props.auth.user.id || $page.props.auth.isCoordinateur" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b81" class="w-5 h-5 cursor-pointer">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
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
            users: [],
            links: []
        }
    },
    methods: {
        editUser (user) {

        },
        getData () {
          axios.get('/information')
              .then(response => {
                  this.users = response.data.users
                  this.links = response.data.links
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
