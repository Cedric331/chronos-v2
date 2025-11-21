<template>
    <div class="p-6">
        <div>
            <div class="mb-6 flex items-center">
                <div class="p-2 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 mr-3 shadow-lg">
                    <i class="fas fa-history text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Journal d'activités</h3>
            </div>
            <div class="block w-full overflow-x-auto bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-gray-200/50 dark:border-gray-700/50">
                <table class="items-center w-full bg-transparent border-collapse">
                    <thead>
                    <tr class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30">
                        <th class="px-5 text-gray-700 dark:text-gray-300 align-middle py-4 text-xs font-bold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Type d'event</th>
                        <th class="px-5 text-gray-700 dark:text-gray-300 align-middle py-4 text-xs font-bold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Team</th>
                        <th class="px-5 text-gray-700 dark:text-gray-300 align-middle py-4 text-xs font-bold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Information</th>
                        <th class="px-5 text-gray-700 dark:text-gray-300 align-middle py-4 text-xs font-bold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px">Type du sujet</th>
                        <th class="px-5 text-gray-700 dark:text-gray-300 align-middle py-4 text-xs font-bold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px">Sujet</th>
                        <th class="px-5 text-gray-700 dark:text-gray-300 align-middle py-4 text-xs font-bold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px">Modifié par</th>
                        <th class="px-5 text-gray-700 dark:text-gray-300 align-middle py-4 text-xs font-bold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px">Date</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="activity in activities" class="text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <th class="border-t-0 px-5 align-middle text-sm font-semibold whitespace-nowrap dark:text-white py-4 text-left">{{ activity.event }}</th>
                        <td class="border-t-0 px-5 align-middle text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap py-4">{{ activity.log_name }}</td>
                        <td class="border-t-0 px-5 align-middle text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap py-4">{{ activity.description }}</td>
                        <td class="border-t-0 px-5 align-middle text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap py-4">{{ activity.subject_type ? activity.subject_type : ''}}</td>
                        <td class="border-t-0 px-5 align-middle text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap py-4">{{ activity.subject ? activity.subject.name : ''}}</td>
                        <td class="border-t-0 px-5 align-middle text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap py-4">{{ activity.causer ? activity.causer.name : null }}</td>
                        <td class="border-t-0 px-5 align-middle text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap py-4">{{ activity.formatted_date }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col items-center mt-8">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-4">
              Page : <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ page }}</span>
              - Résultats : <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ total_count }}</span>
            </span>
                <div class="inline-flex gap-2 mt-2 xs:mt-0">
                    <a v-if="prev_page_url" @click.prevent="loadPage(prev_page_url)" class="flex items-center cursor-pointer justify-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-lg transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                        </svg>
                        Précédent
                    </a>
                    <a v-if="next_page_url" @click.prevent="loadPage(next_page_url)" class="flex items-center cursor-pointer justify-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-lg transform hover:scale-105">
                        Suivant
                        <svg class="w-4 h-4 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        team: {
            type: Object,
            required: false
        },
    },
    data() {
        return {
            activities: null,
            total_count: 0,
            page: 1,
            per_page: 10,
            next_page_url: null,
            prev_page_url: null,
            error: null
        }
    },
    methods: {
        paginateActivities() {
            this.page = this.activities.current_page
            this.per_page = this.activities.per_page
            this.total_count = this.activities.total
            this.next_page_url = this.activities.next_page_url
            this.prev_page_url = this.activities.prev_page_url
            this.activities = this.activities.data
        },
        loadPage(url) {
            axios.get(url).then(response => {
                this.activities = response.data
                this.paginateActivities()
            }).catch(() => {
                this.error = "Erreur lors du chargement des données."
            });
        }
    },
    mounted () {
        if (this.team) {
            this.loadPage('/teams/activities?page=&team_id=' + this.team.id)
        } else {
            this.loadPage('/teams/activities?page=')
        }
    },
}
</script>

<style scoped>

</style>
