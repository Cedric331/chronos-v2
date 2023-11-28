<template>
    <Modal :show="show" @close="this.$emit('close')">
        <div class="py-6 px-9">
            <h2 :class="{'text-white' : $store.state.isDarkMode }" class="flex justify-center mb-5 text-2xl w-full">
                Demande de congés
            </h2>
            <hr class="my-4 dark:text-white">

            <div>
                <label :class="{'text-white' : $store.state.isDarkMode }" class="font-semibold block my-3 text-md">Type</label>
                <select v-model="type" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 transition ease-in-out m-0">
                    <option v-for="day in  type_days" :value="day">
                        {{day}}
                    </option>
                </select>
            </div>

            <div>
                <label :class="{'text-white' : $store.state.isDarkMode }" class="font-semibold block my-3 text-md">Commentaire</label>
                <textarea v-model="comment" rows="5" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 transition ease-in-out m-0"></textarea>
            </div>

            <hr class="my-6">

            <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm my-2">
                Vous êtes sur le point d'effectuer une demande de congés de <span class="font-bold">{{ days.length }}</span> jour{{ days.length > 1 ? 's' : '' }} pour les dates suivantes :
            </p>
            <section v-if="this.days" class="container pb-6 mx-auto h-full overflow-hidden overflow-y-auto">
                <div v-for="day in this.days" class="flex justify-around mt-2">
                    <div class=" rounded-lg p-1 flex text-white bg-gray-600 w-[80%] justify-center">
                        <p>{{ day.date_fr }}</p>
                    </div>
                    <svg @click="deleteDay(day)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b81" class="w-5 h-5 mt-2 mr-2 cursor-pointer hover:text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
            </section>
            <p :class="{'text-white' : $store.state.isDarkMode}" class="text-xs flex justify-center">
                Les jours qui ne sont pas travaillés ne sont pas pris en compte.
            </p>
            <br>
            <p :class="{'text-white' : $store.state.isDarkMode}" class="text-xs mb-4 flex justify-center">
                Cela générera une demande auprès de votre responsable qui devra valider vos congés.
            </p>

            <InputError :message="errors" :can-close="true" @close="this.errors = null"></InputError>

            <div class="flex justify-between">
                <SecondaryButton @click="this.$emit('close')" class="flex justify-center">
                    Fermer
                </SecondaryButton>
                <PrimaryButton @click.prevent="storePaidLeave()" class="flex justify-center">
                    Valider
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

export default {
    name: "ModalPaidLeave",
    emits: ['close', 'store', 'deleteDayList'],
    components: {InputError, SecondaryButton, PrimaryButton, Modal},
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        daysProps: {
            type: Array,
            required: true,
        }
    },
    data () {
        return {
            errors: null,
            days: [],
            type_days: [
                'Congés Payés',
                'Récup JF',
                'CP Matin',
                'CP Après-midi',
                'Congés sans solde'
                ],
            type: 'Congés Payés',
            comment: ''
        }
    },
    methods: {
        deleteDay (day) {
            this.$emit('deleteDayList', day)
            this.days = this.days.filter(d => d.id !== day.id)
        },
        storePaidLeave () {
            axios.post('/paidleave/store', {
                days: this.days,
                type: this.type,
                comment: this.comment
            }).then(() => {
                this.$notify({
                    type: 'success',
                    title: 'Demande de congé payé',
                    text: 'Votre demande de congé payé a bien été envoyé.',
                });
                this.$emit('close');
            }).catch(error => {
                this.errors = error.response.data.message;
                this.$notify({
                    type: 'error',
                    title: 'Demande de congé payé',
                    text: 'Une erreur est survenue lors de l\'envoie de votre demande de congé payé.',
                });
            })
        },
    },
    mounted() {
        if (this.daysProps) {
            this.days = this.daysProps.filter(day => {
                if (day.plannings.length === 0) {
                    return false;
                }
                return day.plannings[0].debut_journee;
            })
        }
    }
}
</script>

<style scoped>

</style>
