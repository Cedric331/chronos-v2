<template>
    <Modal :show="show" @close="this.$emit('close')">
        <form class="py-6 px-9">
            <h2 class="flex justify-center my-5 text-xl w-full" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">
                {{ this.link ? 'Modification' : 'Création' }} d'un lien
            </h2>

            <hr class="my-4 dark:text-white">

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.lien" type="text" id="lien" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="lien" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'" class="absolute text-sm duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Lien</label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <textarea v-model="item.description" rows="5" id="description" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="description" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'" class="absolute text-sm  duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Description</label>
                </div>
            </div>

            <InputError :message="message" :canClose="true" @close="this.message = null"></InputError>

            <div class="flex justify-center">
                <SecondaryButton v-if="this.link" @click.prevent="updateLink()" class="w-2/4 flex justify-center">
                    Modifier
                </SecondaryButton>
                <SecondaryButton v-else @click.prevent="storeLink()" class="w-2/4 flex justify-center">
                    Enregistrer
                </SecondaryButton>
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
        name: "ModalCreateLink",
        emits: ['store-link', 'update-link', 'close'],
        components: {
            InputError,
            SecondaryButton,
            PrimaryButton,
            Modal
        },
        props: {
            link: Object,
            show: Boolean
        },
        watch: {
            show () {
                this.message = null
                if (this.link) {
                    this.item = JSON.parse(JSON.stringify(this.link))
                    this.item.lien = JSON.parse(JSON.stringify(this.link.link))
                } else {
                    this.item = {
                        lien: null,
                        description: null,
                    }
                }
            }
        },
        data() {
            return {
                message: null,
                item: {
                    lien: null,
                    description: null
                },
            };
        },
        methods: {
            storeLink () {
                axios.post('/links', {
                    lien: this.item.lien,
                    description: this.item.description
                })
                    .then(response => {
                        this.$emit('store-link', response.data)
                    })
                    .catch(() => {
                        this.message = 'Vous devez indiquer un lien valide'
                    })
            },
            updateLink() {
                axios.patch('/links/' + this.item.id, {
                    id: this.item.id,
                    lien: this.item.lien,
                    description: this.item.description
                })
                    .then(response => {
                        this.$emit('update-link', response.data)
                    })
                    .catch(() => {
                        this.message = 'Vous devez indiquer un lien valide'
                    })
            }
        },
        mounted() {
            this.message = null
            if (this.link) {
                this.item = JSON.parse(JSON.stringify(this.link))
                this.item.lien = JSON.parse(JSON.stringify(this.link.link))
            }
        }
    }
</script>

<style scoped>

</style>
