<template>
    <Modal ref="modalDark" :show="show" @close="this.$emit('close')">
        <form class="py-6 px-9">
            <h2 :class="{'text-white' : $store.state.isDarkMode }" class="flex justify-center my-5 text-2xl w-full">
                Générer un lien de partage
            </h2>
            <hr class="my-4 dark:text-white">

            <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm">Ceci permet de partager votre planning avec une ou plusieurs personne de votre choix avec durée de validité.</p><br>
            <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm">Vous pouvez gérer les liens créés directement dans votre compte dans la rubrique "Mes informations".</p><br>
            <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm mb-5">Attention, vous ne pouvez générer que 5 liens.</p>

            <div v-if="url" class="m-5">
                <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm">Voici votre lien : </p>
                <div class="flex justify-between">
                    <p :class="{'text-white' : $store.state.isDarkMode}" class="text-sm mt-2">{{ url }}</p>
                    <SecondaryButton @click.prevent="copyUrlToClipboard">Copier URL</SecondaryButton>
                </div>
            </div>
            <div v-else class="mb-5">
                <div class="relative">
                    <select v-model="timeshare" id="timeshare" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        <option v-for="time in times" :key="time" :value="time">{{ time }}</option>
                    </select>
                    <label for="timeshare" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Durée du lien</label>
                </div>
            </div>

            <InputError :message="errors" :can-close="true" @close="this.errors = null"></InputError>

            <div class="flex justify-center">
                <PrimaryButton v-if="url" @close="this.$emit('close')" class="w-2/4 flex justify-center">
                    Fermer
                </PrimaryButton>
                <PrimaryButton v-else @click.prevent="this.generateShare" class="w-2/4 flex justify-center">
                    Générer
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";

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
            url: null
        }
    },
    methods: {
        copyUrlToClipboard() {
            navigator.clipboard.writeText(this.url).then(() => {
                this.$notify({
                    title: "Succès",
                    type: "success",
                    text: "Lien copié avec succès!",
                });
            }).catch(err => {
                console.log('Erreur lors de la copie de l\'URL: ', err);
            });
        },
        generateShare() {
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
                this.errors = error.response.data.errors;
            })
        }
    }
}
</script>

<style scoped>

</style>
