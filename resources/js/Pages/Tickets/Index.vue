<template>
    <Head title="Tickets" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-amber-100 dark:bg-amber-900/30 p-2 rounded-lg">
                            <i class="fas fa-ticket-alt text-amber-600 dark:text-amber-400 text-xl"></i>
                        </div>
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                            Système de Tickets
                        </h2>
                    </div>
                    <Link :href="route('tickets.create')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-amber-600 hover:to-amber-700 active:from-amber-700 active:to-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-opacity-75 transition ease-in-out duration-150 shadow-md">
                        <i class="fas fa-plus mr-2"></i> Nouveau ticket
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Card padding="p-0" class="overflow-hidden">
                    <!-- Filtres -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="p-6 bg-gradient-to-r from-amber-50 to-white dark:from-gray-800 dark:to-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filtres
                                </h3>
                                <button 
                                    @click="resetFilters" 
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 focus:ring-opacity-75 transition-all duration-200"
                                >
                                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Réinitialiser
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Statut</label>
                                    <div class="relative">
                                        <select 
                                            v-model="filters.status" 
                                            @change="applyFilters" 
                                            id="status" 
                                            class="appearance-none pl-8 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                        >
                                            <option value="">Tous les statuts</option>
                                            <option value="open">Ouverts</option>
                                            <option value="closed">Fermés</option>
                                            <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                                        </select>
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Catégorie</label>
                                    <div class="relative">
                                        <select 
                                            v-model="filters.category" 
                                            @change="applyFilters" 
                                            id="category" 
                                            class="appearance-none pl-8 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                        >
                                            <option value="">Toutes les catégories</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Priorité</label>
                                    <div class="relative">
                                        <select 
                                            v-model="filters.priority" 
                                            @change="applyFilters" 
                                            id="priority" 
                                            class="appearance-none pl-8 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                        >
                                            <option value="">Toutes les priorités</option>
                                            <option v-for="priority in priorities" :key="priority.id" :value="priority.id">{{ priority.name }}</option>
                                        </select>
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mt-4">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Recherche</label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        v-model="filters.search" 
                                        @input="debouncedSearch" 
                                        @keyup.enter="applyFilters" 
                                        id="search" 
                                        class="pl-8 pr-10 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200 text-sm" 
                                        placeholder="Rechercher par titre ou description..."
                                    >
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <button 
                                        v-if="filters.search" 
                                        @click="filters.search = ''; applyFilters()" 
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des tickets -->
                    <div class="overflow-hidden">
                        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-800 dark:text-white flex items-center">
                                <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Liste des tickets
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400 font-normal">({{ tickets.total }} tickets)</span>
                            </h3>
                        </div>

                        <EmptyState
                            v-if="tickets.data.length === 0"
                            icon="search"
                            title="Aucun ticket trouvé"
                            description="Essayez de modifier vos filtres ou créez un nouveau ticket"
                        >
                            <template #action>
                                <Link 
                                    :href="route('tickets.create')" 
                                    class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-500 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Nouveau ticket
                                </Link>
                            </template>
                        </EmptyState>

                        <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div 
                                v-for="ticket in tickets.data" 
                                :key="ticket.id" 
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
                            >
                                <div class="p-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full" :style="{ backgroundColor: ticket.priority.color + '15' }">
                                                <span class="text-sm font-bold" :style="{ color: ticket.priority.color }">#{{ ticket.id }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <Link 
                                                    :href="route('tickets.show', ticket.id)" 
                                                    class="text-lg font-medium text-gray-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition-colors duration-150 block truncate"
                                                >
                                                    {{ ticket.title }}
                                                </Link>
                                                <div class="flex items-center mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    <span>Créé par {{ ticket.creator.name }} le {{ formatDate(ticket.created_at) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4 flex-shrink-0">
                                            <Badge :variant="getStatusVariant(ticket.status.name)">
                                                <i :class="ticket.status.icon" class="mr-1 text-xs"></i>
                                                {{ ticket.status.name }}
                                            </Badge>
                                            <Badge variant="info">
                                                <i :class="ticket.category.icon" class="mr-1 text-xs"></i>
                                                {{ ticket.category.name }}
                                            </Badge>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Affichage de <span class="font-medium text-gray-700 dark:text-gray-300">{{ tickets.from || 0 }}</span> à <span class="font-medium text-gray-700 dark:text-gray-300">{{ tickets.to || 0 }}</span> sur <span class="font-medium text-gray-700 dark:text-gray-300">{{ tickets.total }}</span> tickets
                        </div>
                        <Pagination
                            v-if="tickets.links && tickets.links.length > 3"
                            :current-page="tickets.current_page"
                            :total-pages="tickets.last_page"
                            @page-change="changePage"
                            class="pagination-amber"
                        />
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Pagination } from 'flowbite-vue';
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDebounce } from '@/composables/useDebounce';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import EmptyState from '@/Components/EmptyState.vue';

export default {
    components: {
        Head,
        Link,
        AuthenticatedLayout,
        Pagination,
        Card,
        Badge,
        EmptyState,
    },
    props: {
        tickets: Object,
        filters: Object,
        statuses: Array,
        categories: Array,
        priorities: Array,
        can_manage: Boolean,
    },
    setup(props) {
        const filters = ref({
            status: props.filters.status || '',
            category: props.filters.category || '',
            priority: props.filters.priority || '',
            created_by: props.filters.created_by || '',
            search: props.filters.search || '',
            sort_field: props.filters.sort_field || 'created_at',
            sort_direction: props.filters.sort_direction || 'desc',
        });

        const applyFilters = () => {
            router.get(route('tickets.index'), filters.value, {
                preserveState: true,
                replace: true,
            });
        };

        // Debounce pour la recherche
        const debouncedSearch = useDebounce(() => {
            applyFilters();
        }, 500);

        const changePage = (page) => {
            filters.value.page = page;
            applyFilters();
        };

        const resetFilters = () => {
            filters.value = {
                status: '',
                category: '',
                priority: '',
                created_by: '',
                search: '',
                sort_field: 'created_at',
                sort_direction: 'desc',
            };
            applyFilters();
        };

        const sort = (field) => {
            if (filters.value.sort_field === field) {
                filters.value.sort_direction = filters.value.sort_direction === 'asc' ? 'desc' : 'asc';
            } else {
                filters.value.sort_field = field;
                filters.value.sort_direction = 'asc';
            }
            applyFilters();
        };

        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        };

        const getStatusVariant = (statusName) => {
            const statusLower = statusName.toLowerCase();
            if (statusLower.includes('ouvert') || statusLower.includes('en cours')) {
                return 'info';
            } else if (statusLower.includes('résolu') || statusLower.includes('accepté')) {
                return 'success';
            } else if (statusLower.includes('fermé') || statusLower.includes('annulé')) {
                return 'default';
            } else if (statusLower.includes('en attente')) {
                return 'warning';
            } else if (statusLower.includes('refusé') || statusLower.includes('erreur')) {
                return 'error';
            }
            return 'default';
        };

        return {
            filters,
            applyFilters,
            resetFilters,
            sort,
            formatDate,
            changePage,
            debouncedSearch,
            getStatusVariant,
        };
    },
};
</script>

<style scoped>
/* Styles personnalisés pour la pagination */
.pagination-amber :deep(.btn-pagination) {
    @apply border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20;
}

.pagination-amber :deep(.btn-pagination.active) {
    @apply bg-amber-500 dark:bg-amber-600 border-amber-500 dark:border-amber-600 text-white hover:bg-amber-600 dark:hover:bg-amber-700;
}
</style>
