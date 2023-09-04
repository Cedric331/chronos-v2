<template>
    <div class="h-auto bg-gray-300 dark:bg-gray-800 p-5" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
        <div class="p-4 sm:p-6 xl:p-8 ">
            <h3 class="text-xl leading-none font-bold text-gray-900 mb-10 dark:text-white">Log de la Team</h3>
            <div class="block w-full overflow-x-auto bg-white rounded-lg p-2">
                <table class="items-center w-full bg-transparent border-collapse">
                    <thead>
                    <tr>
                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Type d'event</th>
                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Team</th>
                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Information</th>
                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px">Type du sujet</th>
                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px">Sujet</th>
                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px">Modifié par</th>
                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap min-w-140-px">Date</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    <tr v-for="activity in activities" class="text-gray-500">
                        <th class="border-t-0 px-4 align-middle text-sm font-normal whitespace-nowrap p-4 text-left">{{ activity.event }}</th>
                        <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">{{ activity.log_name }}</td>
                        <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">{{ activity.description }}</td>
                        <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">{{ activity.subject_type ? activity.subject_type : ''}}</td>
                        <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">{{ activity.subject ? activity.subject.name : ''}}</td>
                        <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">{{ activity.causer ? activity.causer.name : null }}</td>
                        <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">{{ activity.formatted_date }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col items-center mt-10">
            <span class="text-sm text-gray-700 dark:text-gray-400">
              Page : <span class="font-semibold text-gray-900 dark:text-white">{{ page }}</span>
              - Résultats : <span class="font-semibold text-gray-900 dark:text-white">{{ total_count }}</span>
            </span>
                <div class="inline-flex mt-2 xs:mt-0">
                    <a v-if="prev_page_url" @click.prevent="loadPage(prev_page_url)" class="mr-4 flex items-center cursor-pointer justify-center px-3 h-8 text-sm font-medium text-white bg-gray-800 rounded-l hover:bg-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                        </svg>
                        Précédant
                    </a>
                    <a v-if="next_page_url" @click.prevent="loadPage(next_page_url)" class="flex items-center cursor-pointer justify-center px-3 h-8 text-sm font-medium text-white bg-gray-800 border-0 border-l border-gray-700 rounded-r hover:bg-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        Suivant
                        <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
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
