<template>
    <Head :title="'Ticket #' + ticket.id" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Ticket #{{ ticket.id }} - {{ ticket.title }}
                </h2>
                <div class="flex space-x-2">
                    <Link :href="route('tickets.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-opacity-75 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour à la liste
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Informations du ticket -->
                <Card padding="p-0" class="mb-6">
                    <!-- En-tête du ticket avec statut et badges -->
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-6 py-4 flex flex-wrap items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 flex items-center justify-center rounded-full" :style="{ backgroundColor: ticket.status.color + '20' }">
                                <i :class="ticket.status.icon" :style="{ color: ticket.status.color }" class="text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ ticket.title }}</h3>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Ticket #{{ ticket.id }} • Créé le {{ formatDate(ticket.created_at) }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2 sm:mt-0">
                            <Badge :variant="getStatusVariant(ticket.status.name)">
                                {{ ticket.status.name }}
                            </Badge>
                            <Badge variant="info">
                                {{ ticket.category.name }}
                            </Badge>
                            <Badge variant="warning">
                                {{ ticket.priority.name }}
                            </Badge>
                            <button
                                v-if="!ticket.status.is_closed"
                                @click="toggleSubscription"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white transition-colors"
                                :class="isSubscribed ? 'bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600' : 'bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600'"
                            >
                                <svg v-if="isSubscribed" class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <svg v-else class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                {{ isSubscribed ? 'Se désabonner' : 'S\'abonner' }}
                            </button>

                            <!-- Boutons pour le créateur du ticket -->
                            <div v-if="ticket.creator.id === page.props.auth.user.id && !ticket.status.is_closed" class="flex space-x-2">
                                <button
                                    @click="resolveTicket"
                                    :disabled="isResolving"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 relative transition-colors disabled:opacity-50"
                                >
                                    <span v-if="isResolving" class="absolute inset-0 flex items-center justify-center">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                    <span :class="{ 'opacity-0': isResolving }">
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Marquer comme résolu
                                    </span>
                                </button>
                                <button
                                    @click="closeTicket"
                                    :disabled="isClosing"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 relative transition-colors disabled:opacity-50"
                                >
                                    <span v-if="isClosing" class="absolute inset-0 flex items-center justify-center">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                    <span :class="{ 'opacity-0': isClosing }">
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Fermer ce ticket
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Informations détaillées -->
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Colonne 1: Informations principales -->
                        <div class="col-span-2">
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">Détails</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Créé par</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ ticket.creator.name }}</p>
                                        </div>
                                        <div v-if="ticket.due_date">
                                            <p class="text-gray-500 dark:text-gray-400">Date d'échéance</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(ticket.due_date) }}</p>
                                        </div>
                                        <div v-if="ticket.closed_at">
                                            <p class="text-gray-500 dark:text-gray-400">Fermé le</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(ticket.closed_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">Description</h4>
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-sm">
                                    <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300">
                                        <p style="white-space: pre-line;">{{ ticket.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne 2: Actions (admin seulement) -->
                        <div class="col-span-1">
                            <div v-if="can_manage" class="sticky top-6">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">Actions</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <form @submit.prevent="updateTicket">
                                        <div class="space-y-4">
                                            <!-- Les administrateurs peuvent changer tous les statuts -->
                                            <div v-if="page.props.auth.isAdministrateur">
                                                <label for="status_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                                                <select id="status_id" v-model="form.status_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white sm:text-sm">
                                                    <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                                                </select>
                                            </div>
                                            <!-- Pour tous les autres utilisateurs, le statut est en lecture seule -->
                                            <div v-else>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                                                <div class="mt-1 py-2 px-3 bg-gray-100 dark:bg-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300">
                                                    {{ getStatusName(form.status_id) }}
                                                </div>
                                                <p v-if="ticket.creator.id === $page.props.auth.user.id && !ticket.status.is_closed" class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Utilisez les boutons en haut de la page pour fermer ou résoudre ce ticket.
                                                </p>
                                                <p v-if="ticket.status.is_closed" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Ce ticket est clôturé et ne peut plus être modifié.
                                                </p>
                                            </div>
                                            <!-- Priorité (modifiable uniquement si le ticket n'est pas clôturé) -->
                                            <div v-if="!ticket.status.is_closed">
                                                <label for="priority_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priorité</label>
                                                <select id="priority_id" v-model="form.priority_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white sm:text-sm">
                                                    <option v-for="priority in priorities" :key="priority.id" :value="priority.id">{{ priority.name }}</option>
                                                </select>
                                            </div>
                                            <div v-else>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priorité</label>
                                                <div class="mt-1 py-2 px-3 bg-gray-100 dark:bg-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300">
                                                    {{ getPriorityName(form.priority_id) }}
                                                </div>
                                            </div>
                                            <!-- Seuls les administrateurs peuvent définir une date d'échéance -->
                                            <div v-if="$page.props.auth.isAdministrateur">
                                                <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date d'échéance</label>
                                                <input type="date" id="due_date" v-model="form.due_date" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white sm:text-sm">
                                            </div>
                                            <div>
                                                <button
                                                    type="submit"
                                                    :disabled="form.processing || (ticket.status.is_closed && !page.props.auth.isAdministrateur)"
                                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 relative transition-colors"
                                                    :class="ticket.status.is_closed && !page.props.auth.isAdministrateur ? 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed' : 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600'"
                                                >
                                                    <span v-if="form.processing" class="absolute inset-0 flex items-center justify-center">
                                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </span>
                                                    <span :class="{ 'opacity-0': form.processing }">
                                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Mettre à jour
                                                    </span>
                                                </button>
                                                <p v-if="ticket.status.is_closed && !page.props.auth.isAdministrateur" class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">
                                                    Les tickets clôturés ne peuvent plus être modifiés.
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pièces jointes -->
                    <div v-if="ticket.attachments && ticket.attachments.length > 0" class="px-6 pb-6">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Pièces jointes</h4>
                        <ul class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="attachment in ticket.attachments" :key="attachment.id" class="py-3 flex justify-between items-center">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ attachment.filename }}</span>
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ formatFileSize(attachment.size) }}</span>
                                </div>
                                <a :href="route('attachments.download', attachment.id)" class="text-amber-600 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </Card>

                <!-- Commentaires -->
                <Card padding="p-0" class="mb-6">
                    <!-- En-tête des commentaires -->
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Commentaires</h3>
                    </div>

                    <!-- Liste des commentaires -->
                    <div v-if="ticket.comments && ticket.comments.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="comment in ticket.comments" :key="comment.id" class="p-6" :class="{ 'bg-amber-50 dark:bg-amber-900/20': comment.is_internal }">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ comment.user.name }}</h4>
                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ formatDate(comment.created_at) }}</span>
                                        <span v-if="comment.is_internal" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Note interne
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line prose prose-sm max-w-none">{{ comment.content }}</div>

                                    <!-- Pièces jointes du commentaire -->
                                    <div v-if="comment.attachments && comment.attachments.length > 0" class="mt-3 bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                                        <h5 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Pièces jointes:</h5>
                                        <ul class="space-y-2">
                                            <li v-for="attachment in comment.attachments" :key="attachment.id" class="flex items-center justify-between text-sm">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <span class="text-gray-900 dark:text-white">{{ attachment.filename }}</span>
                                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(attachment.size) }}</span>
                                                </div>
                                                <a :href="route('attachments.download', attachment.id)" class="ml-4 flex-shrink-0 text-amber-600 dark:text-amber-400 hover:text-amber-500 dark:hover:text-amber-300 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>Aucun commentaire pour le moment</p>
                    </div>

                    <!-- Formulaire de commentaire (désactivé si le ticket est clôturé) -->
                    <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                        <div v-if="ticket.status.is_closed" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                            <svg class="w-8 h-8 mx-auto text-gray-400 dark:text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <p class="text-gray-600 dark:text-gray-300">Ce ticket est clôturé. Il n'est plus possible d'ajouter des commentaires.</p>
                        </div>
                        <div v-else>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Ajouter un commentaire</h4>
                            <form @submit.prevent="addComment">
                                <div class="space-y-4">
                                    <div>
                                        <textarea
                                            id="comment"
                                            v-model="commentForm.content"
                                            rows="4"
                                            class="shadow-sm focus:ring-amber-500 focus:border-amber-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                            placeholder="Partagez vos commentaires ou questions..."
                                            required
                                        ></textarea>
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 focus:ring-opacity-75 relative transition-colors"
                                            :disabled="commentForm.processing"
                                        >
                                            <span v-if="commentForm.processing" class="absolute inset-0 flex items-center justify-center">
                                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                            <span :class="{ 'opacity-0': commentForm.processing }">
                                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                Envoyer le commentaire
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </Card>

                <!-- Historique -->
                <Card v-if="ticket.histories && ticket.histories.length > 0" padding="p-6" class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Historique</h3>
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li v-for="(history, historyIdx) in ticket.histories" :key="history.id">
                                <div class="relative pb-8">
                                    <span v-if="historyIdx !== ticket.histories.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-400 dark:bg-gray-600 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ history.user.name }}</span> a modifié
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ formatFieldName(history.field_name) }}</span>
                                                    <template v-if="history.old_value">
                                                        de <span class="font-medium text-gray-900 dark:text-white">{{ history.old_value }}</span>
                                                    </template>
                                                    <template v-if="history.new_value">
                                                        <template v-if="history.old_value"> vers </template>
                                                        <template v-else> à </template>
                                                        <span class="font-medium text-gray-900 dark:text-white"> {{ history.new_value }}</span>
                                                    </template>
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                <time :datetime="history.created_at">{{ formatDate(history.created_at) }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

export default {
    components: {
        Head,
        Link,
        AuthenticatedLayout,
        Card,
        Badge,
    },
    props: {
        ticket: Object,
        isSubscribed: Boolean,
        statuses: Array,
        categories: Array,
        priorities: Array,
        tags: Array,
        users: Array,
        can_manage: Boolean,
    },
    setup(props) {
        // Variables d'état pour suivre le chargement des boutons
        const isResolving = ref(false);
        const isClosing = ref(false);

        // Filtrer les statuts pour n'afficher que ceux qui permettent de clôturer un ticket
        const closingStatuses = computed(() => {
            return props.statuses.filter(status => status.is_closed);
        });

        const form = useForm({
            status_id: props.ticket.status_id,
            priority_id: props.ticket.priority_id,
            assigned_to: props.ticket.assigned_to || '',
            due_date: props.ticket.due_date ? new Date(props.ticket.due_date).toISOString().split('T')[0] : '',
        });

        const commentForm = useForm({
            content: '',
            is_internal: false,
            attachments: [],
        });

        const commentSelectedFiles = ref([]);

        // Accéder à l'objet page
        const page = usePage();

        const updateTicket = () => {
            // Empêcher la mise à jour si le ticket est clôturé et l'utilisateur n'est pas administrateur
            if (props.ticket.status.is_closed && !page.props.auth.isAdministrateur) {
                alert('Ce ticket est clôturé et ne peut plus être modifié.');
                return;
            }

            form.put(route('tickets.update', props.ticket.id), {
                preserveScroll: true,
                onError: (errors) => {
                    if (errors.error) {
                        alert(errors.error);
                    }
                }
            });
        };

        // Méthode pour marquer un ticket comme résolu
        const resolveTicket = () => {
            // Éviter les soumissions multiples
            if (isResolving.value) return;

            // Activer l'indicateur de chargement
            isResolving.value = true;

            // Trouver le statut "Résolu"
            const resolvedStatus = props.statuses.find(status => status.is_closed && (status.name === 'Résolu' || status.name === 'Resolved'));

            if (resolvedStatus) {
                const resolveForm = useForm({
                    status_id: resolvedStatus.id
                });

                resolveForm.put(route('tickets.update', props.ticket.id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        // Recharger la page pour afficher les changements
                        window.location.reload();
                    },
                    onError: (errors) => {
                        // Désactiver l'indicateur de chargement
                        isResolving.value = false;
                        // Afficher une alerte en cas d'erreur
                        alert('Erreur lors de la résolution du ticket: ' + (errors.status || 'Vous ne pouvez pas modifier ce statut'));
                    }
                });
            } else {
                isResolving.value = false;
                alert('Aucun statut "Résolu" n\'est disponible. Veuillez contacter un administrateur.');
            }
        };

        // Méthode pour fermer un ticket
        const closeTicket = () => {
            // Éviter les soumissions multiples
            if (isClosing.value) return;

            // Activer l'indicateur de chargement
            isClosing.value = true;

            // Trouver le statut "Fermé"
            const closedStatus = props.statuses.find(status => status.is_closed && (status.name === 'Fermé' || status.name === 'Closed'));
            // Sinon, utiliser le premier statut de clôture disponible
            const statusToUse = closedStatus || props.statuses.find(status => status.is_closed);

            if (statusToUse) {
                const closeForm = useForm({
                    status_id: statusToUse.id
                });

                closeForm.put(route('tickets.update', props.ticket.id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        // Recharger la page pour afficher les changements
                        window.location.reload();
                    },
                    onError: (errors) => {
                        // Désactiver l'indicateur de chargement
                        isClosing.value = false;
                        // Afficher une alerte en cas d'erreur
                        alert('Erreur lors de la fermeture du ticket: ' + (errors.status || 'Vous ne pouvez pas modifier ce statut'));
                    }
                });
            } else {
                isClosing.value = false;
                alert('Aucun statut de clôture n\'est disponible. Veuillez contacter un administrateur.');
            }
        };

        const addComment = () => {
            commentForm.post(route('tickets.comments.store', props.ticket.id), {
                preserveScroll: true,
                onSuccess: () => {
                    commentForm.reset();
                    commentSelectedFiles.value = [];
                },
            });
        };

        const toggleSubscription = () => {
            axios.post(route('tickets.toggle-subscription', props.ticket.id))
                .then(() => {
                    window.location.reload();
                });
        };

        const handleCommentFileUpload = (e) => {
            const files = Array.from(e.target.files);
            commentSelectedFiles.value = [...commentSelectedFiles.value, ...files];
            commentForm.attachments = commentSelectedFiles.value;
        };

        const removeCommentFile = (index) => {
            commentSelectedFiles.value.splice(index, 1);
            commentForm.attachments = commentSelectedFiles.value;
        };

        const formatDate = (dateString) => {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        };

        const getStatusName = (statusId) => {
            const status = props.statuses.find(s => s.id === statusId);
            return status ? status.name : 'Inconnu';
        };

        const getPriorityName = (priorityId) => {
            const priority = props.priorities.find(p => p.id === priorityId);
            return priority ? priority.name : 'Inconnu';
        };

        const formatFileSize = (bytes) => {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        };

        const formatFieldName = (fieldName) => {
            const fieldMap = {
                'title': 'le titre',
                'description': 'la description',
                'status_id': 'le statut',
                'category_id': 'la catégorie',
                'priority_id': 'la priorité',
                'assigned_to': 'l\'assignation',
                'due_date': 'la date d\'échéance',
                'closed_at': 'la date de fermeture',
                'status': 'le statut',
            };
            return fieldMap[fieldName] || fieldName;
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
            form,
            commentForm,
            commentSelectedFiles,
            closingStatuses,
            isResolving,
            isClosing,
            page,
            updateTicket,
            closeTicket,
            resolveTicket,
            addComment,
            toggleSubscription,
            handleCommentFileUpload,
            removeCommentFile,
            formatDate,
            formatFileSize,
            formatFieldName,
            getStatusName,
            getPriorityName,
            getStatusVariant,
        };
    },
};
</script>
