<template>
<AuthenticatedLayout>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="bg-white rounded shadow-lg w-3/4 p-10  mb-10 mt-10">
            <h1 class="text-center text-2xl font-semibold mb-6">Règles de Confidentialité de Chronos Hub</h1>

            <h2 class="text-xl font-medium mt-4">1. Informations collectées</h2>
            <p class="mt-2">Nous recueillons des informations lorsque vous vous inscrivez sur notre site, connectez-vous à votre compte, faites un achat, participez à un concours, et / ou lorsque vous vous déconnectez. Les informations recueillies incluent votre nom, votre adresse e-mail, numéro de téléphone, et/ou carte de crédit.</p>

            <h2 class="text-xl font-medium mt-4">2. Utilisation des informations</h2>
            <p class="mt-2">Toutes les informations que nous recueillons auprès de vous peuvent être utilisées pour : personnaliser votre expérience et répondre à vos besoins individuels, fournir un contenu publicitaire personnalisé, améliorer notre site Web, améliorer le service client et vos besoins de prise en charge, vous contacter par e-mail, administrer un concours, une promotion, ou une enquête.</p>

            <h2 class="text-xl font-medium mt-4">3. Confidentialité du commerce en ligne</h2>
            <p class="mt-2">Nous sommes les seuls propriétaires des informations recueillies sur ce site. Vos informations personnelles ne seront pas vendues, échangées, transférées, ou données à une autre société pour n’importe quelle raison, sans votre consentement, en dehors de ce qui est nécessaire pour répondre à une demande et / ou une transaction, comme par exemple pour expédier une commande.</p>

            <h2 class="text-xl font-medium mt-4">4. Divulgation à des tiers</h2>
            <p class="mt-2">Nous ne vendons, n'échangeons et ne transférons pas vos informations personnelles identifiables à des tiers. Cela ne comprend pas les tierce parties de confiance qui nous aident à exploiter notre site Web ou à mener nos affaires, tant que ces parties conviennent de garder ces informations confidentielles.</p>

            <h2 class="text-xl font-medium mt-4">5. Protection des informations</h2>
            <p class="mt-2">Nous mettons en œuvre une variété de mesures de sécurité pour préserver la sécurité de vos informations personnelles. Nous utilisons un cryptage à la pointe de la technologie pour protéger les informations sensibles transmises en ligne. Nous protégeons également vos informations hors ligne. Seuls les employés qui ont besoin d’effectuer un travail spécifique (par exemple, la facturation ou le service à la clientèle) ont accès aux informations personnelles identifiables.</p>

            <h2 class="text-xl font-medium mt-4">6. Consentement</h2>
            <p class="mt-2">En utilisant notre site, vous consentez à notre politique de confidentialité.</p>

            <h2 class="text-xl font-medium mt-4">7. Contactez-nous</h2>
            <p class="mt-2">Si vous avez des questions concernant ces règles, n'hésitez pas à nous contacter à limacedric@hotmail.fr.</p>
        </div>
    </div>

</AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "PrivacyPolicy",
    components: {AuthenticatedLayout}
}
</script>

<style scoped>

</style>
