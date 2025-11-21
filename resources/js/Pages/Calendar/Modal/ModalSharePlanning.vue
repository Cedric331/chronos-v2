<template>
    <Modal ref="modalDark" :show="show" @close="this.$emit('close')">
        <div class="py-6 px-6 sm:px-9">
            <!-- Header avec gradient -->
            <div class="mb-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mr-3">
                        <i class="fas fa-share-alt text-white text-2xl"></i>
                    </div>
                    <h2 :class="{'text-white' : $store.state.isDarkMode }" class="text-2xl font-bold">
                        Générer un lien de partage
                    </h2>
                </div>
                <div class="h-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full"></div>
            </div>

            <!-- Informations contextuelles -->
            <div class="mb-6 space-y-3">
                <div class="flex items-start p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5 mr-3"></i>
                    <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm flex-1">
                        Partagez votre planning avec une ou plusieurs personnes de votre choix avec une durée de validité personnalisable.
                    </p>
                </div>
                <div class="flex items-start p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                    <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 mt-0.5 mr-3"></i>
                    <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm flex-1">
                        <span class="font-semibold">Limite :</span> Vous pouvez générer un maximum de 5 liens. Gérez vos liens dans la rubrique "Mes informations".
                    </p>
                </div>
            </div>

            <!-- Lien généré -->
            <div v-if="url" class="mb-6 animate-fade-in">
                <label class="block text-sm font-semibold mb-3" :class="{'text-white' : $store.state.isDarkMode, 'text-gray-700' : !$store.state.isDarkMode}">
                    <i class="fas fa-link mr-2 text-indigo-600 dark:text-indigo-400"></i>
                    Votre lien de partage
                </label>
                <div class="relative group">
                    <div class="flex items-center bg-gray-50 dark:bg-gray-700 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all duration-200 overflow-hidden">
                        <div class="flex-1 px-4 py-3">
                            <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm font-mono break-all">{{ url }}</p>
                        </div>
                        <button
                            @click.prevent="copyUrlToClipboard"
                            class="px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white transition-all duration-200 flex items-center space-x-2 group-hover:shadow-lg"
                            :class="copied ? 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' : ''"
                        >
                            <i :class="copied ? 'fas fa-check' : 'fas fa-copy'" class="text-lg"></i>
                            <span class="font-semibold">{{ copied ? 'Copié !' : 'Copier' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Formulaire de génération -->
            <div v-else class="mb-6">
                <label for="timeshare" class="block text-sm font-semibold mb-3" :class="{'text-white' : $store.state.isDarkMode, 'text-gray-700' : !$store.state.isDarkMode}">
                    <i class="fas fa-clock mr-2 text-indigo-600 dark:text-indigo-400"></i>
                    Durée de validité du lien
                </label>
                <div class="relative">
                    <select 
                        v-model="timeshare" 
                        id="timeshare" 
                        class="w-full px-4 py-3 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-400 dark:hover:border-indigo-500"
                    >
                        <option v-for="time in times" :key="time" :value="time">{{ time }}</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Messages d'erreur -->
            <InputError :message="errors" :can-close="true" @close="this.errors = null"></InputError>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 justify-end mt-6">
                <SecondaryButton 
                    v-if="url"
                    @click="this.$emit('close')" 
                    class="w-full sm:w-auto"
                >
                    <i class="fas fa-times mr-2"></i>
                    Fermer
                </SecondaryButton>
                <PrimaryButton 
                    v-if="url"
                    @click.prevent="copyUrlToClipboard" 
                    class="w-full sm:w-auto bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700"
                >
                    <i class="fas fa-copy mr-2"></i>
                    Copier à nouveau
                </PrimaryButton>
                <PrimaryButton 
                    v-else 
                    @click.prevent="this.generateShare" 
                    class="w-full sm:w-auto bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700"
                    :disabled="isGenerating"
                >
                    <i v-if="!isGenerating" class="fas fa-magic mr-2"></i>
                    <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                    {{ isGenerating ? 'Génération...' : 'Générer le lien' }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import axios from "axios";

export default {
    name: "ModalSharePlanning",
    components: {InputError, SecondaryButton, PrimaryButton, Modal},
    props: {
        show: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            errors: null,
            times: [
                '1 jour',
                '1 semaine',
                '1 mois',
                '1 an',
            ],
            timeshare: '1 jour',
            url: null,
            copied: false,
            isGenerating: false
        }
    },
    methods: {
        copyUrlToClipboard() {
            navigator.clipboard.writeText(this.url).then(() => {
                this.copied = true;
                this.$notify({
                    title: "Succès",
                    type: "success",
                    text: "Lien copié avec succès!",
                });
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            }).catch(err => {
                console.log('Erreur lors de la copie de l\'URL: ', err);
                this.$notify({
                    title: "Erreur",
                    type: "error",
                    text: "Impossible de copier le lien",
                });
            });
        },
        generateShare() {
            this.isGenerating = true;
            this.errors = null;
            axios.post('/planning/share', {
                selected_time: this.timeshare,
            }).then((response) => {
               this.url = response.data.link
                this.$notify({
                    title: "Succès",
                    type: "success",
                    text: "Lien généré avec succès!",
                });
            }).catch((error) => {
                this.errors = error.response?.data?.errors || error.response?.data?.message || 'Une erreur est survenue';
            }).finally(() => {
                this.isGenerating = false;
            })
        }
    }
}
</script>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

/* Style personnalisé pour le select */
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

select:focus {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234f46e5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
}
</style>
