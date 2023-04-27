<template>
    <section class="bg-white dark:bg-gray-900">
        <div class="mx-5 py-10 mx-auto">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-7 lg:grid-cols-5 w-full p-2">
                <div v-for="day in days" :key="day">
                    <div class="w-full h-64 bg-gray-300 rounded-lg dark:bg-gray-600 p-1">
                        <div class="flex justify-center">
                            <h1 class="font-bold text-xl">{{ day.date }}</h1>
                        </div>
                        <div class="flex justify-center">
                            <p class="font-bold">{{ day.is_holiday ? 'Jour Férié ' + '('+ day.name_holiday + ')' : null }}</p>
                        </div>
                    </div>
                    <div v-if="day.zone" class="w-full">
                        <div :class="[day.zone.includes('A') ? 'bg-blue-600 p-1 m-1' : '']" :title="day.zone.includes('A') ? 'Zone A' : ''"></div>
                        <div :class="[day.zone.includes('B') ? 'bg-green-600 p-1 m-1' : '']" :title="day.zone.includes('B') ? 'Zone B' : ''"></div>
                        <div :class="[day.zone.includes('C') ? 'bg-red-600 p-1 m-1' : '']" :title="day.zone.includes('C') ? 'Zone C' : ''"></div>
<!--                        <div :class="[day.zone.includes('Corse') ? 'bg-yellow-600 p-1 m-1' : '']" :title="day.zone.includes('C') ? 'Zone Corse' : ''"></div>-->
                    </div>
                </div>
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
