<template>
    <Modal :show="show" @close="this.$emit('close')">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm">
            <form class="py-8 px-8">
                <!-- Modern Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mr-4">
                            <i class="fas fa-plus-circle text-white text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">
                            Création de l'équipe
                        </h2>
                    </div>
                    <div class="h-1 w-20 mx-auto bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full"></div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-building text-indigo-500 mr-2"></i>{{ $t('name') }}
                        </label>
                        <div class="relative">
                            <input 
                                v-model="item.name" 
                                type="text" 
                                id="name" 
                                class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                                placeholder="Nom de l'équipe" 
                                required 
                            />
                        </div>
                    </div>

                    <!-- Department Code Field -->
                    <div>
                        <label for="code_departement" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-hashtag text-indigo-500 mr-2"></i>{{ $t('department_code') }}
                        </label>
                        <div class="relative">
                            <input 
                                v-model="item.code_departement" 
                                type="number" 
                                id="code_departement" 
                                class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                                placeholder="Code département (ex: 47)" 
                            />
                        </div>
                    </div>

                    <!-- Coordinator Field -->
                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user-tie text-indigo-500 mr-2"></i>Coordinateur de l'équipe
                        </label>
                        <div class="relative">
                            <select 
                                v-model="item.user_id" 
                                id="subject" 
                                class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 appearance-none cursor-pointer"
                            >
                                <option value="" disabled>Sélectionner un coordinateur</option>
                                <option v-for="user in coordinateursProps" :value="user.id">{{ user.name }}</option>
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
                        @click.prevent="storeTeam()" 
                        class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg transform hover:scale-105 transition-all duration-300"
                    >
                        <i class="fas fa-check mr-2"></i>Valider
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
import { Inertia } from '@inertiajs/inertia';
import Checkbox from "@/Components/Checkbox.vue";
import Dropdown from "@/Components/Dropdown.vue";

export default {
    name: "ModalTeam",
    emits: ['store-team', 'close'],
    components: {
        Dropdown,
        Checkbox,
        InputError,
        SecondaryButton,
        PrimaryButton,
        Modal
    },
    props: {
        show: Boolean,
        coordinateursProps: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            message: null,
            item: {
                name: null,
                code_departement: null
            },
            check: true
        };
    },
    methods: {
        async storeTeam() {
            this.message = null
            axios.post('/chronos-admin/team/store', {
                name: this.item.name,
                user_id: this.item.user_id,
                code_departement: this.item.code_departement,
                checked: this.checked
            }).then((response) => {
                this.$emit('store-team', response.data)
            }).catch((error) => {
                console.log(error)
                this.message = error.response.data.message
            })
        }
    },
    mounted () {
        this.message = null
    }
}
</script>
