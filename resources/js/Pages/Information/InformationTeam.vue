<template>
    <AuthenticatedLayout>
        <div id="main-content" class="min-h-screen w-full bg-gray-50 relative overflow-y-auto">
            <main>
                <div class="pt-6 px-4">
                    <div class="w-full grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-4 gap-4 mb-10" :class="[$page.props.auth.team.params.share_link ? '2xl:grid-cols-2' : '2xl:grid-cols-1']">
                        <InformationUser v-if="users" :users="users"></InformationUser>
                        <InformationLink v-if="$page.props.auth.team.params.share_link" :links="links"></InformationLink>
                    </div>
                </div>
            </main>

        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InformationUser from "@/Pages/Information/Partials/InformationUser.vue";
import InformationLink from "@/Pages/Information/Partials/InformationLink.vue";

export default {
    name: "InformationTeam",
    components: {InformationLink, InformationUser, AuthenticatedLayout},
    props: {
            usersProps: Object,
            linksProps: Object
    },
    data () {
      return {
            users: [],
            links: []
      }
    },
    mounted() {
        this.users = this.usersProps
        this.links = this.linksProps
    }
}
</script>

<style scoped>

</style>
