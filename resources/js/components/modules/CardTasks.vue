<template>
    <!-- Card -->

    <div class="card__task block dashboard__projects-block" v-bind:class="{ 'has-no-content': tasksToShow.length == 0 }" style="background: #2b1c56">
        <transition name="switch" appear mode="out-in">
            <div class="card-task-container" v-if="tasksToShow.length == 0" v-on:click="Global._setAction('SET_TASK', {type: 'ADD', formType: 'task'})">
                <img src="images/nocontent.png" alt="" style="width: 63px; height: 47px; margin: auto; position: absolute; top: 49%; left: 50%; transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%); transform: translate3d(-50%, -50%, 0);"/>
                <div style="color: #7665a7; font-weight: 700">
                    Ajouter une tâche
                </div>
            </div>
        </transition>
        <!-- List, if project style="background: #2B1C56;"-->
        <div class="projects-table__content-wrapper">
            <div style="display: flex; padding-left: 10px;">
                <div style="display: flex; background: #1e1438; width: 127px; align-items: center; border-radius: 5px; padding: 3px 3px 3px 24px; justify-content: space-between; color: white; margin-top: 10px; padding-left: 20px; font-weight: bold;">
                    Tâches
                    <div class="">
                        <a href="javascript:;" class="tiny-button-section is-gradient" tiny-button-section is- v-on:click="Global._setAction('SET_TASK', { project: project, type: 'ADD', formType: 'task'})" data-id="TaskForm">
                            <span class="tiny-button icon-plus"></span>
                        </a>
                    </div>
                </div>
                <div class="filter-select" style="display: flex; background: #1e1438; max-width: 81%; align-items: center; border-radius: 5px; padding: 5px 2rem; color: white; margin-left: 19px; margin-top: 10px; font-weight: bold;"
                    v-if="tasks.length > 0 || waiting_tasks.length > 0 || closed_tasks.length > 0">
                    <button type="button" class="c-1 tasks-tabs" :class="tasksStatus == 'progress' ? 'active-tasks' : ''" id="progress-btn" v-if="tasks.length > 0" v-on:click="switchTaskStatus({ status: 'STATUS_PROGRESS' })">
                        Aujourd'hui
                        <div class="poopover" :class="{ checked: TodayChecked }">
                            {{ tasks.length }}
                        </div>
                    </button>
                    <button type="button" class="c-1 tasks-tabs" :class="tasksStatus == 'waiting' ? 'active-tasks' : ''" v-if="waiting_tasks > '0'" id="waiting-btn" v-on:click="switchTaskStatus({ status: 'STATUS_WAITING' })">
                        Demain
                        <div :class="{ checked: TomorowChecked }" class="poopover">
                            {{ waiting_tasks.length }}
                        </div>
                    </button>
                    <button class="c-1 tasks-tabs" :class="tasksStatus == 'closed' ? 'active-tasks' : ''" v-if="closed_tasks.length > '0'" id="closed-btn" v-on:click="switchTaskStatus({ status: 'STATUS_CLOSED' })">
                        Clôturées
                        <div class="poopover" :class="{ checked: CloseChecked }">
                            {{ closed_tasks.length }}
                        </div>
                    </button>
                </div>
            </div>
            <div class="s-a">
            <transition name="switch" appear mode="out-in">
                <transition-group name="slide_cards" tag="div" mode="out-in" class="projects-table__content scroll-content" v-if="tasksToShow.length > 0">
                    <!-- Task -->
                    <div class="project-card__col" v-for="(task, taskKey) in tasksToShow" @mouseenter.once="checked('tasks-' + task.id, task.id, taskKey)" :key="task.id"
                    v-on:contextmenu.prevent="contextMenu.show($event, 'TASK', { task: task, project: task.project }, contextMenuCallback, [true])">
                        <div class="project-card" :class="task.id == taskToClose.id ? 'to-close' : isClosed(task) ? 'is-waiting' : ''" v-if="task.status == 'PROGRESS'" :id="'tasks-' + task.id" :data-id="task.id">
                            <!-- Category -->
                            <div :class="'project__category'" :style="buttonCategoryStyle(task.project_category_color)">
                                {{ $t(task.project_category) }}
                            </div>
                            <span class="text-task main_new text-task-position" style="font-weight: 700" v-show="task.status !== 'CLOSED'" v-if="moment(task.end_date).subtract(1, 'days').diff(today, 'days') > 0">
                                <i class="picto picto-clock"></i>
                                {{ moment(task.end_date).diff(today, "days") }}J Restants</span>
                            <span class="text-task main_new text-task-position" style="font-weight: 700" v-else-if=" moment(task.end_date).subtract(1, 'days').diff(today, 'days') == 0">
                                Dernier Jour
                            </span>
                            <span class="text-task main_new text-task-position" style="font-weight: 700" v-else >Délai écoulé</span>
                            <div v-if="task.project_category !== 'mission-explorer'" class="js-toggle-wrapper">
                                <div class="button project-detail__action-button-task click" style="padding: 0; display: flex; justify-content: center; align-items: center;"
                                    v-on:click.prevent=" contextMenu.show($event, 'TASK', {task: task, project: task.project}, false, [true])">
                                    <span class="dot-button-task"></span>
                                    <span class="dot-button-task"></span>
                                    <span class="dot-button-task"></span>
                                </div>
                                <div class="actions-box">
                                    <ul class="context-menu--items">
                                        <template v-if="Task.status !== 'CLOSED' || task.project_category !== 'mission-explorer'">
                                            <li class="context-menu--items-element" v-if="task.project_category !== 'mission-explorer'"
                                                v-on:click="Global._setAction('SET_TASK', {element: task.project_id, task: task, type: 'EDIT'})">
                                                Modifier la tâche
                                            </li>
                                            <li class="context-menu--items-element" v-if="task.project_category !== 'mission-explorer'"
                                                v-on:click="Task._close(task, callback, params)">
                                                Clôturer la tâche
                                            </li>
                                        </template>
                                        <li class="context-menu--items-element" v-on:click="Project._goTo(project)">
                                            Voir le projet
                                        </li>
                                        <!--    <hr /> -->
                                        <li v-if="task.project_category !== 'mission-explorer'" class="context-menu--items-element"
                                            v-on:click="Task._delete(task, callback, params)">
                                            Supprimer la tâche
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- Infos -->
                            <div class="project-card__infos">
                                <p style="display: flex; align-items: baseline">
                                    <span style=" position: absolute; left: 0; width: 9px; height: 9px; margin-left: 10px; top: 14%; border-radius: 50%;" class="dot-button-task" :class="(task.task_type == '3D') ? 'd3' : task.task_type"></span>
                                    <strong>{{ $t(task.task_type) }}</strong>
                                </p>
                                <template v-if="task.status !== 'CLOSED'">
                                    <p v-on:click="goToProject($event, task.project_id)">{{ task.project_name }}</p>
                                </template>
                                <template v-else><p>{{ task.project_name }}</p></template>
                                <p v-if="task.status == 'CLOSED'">Clôturé le : {{ task.closed_at | dateFormat }}</p>
                            </div>
                            <div class="new-box">
                                <label for="" class="form-label" style="color: #7665a7">Tâche assignée à</label>
                                <div class="name-container">
                                    <span v-if="task && task.talents && task.talents[0] && task.talents[0].avatar == 'user.jpg'" class="user-initials">{{ getUserInitial(task.talents[0]) }}</span>
                                    <img v-else v-bind:src="(task.talents[0].avatar) ? '/upload/avatars/' + task.talents[0].avatar : '/upload/avatars/' + user.avatar" class="img-task-dash" alt="" width="25" height="25"/>
                                    <p style="color: #7665a7">{{ task.talentName }}</p>
                                </div>
                            </div>
                            <div class="new-box-2">
                                <label for="" class="form-label" style="color: #7665a7">Date</label>
                                <div class="date-picker">
                                    <span class="filter-picto picto-calendar"></span>
                                    <div v-if="moment(task.start_date).format('MMMM') == moment(task.end_date).format('MMMM')" class="date-picker_model" mode="range">
                                        Du {{ moment(task.start_date).format("D ") + "au " + moment(task.end_date).format("D ") + moment(task.end_date).format("MMMM")}}
                                    </div>
                                    <div v-else class="date-picker_model" mode="range">
                                        Du {{ moment(task.start_date).format("DD/MM") }} au {{ moment(task.end_date).format("DD/MM") }}
                                    </div>
                                </div>
                            </div>
                            <!-- Actions + Note -->
                            <div v-if="task.status !== 'CLOSED'" class="text-center js-position-wrapper is-actions" style=" position: relative; bottom: 7px; position: relative; bottom: 7px; width: 100%; height: 27%;">
                                <button type="button" class="action-button js-position-button dash-note" style="text-align: center; margin-top: 20px;" v-if="task.note == '[]'">
                                    <span class="icon icon-notes" style=" position: absolute; left: 3em; color: #fff;"></span>
                                    Aucune note
                                </button>
                                <button type="button" :data-id="task.id" class="action-button js-position-button dash-note" style="text-align: center; margin-top: 20px;" v-else-if="task.note">
                                    <span class="icon icon-notes" style=" position: absolute; left: 3em; color: #fff;"></span>
                                    Voir note
                                </button>
                                <div class="notes js-position-content" v-if="task && task.note != '[]'">
                                    <div class="notes-wrapper" v-for="(note, key) in JSON.parse(task.note)">
                                        <span v-on:click="noteCheck(key, !note.state, task.id, JSON.parse(task.note), taskKey);">
                                            <span :class=" note.state ? 'note-checkbox checked' : 'note-checkbox'"></span>
                                        </span>
                                        <span>{{ note.note }}</span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div v-if="hideProgressBar == false" class="project-card__progress tooltiped" style="background: #48367c">
                                    <transition name="bounce">
                                        <span class="tooltiptext progress-bar-tooltip" v-if="moment(today).isSameOrBefore(task.end_date)">
                                            {{ moment(task.end_date).diff(today, "days") }}
                                            <span v-if="moment(task.end_date).diff(today, 'days') <= 1">jour /</span>
                                            <span v-else>jours /</span>
                                            {{ getTaskProgressBar(task).segment + 1 }} avant la <br />
                                            fin de la tâche
                                        </span>
                                    </transition>
                                    <transition name="bounce">
                                        <span class="tooltiptext progress-bar-tooltip spent" v-if="moment(today).isAfter(task.end_date)">
                                            Tache écoulé depuis {{ moment(today).diff( task.end_date, "days") }}
                                            <span v-if="moment(today).diff(task.end_date, 'days') <= 1">jour<br /></span>
                                            <span v-else>jours<br /></span>
                                        </span>
                                    </transition>
                                   <div style="position: absolute; display: flex; width: 100%; justify-content: space-evenly;">
                                        <span class="segment-stepper" v-if="getTaskProgressBar(task).segment < 4" v-for="value in getTaskProgressBar(task).segment"></span>
                                    </div>
                                    <span class="project-card__progress-bar" :style=" 'width:' + getTaskProgressBar(task).progress + '%'">
                                    </span>
                                </div>
                            </div>
                            <div id="countdown" class="count" v-if="task.status !== 'CLOSED' && task.project_category !== 'mission-explorer'" v-on:click="Task._close(task)">
                                Clôturer
                            </div>
                            <div id="countdown" class="count" v-else-if="task.project_category == 'mission-explorer'" @click="goToMessenger()">
                                Aller vers la conversation
                            </div>
                        </div>
                        <div class="project-card" :id="'closed-' + task.id" v-else-if="task.status == 'CLOSED'" v-bind:class="isClosed(task)" @mouseenter.once="checked('closed-' + task.id, task.id, taskKey)" :data-id="task.id">
                            <div v-if="task.status == 'CLOSED'"></div>
                            <!-- ,'is-waiting': task.accepted === null-->

                            <!-- Category -->
                            <div :class="'project__category'" :style="buttonCategoryStyle(task.project_category_color)">
                                {{ $t(task.project_category) }}
                            </div>

                            <span class="text-task main_new text-task-position" v-if="task.status !== 'CLOSED'">
                                <i class="picto picto-clock"></i>
                                {{ moment(task.end_date).diff(today, "days") }}J Restants</span>
                            <div v-if="task.project_category !== 'mission-explorer'" class="js-toggle-wrapper">
                                <div class="button project-detail__action-button-task click" style="padding: 0; display: flex; justify-content: center; align-items: center;" v-on:click.prevent="contextMenu.show($event, 'TASK', {task: task, project: task.project}, contextMenuCallback, [true])">
                                    <span class="dot-button-task"></span>
                                    <span class="dot-button-task"></span>
                                    <span class="dot-button-task"></span>
                                </div>
                                <div class="actions-box">
                                    <ul class="context-menu--items">
                                        <template v-if="task.status !== 'CLOSED' || task.project_category !== 'mission-explorer'">
                                            <li class="context-menu--items-element" v-if="task.project_category !== 'mission-explorer'" v-on:click="Global._setAction('SET_TASK', {element: task.project_id, task: task, type: 'EDIT'})">
                                                Modifier la tâche
                                            </li>
                                            <li class="context-menu--items-element" v-if=" task.project_category !== 'mission-explorer'"
                                                v-on:click="Task._close(task, callback, params)">
                                                Clôturer la tâche
                                            </li>
                                        </template>
                                        <li class="context-menu--items-element" v-on:click="Project._goTo(project)">
                                            Voir le projet
                                        </li>
                                        <!--    <hr /> -->
                                        <li v-if="task.project_category !== 'mission-explorer'" class="context-menu--items-element"
                                            v-on:click="Task._delete(task, callback, params)">
                                            Supprimer la tâche
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- Infos -->
                            <div class="project-card__infos">
                                <p style="display: flex; align-items: baseline">
                                    <span style="position: absolute; left: 0; width: 9px; height: 9px; margin-left: 10px; background: aqua; top: 11.5%; border-radius: 50%;" class="dot-button-task" :class="task.task_type"></span>
                                    <strong>{{ $t(task.task_type) }}</strong>
                                </p>
                                <template v-if="task.status !== 'CLOSED'"><p v-on:click="goToProject($event, task.project_id)">
                                        {{ task.project_name }}
                                    </p></template>
                                <template v-else ><p>{{ task.project_name }}</p></template>
                                <p v-if="task.status == 'CLOSED'">Clôturé le : {{ task.closed_at | dateFormat }}</p>
                            </div>
                            <div class="new-box">
                                <label for="" class="form-label" style="color: #7665a7">Tâche assignée à</label>
                                <div class="name-container">
                                    <span v-if="task && task.talents && task.talents[0] && task.talents[0].avatar == 'user.jpg'" class="user-initials">{{ getUserInitial(task.talents[0]) }}</span>
                                    <img v-else v-bind:src="(task.talents[0].avatar) ? '/upload/avatars/' + task.talents[0].avatar : '/upload/avatars/' + user.avatar" class="img-task-dash" alt="" width="25" height="25"/>
                                    <p style="color: #7665a7"> {{ task.talentName }}</p>
                                </div>
                            </div>
                            <div class="new-box-2 dash-car_padding">
                                <label for="" class="form-label" style="color: #7665a7">Date</label>
                                <div class="date-picker">
                                    <span class="filter-picto picto-calendar"></span>
                                    <div v-if=" moment(task.start_date).format('MMMM') == moment(task.end_date).format('MMMM')" class="date-picker_model" mode="range">
                                        Du {{ moment(task.start_date).format("D ") + "au " + moment(task.end_date).format("D ") + moment(task.end_date).format("MMMM") }}
                                    </div>
                                    <div v-else class="date-picker_model" mode="range">
                                        Du {{ moment(task.start_date).format("DD/MM") }} au {{ moment(task.end_date).format("DD/MM") }}
                                    </div>
                                </div>
                            </div>
                            <!-- Actions + Note -->
                            <div v-if="task.status !== 'CLOSED'" class="text-center js-position-wrapper is-actions" style="position: relative; bottom: 7px">
                                <button type="button" class="action-button js-position-button dash-note" v-if="task.note">
                                    <span class="icon icon-notes"></span> Voir note
                                </button>
                                <div class="notes js-position-content">
                                    <div class="notes__actions">
                                        <button type="button" v-on:click.prevent="Task._edit(task, contextMenuCallback, [true])">
                                            <span class="icon icon-edit"></span>
                                        </button>
                                        <button type="button" class="js-close-note">
                                            <span class="icon icon-cross"></span>
                                        </button>
                                    </div>
                                    <p class="word-wrap">
                                        <strong>Notes :</strong>
                                    </p>

                                    <div class="notes-wrapper" v-for="(n, index) in task.note">
                                        <input type="checkbox" v-if="n.includes('*')" :name="'checkNote[' + task.id + index + ']'" :id="'checkNote[' + task.id + index + ']'"
                                        style="visibility: hidden;" checked/>
                                        <input type="checkbox" v-else :name=" 'checkNote[' + task.id + index + ']'" :id="'checkNote[' + task.id + index + ']'" style="visibility: hidden;"/>
                                        <label @click="checkNotes(task, index)" class="note-checkBox" :for=" 'checkNote[' + task.id + index + ']'"> {{ n }}</label>
                                    </div>
                                </div>
                                <!-- Progress Bar -->
                                <div class="project-card__progress">
                                    <div style="position: absolute; display: flex; width: 100%; justify-content: space-evenly;">
                                        <span class="segment-stepper" v-if="getTaskProgressBar(task).segment < 4"
                                            v-for="value in getTaskProgressBar(task).segment">
                                        </span>
                                    </div>
                                    <span class="project-card__progress-bar" :style=" 'width:' + getTaskProgressBar(task).progress + '%'"></span>
                                </div>
                            </div>
                            <div id="countdown" class="count" v-if="task.status !== 'CLOSED' && task.project_category !== 'mission-explorer'" v-on:click="Task._close(task)">
                                Clôturer
                            </div>
                            <div id="countdown" class="count" v-else-if=" task.project_category == 'mission-explorer'" @click="goToMessenger()">
                                Aller vers la conversation
                            </div>
                        </div>
                    </div>
                </transition-group>
            </transition>
            </div>
            <!-- ./Task -->
        </div>

    </div>
    <!-- ./Card -->
</template>

<script>
import axios from "axios";
import { stringify } from "querystring";

export default {
    props: [
        "all_tasks",
        "tasks",
        "task",
        "progress_tasks",
        "closed_tasks",
        "waiting_tasks",
        "contextMenu",
        "project",
        "_task",
        "_project",
        "task_talent",
        "user",
        "formType",
    ],

    data() {
        let date = new Date();
        /*  date: {
                start: new Date(),
                end: new Date()
        }; */
        let timeleft = 5;
        var intervalId = null;
        const monthNames = [
            "Janvier",
            "Février",
            "Mars",
            "Avril",
            "Mai",
            "Juin",
            "Juillet",
            "Aout",
            "Septembre",
            "Octobre",
            "Novembre",
            "Décembre"
        ];

        return {
            timeleft: 5,
            date: {
                start: new Date(),
                end: new Date()
            },
            filters: {
                status: null
            },
            state: null,
            today: moment().format("YYYY-MM-DD"),
            month: monthNames[new Date().getMonth()],
            callback: null,
            monthNames: monthNames,
            TodayChecked: true,
            TomorowChecked: true,
            CloseChecked: true,
            todayUnviewed: 0,
            tomorowUnviewed: 0,
            closeUnviewed: 0,
            hideProgressBar: false,
            ShowTt: false,
            arrNote: [],
            tasksToShow: [],
            tasksStatus: false,
            tasksToClose: [],
            taskToClose: false,
            hourToDisplayClosePopupStart: 19,
            hourToDisplayClosePopupEnd: 23
        };
    },
    created() {
        this.getUser();
    },

    mounted() {
        const scroll = document.querySelector(".s-a")
        Vue.prototype.Global._yScroll(scroll)

        const $this = this;
        this.setStatusSelect();
        this.getTaskToClose();
        this.$bus.$on('UPDATE_VIEWED_FILTER', (datas) => {
            if (datas) {
                this.TodayChecked = datas.todayViewed.length  == 0 ? true : false;
                this.TomorowChecked = datas.tomorrowViewed.length == 0 ? true : false;
                this.CloseChecked = datas.closedViewed.length  == 0 ? true : false;

                this.todayUnviewed = datas.todayViewed.length;
                this.tomorowUnviewed = datas.tomorrowViewed.length;
                this.closeUnviewed = datas.closedViewed.length;
            }
        });
        this.$bus.$on('UPDATE_TASKS_TO_SHOW', (datas) => {
            if (datas.status) {
                this.tasksStatus = datas.status;
            }
            if (datas.tasks) {
                this.tasksToShow = datas.tasks;
            }
        });

        this.$bus.$on('RESET_TASK_TO_CLOSE', () => {
            let previousTaskToClose = this.tasksToClose.indexOf(this.taskToClose);
            this.tasksToClose.splice(previousTaskToClose, 1);
            if (this.tasksToClose.length > 0 && typeof this.tasksToClose[0] != "undefined") {
                let task = this.tasksToClose[0];
                this.taskToClose = task;
                task.isLast = false;

                if (this.tasksToClose.length == 1) {
                    task.isLast = true;
                }

                this.$bus.$emit("ACTION_CHANGED", {
                    action: "CONGRATS",
                    type: "TASK_OVER",
                    element: task
                });
            } else {
                this.taskToClose = false;
            }
        });

        window.Echo.private('kolab').listen('.task-popup-close', (e) => {
            if (this.tasksToClose && typeof this.tasksToClose[0] != "undefined") {
                let task = this.tasksToClose[0];
                this.taskToClose = task;

                task.isLast = false;

                if (this.tasksToClose.length == 1) {
                    task.isLast = true;
                }

                this.$bus.$emit("ACTION_CHANGED", {
                    action: "CONGRATS",
                    type: "TASK_OVER",
                    element: task
                });
            }
        });

        let now = new Date();
        let currentHour = now.getHours();
        if (currentHour >= this.hourToDisplayClosePopupStart && currentHour <= this.hourToDisplayClosePopupEnd) {
            setTimeout(function() {
            if ($this.tasksToClose && $this.tasksToClose.length > 0 && typeof $this.tasksToClose[0] != "undefined") {
                let task = $this.tasksToClose[0];
                $this.taskToClose = task;

                task.isLast = false;
                if ($thus.tasksToClose.length == 1) {
                    task.isLast = true;
                }

                $this.$bus.$emit("ACTION_CHANGED", {
                    action: "CONGRATS",
                    type: "TASK_OVER",
                    element: task
                });
            }}, 1000);
        }
    },
    beforeUpdate() {
        //this.parseNotes();
    },

    methods: {
        getUserInitial(talent) {
            if (talent && talent.firstname && talent.lastname) {
                return talent.firstname.charAt(0) + talent.lastname.charAt(0);
            }

            return '';
        },
        getUser() {
            axios.get("/api/talent").then(response => {
                this.avatar = response.data.avatar;
            });
        },
        checkNotes(task, index) {
            if (task.note[index].includes("*")) {
                let str = task.note[index];
                let newStr = str.replace("*", "");
                task.note[index] = newStr;
            } else {
                task.note[index] = task.note[index] + "*";
            }
            let str = task.note.join("\n");
            task.note = str;
            task.talent_id = task.talents[0].id;
            axios
                .put("/api/task/note/" + task.id, {
                    params: task.note
                })
                .then(res => {
                    if (res.success === false) {
                        // Error
                    } else {
                        this.$bus.$emit("TASK_ADD_OR_UPDATE", res.data); // Emit add or update talent event
                        this.$bus.$emit("ACTION_CHANGED", {
                            action: null
                        });
                    }
                })
                .catch(error => console.log(error));
        },
        parseNotes() {
            this.tasks.forEach(el => {
                if (el.note) {
                    if (el.note.startsWith("[{")) {
                        if (el.note.note) {
                            this.arrNote = JSON.parse(el.note.note);
                            el.note = this.arrNote;
                        }
                    } else {
                        this.arrNote = JSON.stringify(el.note);
                        el.note = this.arrNote;
                    }
                }
                /*document
                    .getElementsByClassName("note-checkBox")
                    .html()
                    .replace("*", "");*/
            });
            this.arrNote = [];
        },
        checked(elementId, taskId, taskKey) {
            let card = document.getElementById(elementId);
            let cardStatus = this.tasksStatus;
            if (card && card.classList.contains('is-waiting')) {
                if (cardStatus == 'progress') {
                    this.todayUnviewed--;
                    if (this.todayUnviewed == 0) {
                        this.TodayChecked = true;
                    }
                    if (this.todayUnviewed < 0) {
                        this.todayUnviewed = 0;
                    }
                    let task = this.tasks[taskKey];
                    if (task) {
                        if (task.viewed) {
                            let viewed = JSON.parse(task.viewed);
                            if (typeof viewed == 'object') {
                                delete viewed[this.user.id];
                                this.tasks[taskKey].viewed = JSON.stringify(viewed);
                            }
                        }
                    }
                } else if (cardStatus == 'waiting') {
                    this.tomorowUnviewed--;
                    if (this.tomorowUnviewed == 0) {
                        this.TomorowChecked = true;
                    }
                    if (this.tomorowUnviewed < 0) {
                        this.tomorowUnviewed = 0;
                    }
                    let task = this.waiting_tasks[taskKey];
                    if (task) {
                        if (task.viewed) {
                            let viewed = JSON.parse(task.viewed);
                            if (typeof viewed == 'object') {
                                delete viewed[this.user.id];
                                this.waiting_tasks[taskKey].viewed = JSON.stringify(viewed);
                            }
                        }
                    }
                } else if (cardStatus == 'closed') {
                    this.closeUnviewed--;
                    if (this.closeUnviewed == 0) {
                        this.CloseChecked = true;
                    }
                    if (this.closeUnviewed < 0) {
                        this.closeUnviewed = 0;
                    }
                    let task = this.closed_tasks[taskKey];
                    if (task) {
                        if (task.viewed) {
                            let viewed = JSON.parse(task.viewed);
                            if (typeof viewed == 'object') {
                                delete viewed[this.user.id];
                                this.closed_tasks[taskKey].viewed = JSON.stringify(viewed);
                            }
                        }
                    }
                }
                axios.put(`/task/viewed/${taskId}`, { timeout: 500 }).then(res => {
                    if (res.status == 200) {
                        card.classList.remove("is-waiting");
                    }
                });
            }
        },
        // Countdown before closing a task
        goToMessenger() {
            window.location.href = "/explorer/messenger";
        },
        countdown() {
            // let timeleft = 10;
            //let t = setInterval(() => {
            //  if(document.getElementsByClassName('count').onclick == true){
            //    document.getElementById('countdown').onclick = function(){
            //      clearInterval(t);
            //      clearTimeout(t);
            //      document.getElementById('countdown').innerHTML = "Clôturé";
            //     this.timeleft = 5;
            //    }
            //  }
            //this.timeleft--;
            //document.getElementById("countdown").innerHTML =
            //    "Tâche clôturé dans " + this.timeleft + "'";
            //if (this.timeleft == 0) {
            //    clearInterval(t);
            //    this.timeleft = 5;
            //document.getElementById("countdown").innerHTML = "Clôturé";
            //this.setState(this.value);
            //}
            //   }, 1000);
        },

        setBodyClass() {
            if (this.action == "set_closed") {
                $("body").remove(this.tasks);
            } else {
                $("body").add(this.tasks);
            }
        },

        // function display filter
        switchTaskStatus(filters) {
            let progress = document.getElementById("progress-btn");
            let waiting = document.getElementById("waiting-btn");
            let closed = document.getElementById("closed-btn");
            this.hideProgressBar = false;
            if(progress != null ){
                progress.classList.remove("active-tasks");
            }
            if(waiting != null){
                waiting.classList.remove("active-tasks");
            }
            if(closed != null){
                closed.classList.remove("active-tasks");
            }
            if (filters.status == "STATUS_PROGRESS") {
                progress.classList.add("active-tasks");
                this.tasksToShow = this.tasks;
                this.tasksStatus = 'progress';
            } else if (filters.status == "STATUS_WAITING") {
                waiting.classList.add("active-tasks");
                this.hideProgressBar = true;
                this.tasksToShow = this.waiting_tasks;
                this.tasksStatus = 'waiting';
            } else if (filters.status == "STATUS_CLOSED") {
                closed.classList.add("active-tasks");
                this.tasksToShow = this.closed_tasks;
                this.tasksStatus = 'closed';
            }
        },

        close(event) {
            if (!event.target.matches(".is-actions, .is-actions *")) {
                Vue.prototype.Task._close(task);
            } else {
                clearInterval(countdown());
            }
        },

        hasTalentsWithLetter(daystatus) {
            const startsWith = this.all_tasks.filter(tasks => {
                return this.tasks.status.startsWith(daystatus);
            });

            return startsWith.length > 0;
        },

        setBodyClass() {
            if (this.action == "CLOSE") {
                $("body").removeClass("alert-is-open");
            } else {
                $("body").addClass("alert-is-open");
            }
        },

        startFocusOut() {
            $(document).on("click", () => {
                if (this.menuType !== null) this.hideContextMenu();
            });
            // $(document).on('scroll', () => { this.hideContextMenu(); });
            // $('.planning__block').on('scroll', () => { this.hideContextMenu(); });
        },

        hideContextMenu() {
            this.menuType = null;

            // Remove prevent scroll
            $("body").removeClass("overflow-hidden");
            if ($(".scroll-content").length > 0) {
                $(".scroll-content").removeClass("overflow-hidden");
            }
        },

        resetData() {
            this.menuType = null;
            this.task = null;
            this.appointment = null;
            this.talent = null;
            this.callback = null;
        },

        /* Set status select2 */
        setStatusSelect() {
            $(() => {
                $(".card__task .js-status-data").select2({
                    minimumResultsForSearch: Infinity,
                    dropdownCssClass: "dashboard-filters",
                    placeholder: "Toutes les tâches"
                });
                $(".card__task .js-status-data").on("select2:select", () => {
                    this.contextMenuCallback(false, this.filters);
                });
                $(".card__task .js-status-data").on("select2:unselect", () => {
                    this.contextMenuCallback(false, this.filters);
                });
            });
        },
        // Countdown before closing a task

        countdown() {
            // let timeleft = 10;
            //let t = setInterval(() => {
            //  if(document.getElementsByClassName('count').onclick == true){
            //    document.getElementById('countdown').onclick = function(){
            //      clearInterval(t);
            //      clearTimeout(t);
            //      document.getElementById('countdown').innerHTML = "Clôturé";
            //     this.timeleft = 5;
            //    }
            //  }
            //this.timeleft--;
            //document.getElementById("countdown").innerHTML =
            //    "Tâche clôturé dans " + this.timeleft + "'";
            //if (this.timeleft == 0) {
            //    clearInterval(t);
            //    this.timeleft = 5;
            //document.getElementById("countdown").innerHTML = "Clôturé";
            //this.setState(this.value);
            //}
            //   }, 1000);
        },

        setBodyClass() {
            if (this.action == "set_closed") {
                $("body").remove(this.tasks);
            } else {
                $("body").add(this.tasks);
            }
        },

        close(event) {
            if (!event.target.matches(".is-actions, .is-actions *")) {
                Vue.prototype.Task._close(task);
            } else {
                clearInterval(countdown());
            }
        },

        hasTalentsWithLetter(daystatus) {
            const startsWith = this.all_tasks.filter(tasks => {
                return this.tasks.status.startsWith(daystatus);
            });

            return startsWith.length > 0;
        },

        startFocusOut() {
            $(document).on("click", () => {
                if (this.menuType !== null) this.hideContextMenu();
            });
            // $(document).on('scroll', () => { this.hideContextMenu(); });
            // $('.planning__block').on('scroll', () => { this.hideContextMenu(); });
        },

        hideContextMenu() {
            this.menuType = null;

            // Remove prevent scroll
            $("body").removeClass("overflow-hidden");
            if ($(".scroll-content").length > 0) {
                $(".scroll-content").removeClass("overflow-hidden");
            }
        },

        resetData() {
            this.menuType = null;
            this.task = null;
            this.appointment = null;
            this.talent = null;
            this.callback = null;
        },

        /**
         * Set status select2
         */
        setStatusSelect() {
            $(() => {
                $(".card__task .js-status-data").select2({
                    minimumResultsForSearch: Infinity,
                    dropdownCssClass: "dashboard-filters",
                    placeholder: "Toutes les tâches"
                });
                $(".card__task .js-status-data").on("select2:select", () => {
                    this.contextMenuCallback(false, this.filters);
                });
                $(".card__task .js-status-data").on("select2:unselect", () => {
                    this.contextMenuCallback(false, this.filters);
                });
            });
        },
        setState(value) {
            this.state = value;
            $(".js-toggle-content").fadeOut();
        },
        goToProject(event, project_id) {
            if (!event.target.matches(".is-actions, .is-actions *")) {
                Vue.prototype.Project._goTo(project_id);
            }
        },
        noteCheck(key, state, taskId, notes, taskKey) {
            if (typeof notes !== "undefined" && typeof notes[key] !== "undefined") {
                notes[key].state = state;
                axios
                    .put("/api/task/note/" + taskId, {
                        params: notes
                    })
                    .then(res => {
                        if (res.success === false) {
                            // Error
                        } else {
                            this.$bus.$emit("TASK_ADD_OR_UPDATE", res.data); // Emit add or update talent event
                            this.$bus.$emit("ACTION_CHANGED", {
                                action: null
                            });
                            this.tasksToShow[taskKey].note = JSON.stringify(notes);
                        }
                    })
                    .catch(error => console.log(error));
            }
        },
        buttonCategoryStyle(projectCategoryStyle) {
            let style = JSON.parse(projectCategoryStyle);
            if (style) {
                return {'background': style.bgColor, 'border': '1px solid ' + style.borderColor, 'borderRadius': '3px'};
            }
        },
        isClosed(task)
        {
            let viewed = JSON.parse(task.viewed);
            if (viewed) {
                if (typeof viewed[this.user.id] != 'undefined') {
                    return 'is-waiting';
                }
            }
            return '';
        },
        getTaskToClose() {
            axios.get("/api/dashboard/getTaskToClose/" + this.user.id).then(res => {
                if (res.status == 200) {
                    this.tasksToClose = res.data.datas;
                }
            }).catch(error => console.log(error));
        },
        getTaskProgressBar(task) {
            let taskStartDate = task.start_date;
            let taskEndDate = task.end_Date;
            if (Number.isNaN(new Date(taskStartDate).getTime())) {
                taskStartDate = moment(taskStartDate).format('YYYY/MM/DD');
            }
            if (Number.isNaN(new Date(taskEndDate).getTime())) {
                taskEndDate = moment(taskEndDate).format('YYYY/MM/DD');
            }
            var passed = new Date() - new Date(taskStartDate);
            var todo = new Date(taskEndDate) - new Date();
            var progress = passed / (passed + todo);
            var segment = Math.floor((passed + todo) / (1000 * 60 * 60 * 24));
            if (segment < 0) {
                segment = 0;
            }
            var today = moment().format('YYYY-MM-DD 19:00:00')
            progress = progress * 100;
            progress = progress > 100 ? 100 : progress;
            if (progress < 0) {
                progress = 0;
            }

            return {progress: progress, segment: segment};
        }
    }
};
</script>
