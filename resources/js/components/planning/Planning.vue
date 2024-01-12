<template>
    <!-- Wrapper -->
    <div class="planning" id="vue__planning">
        <ContextMenu ref="ContextMenu"></ContextMenu>
        <!-- Container -->
        <div class="main-container planning" id="context_menu_wrapper">
            <div id="planning-calendar">
                <PlanningDays :user="user" v-show="type == 'DAYS'" :_workspace="workspace"></PlanningDays>
                <PlanningCalendar :user="user" v-show="type == 'MONTH'" :_workspace="workspace"></PlanningCalendar>
            </div>
        </div>
        <!-- ./Container -->
    </div>
    <!-- ./Wrapper -->
</template>

<script>
    export default {
        props: ["user", '_workspace'],

        data() {
            let date = new Date();
            return {
                workspace: this._workspace,
                planning: {},
                selected_talents_id: [],
                selected_talents_name: '',
                selected_task_types_id: [],
                filters: {
                    talents_id: [],
                    projects_id: null,
                    talent_name: "",
                    task_types_id: []
                },
                date_month: date.getMonth() + 1,
                date_year: date.getFullYear(),
                today: moment().format("YYYY-MM-DD"),
                counter_slide: 0,
                translation: 0,
                cell_width: 250,
                type: 'MONTH',
                activeGroupId: false,
                currentGroup: false,
            };
        },
        mounted() {
            this.$bus.$on('UPDATE_ACTIVE_GROUP', (data) => {
                if (data.id) {
                    this.activeGroupId = data.id;
                    this.currentGroup = data;
                } else {
                    this.activeGroupId = data.id;
                    this.currentGroup = false;
                }
            });

            this.$bus.$on('UPDATE_PLANNING_TYPE', (data) => {
                this.type = data;
            });

            this.$bus.$on('PLANNING_FILTERS_SEARCH', (data) => {
                if (data.talent) {
                    let talent = data.talent;
                    this.filters.talent_name = talent.name;
                    this.activeGroupId = false; 
					this.currentGroup = false;
                }
            });
        },
        methods: {
        }
    };
</script>
