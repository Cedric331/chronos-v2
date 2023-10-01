<template>
    <Modal :show="show" :maxWidth="widthCustom">

        <div v-if="showDates[0]" class="overflow-x-auto p-4 bg-white">
            <div v-if="isMobile()">
                <ViewDayTeam :showDates="showDates"></ViewDayTeam>
            </div>

            <div v-else>
                <ViewWeekTeam :showDates="showDates" :weeklyHours="weeklyHours"></ViewWeekTeam>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-center">
            <PrimaryButton @click="this.$emit('close')">Fermer</PrimaryButton>
        </div>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ViewDayTeam from "@/Pages/Calendar/Partials/ViewDayTeam.vue";
import ViewWeekTeam from "@/Pages/Calendar/Partials/ViewWeekTeam.vue";
import {nextTick} from "vue";

export default {
    name: "ModalShowPlanningTeam",
    components: {ViewWeekTeam, ViewDayTeam, PrimaryButton, Modal},
    props: {
        show: Boolean,
        showDay: Object
    },
    data () {
        return {
            showDates: [],
            weeklyHours: [],
            widthCustom: '5xl',
            mobile: false
        }
    },
    watch: {
        mobile () {
            this.showDates = []
            this.getPlanningTeam();
            this.widthModal();
        },
    },
    methods: {
        widthModal () {
            if (this.mobile) {
                this.widthCustom = 'sm';
            } else {
                this.widthCustom = '5xl';
            }
        },
        handleResize() {
            this.mobile = window.innerWidth <= 1200;
        },
        isMobile() {
            return this.mobile;
        },
        getPlanningTeam () {
            axios.post('/planning/team', {
                day_id: this.showDay.id,
                mobile: this.mobile
            })
            .then((response) => {
                this.showDates = response.data.calendar
                this.weeklyHours = response.data.weeklyHours
            })
            .catch((error) => {
                console.log(error);
            })
        },
        checkBgColor (type_day) {
            let color = '';
            if (type_day === 'Planifié') {
                color = 'bg-[#7bed9f]';
            } else if (type_day === 'Congé Payé' || type_day === 'Récup JF') {
                color = 'bg-[#60a3bc]';
            } else if (type_day === 'Repos' || type_day === 'Jour Férié') {
                color = 'bg-[#82ccdd]';
            } else if (type_day === 'Maladie') {
                color = 'bg-[#f8c291]';
            } else {
                color = 'bg-[#b8e994]';
            }
            return { [color]: true };
        }
    },
    mounted() {
        window.addEventListener('resize', this.handleResize);
        this.handleResize();
        this.getPlanningTeam();
        this.widthModal();
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.handleResize);
    }
}
</script>

<style scoped>
td, .tdCustom {
    border: 1px solid #000;
    padding: 10px;
    margin: 5px;
    text-align: center;
    vertical-align: middle;
    font-size: 14px;
    font-weight: bold;
}

</style>
