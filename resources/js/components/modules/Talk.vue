<template v-if="talk.length == 0">
    <div class="block has-bg overflow-hidden has-no-content" style="box-shadow: 0px 0px 15px #120b23">
        <div style="display: flex">
            <div class="main-talk">
                à venir
                <div class="">
                    <a href="javascript:;" class="tiny-button-section is-gradient" v-on:click="Global._setAction('SET_TASK', { project: project, type: 'ADD', formType: 'task'})" data-id="TaskForm">
                        <span class="tiny-button icon-plus"> </span>
                    </a>
                </div>
            </div>
            <!-- Date picker -->
            <div class="planning-dashboard is-date">
                <button v-on:click="handleDateChange('DOWN')" class="nav-arrow is-prev">
                    <span class="sr-only">Précédent</span>
                </button>

                <p style="text-align: center; width: 100%">
                    <strong>{{ date_month | monthLabel }}</strong>
                    {{ date_year }}
                </p>

                <button v-on:click="handleDateChange('UP')" class="nav-arrow is-next">
                    <span class="sr-only">Suivant</span>
                </button>
            </div>
        </div>
        <div class="plan-wrapper">
            <transition-group name="switch-side" appear mode="out-in">
                <div v-for="(day, i) in tasksByDay" :key="day[0]" :class="{ nextDays: i >= 2 || date_month != this_mounth }" class="day-plan-container">
                    <span v-if="day[0] === tomorrow" class="date">Demain</span>
                    <span v-else class="date"> {{ day[0] }}</span>
                    <div class="event-container">
                        <div class="add-btn" v-on:click="Global._setAction('SET_TASK', {project: project, type: 'ADD', formType: 'task' })" data-id="TaskForm">
                            <p><span>+</span> Ajouter une tâche</p>
                        </div>
                        <div v-for="events in day[1]" v-if="events.type == 'MISSION EXPLORER'" v-bind:key="events" class="is-actions" style="display: flex; flex-direction: column; width: 100%; justify-content: flex-end; gap: 0.5rem;">
                            <div class="planned-task-container" v-on:click.prevent="contextMenu.show($event, 'PLANNING', { planning: events }, contextMenuCallback, [true])">
                                <span class="dot-button-task" :class="(events.task_type == '3D') ? 'd3' : events.task_type"></span>
                                <span class="task-type" :class="(events.task_type == '3D') ? 'd3' : events.task_type">{{ $t(events.task_type) }}</span>
                                <p v-if="events.project_name.length > 35" class="description-task" :title="events.project_name">
                                    {{ events.project_name.substring(0, 35) +  "..."}} </p>
                                <p v-else class="description-task">
                                    {{ events.project_name }}
                                </p>
                                <span class="task-mission-explorer">{{ $t('mission-explorer') }}</span>
                            </div>
                        </div>
                        <div v-for="events in day[1]" v-if="events.type == 'APPOINTMENT'" v-bind:key="events" class="is-actions"
                            style="display: flex; flex-direction: column; width: 100%; justify-content: flex-end; gap: 0.5rem;">
                            <div class="planned-task-container"
                                v-on:click.prevent="contextMenu.show($event, 'PLANNING', { planning: events }, contextMenuCallback,
                                { projectId: events.project_id, task: events, type: 'EDIT' })">
                                <span class="dot-button-task RDV"></span>
                                <span class="task-type RDV" :class="events.type">RDV</span>
                                <p class="description-task" v-if="events.firstname.length > 25" :title="events.firstname"> {{ events.firstname.substring(0, 25) + "..." }}</p>
                                <p class="description-task" v-else> {{ events.firstname }}</p>
                                <span class="rdv__clock">
                                    <span class="picto picto-time"></span>
                                    {{ events.hourtime }}
                                </span>
                            </div>
                        </div>
                        <div v-for="events in day[1]" v-if="events.type == 'TASK'" v-bind:key="events" class="is-actions" style="display: flex; flex-direction: column; width: 100%; justify-content: flex-end; gap: 0.5rem;">
                            <div class="planned-task-container" v-on:click.prevent="contextMenu.show($event, 'PLANNING', { planning: events }, contextMenuCallback,
                                        { projectId: events.project_id, status: events.status, closedAt: events.closedAt, task: events} )">
                                <span class="dot-button-task" :class="(events.task_type == '3D') ? 'd3' : events.task_type"></span>
                                <span class="task-type" :class="(events.task_type == '3D') ? 'd3' : events.task_type">
                                    {{ $t(events.task_type) }}
                                </span>
                                <p v-if="events.project_name.length > 35" class="description-task" :title="events.project_name">
                                    {{ events.project_name.substring(0, 35) + "..." }}
                                </p>
                                <p v-else class="description-task">
                                    {{ events.project_name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </transition-group>
        </div>
    </div>
</template>

<script>
import Planning from "../planning/Planning.vue";
export default {
    components: {
        Planning
    },
    props: ["all_tasks", "tasks", "user", "contextMenu", "contextMenuCallback", "_workspace"],
    data() {
        let date = new Date();
        return {
            planning: {},
            selected_talents_id: [],
            selected_talents_name: [],
            filters: {
                talents_id: [],
                projects_id: null,
                project_name: "",
                task_types_id: []
            },
            this_mounth: date.getMonth() + 1,
            date_month: date.getMonth() + 1,
            date_year: date.getFullYear(),
            tomorrow: moment()
                .add(1, "days")
                .format("dddd DD"),
            week: [
                moment().add(1, "days"),
                moment().add(2, "days"),
                moment().add(3, "days"),
                moment().add(4, "days"),
                moment().add(5, "days"),
                moment().add(6, "days"),
                moment().add(7, "days")
            ],
            counter_slide: 0,
            translation: 0,
            cell_width: 250,
            plan: [],
            tasksToday: [],
            dayTask: [],
            workingWeek: {},
            tasksByDay: [],
            workspace: this._workspace
        };
    },

    mounted() {
        this.getPlanning();
        this.$bus.$on("TASK_ADD_OR_UPDATE", () => {
            this.getPlanning();
        });

        this.$bus.$on("APPOINTMENT_ADD_OR_UPDATE", () => {
            this.getPlanning();
        });
    },

    methods: {
        handleDateChange(type) {
            switch (type) {
                case "UP":
                    this.date_month++;
                    if (this.date_month > 12) {
                        this.date_year++;
                        this.date_month = 1;
                    }
                    this.getPlanning();
                    //this.$emit('reload', this.planning)
                    break;
                case "DOWN":
                    this.date_month--;
                    if (this.date_month < 1) {
                        this.date_year--;
                        this.date_month = 12;
                    }
                    this.getPlanning();
                    break;
            }
        },
        getPlanning() {
            // Check if the current user id is the array
            this.tasksByDay = [];
            this.workingWeek = [];
            this.tasksToday = [];
            this.filters.talents_id = [this.user.id];

            axios
                .get("/api/planning", {
                    params: {
                        workspace: this.workspace,
                        filter_talents_id: this.filters.talents_id,
                        filter_projects_id: this.filters.projects_id,
                        filter_task_types_id: this.filters.task_types_id,
                        date_month: this.date_month,
                        date_year: this.date_year
                    }
                })
                .then(res => {
                    if (res.success === false) {
                        // Todo : Error
                        console.log("error : impossible to get to planning");
                    } else {
                        let datas = res.data.datas;
                        this.planning = datas.planning; // Update project task list
                        this.selected_talents_name = datas.talent_name; // Talent name list
                        this.selected_task_types_name = datas.task_name; // Task name list
                        this.SetPlanningVariables();
                    }
                })
                .catch(error => console.log(error));
        },
        SetPlanningVariables() {
            Object.keys(this.planning).forEach(date => {
                this.plan.push(this.planning[date]);
                this.dayTask.push(new Date(date));
                Object.keys(this.planning[date]).forEach(id => {
                    this.tasksToday.push(this.planning[date][id].events);
                });
            });
            this.getThisWeek();
        },
        getThisWeek() {
            let i = [];
            let e = [];
            let a = 0;
            let c = 0;
            let v = 0;
            if (this.date_month == new Date().getMonth() + 1) {
                Object.keys(this.planning).forEach(day => {
                    for (var d of this.week) {
                        if (day == d.format("YYYY-MM-DD") && this.planning[day] != undefined) {
                            e.push(Object.entries(this.planning[day]));
                            let anArr = [];
                            for (let b = 0; b < e[a].length; b++) {
                                if (e[a][b][0] != undefined) {
                                    anArr.push(e[a][b][1].events);
                                }
                            }
                            i = [];
                            anArr.forEach(ele => {
                                i.push(ele);
                            });
                            for (let c = 0; c < i.length; c++) {
                                if (i[c] != undefined) {
                                    i[c].forEach(el => {
                                        el.start_date = moment(
                                            el.start_date
                                        ).format("DD");
                                        el.end_date = moment(
                                            el.end_date
                                        ).format("DD MMMM");
                                        el.hourtime = moment(
                                            el.datetime
                                        ).format("HH:mm");
                                        el.datetime = moment(
                                            el.datetime
                                        ).format("dddd DD");
                                    });
                                }
                            }
                            let myArr = [];
                            i.forEach(el => {
                                el.forEach(ele => {
                                    myArr.push(ele);
                                });
                            });
                            this.workingWeek[d.format("dddd DD")] = myArr;
                            myArr = [];
                            a++;
                        }
                    }
                });
            } else if (
                this.date_month > new Date().getMonth() + 1 ||
                this.date_month < new Date().getMonth() + 1
            ) {
                Object.keys(this.planning).forEach(day => {
                    if (day != undefined) {
                        e.push(Object.entries(this.planning[day]));
                        let anArr = [];
                        for (let b = 0; b < e[a].length; b++) {
                            if (e[a][b][0] != undefined) {
                                anArr.push(e[a][b][1].events);
                            }
                        }
                        i = [];
                        anArr.forEach(ele => {
                            i.push(ele);
                        });
                        for (let c = 0; c < i.length; c++) {
                            if (i[c] != undefined) {
                                i[c].forEach(el => {
                                    el.start_date = moment(
                                        el.start_date
                                    ).format("DD");
                                    el.end_date = moment(el.end_date).format(
                                        "DD MMMM"
                                    );
                                    el.hourtime = moment(el.datetime).format(
                                        "HH:mm"
                                    );
                                    el.datetime = moment(el.datetime).format(
                                        "dddd DD"
                                    );
                                });
                            }
                        }
                        let myArr = [];
                        i.forEach(el => {
                            el.forEach(ele => {
                                myArr.push(ele);
                            });
                        });
                        this.workingWeek[moment(day).format("dddd DD")] = myArr;
                        myArr = [];
                        a++;
                    }
                });
            }
            this.tasksByDay = Object.entries(this.workingWeek);
        },
    }
};
</script>
