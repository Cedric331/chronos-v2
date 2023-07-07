<template>
    <div class="bg-gray-300 dark:bg-gray-800 shadow rounded-lg p-4 2xl:col-span-2 min-h-screen">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Partage des liens
                    </h3>
                </div>
                <div class="flex-shrink-0">
                    <PrimaryButton class="flex justify-center">
                        Ajouter un lien
                    </PrimaryButton>
                </div>
            </div>
        <div class="flex flex-col mt-5">
            <div class="overflow-x-auto rounded-lg">
                <div class="align-middle inline-block min-w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                Lien
                            </th>
                            <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                Ajouté par
                            </th>
                            <th scope="col" class="p-2 text-xs font-medium text-gray-500 w-1/5 tracking-wider">
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-200">
                        <tr v-for="(link, i) in links" class="hover:bg-gray-200 dark:hover:bg-gray-400">
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                <a :href="link.link" target="_blank" class="text-[#70a1ff]">Accéder au lien</a>
                            </td>
                            <td class="p-4 whitespace-normal text-sm font-normal text-gray-900">
                                <div class="w-full">
                                    {{ link.description }}
                                </div>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                {{ link.user.name }}
                            </td>
                            <td class="p-4 flex items-center justify-center">
                                <svg v-if="link.user.id === $page.props.auth.user.id || $page.props.auth.isCoordinateur" @click="editUser(link)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#70a1ff" class="w-5 h-5 mr-5 cursor-pointer">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                <svg v-if="link.user.id === $page.props.auth.user.id || $page.props.auth.isCoordinateur" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b81" class="w-5 h-5 cursor-pointer">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
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
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    name: "InformationLink",
    components: {
        PrimaryButton
    },
    props: {
        links: {
            type: Array,
            required: true
        }
    },
}
</script>

<style scoped>

</style>
