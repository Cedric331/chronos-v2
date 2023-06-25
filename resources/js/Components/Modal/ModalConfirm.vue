<template>
    <Modal :show="show" @close="this.$emit('close')">
        <div class="bg-gray-200 dark:bg-white w-full flex items-center justify-center overflow-y-auto sm:rounded-2xl">
            <div class="max-h-full flex items-center justify-center">
                <div class="m-8 my-10 max-w-[400px] mx-auto">
                    <div class="mb-8">
                        <h1 class="mb-4 text-3xl font-extrabold">{{ title }}</h1>
                        <p class="text-gray-600">{{ message }}</p>
                    </div>
                    <div class="flex justify-between">
                        <PrimaryButton @click="this.$emit('close')">Annuler</PrimaryButton>
                        <DangerButton @click="this.$emit('delete-user')">Oui, je confirme</DangerButton>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

export default {
    name: "ModalConfirm",
    emits: ['close', 'delete-user'],
    components: {DangerButton, SecondaryButton, PrimaryButton, Modal},
    props: {
        show: {
            type: Boolean,
            default: true
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
