<template>
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $t('titre_gestion_team') }}</h3>
            </div>
            <div class="flex-shrink-0">
                <SecondaryButton>Ajouter un membre</SecondaryButton>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto rounded-lg">
                <div class="align-middle inline-block min-w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-200">
                        <tr>
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                {{ team.name }}
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";

export default {
    name: "TeamGestion",
    components: {
        SecondaryButton
    },
    props: {
        team: Object
    }
}
</script>

<style scoped>

</style>
