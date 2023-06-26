<template>
    <Modal :show="show" @close="this.$emit('close')">
        <form class="py-6 px-9">
            <h2 class="flex justify-center my-5 text-xl w-full text-gray-400">
                Générer un lien de partage
            </h2>
            <hr class="my-4 dark:text-white">

            <div class="mb-5">
                <div class="relative">
                    <select v-model="timeshare" id="timeshare" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        <option v-for="time in times" :key="time" :value="time">{{ time }}</option>
                    </select>
                    <label for="timeshare" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Durée du lien</label>
                </div>
            </div>



            <div class="flex justify-center">
                <PrimaryButton @click.prevent="this.generateShare" class="w-2/4 flex justify-center">
                    Générer le lien
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    name: "ModalSharePlanning",
    components: {PrimaryButton, Modal},
    props: {
        show: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
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
        generateShare() {
            axios.post('/planning/share', {
                selected_time: this.timeshare,
            }).then((response) => {
               this.url = response.data.link
            }).catch((error) => {
                console.log(error);
            })
        }
    }
}
</script>

<style scoped>

</style>
