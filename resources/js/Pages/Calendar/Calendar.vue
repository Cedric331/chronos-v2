<template>
    <section class="bg-white dark:bg-gray-900">
        <div class="mx-5 py-10 mx-auto">
            <div v-if="days.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 w-full p-2">
                <div v-for="day in days" :key="day" class="h-full rounded-lg flex flex-col justify-between">
                    <div class="w-full rounded-lg p-1 min-h-72 dark:bg-gray-600 bg-gray-300">
                        <div class="flex justify-center">
                            <h1 class="font-bold text-xl">{{ day.date }}</h1>
                        </div>
                        <div v-for="planning in day.plannings" class="flex flex-col p-2">
                            <p class="text-lg font-bold flex justify-center">{{ planning.type_day }} {{ day.is_holiday ? '(Jour Férié)' : null }}</p>
                            <div v-if="planning.type_day !== 'Repos'">
                                <p class="text-lg font-bold">Début de Journée : {{ planning.debut_journee }}</p>
                                <p class="text-lg font-bold">Début de Pause : {{ planning.debut_pause }}</p>
                                <p class="text-lg font-bold">Fin de Pause : {{ planning.fin_pause }}</p>
                                <p class="text-lg font-bold">Fin de Journée : {{ planning.fin_journee }}</p>
                            </div>
                            <div v-else class="h-28"></div>
                        </div>
                    </div>
                    <div v-if="day.zone" class="w-full mt-1">
                        <div :class="[day.zone.includes('A') ? 'bg-blue-600 p-1 m-1' : '']" :title="day.zone.includes('A') ? 'Zone A' : ''"></div>
                        <div :class="[day.zone.includes('B') ? 'bg-green-600 p-1 m-1' : '']" :title="day.zone.includes('B') ? 'Zone B' : ''"></div>
                        <div :class="[day.zone.includes('C') ? 'bg-red-600 p-1 m-1' : '']" :title="day.zone.includes('C') ? 'Zone C' : ''"></div>
                    </div>
                </div>
            </div>
            <div v-else class="flex justify-center bg-white dark:bg-gray-900 h-auto">
                <h2 class="text-2xl dark:text-white ">-- Aucun planning --</h2>
            </div>
        </div>

    </section>
</template>

<script>

export default {
    name: "Calendar",
    props: {
        days: Object
    }
}
</script>

<style scoped>

</style>
