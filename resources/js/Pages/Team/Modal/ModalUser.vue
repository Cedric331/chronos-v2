<template>
    <Modal :show="showUser" @close="this.$emit('close')">
        <form class="py-6 px-9">
            <h2 class="flex justify-center my-5 text-xl w-full" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">
                {{ this.user ? 'Modifier' : 'Ajouter' }} un utilisateur
            </h2>
            <hr class="my-4 dark:text-white">
            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.name" type="text" id="name" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                    <label for="name" class="absolute text-blue-500 text-sm duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Nom Prénom</label>
                </div>
            </div>


            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.email" type="text" id="email" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                    <label for="email" class="absolute text-sm text-blue-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Email</label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.birthday" type="date" id="birthday" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="birthday" class="absolute text-sm text-blue-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Anniversaire</label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.phone" type="text" id="phone" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="phone" class="absolute text-sm text-blue-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Téléphone</label>
                </div>
            </div>

            <div v-show="$page.props.auth.isAdmin" v-if="$page.props.auth.isAdmin" class="mb-5">
                <div class="relative">
                    <select v-model="item.role" id="role" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm bg-gray-50 border-0 border-b-2 border-gray-300 appearance-none text-black dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        <option value="Administrateur">Administrateur</option>
                        <option value="Responsable">Responsable</option>
                        <option value="Coordinateur">Coordinateur</option>
                        <option value="Conseiller">Conseiller</option>
                    </select>
                    <label for="role" class="absolute text-sm text-blue-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Rôle</label>
                </div>
            </div>

            <div v-show="$page.props.auth.isAdmin" v-if="$page.props.auth.isAdmin" class="mb-5">
                <div class="relative">
                    <checkbox v-model="item.hasAccessAdmin" :checked="item.hasAccessAdmin"></checkbox>
                    <label for="access" class="ml-4 text-sm text-blue-500 duration-300 transform z-10 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Accès administrateur</label>
                    <p class="text-xs" :class="[$store.state.isDarkMode ? 'text-white' : 'text-black']">Permets de donner des autorisations d'administrateur sans avoir le rôle.</p>
                </div>
            </div>

            <div v-if="$page.props.config.active && $page.props.auth.isCoordinateur" class="mb-5">
                <div class="relative">
                    <select v-model="item.team_id" id="hub" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm bg-gray-50 border-0 border-b-2 border-gray-300 appearance-none text-black dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        <option v-for="team in $page.props.teams" :key="team" :value="team.id">{{ team.name }}</option>
                    </select>
                    <label for="hub" class="absolute text-sm text-blue-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Hub</label>
                </div>
            </div>
            <InputError :message="message" :canClose="true" @close="this.message = null"></InputError>

            <div class="flex justify-center">
                <SecondaryButton v-if="this.user" @click.prevent="updateUser()" class="w-2/4 flex justify-center">
                    Modifier
                </SecondaryButton>
                <SecondaryButton v-else @click.prevent="storeUser()" class="w-2/4 flex justify-center">
                    Enregistrer
                </SecondaryButton>
            </div>
        </form>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
    name: "ModalUser",
    emits: ['store-users', 'update-users', 'close'],
    components: {
        Checkbox,
        InputError,
        SecondaryButton,
        PrimaryButton,
        Modal
    },
    props: {
        user: Object,
        showUser: Boolean
    },
    watch: {
        showUser () {
            this.message = null
            if (this.user) {
                this.item = JSON.parse(JSON.stringify(this.user))
            } else {
                this.item = {
                    name: null,
                    email: null,
                    birthday: null,
                    role: 'Conseiller',
                    hasAccessAdmin: false,
                    account_active: null,
                    phone: null,
                    team_id: this.$page.props.auth.user.team_id
                }
            }
        }
    },
    data() {
        return {
            message: null,
            item: {
                name: null,
                email: null,
                birthday: null,
                hasAccessAdmin: false,
                account_active: null,
                phone: null,
                team_id: null
            },
        };
    },
    methods: {
        storeUser () {
            axios.post('/user', {
                name: this.item.name,
                team_id: this.item.team_id,
                birthday: this.item.birthday,
                phone: this.item.phone,
                hasAccessAdmin: this.item.hasAccessAdmin,
                role: this.item.role,
                email: this.item.email
            })
                .then(response => {
                    this.$emit('store-users', response.data)
                })
                .catch(error => {
                    this.message = error.response.data.message
                })
        },
        updateUser() {
            axios.patch('/user/' + this.item.id, {
                id: this.item.id,
                name: this.item.name,
                team_id: this.item.team_id,
                birthday: this.item.birthday,
                phone: this.item.phone,
                hasAccessAdmin: this.item.hasAccessAdmin,
                role: this.item.role,
                email: this.item.email
            })
            .then(response => {
                this.$emit('update-users', response.data)
            })
            .catch(error => {
                this.message = error.response.data.message
            })
        }
    },
    mounted() {
        this.message = null
        if (this.user) {
            this.item = JSON.parse(JSON.stringify(this.user))
        }
    }
}
</script>
