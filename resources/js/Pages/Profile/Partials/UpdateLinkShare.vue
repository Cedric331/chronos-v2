<script setup>
import axios from 'axios';
import { useForm, usePage } from '@inertiajs/vue3';
import { notify } from "@kyvg/vue3-notification";

const props = defineProps({
    links: Object,
});

const links = props.links;

const user = usePage().props.auth.user;

let page = usePage();

function copyUrlToClipboard(url) {
    navigator.clipboard.writeText(url).then(() => {
        notify({
            title: "Succès",
            type: "success",
            text: "Lien copié avec succès!",
        });
    }).catch(err => {
        console.log('Erreur lors de la copie de l\'URL: ', err);
    });
}
    function deleteLink(id) {
        axios.delete('/planning/share/'+ id)
            .then(() => {
                links.splice(links.findIndex(link => link.id === id), 1);
                notify({
                    title: "Succès",
                    type: "success",
                    text: "Lien supprimé avec succès!",
                });
            })
            .catch((error) => {
                console.error(error);
                notify({
                    title: "Erreur",
                    type: "error",
                    text: error.response.data.message,
                });
            });
        }

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mes liens partagés</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Vous pouvez visualiser et supprimer les liens que vous avez créés.
            </p>
        </header>

        <div class="mt-6 space-y-6 w-full">

            <div v-if="links.length > 0" class="relative w-full overflow-hidden sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <tbody>
                    <tr @click.prevent="copyUrlToClipboard(link.token)" v-for="link in links" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 cursor-pointer dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ link.token }}
                        </th>
                        <td class="px-6 py-4 text-right">
                            Utilisé {{ link.count_view }} fois
                        </td>
                        <td class="px-6 py-4 text-right" :class="[!link.linkValide ? 'text-[#ff4757]' : 'text-[#2ed573]']">
                            Expire le {{ link.DateExpired }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            <button @click.stop="deleteLink(link.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b81" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="relative w-full overflow-hidden sm:rounded-lg">
                <p class="dark:text-white text-sm">Vous n'avez pas encore de lien partagé.</p>
            </div>
        </div>
    </section>
</template>
