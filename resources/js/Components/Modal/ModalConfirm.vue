<template>
        <!-- component -->
        <div class="min-w-screen h-screen animated fadeIn faster  fixed  left-0 top-0 flex justify-center items-center inset-0 z-50 outline-none focus:outline-none bg-no-repeat bg-center bg-cover" id="modal-id">
            <div class="absolute bg-black opacity-80 inset-0 z-0" @click="this.$emit('close')"></div>
            <div class="w-full  max-w-lg p-5 relative mx-auto my-auto rounded-xl shadow-lg  bg-white ">
                <!--content-->
                <div class="">
                    <!--body-->
                    <div class="text-center p-5 flex-auto justify-center">
                        <div v-if="isAccept">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 flex items-center text-green-500 mx-auto">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>

                        </div>
                        <div v-else>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 flex items-center text-red-500 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold py-4 ">{{ title }}</h2>
                        <p class="text-md text-gray-500 px-8">{{ message }}</p>
                    </div>
                    <!--footer-->
                    <div class="p-3 mt-2 text-center space-x-4 flex justify-between">
                        <div>
                            <SecondaryButton @click="this.$emit('close')" class="mb-2 md:mb-0 px-5 py-2 text-sm shadow-sm font-medium tracking-wider border text-gray-600 rounded-full hover:shadow-lg hover:bg-gray-100">
                                Annuler
                            </SecondaryButton>
                        </div>
                        <div v-if="isAccept">
                            <PrimaryButton @click="this.$emit('accept-confirm')" class="mb-2 md:mb-0 bg-green-500 border border-green-500 px-5 py-2 text-sm shadow-sm font-medium tracking-wider text-white rounded-full hover:shadow-lg hover:bg-green-600">
                                Oui, je confirme
                            </PrimaryButton>
                        </div>
                        <div v-else>
                            <DangerButton @click="this.$emit('delete-confirm')" class="mb-2 md:mb-0 bg-red-500 border border-red-500 px-5 py-2 text-sm shadow-sm font-medium tracking-wider text-white rounded-full hover:shadow-lg hover:bg-red-600">Oui, je confirme</DangerButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

export default {
    name: "ModalConfirm",
    emits: ['close', 'delete-confirm', 'accept-confirm'],
    components: {DangerButton, SecondaryButton, PrimaryButton, Modal},
    props: {
        show: {
            type: Boolean,
            default: true
        },
        isAccept: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: 'Confirmation'
        },
        message: {
            type: String,
            default: 'Êtes-vous sûr de vouloir continuer ?'
        }
    }
}
</script>

<style scoped>

</style>
