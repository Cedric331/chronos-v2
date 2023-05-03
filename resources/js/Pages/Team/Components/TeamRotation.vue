<template>
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Gestion des rotations</h3>
            <SecondaryButton @click="addRotation()">
                {{ $t('team_rotation.button') }}
            </SecondaryButton>
        </div>
        <div class="flow-root">
            <ul role="list" class="divide-y divide-gray-200">
                <li v-for="rotation in rotations" :key="rotation.id" class="py-3 sm:py-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                {{ rotation.name }}
                            </p>
                        </div>
                        <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">

                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <ModalGestionRotation :showRotation="showRotation" :rotation="rotation"></ModalGestionRotation>
    </div>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ModalGestionRotation from "@/Pages/Team/Modal/ModalGestionRotation.vue";

export default {
    name: "TeamRotation",
    components: {
        ModalGestionRotation,
        SecondaryButton,
        PrimaryButton
    },
    props: {
        rotations: Object
    },
    data () {
        return {
            showRotation: false,
            rotation: null
        }
    },
    methods: {
        addRotation () {
            this.showRotation = true
        }
    }
}
</script>

<style scoped>

</style>
