<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="">
            <div class="w-full mx-auto">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <Calendar :days="days"></Calendar>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Calendar from "@/Pages/Calendar/Calendar.vue";

export default {
    components: {
        Calendar,
        AuthenticatedLayout,
        Head
    },
    props: {
        days: Object
    },
    data() {
        return {

        }
    }
}
</script>
