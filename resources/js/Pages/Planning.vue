<template>
    <Head title="Planning" />

    <AuthenticatedLayout>
        <Loading :show="isLoading"></Loading>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl flex justify-center items-center text-gray-800 dark:text-gray-200 leading-tight">Planning de {{ user.name }}</h2>
                <div>
                    <Listbox v-model="selectedUser" class="min-w-[230px]">
                        <div class="relative mt-1">
                            <ListboxButton class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm">
                                <span class="block truncate">{{ selectedUser.name }}</span>
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                              </span>
                            </ListboxButton>

                            <transition
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="opacity-100"
                                leave-to-class="opacity-0">
                                <ListboxOptions
                                    class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ListboxOption
                                        v-slot="{ active, selectedUser }"
                                        v-for="user in users"
                                        :key="user.name"
                                        :value="user"
                                        as="template">
                                        <li :class="[
                                          active ? 'bg-amber-100 text-amber-900' : 'text-gray-900',
                                          'relative cursor-default select-none py-2 pl-10 pr-4',]">

                                        <span :class="[selectedUser ? 'font-medium' : 'font-normal', 'block truncate']">{{ user.name }}</span>
                                        <span
                                            v-if="selectedUser"
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 text-amber-600">
                                        </span>
                                        </li>
                                    </ListboxOption>
                                </ListboxOptions>
                            </transition>
                        </div>
                    </Listbox>
                </div>
            </div>
        </template>

        <div>
            <div class="w-full mx-auto">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <Calendar
                        ref="calendar"
                        :daysProps="daysProps"
                        :isToday="isToday"
                        @shareSchedule="this.showShare = true"
                        @planningFull="this.getAllPlanning = !this.getAllPlanning">
                    </Calendar>
                </div>
            </div>
            <ModalSharePlanning v-if="showShare" :show="showShare" @close="this.showShare = false"></ModalSharePlanning>

        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Calendar from "@/Pages/Calendar/Calendar.vue";
import {
    Listbox,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from '@headlessui/vue'
import Loading from "@/Components/Loading.vue";
import ModalSharePlanning from "@/Pages/Calendar/Modal/ModalSharePlanning.vue";

export default {
    components: {
        ModalSharePlanning,
        Loading,
        ListboxOption,
        Listbox,
        ListboxButton,
        ListboxOptions,
        Calendar,
        AuthenticatedLayout,
        Head
    },
    props: {
        isToday: String,
        user: Object,
        users: Object,
        calendar: Object
    },
    data() {
        return {
            isLoading: false,
            getAllPlanning: false,
            showShare: false,
            selectedUser: this.user,
            daysProps: this.calendar
        }
    },
    watch: {
        selectedUser () {
            this.isLoading = true
            this.getPlanning()
        },
        getAllPlanning () {
            this.isLoading = true
            this.getPlanning()
        }
    },
    methods: {
        getPlanning () {
            axios.post('/planning', {
                user: this.selectedUser,
                getAllPlanning: this.getAllPlanning
            })
            .then(response => {
                this.daysProps = response.data
            })
            .catch(error => {
                console.log(error)
            })
            .finally(() => {
                setTimeout(() => {
                    this.isLoading = false
                    this.$refs.calendar.resetDaySelected()
                }, 200)
            })
        }
    }
}
</script>

<style>
li {
    list-style-type:none;
}
</style>
