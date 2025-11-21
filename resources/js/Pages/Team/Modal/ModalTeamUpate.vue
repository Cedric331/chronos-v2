<template>
    <Modal :show="show" @close="this.$emit('close')">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm">
            <form class="py-8 px-8">
                <!-- Modern Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mr-4">
                            <i class="fas fa-edit text-white text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">
                            {{ $t('team_gestion.modalTeamUpdate.title') }}
                        </h2>
                    </div>
                    <div class="h-1 w-20 mx-auto bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full"></div>
                </div>
                <!-- Form Fields -->
                <div class="space-y-6">
                    <!-- Logo Upload -->
                    <div v-if="$page.props.config.logo">
                        <label for="logo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-image text-indigo-500 mr-2"></i>Logo de l'équipe
                        </label>
                        <div class="relative">
                            <input 
                                @change="handleFileUpload" 
                                type="file" 
                                id="logo" 
                                class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300" 
                                accept="image/jpeg,image/png,image/gif" 
                            />
                        </div>
                    </div>

                    <!-- Color Adaptation Toggle -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                        <label class="relative inline-flex items-center cursor-pointer w-full">
                            <input v-model="check" type="checkbox" class="sr-only" />
                            <div class="relative w-14 h-7 flex-shrink-0">
                                <div 
                                    class="w-full h-full rounded-full transition-all duration-300"
                                    :class="check ? 'bg-gradient-to-r from-indigo-500 to-purple-600' : 'bg-gray-300 dark:bg-gray-600'"
                                >
                                    <div 
                                        class="absolute top-0.5 w-6 h-6 bg-white rounded-full shadow-lg transform transition-transform duration-300 ease-in-out"
                                        :class="check ? 'left-7' : 'left-1'"
                                    ></div>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Adapter les couleurs du fond par rapport au logo (mode light)</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Attention, les couleurs générées via le logo peuvent parfois rendre les informations plus difficile à lire.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-building text-indigo-500 mr-2"></i>{{ $t('name') }}
                        </label>
                        <input 
                            v-model="item.name" 
                            type="text" 
                            id="name" 
                            class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                            placeholder="Nom de l'équipe" 
                            required 
                        />
                    </div>

                    <!-- Department Field -->
                    <div>
                        <label for="departement" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-map-marker-alt text-indigo-500 mr-2"></i>{{ $t('department') }}
                        </label>
                        <input 
                            v-model="item.departement" 
                            type="text" 
                            id="departement" 
                            class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                            placeholder="Département" 
                        />
                    </div>

                    <!-- Department Code Field -->
                    <div>
                        <label for="code_departement" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-hashtag text-indigo-500 mr-2"></i>{{ $t('department_code') }}
                        </label>
                        <input 
                            v-model="item.code_departement" 
                            type="number" 
                            id="code_departement" 
                            class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300" 
                            placeholder="Code département (ex: 47)" 
                        />
                    </div>

                    <!-- Coordinator Field -->
                    <div v-if="$page.props.auth.isResponsable">
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
                        @click.prevent="updateTeam()" 
                        class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg transform hover:scale-105 transition-all duration-300"
                    >
                        <i class="fas fa-save mr-2"></i>{{ $t('team_gestion.modalTeamUpdate.button') }}
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

export default {
    name: "ModalTeamUpdate",
    emits: ['update-team', 'close'],
    components: {
        Checkbox,
        InputError,
        SecondaryButton,
        PrimaryButton,
        Modal
    },
    props: {
        team: Object,
        show: Boolean,
        coordinateursProps: {
            type: Array,
            required: true
        },
    },
    data() {
        return {
            shouldReset: true,
            message: null,
            item: null,
            check: true
        };
    },
    methods: {
        async updateTeam() {
            try {
                const formData = new FormData()

                if (this.item.name !== null) {
                    formData.append('name', this.item.name)
                }
                if (this.item.departement !== null) {
                    formData.append('departement', this.item.departement)
                }
                if (this.item.code_departement !== null) {
                    formData.append('code_departement', this.item.code_departement)
                }
                if (this.item.user_id !== null) {
                    formData.append('user_id', this.item.user_id)
                }

                if (typeof this.item.logo === 'object') {
                    formData.append('logo', this.item.logo)
                }
                formData.append('checked', this.check)

                const response = await axios.post('/team/update/' + this.item.id, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                if (response.data.url) {
                    Inertia.visit(response.data.url);
                } else {
                    Inertia.reload();
                }

            } catch (error) {
                this.message = error.response.data.message
            }
        },
        handleFileUpload(event) {
            this.item.logo = event.target.files[0]
        }
    },
    mounted() {
        if (this.shouldReset) {
            this.item = JSON.parse(JSON.stringify(this.team));
        }
        this.message = null
        this.shouldReset = false;
    }

}
</script>

