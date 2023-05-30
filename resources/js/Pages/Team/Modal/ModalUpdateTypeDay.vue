<template>
    <Modal :show="show" @close="this.$emit('close')">
        <div class="py-6 px-9">
            <h2 class="flex justify-center my-5 text-xl w-full text-gray-400">
                Modification des types de jour
            </h2>
            <hr class="my-4 dark:text-white">
            <div class="w-full">
                <div class="relative mb-4 flex w-full flex-wrap items-stretch">

                    <input
                        v-model="newTypeDay"
                        @keydown.enter.prevent="addTypeDay"
                        placeholder="Ajouter un nouveau type de jour"
                        class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"

                    />

                    <!--Search button-->
                    <button
                        @click="addTypeDay()"
                        class="relative flex items-center rounded-r px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white bg-gray-600 dark:text-black dark:bg-white shadow-md transition duration-150 ease-in-out"
                        type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>
                <div class="flex flex-wrap items-center">
                      <span
                          v-for="(type_day, index) in item.params.type_day"
                          :key="index"
                          class="bg-blue-500 text-white px-2 py-1 mb-5 rounded-full m-1">
                        {{ type_day }}
                        <button @click="removeTypeDay(index)" class="ml-1 text-white font-bold">×</button>
                      </span>
                </div>
            </div>
            <InputError :message="message" :canClose="true" @close="this.message = null"></InputError>

            <div class="flex justify-center">
                <SecondaryButton @click.prevent="updateTeamParams()" class="w-2/4 flex justify-center">
                    Enregistrer
                </SecondaryButton>
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
    name: "ModalUpdateTypeDay",
    emits: ['update:type_day', 'close'],
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
            item: null,
            newTypeDay: ""
        };
    },
    watch: {
        show () {
          this.changeData()
        }
    },
    methods: {
        updateTeamParams () {
            this.$emit('update:type_day', this.item.params.type_day);
        },
        addTypeDay() {
            if (this.newTypeDay.trim() !== "") {
                this.item.params.type_day.push(this.newTypeDay.trim())
                this.newTypeDay = ""
            }
        },
        removeTypeDay(index) {
            this.item.params.type_day.splice(index, 1)
        },
        changeData () {
            this.message = null
            this.item = JSON.parse(JSON.stringify(this.team))
        }
    }
}
</script>
