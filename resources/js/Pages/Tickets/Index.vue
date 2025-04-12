<template>
    <Head title="Tickets" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex justify-center items-center">
                    <div class="w-full md:w-2/3 lg:w-1/2 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="bg-amber-100 p-2 rounded-lg">
                                <i class="fas fa-ticket-alt text-amber-600 text-xl"></i>
                            </div>
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                Système de Tickets
                            </h2>
                        </div>
                        <Link :href="route('tickets.create')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-amber-600 hover:to-amber-700 active:from-amber-700 active:to-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                            <i class="fas fa-plus mr-2"></i> Nouveau ticket
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-0">
                        <!-- Filtres -->
                        <div class="border-b border-gray-200">
                            <div class="p-6 bg-gradient-to-r from-amber-50 to-white">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-800 flex items-center">
                                        <i class="fas fa-filter text-amber-500 mr-2"></i> Filtres
                                    </h3>
                                    <button @click="resetFilters" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200">
                                        <i class="fas fa-redo-alt mr-1.5 text-amber-500"></i> Réinitialiser
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                                        <div class="relative">
                                            <select v-model="filters.status" @change="applyFilters" id="status" class="appearance-none pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                                <option value="">Tous les statuts</option>
                                                <option value="open">Ouverts</option>
                                                <option value="closed">Fermés</option>
                                                <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                                            </select>
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                                <i class="fas fa-tasks text-amber-500"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                                        <div class="relative">
                                            <select v-model="filters.category" @change="applyFilters" id="category" class="appearance-none pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                                <option value="">Toutes les catégories</option>
                                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                            </select>
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                                <i class="fas fa-tag text-amber-500"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1.5">Priorité</label>
                                        <div class="relative">
                                            <select v-model="filters.priority" @change="applyFilters" id="priority" class="appearance-none pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                                <option value="">Toutes les priorités</option>
                                                <option v-for="priority in priorities" :key="priority.id" :value="priority.id">{{ priority.name }}</option>
                                            </select>
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                                <i class="fas fa-flag text-amber-500"></i>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100 md:col-span-2" style="margin-top: 10px">
                                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1.5">Recherche</label>
                                    <div class="relative">
                                        <input type="text" v-model="filters.search" @keyup.enter="applyFilters" id="search" class="pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Rechercher par titre ou description...">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                            <i class="fas fa-search text-amber-500"></i>
                                        </div>
                                        <button v-if="filters.search" @click="filters.search = ''; applyFilters()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Liste des tickets -->
                        <div class="overflow-hidden">
                            <div class="flex items-center justify-between p-4 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-800 flex items-center">
                                    <i class="fas fa-list text-amber-500 mr-2"></i> Liste des tickets
                                    <span class="ml-2 text-sm text-gray-500 font-normal">({{ tickets.total }} tickets)</span>
                                </h3>
<!--                                <div class="flex items-center space-x-2">-->
<!--                                    <span class="text-sm text-gray-600">Trier par:</span>-->
<!--                                    <button @click="sort('id')" class="px-2 py-1 text-sm rounded-md hover:bg-gray-100" :class="{'bg-amber-100 text-amber-700': filters.sort_field === 'id'}">-->
<!--                                        ID <i v-if="filters.sort_field === 'id'" :class="filters.sort_direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>-->
<!--                                    </button>-->
<!--                                    <button @click="sort('title')" class="px-2 py-1 text-sm rounded-md hover:bg-gray-100" :class="{'bg-amber-100 text-amber-700': filters.sort_field === 'title'}">-->
<!--                                        Titre <i v-if="filters.sort_field === 'title'" :class="filters.sort_direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>-->
<!--                                    </button>-->
<!--                                    <button @click="sort('created_at')" class="px-2 py-1 text-sm rounded-md hover:bg-gray-100" :class="{'bg-amber-100 text-amber-700': filters.sort_field === 'created_at'}">-->
<!--                                        Date <i v-if="filters.sort_field === 'created_at'" :class="filters.sort_direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>-->
<!--                                    </button>-->
<!--                                </div>-->
                            </div>

                            <div v-if="tickets.data.length === 0" class="flex flex-col items-center justify-center py-12 bg-gray-50">
                                <div class="text-amber-400 mb-4">
                                    <i class="fas fa-ticket-alt text-6xl"></i>
                                </div>
                                <h3 class="text-xl font-medium text-gray-900 mb-1">Aucun ticket trouvé</h3>
                                <p class="text-gray-500 mb-4">Essayez de modifier vos filtres ou créez un nouveau ticket</p>
                                <Link :href="route('tickets.create')" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-500 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-2"></i> Nouveau ticket
                                </Link>
                            </div>

                            <div v-else class="divide-y divide-gray-200">
                                <div v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50 transition-colors duration-150">
                                    <div class="p-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full" :style="{ backgroundColor: ticket.priority.color + '15' }">
                                                    <span class="text-sm font-bold" :style="{ color: ticket.priority.color }">#{{ ticket.id }}</span>
                                                </div>
                                                <div>
                                                    <Link :href="route('tickets.show', ticket.id)" class="text-lg font-medium text-gray-900 hover:text-amber-600 transition-colors duration-150">
                                                        {{ ticket.title }}
                                                    </Link>
                                                    <div class="flex items-center mt-1 text-sm text-gray-500">
                                                        <span>Créé par {{ ticket.creator.name }} le {{ formatDate(ticket.created_at) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full" :style="{ backgroundColor: ticket.status.color + '20', color: ticket.status.color }">
                                                    <i :class="ticket.status.icon" class="mr-1 text-xs flex-shrink-0"></i> {{ ticket.status.name }}
                                                </span>
                                                <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full" :style="{ backgroundColor: ticket.category.color + '20', color: ticket.category.color }">
                                                    <i :class="ticket.category.icon" class="mr-1 text-xs flex-shrink-0"></i> {{ ticket.category.name }}
                                                </span>
<!--                                                <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full" :style="{ backgroundColor: ticket.priority.color + '20', color: ticket.priority.color }">-->
<!--                                                    <i :class="ticket.priority.icon" class="mr-1 text-xs flex-shrink-0"></i> {{ ticket.priority.name }}-->
<!--                                                </span>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="p-4 bg-white border-t border-gray-200 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Affichage de <span class="font-medium">{{ tickets.from || 0 }}</span> à <span class="font-medium">{{ tickets.to || 0 }}</span> sur <span class="font-medium">{{ tickets.total }}</span> tickets
                            </div>
                            <Pagination
                                v-if="tickets.links && tickets.links.length > 3"
                                :current-page="tickets.current_page"
                                :total-pages="tickets.last_page"
                                @page-change="changePage"
                                class="pagination-amber"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Pagination } from 'flowbite-vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    components: {
        Head,
        Link,
        AuthenticatedLayout,
        Pagination,
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

        return {
            filters,
            applyFilters,
            resetFilters,
            sort,
            formatDate,
            changePage,
        };
    },
};
</script>

<style>
/* Styles personnalisés pour la pagination */
.pagination-amber :deep(.btn-pagination) {
    @apply border-amber-200 text-amber-600 hover:bg-amber-50;
}

.pagination-amber :deep(.btn-pagination.active) {
    @apply bg-amber-500 border-amber-500 text-white hover:bg-amber-600;
}
</style>
