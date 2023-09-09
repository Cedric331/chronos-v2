<template>
    <Modal :show="showContact" @close="this.$emit('close', false)">
        <h2 class="flex justify-center my-5 text-xl w-full" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">
            Formulaire de contact
        </h2>
        <form class="py-6 px-9">
            <label for="subject" class="text-sm font-bold leading-tight tracking-normal" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">Sujet</label>
            <div class="relative mb-5 mt-2">
                <select v-model="subject" id="subject" class="text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-12 flex items-center text-lg border-gray-300 rounded border">
                    <option value="Signaler un bug" selected>Signaler un bug</option>
                    <option value="Demander une amélioration">Demander une amélioration</option>
                </select>
            </div>

            <label for="text" class="text-sm font-bold" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">Message</label>
            <div class="relative mb-5 mt-2">
                <textarea id="text" v-model="text" rows="10" cols="10" class="text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full flex items-center text-sm border-gray-300 rounded border" placeholder="Votre message..." />
            </div>

            <div class="flex items-center justify-between w-full">
                <button class="focus:outline-none focus:ring-2 focus:ring-offset-2  focus:ring-gray-400 ml-3 bg-gray-100 transition duration-150 text-gray-600 ease-in-out hover:border-gray-400 hover:bg-gray-300 border rounded px-8 py-2 text-sm" @click="closeModal()">Annuler</button>
                <button @click.prevent="send()" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 inline-flex items-center">
                    <svg v-if="isLoading" class="inline mr-3 w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                    </svg>
                    Envoyer
                </button>
            </div>
        </form>

        <div v-if="errors"  class="flex justify-center my-5 w-full mt-2 sm:align-middle font-medium text-sm text-red-600">
            {{ errors }}
        </div>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
export default {
    name: "ModalContact",
    components: {Modal},
    props: {
        showContact: {
            type: Boolean,
            default: false
        }
    },
    data () {
        return {
            subject: 'Signaler un bug',
            isLoading: false,
            text: null,
            errors: null
        }
    },
    methods: {
        closeModal (value = false) {
            this.subject = 'Signaler un bug'
            this.text = null
            this.errors = null
            this.$emit('close', value)
        },
        send () {
            if (this.text === null || this.text === '') {
                this.errors = 'Vous devez indiquer le sujet et un message'
                return
            }
            this.errors = null
            this.isLoading = true
            axios.post('/contact', {
                subject: this.subject,
                text: this.text
            })
                .then(() => {
                    this.subject = 'Signaler un bug'
                    this.text = null
                    this.closeModal(true)
                })
                .catch(error => {
                    this.errors = error.response.data.error
                    console.log(error)
                })
                .finally(() => {
                    this.isLoading = false
                })
        }
    }
}
</script>

<style scoped>

</style>
