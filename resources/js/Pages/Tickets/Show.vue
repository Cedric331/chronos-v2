<template>
    <Head :title="'Ticket #' + ticket.id" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ticket #{{ ticket.id }} - {{ ticket.title }}
                </h2>
                <div class="flex space-x-2">
                    <Link :href="route('tickets.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Informations du ticket -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-0">
                        <!-- En-tête du ticket avec statut et badges -->
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 flex flex-wrap items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 flex items-center justify-center rounded-full" :style="{ backgroundColor: ticket.status.color + '20' }">
                                    <i :class="ticket.status.icon" :style="{ color: ticket.status.color }" class="text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ ticket.title }}</h3>
                                    <div class="text-sm text-gray-500">
                                        Ticket #{{ ticket.id }} • Créé le {{ formatDate(ticket.created_at) }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-2 sm:mt-0">
                                <span class="px-3 py-1 rounded-full text-xs font-medium" :style="{ backgroundColor: ticket.status.color + '20', color: ticket.status.color }">
                                    {{ ticket.status.name }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-medium" :style="{ backgroundColor: ticket.category.color + '20', color: ticket.category.color }">
                                    {{ ticket.category.name }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-medium" :style="{ backgroundColor: ticket.priority.color + '20', color: ticket.priority.color }">
                                    {{ ticket.priority.name }}
                                </span>
                                <button
                                    v-if="!ticket.status.is_closed"
                                    @click="toggleSubscription"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white"
                                    :class="isSubscribed ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                                >
                                    <i :class="isSubscribed ? 'fas fa-bell-slash mr-1' : 'fas fa-bell mr-1'"></i>
                                    {{ isSubscribed ? 'Se désabonner' : 'S\'abonner' }}
                                </button>

                                <!-- Boutons pour le créateur du ticket -->
                                <div v-if="ticket.creator.id === page.props.auth.user.id && !ticket.status.is_closed" class="flex space-x-2">
                                    <button
                                        @click="resolveTicket"
                                        :disabled="isResolving"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 relative"
                                    >
                                        <span v-if="isResolving" class="absolute inset-0 flex items-center justify-center">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        <span :class="{ 'opacity-0': isResolving }">
                                            <i class="fas fa-check-circle mr-1"></i> Marquer comme résolu
                                        </span>
                                    </button>
                                    <button
                                        @click="closeTicket"
                                        :disabled="isClosing"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-amber-600 hover:bg-amber-700 relative"
                                    >
                                        <span v-if="isClosing" class="absolute inset-0 flex items-center justify-center">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        <span :class="{ 'opacity-0': isClosing }">
                                            <i class="fas fa-times-circle mr-1"></i> Fermer ce ticket
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
                                    <h4 class="text-sm font-medium text-gray-700 mb-2 uppercase tracking-wider">Détails</h4>
                                    <div class="bg-gray-50 rounded-lg p-4 text-sm">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-gray-500">Créé par</p>
                                                <p class="font-medium">{{ ticket.creator.name }}</p>
                                            </div>
                                            <div v-if="ticket.due_date">
                                                <p class="text-gray-500">Date d'échéance</p>
                                                <p class="font-medium">{{ formatDate(ticket.due_date) }}</p>
                                            </div>
                                            <div v-if="ticket.closed_at">
                                                <p class="text-gray-500">Fermé le</p>
                                                <p class="font-medium">{{ formatDate(ticket.closed_at) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2 uppercase tracking-wider">Description</h4>
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 text-sm">
                                        <div class="prose prose-sm max-w-none text-gray-700">
                                            <p style="white-space: pre-line;">{{ ticket.description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne 2: Actions (admin seulement) -->
                            <div class="col-span-1">
                                <div v-if="can_manage" class="sticky top-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2 uppercase tracking-wider">Actions</h4>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <form @submit.prevent="updateTicket">
                                            <div class="space-y-4">
                                                <!-- Les administrateurs peuvent changer tous les statuts -->
                                                <div v-if="page.props.auth.isAdministrateur">
                                                    <label for="status_id" class="block text-sm font-medium text-gray-700">Statut</label>
                                                    <select id="status_id" v-model="form.status_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                                        <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                                                    </select>
                                                </div>
                                                <!-- Pour tous les autres utilisateurs, le statut est en lecture seule -->
                                                <div v-else>
                                                    <label class="block text-sm font-medium text-gray-700">Statut</label>
                                                    <div class="mt-1 py-2 px-3 bg-gray-100 rounded-md text-sm text-gray-700">
                                                        {{ getStatusName(form.status_id) }}
                                                    </div>
                                                    <p v-if="ticket.creator.id === $page.props.auth.user.id && !ticket.status.is_closed" class="mt-1 text-xs text-amber-600">
                                                        <i class="fas fa-info-circle mr-1"></i> Utilisez les boutons en haut de la page pour fermer ou résoudre ce ticket.
                                                    </p>
                                                    <p v-if="ticket.status.is_closed" class="mt-1 text-xs text-gray-500">
                                                        Ce ticket est clôturé et ne peut plus être modifié.
                                                    </p>
                                                </div>
                                                <!-- Priorité (modifiable uniquement si le ticket n'est pas clôturé) -->
                                                <div v-if="!ticket.status.is_closed">
                                                    <label for="priority_id" class="block text-sm font-medium text-gray-700">Priorité</label>
                                                    <select id="priority_id" v-model="form.priority_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                                        <option v-for="priority in priorities" :key="priority.id" :value="priority.id">{{ priority.name }}</option>
                                                    </select>
                                                </div>
                                                <div v-else>
                                                    <label class="block text-sm font-medium text-gray-700">Priorité</label>
                                                    <div class="mt-1 py-2 px-3 bg-gray-100 rounded-md text-sm text-gray-700">
                                                        {{ getPriorityName(form.priority_id) }}
                                                    </div>
                                                </div>
                                                <!-- Pas d'assignation -->
                                                <!-- Seuls les administrateurs peuvent définir une date d'échéance -->
                                                <div v-if="$page.props.auth.isAdministrateur">
                                                    <label for="due_date" class="block text-sm font-medium text-gray-700">Date d'échéance</label>
                                                    <input type="date" id="due_date" v-model="form.due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                                </div>
                                                <div>
                                                    <button
                                                        type="submit"
                                                        :disabled="form.processing || (ticket.status.is_closed && !page.props.auth.isAdministrateur)"
                                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 relative"
                                                        :class="ticket.status.is_closed && !page.props.auth.isAdministrateur ? 'bg-gray-400 cursor-not-allowed' : 'bg-amber-600 hover:bg-amber-700'"
                                                    >
                                                        <span v-if="form.processing" class="absolute inset-0 flex items-center justify-center">
                                                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                        </span>
                                                        <span :class="{ 'opacity-0': form.processing }">
                                                            <i class="fas fa-save mr-2"></i> Mettre à jour
                                                        </span>
                                                    </button>
                                                    <p v-if="ticket.status.is_closed && !page.props.auth.isAdministrateur" class="mt-2 text-xs text-center text-gray-500">
                                                        Les tickets clôturés ne peuvent plus être modifiés.
                                                    </p>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                        </div>

                        <!-- Cette section de description a été déplacée plus haut -->

                        <!-- Pièces jointes -->
                        <div v-if="ticket.attachments && ticket.attachments.length > 0" class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900">Pièces jointes</h4>
                            <ul class="mt-2 divide-y divide-gray-200">
                                <li v-for="attachment in ticket.attachments" :key="attachment.id" class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <i class="fas fa-file text-gray-400 mr-2"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ attachment.filename }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ formatFileSize(attachment.size) }}</span>
                                    </div>
                                    <a :href="route('attachments.download', attachment.id)" class="text-amber-600 hover:text-amber-900">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Commentaires -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-0">
                        <!-- En-tête des commentaires -->
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900">Commentaires</h3>
                        </div>

                        <!-- Liste des commentaires -->
                        <div v-if="ticket.comments && ticket.comments.length > 0" class="divide-y divide-gray-200">
                            <div v-for="comment in ticket.comments" :key="comment.id" class="p-6" :class="{ 'bg-amber-50': comment.is_internal }">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <h4 class="text-sm font-medium text-gray-900">{{ comment.user.name }}</h4>
                                            <span class="ml-2 text-xs text-gray-500">{{ formatDate(comment.created_at) }}</span>
                                            <span v-if="comment.is_internal" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                <i class="fas fa-lock text-xs mr-1"></i> Note interne
                                            </span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700 whitespace-pre-line prose prose-sm max-w-none">{{ comment.content }}</div>

                                        <!-- Pièces jointes du commentaire -->
                                        <div v-if="comment.attachments && comment.attachments.length > 0" class="mt-3 bg-gray-50 rounded-md p-3">
                                            <h5 class="text-xs font-medium text-gray-700 mb-2">Pièces jointes:</h5>
                                            <ul class="space-y-2">
                                                <li v-for="attachment in comment.attachments" :key="attachment.id" class="flex items-center justify-between text-sm">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-paperclip text-gray-400 mr-2"></i>
                                                        <span class="text-gray-900">{{ attachment.filename }}</span>
                                                        <span class="ml-2 text-xs text-gray-500">{{ formatFileSize(attachment.size) }}</span>
                                                    </div>
                                                    <a :href="route('attachments.download', attachment.id)" class="ml-4 flex-shrink-0 text-amber-600 hover:text-amber-500">
                                                        <i class="fas fa-download mr-1"></i> Télécharger
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-6 text-center text-gray-500 border-t border-gray-200">
                            <i class="fas fa-comments text-gray-300 text-4xl mb-2"></i>
                            <p>Aucun commentaire pour le moment</p>
                        </div>

                        <!-- Formulaire de commentaire (désactivé si le ticket est clôturé) -->
                        <div class="border-t border-gray-200 p-6">
                            <div v-if="ticket.status.is_closed" class="bg-gray-50 p-4 rounded-lg text-center">
                                <i class="fas fa-lock text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-600">Ce ticket est clôturé. Il n'est plus possible d'ajouter des commentaires.</p>
                            </div>
                            <div v-else>
                                <h4 class="text-sm font-medium text-gray-700 mb-4">Ajouter un commentaire</h4>
                                <form @submit.prevent="addComment">
                                <div class="space-y-4">
                                    <div>
                                        <textarea
                                            id="comment"
                                            v-model="commentForm.content"
                                            rows="4"
                                            class="shadow-sm focus:ring-amber-500 focus:border-amber-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                            placeholder="Partagez vos commentaires ou questions..."
                                            required
                                        ></textarea>
                                    </div>

                                    <!-- Option commentaire interne (admin seulement) -->
<!--                                    <div v-if="can_manage" class="bg-amber-50 rounded-md p-3">-->
<!--                                        <div class="flex items-start">-->
<!--                                            <div class="flex items-center h-5">-->
<!--                                                <input-->
<!--                                                    id="is_internal"-->
<!--                                                    v-model="commentForm.is_internal"-->
<!--                                                    type="checkbox"-->
<!--                                                    class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-gray-300 rounded"-->
<!--                                                >-->
<!--                                            </div>-->
<!--                                            <div class="ml-3 text-sm">-->
<!--                                                <label for="is_internal" class="font-medium text-amber-800">Note interne</label>-->
<!--                                                <p class="text-amber-700 text-xs">Ce commentaire ne sera visible que par les administrateurs</p>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->

                                    <!-- Pièces jointes -->
                                    <div class="hidden">
                                        <label for="attachments" class="block text-sm font-medium text-gray-700">Pièces jointes</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="comment-file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-amber-600 hover:text-amber-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-amber-500">
                                                        <span>Télécharger des fichiers</span>
                                                        <input id="comment-file-upload" name="comment-file-upload" type="file" class="sr-only" multiple @change="handleCommentFileUpload">
                                                    </label>
                                                    <p class="pl-1">ou glisser-déposer</p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    PNG, JPG, GIF, PDF jusqu'à 10MB
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="commentSelectedFiles.length > 0" class="mt-2">
                                            <h4 class="text-sm font-medium text-gray-700">Fichiers sélectionnés:</h4>
                                            <ul class="mt-1 space-y-1">
                                                <li v-for="(file, index) in commentSelectedFiles" :key="index" class="flex items-center justify-between py-1 pl-3 pr-4 text-sm">
                                                    <div class="flex items-center flex-1 w-0">
                                                        <i class="fas fa-file flex-shrink-0 h-5 w-5 text-gray-400"></i>
                                                        <span class="ml-2 flex-1 w-0 truncate">{{ file.name }}</span>
                                                    </div>
                                                    <div class="ml-4 flex-shrink-0">
                                                        <button type="button" @click="removeCommentFile(index)" class="font-medium text-red-600 hover:text-red-500">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 relative"
                                            :disabled="commentForm.processing"
                                        >
                                            <span v-if="commentForm.processing" class="absolute inset-0 flex items-center justify-center">
                                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                            <span :class="{ 'opacity-0': commentForm.processing }">
                                                <i class="fas fa-paper-plane mr-2"></i> Envoyer le commentaire
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique -->
                <div v-if="ticket.histories && ticket.histories.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Historique</h3>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li v-for="(history, historyIdx) in ticket.histories" :key="history.id">
                                    <div class="relative pb-8">
                                        <span v-if="historyIdx !== ticket.histories.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-history text-white"></i>
                                            </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        <span class="font-medium text-gray-900">{{ history.user.name }}</span> a modifié
                                                        <span class="font-medium text-gray-900">{{ formatFieldName(history.field_name) }}</span>
                                                        <template v-if="history.old_value">
                                                            de <span class="font-medium text-gray-900">{{ history.old_value }}</span>
                                                        </template>
                                                        <template v-if="history.new_value">
                                                            <template v-if="history.old_value"> vers </template>
                                                            <template v-else> à </template>
                                                            <span class="font-medium text-gray-900"> {{ history.new_value }}</span>
                                                        </template>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time :datetime="history.created_at">{{ formatDate(history.created_at) }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

export default {
    components: {
        Head,
        Link,
        AuthenticatedLayout,
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
        };
    },
};
</script>
