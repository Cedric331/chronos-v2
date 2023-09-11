<template>
    <Modal :show="show" @close="this.$emit('close')">
        <form class="py-6 px-9">
            <h2 class="flex justify-center my-5 text-xl w-full" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">
                Création de la team
            </h2>
            <hr class="my-4">

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.name" type="text" id="name" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                    <label for="name"  class="absolute text-sm  duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                        {{ $t('name') }}
                    </label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.code_departement" type="number" id="code_departement" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="code_departement" class="absolute text-sm  duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                        {{ $t('department_code') }}
                    </label>
                </div>
            </div>

            <div class="mb-5">
                <label for="subject" class="text-sm font-bold leading-tight tracking-normal" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">Coordinateur de la team</label>
                <div class="relative mb-5 mt-2">
                    <select v-model="item.user_id" id="subject" class="text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-12 flex items-center text-lg border-gray-300 rounded border">
                        <option v-for="user in coordinateursProps" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>
            </div>

            <InputError :message="message" :canClose="true" @close="this.message = null"></InputError>

            <div class="flex justify-center">
                <SecondaryButton @click.prevent="storeTeam()" class="w-2/4 flex justify-center">
                    Valider
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
import { Inertia } from '@inertiajs/inertia';
import Checkbox from "@/Components/Checkbox.vue";
import Dropdown from "@/Components/Dropdown.vue";

export default {
    name: "ModalTeam",
    emits: ['store-team', 'close'],
    components: {
        Dropdown,
        Checkbox,
        InputError,
        SecondaryButton,
        PrimaryButton,
        Modal
    },
    props: {
        show: Boolean,
        coordinateursProps: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            message: null,
            item: {
                name: null,
                code_departement: null
            },
            check: true
        };
    },
    methods: {
        async storeTeam() {
            this.message = null
            axios.post('/chronos-admin/team/store', {
                name: this.item.name,
                user_id: this.item.user_id,
                code_departement: this.item.code_departement,
                checked: this.checked
            }).then((response) => {
                this.$emit('store-team', response.data)
            }).catch((error) => {
                console.log(error)
                this.message = error.response.data.message
            })
        }
    },
    mounted () {
        this.message = null
    }
}
</script>
