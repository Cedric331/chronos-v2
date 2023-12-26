<template>
    <div class="bg-gray-300 dark:bg-gray-800 rounded-lg mb-4 p-4 sm:p-6 h-full shadow-md" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Gestion des rotations</h3>
            <div>
                <PrimaryButton @click="createPlanning()" :title="this.team.rotations.length === 0 ? 'Vous devez créer des rotations avant de pouvoir générer un planning' : ''" class="mr-5" :class="[this.team.rotations.length === 0 ? 'opacity-50 cursor-not-allowed' : '']" :disabled="this.team.rotations.length === 0">
                    {{ $t('team_rotation.button_planning') }}
                </PrimaryButton>
                <SecondaryButton @click="addRotation()">
                    {{ $t('team_rotation.button_rotation') }}
                </SecondaryButton>
            </div>
        </div>
        <hr>

        <div class="flow-root">
            <div class="divide-y divide-gray-200" style="max-height: 600px; overflow-y: auto;">
                <div v-for="rotation in this.team.rotations" :key="rotation.id" class="py-3 sm:py-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex justify-between">
                            <button @click="editRotation(rotation)" class="mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#70a1ff" class="w-5 h-5 text-blue-400 dark:text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            <button @click="deleteRotation(rotation)" class="mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#e74c3c" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                            <p class="text-md font-bold text-gray-900 truncate dark:text-white ">
                                {{ rotation.name }} - <span :class="[rotation.total_hours !== '35h00' ? 'text-red-600' : '']">({{ rotation.total_hours }})</span>
                            </p>
                        </div>
                        <div v-for="detail in rotation.details" class="hidden sm:block w-full">
                            <div class="text-center w-full ">
                                <div class="text-md font-bold font-medium text-gray-900 truncate dark:text-white flex items-center justify-center">
                                    {{ detail.day }}
                                    <div v-if="detail.technicien" title="Technicien">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                                        </svg>
                                    </div>
                                    <div v-if="detail.teletravail" title="Télétravail">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="detail.is_off" class="text-md font-bold font-medium text-gray-900 mx-auto truncate dark:text-white">
                                    {{ detail.is_off ? 'OFF' : '' }}
                                </p>
                                <p v-else  class="text-md font-bold font-medium text-gray-900 truncate dark:text-white">
                                    {{ detail.debut_journee }} - {{ detail.fin_journee }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ModalPlanning v-if="showPlanning" :showPlanning="showPlanning" :team="team" @close="this.showPlanning = false"></ModalPlanning>
        <ModalGestionRotation v-if="showRotation" :showRotation="showRotation" :rotation="rotation" :team_id="this.team.id" @storeRotation="(data, isUpdate) => {storeRotation(data, isUpdate)}" @close="this.showRotation = false; this.rotation = null"></ModalGestionRotation>
        <ModalConfirm v-if="confirm" @close="confirm = false" @delete-confirm="this.confirmDeleteRotation()" title="Supprimer la rotation" text="Êtes-vous sûr de vouloir supprimer la rotation ?" confirm="Oui, supprimer" cancel="Annuler"></ModalConfirm>
    </div>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ModalGestionRotation from "@/Pages/Team/Modal/ModalGestionRotation.vue";
import ModalPlanning from "@/Pages/Team/Modal/ModalPlanning.vue";
import ModalConfirm from "@/Components/Modal/ModalConfirm.vue";

export default {
    name: "TeamRotation",
    emits: ['rotation-deleted'],
    components: {
        ModalConfirm,
        ModalPlanning,
        ModalGestionRotation,
        SecondaryButton,
        PrimaryButton
    },
    props: {
        team: Object
    },
    data () {
        return {
            showRotation: false,
            showPlanning: false,
            confirm: false,
            item: null,
            rotation: null
        }
    },
    methods: {
        editRotation (rotation) {
            this.rotation = rotation
            this.showRotation = true
        },
        deleteRotation (rotation) {
            this.rotation = rotation
            this.confirm = true
        },
        confirmDeleteRotation () {
            axios.delete('/team/rotation/' + this.rotation.id)
                .then(response => {
                    this.$notify({
                        title: "Succès",
                        type: "success",
                        text: "Rotation supprimée avec succès!",
                    });
                    this.team.rotations = response.data
                })
                .catch(error => {
                    if (error.response.data.message) {
                        this.$notify({
                            title: "Erreur",
                            type: "error",
                            text: error.response.data.message,
                        });
                    } else {
                        this.$notify({
                            title: "Erreur",
                            type: "error",
                            text: "Une erreur est survenue lors de la suppression de la rotation!",
                        });
                    }
                })
                .finally(() => {
                    this.confirm = false
                })
        },
        storeRotation (data, isUpdate = false) {
            this.team.rotations = data
            this.showRotation = false
            if (isUpdate) {
                this.$notify({
                    title: "Succès",
                    type: "success",
                    text: "Rotation modifiée avec succès!",
                });
            } else {
                this.$notify({
                    title: "Succès",
                    type: "success",
                    text: "Rotation ajoutée avec succès!",
                });
            }
        },
        createPlanning () {
          this.showPlanning = true
        },
        addRotation () {
            this.showRotation = true
        }
    },
    mounted () {
        this.item = JSON.parse(JSON.stringify(this.team))
    }
}
</script>

<style scoped>

</style>
