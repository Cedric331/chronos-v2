<template>
    <Modal :show="showUser" @close="this.$emit('close')">
        <form class="py-6 px-9">
            <h2 class="flex justify-center my-5 text-xl w-full text-gray-400">
              {{ this.isUpdate ? "Modification du conseiller " +  item.name : "Ajout d'un conseiller" }}
            </h2>
            <hr class="my-4 dark:text-white">
            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.name" type="text" id="name" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                    <label for="name" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Nom</label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.email" type="text" id="email" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                    <label for="email" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Email</label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.birthday" type="date" id="birthday" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="birthday" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Anniversaire</label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.phone" type="text" id="phone" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="phone" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Téléphone</label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <select v-model="item.hub_id" id="hub" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        <option v-for="hub in $page.props.hubs" :key="hub" :value="hub.id">{{ hub.name }}</option>
                    </select>
                    <label for="hub" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Hub</label>
                </div>
            </div>
            <InputError :message="message" :canClose="true" @close="this.message = null"></InputError>

            <div class="flex justify-center">
                <SecondaryButton @click.prevent="actionUser()" class="w-2/4 flex justify-center">
                    {{ this.isUpdate ? "Modifier" : "Créer" }}
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
    name: "ModalUserUpdate",
    components: {
        InputError,
        SecondaryButton,
        PrimaryButton,
        Modal
    },
    props: {
        user: Object,
        isUpdate: Boolean,
        showUser: Boolean
    },
    watch: {
        showUser () {
            if (this.isUpdate) {
                this.item = this.user
            } else {
                this.item = {
                    name: null,
                    email: null,
                    birthday: null,
                    phone: null,
                    hub_id: null
                }
            }
        }
    },
    data() {
        return {
            message: null,
            item: {
                name: null,
                email: null,
                birthday: null,
                phone: null,
                hub_id: null
            },
        };
    },
    methods: {
        actionUser() {
            if (this.isUpdate) {
                this.updateUser()
            } else {

            }
        },
        updateUser() {
            console.log(this.item.name)
            axios.patch('/user/' + this.item.id, {
                name: this.item.name,
                hub: this.item.hub_id,
                birthday: this.item.birthday,
                phone: this.item.phone,
                email: this.item.email
            })
            .then(response => {

            })
            .catch(error => {
                this.message = error.response.data.message
            })
        }
    },
    mounted() {
        if (this.isUpdate) {
            this.item = this.user
        }
    }
}
</script>
