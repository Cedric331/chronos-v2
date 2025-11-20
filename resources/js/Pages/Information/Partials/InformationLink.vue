<template>
    <div 
        class="link-card dark:bg-gray-800 bg-white shadow-lg rounded-xl p-6 2xl:col-span-2 h-auto border border-gray-200 dark:border-gray-700" 
        :class="$attrs.class"
        :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }"
    >
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="icon-container mr-4 hidden sm:flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Partage des liens
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Partagez des ressources utiles avec votre équipe</p>
                </div>
            </div>
            <div class="flex-shrink-0">
                <button @click="show = true" class="add-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter un lien
                </button>
            </div>
        </div>

        <div class="flex flex-col mt-6">
            <div class="rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="align-middle inline-block min-w-full">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Lien
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ajouté par
                            </th>
                            <th scope="col" class="p-2 text-xs font-medium text-gray-500 dark:text-gray-300 w-1/5 tracking-wider text-center">
                                Actions
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                        <tr v-for="(link, i) in links" class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
                            <td class="p-4 whitespace-nowrap text-sm font-medium">
                                <a :href="link.link" target="_blank" class="link-button flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Accéder
                                </a>
                            </td>
                            <td class="p-4 whitespace-normal text-sm text-gray-700 dark:text-gray-300">
                                <div class="w-full max-w-xs sm:max-w-md truncate hover:text-clip">
                                    {{ link.description }}
                                </div>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center text-indigo-500 dark:text-indigo-300 font-medium">
                                        {{ link.user.name.charAt(0) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium">{{ link.user.name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <div class="flex items-center justify-center space-x-3">
                                    <button v-if="link.user.id === $page.props.auth.user.id || $page.props.auth.isCoordinateur" @click="editLink(link)" class="action-button edit-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        <span class="sr-only">Modifier</span>
                                    </button>
                                    <button @click="confirmDelete(link)" v-if="link.user.id === $page.props.auth.user.id || $page.props.auth.isCoordinateur" class="action-button delete-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                        <span class="sr-only">Supprimer</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="links.length === 0">
                            <td colspan="4" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <p class="text-lg font-medium">Aucun lien partagé</p>
                                    <p class="mt-1">Cliquez sur "Ajouter un lien" pour partager une ressource avec votre équipe</p>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center mt-8">
            <!-- Help text -->
            <span class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-900 dark:text-white">{{ total }}</span> Résultats
            </span>
            <div class="inline-flex mt-3 xs:mt-0">
                <button v-if="prev_page_url" @click="goToPage(prev_page_url)" class="pagination-button rounded-l-lg">
                    <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                    </svg>
                    Précédent
                </button>
                <button v-if="next_page_url" @click="goToPage(next_page_url)" class="pagination-button rounded-r-lg border-l border-gray-300 dark:border-gray-600">
                    Suivant
                    <svg class="w-4 h-4 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <ModalCreateLink v-if="show" :show="show" :link="link" @store-link="(data) => this.storeLink(data)" @update-link="(data) => this.updateLink(data)" @close="cancelDelete()"></ModalCreateLink>
    <ModalConfirm v-if="showDelete" :title="title" :message="message" @delete-confirm="deleteLink()" @close="cancelDelete()"></ModalConfirm>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {Pagination} from "flowbite-vue";
import ModalConfirm from "@/Components/Modal/ModalConfirm.vue";
import ModalCreateLink from "@/Pages/Information/Modal/ModalCreateLink.vue";

export default {
    name: "InformationLink",
    inheritAttrs: false,
    components: {
        ModalCreateLink,
        ModalConfirm,
        Pagination,
        PrimaryButton
    },
    props: {
        linksProps: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            links: [],
            next_page_url: null,
            prev_page_url: null,
            currentPage: null,
            total: null,
            showDelete: false,
            show: false,
            title: '',
            message: '',
            link: null
        }
    },
    methods: {
        editLink (link) {
            this.link = link
            this.show = true
        },
        updateLink (links) {
            this.refreshData(links)

            this.$notify({
                type: 'success',
                title: 'Succès',
                text: 'Le lien a bien été modifié'
            })
            this.show = false
        },
        storeLink (links) {
            this.refreshData(links)

            this.$notify({
                type: 'success',
                title: 'Succès',
                text: 'Le lien a bien été ajouté'
            })
            this.show = false
        },
        refreshData (links) {
            this.links = links.data
            this.total = links.total
        },
        cancelDelete() {
            this.showDelete = false
            this.show = false
            this.title = ''
            this.message = ''
            this.link = null
        },
        confirmDelete(link) {
            this.showDelete = true
            this.title = 'Supprimer le lien'
            this.message = 'Êtes-vous sûr de vouloir supprimer ce lien ?'
            this.link = link
        },
        deleteLink () {
            axios.delete(`/links/${this.link.id}`)
                .then(() => {
                    this.links = this.links.filter(item => item.id !== this.link.id)
                    this.total = this.total - 1
                    this.$notify({
                        type: 'success',
                        title: 'Succès',
                        text: 'Le lien a bien été supprimé'
                    })
                    this.cancelDelete()
                })
                .catch(error => {
                    this.$notify({
                        type: 'error',
                        title: 'Erreur',
                        text: error.response.data.message
                    })
                })
        },
        goToPage(url) {
            // Utiliser Inertia ou rediriger vers l'URL de pagination
            window.location.href = url;
        }
    },
    mounted() {
        this.currentPage = this.linksProps.current_page
        this.links = this.linksProps.data
        this.total = this.linksProps.total
        this.next_page_url = this.linksProps.next_page_url
        this.prev_page_url = this.linksProps.prev_page_url
    }
}
</script>

<style scoped>
.link-card {
    transition: all 0.3s ease;
}

.link-card:hover {
    transform: translateY(-2px);
}

.icon-container {
    @apply p-3 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300;
}

.add-link-btn {
    @apply flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition-all duration-200 font-medium;
}

.link-button {
    @apply inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-full hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-800 transition-colors duration-200;
}

.action-button {
    @apply p-2 rounded-full transition-colors duration-200;
}

.edit-button {
    @apply text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-900;
}

.delete-button {
    @apply text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900;
}

.pagination-button {
    @apply flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200;
}
</style>
