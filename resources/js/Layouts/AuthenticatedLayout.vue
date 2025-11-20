<template>
    <notifications position="bottom right" />
    <Loading :show="isLoading" :messageLoading="messageLoading"></Loading>
    <div :class="{ 'dark': $store.state.isDarkMode }">
        <div id="wave" :class="{ wave: triggerWave }" :style="{ left: waveX + 'px', top: waveY + 'px' }"></div>
        <div class="min-h-screen dark:bg-gray-900" >
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-50 shadow-sm" :style="{background: $store.state.isDarkMode ? '' : 'linear-gradient(135deg, ' + $page.props.auth.team.params.color1 + '80 0%, ' + $page.props.auth.team.params.color2 + '90 100%)'}" aria-label="Navigation principale">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Left Section: Logo + Navigation Links -->
                        <div class="flex items-center flex-1">
                            <!-- Logo -->
                            <div class="shrink-0 mr-10">
                                <Link :href="route('planning')" class="flex items-center justify-center h-16 group">
                                    <ApplicationLogo />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden lg:flex items-center space-x-2">
                                <NavLink :href="route('planning')" :active="route().current('planning')" class="nav-link-item">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    {{ $t('nav.dashboard') }}
                                </NavLink>
                                
                                <NavLink v-if="$page.props.auth.isCoordinateur && $page.props.auth.user.team_id" :href="route('team.show', {name: $page.props.auth.user.team.name.toLowerCase()})" :active="route().current('team.show')" class="nav-link-item">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    {{ $t('nav.management') }}
                                </NavLink>
                                
                                <NavLink :href="route('information.index')" :active="route().current('information.index')" class="nav-link-item">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Information
                                </NavLink>
                                
                                <NavLink v-if="$page.props.auth.team.params.paid_leave" :href="route('paidleave.index')" :active="route().current('paidleave.index')" class="nav-link-item">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Congés
                                </NavLink>
                                
                                <NavLink v-if="$page.props.auth.team && $page.props.auth.team.params && Boolean($page.props.auth.team.params.exchange_module)" :href="route('exchanges.index')" :active="route().current('exchanges.*')" class="nav-link-item">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    Échanges
                                </NavLink>
                                
                                <NavLink :href="route('tickets.index')" :active="route().current('tickets.*')" class="nav-link-item">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                    Tickets
                                </NavLink>
                            </div>
                        </div>

                        <!-- Right Section: Actions + User Menu -->
                        <div class="flex items-center space-x-6">
                            <!-- Notifications -->
                            <div v-if="$page.props.auth.isCoordinateur && $page.props.auth.team.params.module_alert" class="hidden lg:block">
                                <Dropdown align="right" width="96" contentClasses="bg-white dark:bg-gray-700 mt-1 border border-gray-200 dark:border-gray-600 overflow-y-auto max-h-72 rounded-lg shadow-lg z-90">
                                    <template #trigger>
                                        <button
                                            class="relative p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200"
                                            aria-label="Notifications"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                            </svg>
                                            <span v-if="unreadAlertsItems.length > 0" class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center font-semibold animate-pulse">
                                                {{ unreadAlertsItems.length }}
                                            </span>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div v-if="unreadAlertsItems.length > 0" class="p-2 border-b border-gray-200 dark:border-gray-600">
                                            <button @click="markAllRead()" class="w-full text-center text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium py-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                                Marquer tout comme lu
                                            </button>
                                        </div>
                                        <div v-if="unreadAlertsItems.length === 0" class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Aucune notification
                                        </div>
                                        <div v-for="alert in unreadAlertsItems" :key="alert.id" class="p-2">
                                            <div v-if="!alert.is_read" @click="markAllReadOne(alert.id)" class="w-full hover:bg-gray-100 dark:hover:bg-gray-600 p-3 text-sm dark:text-white cursor-pointer rounded-md transition-all duration-200 border-l-4 border-blue-500">
                                                {{ alert.message }}
                                            </div>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Team Selector -->
                            <div v-if="$page.props.config.active && $page.props.auth.team.name" class="hidden lg:block">
                                <div v-if="!$page.props.auth.isCoordinateur" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $page.props.auth.team.name }}
                                </div>
                                <Dropdown v-else align="right" width="48" contentClasses="bg-white dark:bg-gray-700 rounded-lg shadow-lg z-90 border border-gray-200 dark:border-gray-600">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200"
                                        >
                                            {{ $page.props.auth.team.name }}
                                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div v-for="team in $page.props.teams" :key="team.id">
                                            <div @click="switchTeam(team)" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors rounded-md">
                                                {{ team.name }}
                                            </div>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Dark Mode Toggle -->
                            <div class="hidden lg:block">
                                <button
                                    @click="updateDarkMode($event)"
                                    class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200"
                                    aria-label="Basculer le mode sombre"
                                >
                                    <svg v-if="!$store.state.isDarkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- User Menu -->
                            <div class="hidden lg:block">
                                <Dropdown align="right" width="64" contentClasses="bg-white dark:bg-gray-700 rounded-lg shadow-lg z-90 border border-gray-200 dark:border-gray-600">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="flex items-center space-x-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200"
                                        >
                                            <img :src="$page.props.auth.user.avatar" class="h-8 w-8 rounded-full border-2 border-white dark:border-gray-600 ring-2 ring-gray-200 dark:ring-gray-600" alt="Avatar">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden xl:block">
                                                {{ $page.props.auth.user.name }}
                                            </span>
                                            <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $page.props.auth.user.email }}</div>
                                        </div>
                                        <DropdownLink v-if="$page.props.auth.isAdmin" :href="route('admin.index')" class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>Administration</span>
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.edit')" class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>{{ $t('nav.profil') }}</span>
                                        </DropdownLink>
                                        <button @click="this.showContact = true" class="w-full flex items-center px-4 py-2 text-sm text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none transition-colors rounded-md">
                                            <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>Contacter l'administrateur</span>
                                        </button>
                                        <div class="border-t border-gray-200 dark:border-gray-600 mt-1 pt-1">
                                            <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                <span>{{ $t('nav.logout') }}</span>
                                            </DropdownLink>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Mobile Menu Button -->
                            <div class="lg:hidden">
                                <button
                                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200"
                                    aria-label="Ouvrir le menu de navigation"
                                    :aria-expanded="showingNavigationDropdown"
                                >
                                    <svg v-if="!showingNavigationDropdown" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    <svg v-else class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    id="mobile-menu"
                    :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                    class="lg:hidden border-t border-gray-200 dark:border-gray-700"
                    role="menu"
                >
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('planning')" :active="route().current('planning')">
                            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ $t('nav.dashboard') }}
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink v-if="$page.props.auth.isCoordinateur && $page.props.auth.user.team_id" :href="route('team.show', {name: $page.props.auth.user.team.name.toLowerCase()})" :active="route().current('team.show')">
                            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ $t('nav.management') }}
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink :href="route('information.index')" :active="route().current('information.index')">
                            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Information
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink v-if="$page.props.auth.team.params.paid_leave" :href="route('paidleave.index')" :active="route().current('paidleave.index')">
                            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Gestion des congés
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink v-if="$page.props.auth.team && $page.props.auth.team.params && Boolean($page.props.auth.team.params.exchange_module)" :href="route('exchanges.index')" :active="route().current('exchanges.*')">
                            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            Échanges de planning
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink :href="route('tickets.index')" :active="route().current('tickets.*')">
                            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Tickets
                        </ResponsiveNavLink>
                    </div>

                    <!-- Mobile User Section -->
                    <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="px-4 mb-3">
                            <div class="flex items-center space-x-3">
                                <img :src="$page.props.auth.user.avatar" class="h-10 w-10 rounded-full border-2 border-gray-200 dark:border-gray-600" alt="Avatar">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $page.props.auth.user.email }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="px-2 space-y-1">
                            <ResponsiveNavLink v-if="$page.props.auth.isAdmin" :href="route('admin.index')">
                                Administration
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.edit')">
                                {{ $t('nav.profil') }}
                            </ResponsiveNavLink>
                            <button @click="this.showContact = true" class="block w-full pl-3 pr-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                                Contacter l'administrateur
                            </button>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-red-600 dark:text-red-400">
                                {{ $t('nav.logout') }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 z-40 shadow-sm" v-if="$slots.header" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2, backgroundImage: $store.state.isDarkMode ? '' : 'linear-gradient(to right, ' + $page.props.auth.team.params.color2 + '99, ' + $page.props.auth.team.params.color2 + ')' }">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
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
            setTimeout(() => this.triggerWave = false, 1000);
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
/* Navigation Link Styles */
.nav-link-item {
    @apply inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200;
    @apply text-gray-700 dark:text-gray-300;
    @apply hover:bg-gray-100 dark:hover:bg-gray-700;
    @apply focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2;
}

.nav-link-item.active {
    @apply bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300;
    @apply border-b-2 border-amber-500 dark:border-amber-400;
}

/* Smooth transitions */
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
    transform: translate(-50%, -50%) scale(0);
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
</style>
