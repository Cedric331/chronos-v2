<template>
    <notifications position="bottom right" />
    <Loading :show="isLoading" :messageLoading="messageLoading"></Loading>
    <div :class="{ 'dark': isDarkMode }">
        <div id="wave" :class="{ wave: triggerWave }" :style="{ left: waveX + 'px', top: waveY + 'px' }"></div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav class="bg-gray-300 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <!-- Primary Navigation Menu -->
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('planning')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"/>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('planning')" :active="route().current('planning')">
                                    {{ $t('nav.dashboard') }}
                                </NavLink>
                                <NavLink v-if="$page.props.auth.isCoordinateur && $page.props.auth.user.team_id" :href="route('team.show', {name: $page.props.auth.user.team.name.toLowerCase()})" :active="route().current('team.show')">
                                    {{ $t('nav.management') }}
                                </NavLink>
                            </div>
                        </div>

                        <div class="flex justify-between h-16">

                            <div v-if="$page.props.auth.team.name" class="hidden sm:flex sm:items-center sm:ml-6">
                                <div v-if="$page.props.auth.isCoordinateur && $page.props.auth.team.params.module_alert">
                                    <div v-if="$page.props.auth.alerts && $page.props.auth.alerts.length > 0 && $page.props.auth.alerts.find(item => !item.is_read)">
                                        <Dropdown align="right" width="96" contentClasses="bg-white dark:bg-gray-700 mt-1 border-1 overflow-y-auto max-h-72">
                                            <template #trigger>
                                            <div class="inline-flex rounded-md">
                                                <button
                                                    class="inline-flex relative items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-md text-gray-500 dark:text-white bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                                    </svg>
                                                    <span class="absolute top-3 right-4 bg-[#ff4757] text-white rounded-full w-2 h-2 p-2 text-xs flex items-center justify-center transform translate-x-1/2 -translate-y-1/2">
                                                        {{ unreadAlertsItems.length }}
                                                    </span>
                                                </button>
                                            </div>
                                            </template>

                                            <template #content>
                                                <div v-if="$page.props.auth.alerts.find(item => !item.is_read)">
                                                    <div class="flex justify-center text-blue-400 p-1 text-xs cursor-pointer" @click="markAllRead()">
                                                        Marquer comme lu
                                                    </div>
                                                </div>
                                                <div v-for="alert in $page.props.auth.alerts" :key="alert.id">
                                                    <div v-if="!alert.is_read" @click="markAllReadOne(alert.id)" class="dark:text-white w-full hover:bg-gray-200 p-1 text-xs dark:hover:bg-gray-50 dark:hover:text-black cursor-pointer"> {{ alert.message }} </div>
                                                </div>
                                            </template>
                                        </Dropdown>
                                    </div>
                                    <div v-else>
                                        <div class="inline-flex rounded-md">
                                            <div
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-md text-gray-500 dark:text-white bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
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
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-md text-gray-500 dark:text-white bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.auth.team.name }}
                                            </strong>
                                        </span>
                                        </div>
                                        <Dropdown v-if="$page.props.auth.isCoordinateur" align="right" width="48">
                                            <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-md leading-4 font-medium rounded-md text-gray-500 dark:text-white bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
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
                                                    <div @click="switchTeam(team)" class="text-gray-600 hover:bg-gray-200 p-1 text-md dark:text-gray-400 dark:hover:bg-gray-50 cursor-pointer flex justify-center"> {{ team.name }} </div>
                                                </div>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <div class="checkbox-wrapper-54 relative inline-flex items-center cursor-pointer">
                                    <label class="switch">
                                        <input @click="updateDarkMode($event)" :checked="isDarkMode" type="checkbox">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>


                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <!-- Settings Dropdown -->
                                <div class="ml-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-white bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                                >
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
                                            <DropdownLink :href="route('profile.edit')">
                                                {{ $t('nav.profil') }}
                                            </DropdownLink>
                                            <DropdownLink :href="route('logout')" method="post" as="button">
                                                {{ $t('nav.logout') }}
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
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
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('planning')" :active="route().current('planning')">
                            {{ $t('nav.dashboard') }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                {{ $t('nav.profil') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                {{ $t('nav.logout') }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-gray-100 border-1 top-0 dark:bg-gray-800 sticky top-0" v-if="$slots.header">
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
</template>

<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import Loading from "@/Components/Loading.vue";

export default {
    name: 'AuthenticatedLayout',
    components: {
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
            isDarkMode: false,
            isLoading: false,
            messageLoading: '',
            showingNavigationDropdown: false,
            triggerWave: false,
            waveX: 0,
            waveY: 0
        }
    },
    methods: {
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
            this.isDarkMode = !this.isDarkMode
            localStorage.setItem('isDarkMode', JSON.stringify(this.isDarkMode));

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
                    this.$page.props.auth.alerts = this.$page.props.auth.alerts.filter(item => item.id !== id);
                    this.unreadAlerts()
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
                  this.$page.props.auth.alerts = [];
                    this.unreadAlerts()
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
        darkMode () {
            const isDarkMode = localStorage.getItem('isDarkMode');
            if (isDarkMode) {
                this.isDarkMode = JSON.parse(isDarkMode);
            } else {
                localStorage.setItem('isDarkMode', JSON.stringify(this.isDarkMode));
            }
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
        this.darkMode()
    },
    beforeDestroy() {
        window.removeEventListener('message', this.handleAuthMessage);
    },
}
</script>

<style scoped>
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
}

#wave.wave {
    opacity: 1;
    background: radial-gradient(circle, transparent 1%, #000 1%);
    animation: wave 1s ease-out;
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
;
    transition: .4s;
}

.checkbox-wrapper-54 input:checked + .slider {
    background-color: #ffffff;
}

.checkbox-wrapper-54 input:checked + .slider:before {
    left: calc(100% - (var(--size-of-icon,1.4em) + var(--slider-offset,0.3em)));
    background: #ffffff;
    /* change the value of second inset in box-shadow to change the angle and direction of the moon  */
    box-shadow: inset -3px -2px 5px -2px #8983f7, inset -10px -4px 0 0 #a3dafb;
}

</style>
