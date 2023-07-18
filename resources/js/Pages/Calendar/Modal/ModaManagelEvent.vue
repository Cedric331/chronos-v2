<template>
    <Modal :show="showEvent" @close="this.$emit('close')">
        <form class="py-6 px-9">
            <h2 :class="{'text-white' : this.$store.state.isDarkMode }" class="flex justify-center my-5 text-2xl w-full">
                Création d'un événement
            </h2>
            <hr class="my-4 dark:text-white">

            <p :class="{'text-white' : this.$store.state.isDarkMode}" class="text-sm">Vous pouvez créer des évènements sur les journées.</p><br>


            <InputError :message="errors" :can-close="true" @close="this.errors = null"></InputError>

            <div class="flex justify-center">
                <PrimaryButton @close="this.$emit('close')" class="w-2/4 flex justify-center">
                    Fermer
                </PrimaryButton>
                <PrimaryButton @click.prevent="" class="w-2/4 flex justify-center">
                    Créer
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
    name: "ModaManagelEvent",
    emits: ['close'],
    components: {InputError, SecondaryButton, PrimaryButton, Modal},
    props: {
        showEvent: {
            type: Boolean,
            required: true,
        },
    },
    data () {
        return {
            errors: null,
        }
    }
}
</script>

<style scoped>

</style>
