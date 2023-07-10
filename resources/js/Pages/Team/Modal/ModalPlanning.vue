<template>
    <Modal :show="showPlanning" :maxWidth="tabs === 3 ? '3xl' : '2xl'">
        <Loading :show="isLoading"></Loading>
        <h2 class="flex justify-center my-5 text-xl w-full" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">
            {{ $t('team_rotation.title_planning') }}
        </h2>

        <!-- component -->
        <section class="body-font bg-gray-100 text-gray-600">
            <div class="container flex w-full flex-wrap justify-between rounded-lg bg-white px-2">

                <div class="w-full md:flex mt-8">

                    <div class="mt-8 max-w-sm md:mt-0 md:ml-10 md:w-1/3">
                        <div class="relative flex pb-12">
                            <div class="absolute inset-0 flex h-full w-10 items-center justify-center">
                                <div class="pointer-events-none h-full w-1 bg-gray-200"></div>
                            </div>
                            <div @click="tabs = 1" class="relative z-10 inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full text-white cursor-pointer" :class="[dateStart && dateEnd ? 'bg-green-600' : 'bg-blue-500']">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                </svg>
                            </div>
                            <div class="flex-grow pl-4">
                                <h2 class="title-font mb-1 mt-3 text-sm font-medium tracking-wider" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Sélection des dates</h2>
                            </div>
                        </div>

                        <div class="relative flex pb-12">
                            <div class="absolute inset-0 flex h-full w-10 items-center justify-center">
                                <div class="pointer-events-none h-full w-1 bg-gray-200"></div>
                            </div>
                            <div @click="tabs = 2" :class="[user ? 'bg-green-600' : 'bg-blue-500']" class="relative z-10 inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-500 text-white cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div class="flex-grow pl-4">
                                <h2 class="title-font mt-3 mb-1 text-sm font-medium tracking-wider" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Sélection du collaborateur</h2>
                            </div>
                        </div>

                        <div class="relative flex pb-12">
                            <div @click="tabs = 3" :class="[rotations.length > 0 ? 'bg-green-600' : 'bg-blue-500']" class="relative z-10 inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-500 text-white cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                </svg>
                            </div>
                            <div class="flex-grow pl-4 mt-3">
                                <h2 class="title-font mb-1 text-sm font-medium tracking-wider" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Sélection des rotations</h2>
                            </div>
                        </div>
                    </div>

                    <div v-if="tabs === 1" class="mx-auto w-max">
                        <div class="mx-auto">
                            <div class="w-full flex flex-col bg-white rounded-lg">
                                <h1 class="font-semibold text-center tracking-wide mb-2" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Sélection des dates</h1>
                                <div class="flex flex-col justify-between">
                                    <label :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'" for="start">Semaine de début:</label>
                                    <input type="week" id="start" name="trip-start" class="w-full"
                                           v-model="dateStart"
                                           :min="dateLimitStart" :max="dateEnd">
                                    <label :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'" for="end" class="mt-6">Semaine de Fin :</label>
                                    <input type="week" id="end" name="trip-end"
                                           v-model="dateEnd"
                                           :min="dateStart" :max="dateLimitEnd">
                                </div>
                                <p class="text-xs mt-6 w-[300px]" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Vous devez indiquer les dates de début et de fin du planning qui sera généré.</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="tabs === 2" class="mx-auto">
                        <div class="mx-auto">
                            <div class="p-16 flex flex-col bg-white rounded-lg">
                                <h1 class="font-semibold tracking-wide mb-2" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Choisir le conseiller</h1>

                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:pr-0">
                                    <div class="flex justify-center">
                                        <div class="w-auto">
                                            <select v-model="user" class=" block w-full text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600" aria-label="Voir le planning">
                                                <option v-for="user in team.users" :key="user.id" :value="user">
                                                    {{ user.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="tabs === 3" class="mx-auto grid w-full grid-cols-12 gap-4 p-1">
                        <div class="header col-span-12 mx-auto">
                            <h1 class="font-semibold text-lg tracking-wide mb-2" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Choix des rotations</h1>
                        </div>
                        <div class="col-span-12 rounded-lg sm:col-span-6 overflow-y-auto h-72">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-black">
                                <tr>
                                    <th scope="col" class="p-4 text-left text-xs font-medium uppercase tracking-wider text-white">
                                        Ordre des rotations
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white border">
                                    <tr v-for="(rotation, index) in rotations">
                                        <td class="p-4 whitespace-nowrap text-sm font-bold" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">
                                            {{ rotation.name }}
                                        </td>
                                        <td @click="this.rotations.splice(index, 1)" class="p-4 cursor-pointer bg-black whitespace-nowrap text-sm font-bold" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-span-12 rounded-lg sm:col-span-6 overflow-y-auto h-72">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-black">
                                <tr>
                                    <th scope="col" class="p-4 text-left text-xs font-medium uppercase tracking-wider text-white">
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium uppercase tracking-wider text-white">
                                        Nom de la Rotation
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium uppercase tracking-wider text-white">
                                        Nombre d'heures
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white overflow-y-auto">
                                    <tr v-for="rotation in this.team.rotations">
                                        <td @click="this.rotations.push(rotation)" class="p-4 cursor-pointer bg-black whitespace-nowrap text-sm font-bold">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                                            </svg>
                                        </td>
                                        <td class="p-4 whitespace-nowrap text-sm font-bold" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">
                                            {{ rotation.name }}
                                        </td>
                                        <td class="p-4 whitespace-nowrap text-sm font-semibold" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">
                                            {{ rotation.total_hours }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>


                <div class="flex w-full mb-5 mr-5" :class="tabJustify">
                    <svg @click="this.tabs--" v-if="tabs !== 1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    <svg @click="this.tabs++" v-if="tabs !== 3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

            </div>
        </section>

        <div class="flex justify-between m-4">
            <SecondaryButton @click="this.$emit('close')">Annuler</SecondaryButton>
            <PrimaryButton @click="generatePlanning()" :disabled="!user || !dateStart || !dateEnd || rotations.length === 0" :class="[!user || !dateStart || !dateEnd || rotations.length === 0 ? 'opacity-50 cursor-not-allowed' : '']" class="text-sm font-medium hover:bg-gray-700 bg-black text-white rounded-lg p-2">Générer le Planning</PrimaryButton>
        </div>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Loading from "@/Components/Loading.vue";

export default {
    name: "ModalPlanning",
    components: {
        Loading,
        Modal,
        PrimaryButton,
        SecondaryButton
    },
    props: {
        team: Object,
        showPlanning: Boolean
    },
    data () {
        return {
            tabs: 1,
            tabJustify: 'justify-end',
            rotations: [],
            isLoading: false,
            user: null,
            dateStart: null,
            dateEnd: null,
        }
    },
    computed: {
        dateLimitStart() {
            const now = new Date();
            const day = now.getDay();
            const diff = now.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is sunday
            const monday = new Date(now.setDate(diff));
            const weekNumber = this.getWeekNumber(monday);
            return `${monday.getFullYear()}-W${weekNumber.toString().padStart(2, '0')}`;
        },
        dateLimitEnd() {
            const now = new Date();
            now.setFullYear(now.getFullYear() + 1);
            now.setMonth(now.getMonth() + 1);
            const weekNumber = this.getWeekNumber(now);
            return `${now.getFullYear()}-W${weekNumber.toString().padStart(2, '0')}`;
        },
    },
    watch: {
        tabs() {
            if (this.tabs === 2) {
                this.tabJustify = 'justify-between';
            } else if (this.tabs === 1) {
                this.tabJustify = 'justify-end';
            } else if (this.tabs === 3) {
                this.tabJustify = 'justify-start';
            }
        }
    },
    methods: {
        generatePlanning () {
            this.isLoading = true
            axios.post('/planning/generate', {
              team: this.team.id,
              user: this.user.id,
              rotations: this.rotations,
              dateStart: this.dateStart,
              dateEnd: this.dateEnd,
          })
              .then(() => {
                  this.$notify({
                      title: "Planning généré",
                      type: "success",
                      text: "Planning généré avec succès !",
                  });
                  this.$emit('close')
              })
              .catch(error => {
                  console.log(error)
              })
              .finally(() => {
                  this.isLoading = false
              })
        },
        getWeekNumber(d) {
            d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
            const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            return weekNo;
        }
    }
}
</script>

<style scoped>

</style>
