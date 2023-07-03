<template>
    <Modal :show="show" @close="this.$emit('close')">
        <form class="py-6 px-9">
            <h2 class="flex justify-center my-5 text-xl w-full text-gray-400">
                {{ $t('team_gestion.modalTeamUpdate.title') }}
            </h2>
            <hr class="my-4">
            <div v-if="$page.props.config.logo" class="mb-5">
                <div class="relative">
                    <input @change="handleFileUpload" type="file" id="logo" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" accept="image/jpeg,image/png,image/gif" />
                    <label for="logo" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">Logo</label>
                </div>
                <p class="text-gray-400 text-xs">{{ $page.props.getMaxSizeFile }}</p>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.name" type="text" id="name" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                    <label for="name" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                        {{ $t('name') }}
                    </label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.departement" type="text" id="departement" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="departement" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                        {{ $t('department') }}
                    </label>
                </div>
            </div>

            <div class="mb-5">
                <div class="relative">
                    <input v-model="item.code_departement" type="number" id="code_departement" class="block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="code_departement" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                        {{ $t('department_code') }}
                    </label>
                </div>
            </div>

            <InputError :message="message" :canClose="true" @close="this.message = null"></InputError>

            <div class="flex justify-center">
                <SecondaryButton @click.prevent="updateTeam()" class="w-2/4 flex justify-center">
                    {{ $t('team_gestion.modalTeamUpdate.button') }}
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

export default {
    name: "ModalTeamUpdate",
    emits: ['update-team', 'close'],
    components: {
        InputError,
        SecondaryButton,
        PrimaryButton,
        Modal
    },
    props: {
        team: Object,
        show: Boolean
    },
    data() {
        return {
            message: null,
            item: null
        };
    },
    methods: {
        async updateTeam() {
            try {
                const formData = new FormData()

                if (this.item.name !== null) {
                    formData.append('name', this.item.name)
                }
                if (this.item.departement !== null) {
                    formData.append('departement', this.item.departement)
                }
                if (this.item.code_departement !== null) {
                    formData.append('code_departement', this.item.code_departement)
                }


                if (typeof this.item.logo === 'object') {
                    formData.append('logo', this.item.logo)
                }
                const response = await axios.post('/team/update/' + this.item.id, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                Inertia.visit(response.data.url);

            } catch (error) {
                this.message = error.response.data.message
            }
        },
        handleFileUpload(event) {
            this.item.logo = event.target.files[0]
        }
    },
    mounted () {
        this.message = null
        this.item = JSON.parse(JSON.stringify(this.team))
    }
}
</script>
