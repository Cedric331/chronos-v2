
<template>
    <div class="bg-gray-300 dark:bg-gray-800  2xl:col-span-2 shadow p-4" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
        <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Statistique</h3>
        <p class="dark:text-white my-2">
            Vous pouvez consulter le détail de la planification de chaque conseiller.
        </p>

        <div class="flex flex-col">
            <div class="overflow-x-auto rounded-lg">
                <div class="align-middle inline-block min-w-full">
                    <div>
                        <label for="user">Conseiller:</label>
                        <select v-model="selectedUser" id="user">
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>

                        <label for="startDate">Date de début:</label>
                        <input type="date" v-model="startDate" id="startDate" />

                        <label for="endDate">Date de fin:</label>
                        <input type="date" v-model="endDate" id="endDate" />

                        <button @click="submitForm">Obtenir les statistiques</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    name: "Statistique",
    props: {
        users: Array
    },
    data() {
        return {
            selectedUser: null,
            startDate: '',
            endDate: ''
        };
    },
    methods: {
        submitForm() {
            axios.post('/api/statistics', {
                user_id: this.selectedUser,
                start_date: this.startDate,
                end_date: this.endDate
            });
        }
    }
}
</script>


<style scoped>

</style>
