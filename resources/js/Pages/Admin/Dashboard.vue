<template>
    <AuthenticatedLayout>
        <div class="flex overflow-hidden" :class="[this.$store.state.isDarkMode ? 'bg-gray-900' : 'bg-white']">
            <div class="opacity-50 hidden fixed inset-0 z-10 " id="sidebarBackdrop"></div>
            <div id="main-content" class="h-full w-full relative overflow-y-auto" :style="{ backgroundColor: this.$store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
                <main>
                    <div class="p-6">
                        <div class="w-full p-4 grid grid-cols-1">
                            <Teams :teams-props="teamsProps"  />
                        </div>
                        <div class="grid grid-cols-1 my-4">
                            <Log :team="this.team"></Log>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>

import {defineComponent} from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Teams from "@/Pages/Admin/Components/Teams.vue";
import Log from "@/Pages/Team/Components/Log.vue";

export default defineComponent({
    components: {
        Log,
        Teams,
        SecondaryButton,
        PrimaryButton,
        AuthenticatedLayout
    },
    props: {
        teamsProps: {
            type: Array,
            required: true
        }
    },
    data () {
        return {
        }
    },
    methods: {
      //
    },
})
</script>

<style scoped>

</style>
