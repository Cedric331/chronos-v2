<template>
    <notifications position="bottom right" />
    <Loading :show="isLoading" :messageLoading="messageLoading"></Loading>
    <div :class="{ 'dark': $store.state.isDarkMode }">
        <div id="wave" :class="{ wave: triggerWave }" :style="{ left: waveX + 'px', top: waveY + 'px' }"></div>
        <div class="min-h-screen dark:bg-gray-900" >
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 z-50 shadow-lg backdrop-filter backdrop-blur-sm bg-opacity-90 dark:bg-opacity-90" :style="{background: $store.state.isDarkMode ? '': 'linear-gradient(135deg, ' + $page.props.auth.team.params.color1 + '80 0%, ' + $page.props.auth.team.params.color2 + '90 100%)'}">
                <!-- Primary Navigation Menu -->
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-1">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('planning')">
                                    <ApplicationLogo class="block w-auto fill-current text-gray-800 dark:text-gray-200"/>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-6 lg:-my-px lg:ml-10 lg:flex">
                                <NavLink v-if="$page.props.auth.isCoordinateur && $page.props.auth.user.team_id" :href="route('team.show', {name: $page.props.auth.user.team.name.toLowerCase()})" :active="route().current('team.show')" class="font-medium transition-all duration-200 hover:text-opacity-90 hover:scale-105">
                                    {{ $t('nav.management') }}
                                </NavLink>
                                <NavLink :href="route('planning')" :active="route().current('planning')" class="font-medium transition-all duration-200 hover:text-opacity-90 hover:scale-105">
                                    {{ $t('nav.dashboard') }}
                                </NavLink>
                                <NavLink :href="route('information.index')" :active="route().current('information.index')" class="font-medium transition-all duration-200 hover:text-opacity-90 hover:scale-105">
                                    Information de la team
                                </NavLink>
                                <NavLink v-if="$page.props.auth.team.params.paid_leave" :href="route('paidleave.index')" :active="route().current('paidleave.index')" class="font-medium transition-all duration-200 hover:text-opacity-90 hover:scale-105">
                                    Gestion des congés
                                </NavLink>
                                <NavLink v-if="$page.props.auth.team && $page.props.auth.team.params && Boolean($page.props.auth.team.params.exchange_module)" :href="route('exchanges.index')" :active="route().current('exchanges.*')" class="font-medium transition-all duration-200 hover:text-opacity-90 hover:scale-105">
                                    Échanges de planning
                                </NavLink>
                            </div>
                        </div>

                        <div class="flex justify-between h-16">
                            <NavLink :href="route('tickets.index')" :active="route().current('tickets.*')" class="font-medium transition-all duration-200 hover:text-opacity-90 hover:scale-105">
                                <i class="fas fa-ticket-alt mr-1"></i> Tickets
                            </NavLink>
                            <div v-if="$page.props.auth.team.name" class="hidden lg:flex lg:items-center lg:ml-6">
                                <div v-if="$page.props.auth.isCoordinateur && $page.props.auth.team.params.module_alert">
                                    <div v-if="$page.props.auth.alerts && $page.props.auth.alerts.length > 0 && $page.props.auth.alerts.find(item => !item.is_read)">
                                        <Dropdown align="right" width="96" contentClasses="bg-white dark:bg-gray-700 mt-1 border-1 overflow-y-auto max-h-72 rounded-lg shadow-lg z-90">
                                            <template #trigger>
                                            <div class="inline-flex rounded-md">
                                                <button
                                                    class="inline-flex relative items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-lg text-gray-800 dark:text-white bg-gray-300 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 hover:bg-opacity-20 hover:bg-gray-200 dark:hover:bg-gray-700 transform hover:scale-105" style="background-color: transparent">
                                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                                    </svg>
                                                    <span v-if="unreadAlertsItems.length > 0" class="absolute top-3 right-4 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-full w-2 h-2 p-2 text-xs flex items-center justify-center transform translate-x-1/2 -translate-y-1/2 animate-pulse shadow-md hover:scale-110 transition-transform duration-300">
                                                        {{ unreadAlertsItems.length }}
                                                    </span>
                                                </button>
                                            </div>
                                            </template>

                                            <template #content>
                                                <div v-if="unreadAlertsItems.length > 0">
                                                    <div class="flex justify-center my-1 text-blue-600 hover:text-white hover:bg-gradient-to-r hover:from-blue-500 hover:to-indigo-600 p-2 text-xs cursor-pointer rounded-md transition-all duration-200 font-medium" @click="markAllRead()">
                                                        Marquer comme lu
                                                    </div>
                                                </div>
                                                <div v-for="alert in unreadAlertsItems" :key="alert.id" class="p-1">
                                                    <div v-if="!alert.is_read" @click="markAllReadOne(alert.id)" class="dark:text-white w-full hover:bg-gray-200 p-2 text-xs dark:hover:bg-gray-50 dark:hover:text-black cursor-pointer rounded-md transition-all duration-200 hover:shadow-sm"> {{ alert.message }} </div>
                                                </div>
                                            </template>
                                        </Dropdown>
                                    </div>
                                    <div v-else>
                                        <div class="inline-flex rounded-md">
                                            <div
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-lg text-gray-800 dark:text-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 hover:bg-opacity-20 hover:bg-gray-200 dark:hover:bg-gray-700 transform hover:scale-105 shadow-sm">
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="$page.props.config.active">
                                    <div class="ml-3 relative">
                                        <div v-if="!$page.props.auth.isCoordinateur">
                                        <span class="inline-flex rounded-md">
                                            <strong
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-lg text-gray-800 dark:text-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 shadow-sm backdrop-blur-sm bg-opacity-90">
                                                {{ $page.props.auth.team.name }}
                                            </strong>
                                        </span>
                                        </div>
                                        <Dropdown v-if="$page.props.auth.isCoordinateur" align="right" width="48" contentClasses="overflow-y-auto max-h-96 bg-white dark:bg-gray-700 rounded-lg shadow-lg z-90">
                                            <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-lg text-gray-800 dark:text-white hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 hover:bg-opacity-20 hover:bg-gray-200 dark:hover:bg-gray-700 shadow-sm transform hover:scale-105">
                                                    {{ $page.props.auth.team.name }}

                                                    <svg
                                                        class="ml-2 -mr-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                            </template>

                                            <template #content>
                                                <div v-if="$page.props.auth.isCoordinateur" v-for="team in $page.props.teams" :key="team.id">
                                                    <div @click="switchTeam(team)" class="text-gray-600 hover:bg-gradient-to-r hover:from-gray-100 hover:to-gray-200 p-2 text-md dark:text-gray-400 dark:hover:bg-gray-50 dark:hover:text-gray-800 cursor-pointer flex justify-center rounded-md transition-all duration-200 font-medium"> {{ team.name }} </div>
                                                </div>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>
                            </div>

                            <div class="space-x-8 lg:-my-px lg:ml-10 flex items-center justify-center mx-2">
                                <div class="checkbox-wrapper-54 relative inline-flex items-center cursor-pointer">
                                    <label class="switch">
                                        <input @click="updateDarkMode($event)" :checked="$store.state.isDarkMode" type="checkbox">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="hidden lg:flex lg:items-center lg:ml-6">
                                <!-- Settings Dropdown -->
                                <div class="ml-3 relative">
                                    <Dropdown align="right" width="48" contentClasses="bg-white dark:bg-gray-700 rounded-lg shadow-lg z-90">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-800 dark:text-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 hover:bg-opacity-20 hover:bg-gray-200 dark:hover:bg-gray-700 shadow-sm transform hover:scale-105"
                                                >
                                                    <img :src="$page.props.auth.user.avatar" class="h-8 w-8 object-cover rounded-full overflow-hidden shadow-md mr-2 border-2 border-white dark:border-gray-700 ring-2 ring-opacity-50 hover:scale-110 transition-transform duration-300" :class="{'ring-blue-400 dark:ring-blue-500': $store.state.isDarkMode, 'ring-white': !$store.state.isDarkMode}" alt="Avatar">

                                                    {{ $page.props.auth.user.name }}

                                                    <svg
                                                        class="ml-2 -mr-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <DropdownLink v-if="$page.props.auth.isAdmin" :href="route('admin.index')">
                                               Administration
                                            </DropdownLink>
                                            <DropdownLink :href="route('profile.edit')">
                                                {{ $t('nav.profil') }}
                                            </DropdownLink>
                                            <button @click="this.showContact = true" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out rounded-md hover:shadow-sm">
                                                Contacter l'administrateur
                                            </button>
                                            <DropdownLink :href="route('logout')" method="post" as="button">
                                                {{ $t('nav.logout') }}
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center lg:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out shadow-sm"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="lg:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink v-if="$page.props.auth.isCoordinateur && $page.props.auth.user.team_id" :href="route('team.show', {name: $page.props.auth.user.team.name.toLowerCase()})" :active="route().current('team.show')">
                            {{ $t('nav.management') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('information.index')" :active="route().current('information.index')">
                            Information de la team
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('planning')" :active="route().current('planning')">
                            {{ $t('nav.dashboard') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.team.params.paid_leave" :href="route('paidleave.index')" :active="route().current('paidleave.index')">
                            Gestion des congés
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.team && $page.props.auth.team.params && Boolean($page.props.auth.team.params.exchange_module)" :href="route('exchanges.index')" :active="route().current('exchanges.*')">
                            Échanges de planning
                        </ResponsiveNavLink>
                    </div>

                    <div v-if="$page.props.config.active">
                        <div class="ml-3 relative">
                            <Dropdown v-if="$page.props.auth.isCoordinateur" align="right" width="48" contentClasses="overflow-y-auto max-h-96 bg-white dark:bg-gray-700 rounded-lg shadow-lg z-90">
                                <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-md text-gray-800 dark:text-white hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                    {{ $page.props.auth.team.name }}

                                                    <svg
                                                        class="ml-2 -mr-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                </template>

                                <template #content>
                                    <div v-if="$page.props.auth.isCoordinateur" v-for="team in $page.props.teams" :key="team.id">
                                        <DropdownLink @click="switchTeam(team)" class="text-gray-600 hover:bg-gray-200 p-1 text-md dark:text-gray-400 dark:hover:bg-gray-50 cursor-pointer flex justify-center"> {{ team.name }} </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-800">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink v-if="$page.props.auth.isAdmin" :href="route('admin.index')">
                                Administration
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.edit')">
                                {{ $t('nav.profil') }}
                            </ResponsiveNavLink>
                            <button @click="this.showContact = true" class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out hover:shadow-inner">
                                Contacter l'administrateur
                            </button>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                {{ $t('nav.logout') }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="border-1 top-0 dark:bg-gray-800 bg-gray-100 sticky z-40 shadow-sm" v-if="$slots.header" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2, backgroundImage: $store.state.isDarkMode ? '' : 'linear-gradient(to right, ' + $page.props.auth.team.params.color2 + '99, ' + $page.props.auth.team.params.color2 + ')' }">
                <div class="w-auto mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
    <ModalContact :showContact="showContact" @close="(data) => closeModalContact(data)"></ModalContact>
</template>

<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Head, Link} from '@inertiajs/vue3';
import Loading from "@/Components/Loading.vue";
import ModalContact from "@/Components/Modal/ModalContact.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    name: 'AuthenticatedLayout',
    components: {
        PrimaryButton,
        ModalContact,
        Head,
        ApplicationLogo,
        Dropdown,
        DropdownLink,
        NavLink,
        ResponsiveNavLink,
        Link,
        Loading
    },
    data () {
        return {
            unreadAlertsItems: [],
            team: {'name': null},
            showContact: false,
            isLoading: false,
            messageLoading: '',
            showingNavigationDropdown: false,
            triggerWave: false,
            waveX: 0,
            waveY: 0
        }
    },
    methods: {
        closeModalContact (data) {
            this.showContact = false
            if (data) {
                this.$notify({
                    title: "Succès",
                    text: "Votre message a bien été envoyé",
                    type: 'success',
                });
            }
        },
        switchTeam (team) {
            axios.patch('/switch/team/' + team.id)
                .then(() => {
                    this.$inertia.visit(this.$page.url)
                })
                .catch(error => {
                    console.log(error)
                    this.$notify({
                        title: "Erreur",
                        text: "Oups désolé une erreur est survenue",
                        type: 'info',
                    });
                })
        },
        updateDarkMode (event) {
            this.$store.commit('toggleDarkMode');

            this.waveX = event.clientX;
            this.waveY = event.clientY;

            this.triggerWave = true;
            setTimeout(() => this.triggerWave = false, 1000); // Remove the class after the animation is done
        },
        unreadAlerts () {
            if (this.$page.props.auth.alerts) {
                this.unreadAlertsItems = this.$page.props.auth.alerts.filter(item => !item.is_read);
            }
        },
        markAllReadOne (id) {
            axios.patch('/mark-read/' + id)
                .then(() => {
                    this.$notify({
                        title: "Succès",
                        text: "La notification a été marquée comme lue",
                        type: 'success',
                    });
                    this.unreadAlertsItems = this.unreadAlertsItems.filter(item => item.id !== id);
                })
                .catch(error => {
                    console.log(error)
                    this.$notify({
                        title: "Erreur",
                        text: "Oups désolé une erreur est survenue",
                        type: 'info',
                    });
                })
        },
        markAllRead () {
          axios.patch('/mark-all-read')
              .then(() => {
                  this.$notify({
                      title: "Succès",
                      text: "Toutes les notifications ont été marquées comme lues",
                      type: 'success',
                  });
                  this.unreadAlertsItems = [];
              })
              .catch(error => {
                  console.log(error)
                  this.$notify({
                      title: "Erreur",
                      text: "Oups désolé une erreur est survenue",
                      type: 'info',
                  });
              })
        }
    },
    mounted () {
        window.addEventListener('message', this.handleAuthMessage);
        if (this.$page.props.auth.user.team_id && this.$page.props.config.active) {
            this.team = this.$page.props.teams.find(item => {
                return item.id === this.$page.props.auth.user.id
            })
        }
        this.unreadAlerts()
    },
    beforeDestroy() {
        window.removeEventListener('message', this.handleAuthMessage);
    },
}
</script>

<style scoped>
/* Add smooth transitions to all elements */
* {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-duration: 200ms;
}
#wave {
    position: fixed;
    width: 2000px;
    height: 2000px;
    border-radius: 50%;
    background: transparent;
    pointer-events: none;
    z-index: 9999;
    overflow: hidden;
    opacity: 0;
    transform: translate(-50%, -50%) scale(0); /* Adjust the div to be centered on the click */
    will-change: transform, opacity;
}

#wave.wave {
    opacity: 1;
    background: radial-gradient(circle, rgba(137, 131, 247, 0.2) 1%, rgba(0, 0, 0, 0.8) 1%);
    animation: wave 1s cubic-bezier(0.22, 1, 0.36, 1);
}

@keyframes wave {
    0% {
        transform: translate(-50%, -50%) scale(0);
    }
    100% {
        transform: translate(-50%, -50%) scale(2);
        opacity: 0;
    }
}

 .checkbox-wrapper-54 input[type="checkbox"] {
     visibility: hidden;
     display: none;
 }

.checkbox-wrapper-54 *,
.checkbox-wrapper-54 ::after,
.checkbox-wrapper-54 ::before {
    box-sizing: border-box;
}

/* The switch - the box around the slider */
.checkbox-wrapper-54 .switch {
    --width-of-switch: 3.5em;
    --height-of-switch: 2em;
    /* size of sliding icon -- sun and moon */
    --size-of-icon: 1.4em;
    /* it is like a inline-padding of switch */
    --slider-offset: 0.3em;
    position: relative;
    width: var(--width-of-switch);
    height: var(--height-of-switch);
    display: inline-block;
}

/* The slider */
.checkbox-wrapper-54 .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #303136;
    transition: .4s;
    border-radius: 30px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

.checkbox-wrapper-54 .slider:before {
    position: absolute;
    content: "";
    height: var(--size-of-icon,1.4em);
    width: var(--size-of-icon,1.4em);
    border-radius: 20px;
    left: var(--slider-offset,0.3em);
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(40deg,#ff0080,#ff8c00 70%);
    box-shadow: 0 2px 10px rgba(255, 140, 0, 0.7);
    transition: .4s;
}

.checkbox-wrapper-54 input:checked + .slider {
    background-color: #ffffff;
    box-shadow: 0 2px 10px rgba(137, 131, 247, 0.5);
}

.checkbox-wrapper-54 input:checked + .slider:before {
    left: calc(100% - (var(--size-of-icon,1.4em) + var(--slider-offset,0.3em)));
    background: #ffffff;
    /* change the value of second inset in box-shadow to change the angle and direction of the moon  */
    box-shadow: inset -3px -2px 5px -2px #8983f7, inset -10px -4px 0 0 #a3dafb;
    transform: translateY(-50%) rotate(35deg);
}

</style>
