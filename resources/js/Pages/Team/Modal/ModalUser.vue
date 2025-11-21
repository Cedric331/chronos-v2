<template>
    <Modal :show="showUser" @close="this.$emit('close')">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm">
            <form class="py-8 px-8">
                <!-- Modern Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mr-4">
                            <i :class="this.user ? 'fas fa-user-edit' : 'fas fa-user-plus'" class="text-white text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">
                            {{ this.user ? 'Modifier' : 'Ajouter' }} un utilisateur
                        </h2>
                    </div>
                    <div class="h-1 w-20 mx-auto bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full"></div>
                </div>
                <!-- Form Fields -->
                <div class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user text-indigo-500 mr-2"></i>Nom Prénom
                        </label>
                        <input 
                            v-model="item.name" 
                            type="text" 
                            id="name" 
                            class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                            placeholder="Nom et prénom de l'utilisateur" 
                            required 
                        />
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-envelope text-indigo-500 mr-2"></i>Email
                        </label>
                        <input 
                            v-model="item.email" 
                            type="email" 
                            id="email" 
                            class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                            placeholder="email@exemple.com" 
                            required 
                        />
                    </div>

                    <!-- Birthday Field -->
                    <div>
                        <label for="birthday" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-birthday-cake text-indigo-500 mr-2"></i>Anniversaire
                        </label>
                        <input 
                            v-model="item.birthday" 
                            type="date" 
                            id="birthday" 
                            class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                        />
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-phone text-indigo-500 mr-2"></i>Téléphone
                        </label>
                        <input 
                            v-model="item.phone" 
                            type="tel" 
                            id="phone" 
                            class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                            placeholder="06 12 34 56 78" 
                        />
                    </div>

                    <!-- Role Field (Admin only) -->
                    <div v-show="$page.props.auth.isAdmin" v-if="$page.props.auth.isAdmin">
                        <label for="role" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user-shield text-indigo-500 mr-2"></i>Rôle
                        </label>
                        <div class="relative">
                            <select 
                                v-model="item.role" 
                                id="role" 
                                class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 appearance-none cursor-pointer"
                            >
                                <option value="Administrateur">Administrateur</option>
                                <option value="Responsable">Responsable</option>
                                <option value="Coordinateur">Coordinateur</option>
                                <option value="Conseiller">Conseiller</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Access Toggle (Admin only) -->
                    <div v-show="$page.props.auth.isAdmin" v-if="$page.props.auth.isAdmin" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                        <label class="relative inline-flex items-center cursor-pointer w-full">
                            <Checkbox v-model="item.hasAccessAdmin" :checked="item.hasAccessAdmin"></Checkbox>
                            <div class="ml-4 flex-1">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Accès administrateur</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Permet de donner des autorisations d'administrateur sans avoir le rôle.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Hub Field (Coordinateur only) -->
                    <div v-if="$page.props.config.active && $page.props.auth.isCoordinateur">
                        <label for="hub" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-building text-indigo-500 mr-2"></i>Hub
                        </label>
                        <div class="relative">
                            <select 
                                v-model="item.team_id" 
                                id="hub" 
                                class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 appearance-none cursor-pointer"
                            >
                                <option value="" disabled>Sélectionner un hub</option>
                                <option v-for="team in $page.props.teams" :key="team" :value="team.id">{{ team.name }}</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <InputError :message="message" :canClose="true" @close="this.message = null" class="mt-6"></InputError>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <SecondaryButton 
                        @click.prevent="this.$emit('close')" 
                        class="px-6 py-3 rounded-xl font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-all duration-300"
                    >
                        Annuler
                    </SecondaryButton>
                    <SecondaryButton 
                        v-if="this.user" 
                        @click.prevent="updateUser()" 
                        class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg transform hover:scale-105 transition-all duration-300"
                    >
                        <i class="fas fa-save mr-2"></i>Modifier
                    </SecondaryButton>
                    <SecondaryButton 
                        v-else 
                        @click.prevent="storeUser()" 
                        class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg transform hover:scale-105 transition-all duration-300"
                    >
                        <i class="fas fa-check mr-2"></i>Enregistrer
                    </SecondaryButton>
                </div>
            </form>
        </div>
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
                console.log(error)
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
