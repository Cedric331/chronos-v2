
<template>
<AuthenticatedLayout>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="bg-white rounded shadow-lg w-3/4 p-10  mb-10 mt-10">
            <h1 class="text-center text-2xl font-semibold mb-6">Conditions d'Utilisation de Chronos Hub</h1>

            <h2 class="text-xl font-medium mt-4">1. Acceptation des conditions</h2>
            <p class="mt-2">En utilisant le site www.chronos-hub.fr, vous acceptez les conditions d'utilisation énoncées dans ce document.</p>

            <h2 class="text-xl font-medium mt-4">2. Accès au site</h2>
            <p class="mt-2">Nous nous réservons le droit de retirer ou de modifier le service que nous fournissons sur notre site sans préavis. Nous ne serons pas responsables si, pour une raison quelconque, notre site est indisponible à tout moment ou pendant une période quelconque.</p>

            <h2 class="text-xl font-medium mt-4">3. Utilisation licite</h2>
            <p class="mt-2">Vous vous engagez à ne pas utiliser notre site à des fins illégales ou prohibées par ces termes.</p>

            <h2 class="text-xl font-medium mt-4">4. Restrictions sur l'utilisation</h2>
            <p class="mt-2">La reproduction, la distribution, l'affichage ou la transmission du contenu du site est strictement interdite, sauf autorisation de Chronos Hub.</p>

            <h2 class="text-xl font-medium mt-4">5. Liens vers d'autres sites</h2>
            <p class="mt-2">Notre site peut contenir des liens vers des sites tiers. Ces liens sont fournis uniquement pour votre commodité. Nous ne sommes pas responsables du contenu des sites tiers.</p>

            <h2 class="text-xl font-medium mt-4">6. Modification des conditions</h2>
            <p class="mt-2">Chronos Hub peut réviser ces conditions d'utilisation à tout moment en mettant à jour cette page. Vous êtes censé consulter cette page de temps en temps pour prendre connaissance des changements que nous avons apportés.</p>

            <h2 class="text-xl font-medium mt-4">7. Contactez-nous</h2>
            <p class="mt-2">Si vous avez des questions concernant ces conditions, n'hésitez pas à nous contacter à limacedric@hotmail.fr.</p>
        </div>
    </div>

</AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "Conditions",
    components: {AuthenticatedLayout}
}
</script>

<style scoped>

</style>
