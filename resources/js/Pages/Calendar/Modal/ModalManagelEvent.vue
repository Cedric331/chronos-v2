<template>
    <Modal :show="showEvent" @close="this.$emit('close')">
        <div class="py-6 px-9">
            <h2 :class="{'text-white' : this.$store.state.isDarkMode }" class="flex justify-center mb-5 text-2xl w-full">
                Création d'un événement
            </h2>
            <hr class="my-4 dark:text-white">

            <p :class="{'text-white' : this.$store.state.isDarkMode}" class="text-sm">Vous pouvez créer des évènements sur les journées.</p><br>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="title" type="text" id="title" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                    <label for="title" class="absolute text-black text-sm duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">*Titre</label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <textarea v-model="description" type="text" id="description" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                    <label for="description" class="absolute text-sm text-black duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Description</label>
                </div>
            </div>

            <div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input v-model="check" type="checkbox" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'" >Créer l'event pour toute la team</span>
                </label>
            </div>
            <p class="mb-5 text-xs mt-1" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Obligatoire (*)</p>

            <InputError :message="errors" :can-close="true" @close="this.errors = null"></InputError>

            <div class="flex justify-between">
                <SecondaryButton @close="this.$emit('close')" class="flex justify-center">
                    Fermer
                </SecondaryButton>
                <PrimaryButton @click.prevent="store()" class="flex justify-center">
                    Créer
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
    name: "ModalManagelEvent",
    emits: ['close', 'store'],
    components: {InputError, SecondaryButton, PrimaryButton, Modal},
    props: {
        showEvent: {
            type: Boolean,
            required: true,
        },
        days: {
            type: Array,
            required: true,
        }
    },
    data () {
        return {
            errors: null,
            check: false,
            title: '',
            description: ''
        }
    },
    methods: {
        store() {
            axios.post('/event', {
                days: this.days,
                check: this.check,
                title: this.title,
                description: this.description
            }).then(response => {
                this.$emit('store', response.data)
            }).catch((error) => {
                this.errors = error.response.data.errors
            })
        }
    }
}
</script>

<style scoped>

</style>
