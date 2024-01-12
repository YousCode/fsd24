<template>
    <div class="block has-bg overflow-hidden has-no-content planning">
        <VueSelecto
        v-if="selecto"
        :container="body"
        :dragContainer="planningDaysContainer"
        :selectableTargets='[".day-plan-container"]'
        :selectByClick="true"
        :selectFromInside="true"
        :continueSelect="false"
        :continueSelectWithoutDeselect="true"
        :toggleContinueSelect='"shift"'
        :keyContainer="planningDaysContainer"
        :hitRate="0"
        :ratio="0"
        @select="onSelect"
        @selectEnd="onSelectEnd"
        />
        <div class="row planning-infos" style="margin-bottom: 15px;">
            <!-- Date picker -->
            <div class="planning-dashboard is-date col-md-4">
                <button
                        v-on:click="handleDateChange('DOWN')"
                        class="nav-arrow is-prev"
                >
                    <span class="sr-only">Précédent</span>
                </button>

                <p style="text-align: center; width: 100%">
                    <strong>{{ date_month | monthLabel }}</strong>
                    {{ date_year }}
                </p>

                <button
                        v-on:click="handleDateChange('UP')"
                        class="nav-arrow is-next"
                >
                    <span class="sr-only">Suivant</span>
                </button>
            </div>
            <div class="planned-days col-md-2" :style="{ 'color': 'white' }">
                <button class="active-planning">Jour</button>
                <button class="inactive-planning" v-on:click="setPlanningType('MONTH')">Mois</button>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-4 project-end-days">
                <span>Livraison finale prévue <br/> {{ project.reformat_deadline }} </span>
            </div>
        </div>
        <div class="row text-center mobile planning">
            <div class="col-md-3 total-planned-header days" v-on:click="openTypeTasks()">
                <p v-if="nbDays > 1">
                    <span class="left">Tot planifiés :</span> <span class="right">{{ nbDays }}j</span>
                    <span class="picto-days-chevron"></span>
                </p>
                <p v-else>
                    <span class="left">Tot planifié :</span> <span class="right">{{ nbDays }} jour</span>
                    <span class="picto-days-chevron"></span>
                </p>
                    <div id="project-type-tasks-mobile" class="project-type-tasks-month">
                        <div class="total-planned" :class="(taskType.class == '3d' ? 'd3' : taskType.class )" v-on:mouseover="(typeTaskOpen) ? hoverTaskTypeId = taskType.taskTypes[0].id : false" v-on:mouseleave="hoverTaskTypeId = false" v-for="taskType in projectTypeTasks">
                            <span class="left"><span class="dot-button-task" :class="taskType.task_type == '3D' ? 'd3' : taskType.task_type" :style="{marginRight: '5px'}"></span>{{ $t(taskType.task_type) }}</span>
                            <span class="right">{{ taskType.nb_days }} {{ (taskType.nb_days > 1) ? 'jours' : 'jour' }}</span>
                        </div>
                    </div>
            </div>
            <div class="col-md-3 project-end-days">
                <p>Livraison finale prévue<br/>{{ project.reformat_deadline }}</p>
            </div>
        </div>
        <!-- <div class="block__inner">
            <img src="/images/talk-block.png" alt="" class="img-fluid is-theme-light">
            <img src="/images/talk-block-dark.png" alt="" class="img-fluid is-theme-dark">
        </div> -->

        <div class="planning-days-wrapper">
            <div id="mobile-planning-monthes">
                <p class="month" :class="(monthKey == date_month) ? 'active' : ''" :ref="'month-' + monthKey" v-on:click="changeMonth(monthKey)" v-for="(monthName, monthKey) in project.planningMonthes">
                    {{ $t(monthName) }}
                </p>
            </div>
            <!--    <div
                class="no-content__planning"
                style="padding-top: 100px;
                        margin: auto;
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);"
            > -->
            <div id="planning-days-container">
                <div v-for="(day, i) in calendarDays"
                        :key="day[0]"
                        :ref="isToday(i, date_month)"
                        :class="isTodayOrGone(i, date_month, date_year)"
                        class="day-plan-container"
                        :style="((i + 1) % 2 == 0) ? { backgroundColor: '#130B28' } : { backgroundColor: '#1E1438' }"
                        @dragover.prevent="onDragOver($event)" @dragenter.prevent="onDragOver($event)" @dragleave="onDragLeave()"
                        >
                        <div class="calendar-drop" :id="getId(date_year, i + 1, date_month)" @drop="onDrop($event)"></div>
                        <span class="date"> {{ day[0] }} <span class="today" v-show="isToday(i, date_month) == 'isToday'"></span></span>
                            <div class="event-container">
                                 <div v-show="!_isShared && !_isClient && project.id != 1" class="add-task-btn" v-on:click=" Global._setAction('SET_TASK', { project: project, selectedDate: getSelectedDay(i + 1, date_month, date_year), type: 'ADD', formType: 'days'})">
                                    <p><span>+</span> Ajouter une tâche</p>
                                </div>
                                <div v-for="(task,index) in day[1]" class="is-actions" v-if="typeof day[1] != 'undefined'"
                                    :id="getId(date_year, i + 1, date_month)"
                                    :draggable="draggable" @dragstart="startDrag($event, task[1])" @dragend="dragEnd($event)"
                                    style="display: flex;flex-direction: column;width: 100%; justify-content: flex-end;gap: 0.5rem;">
                                        <div v-if="task[1]">
                                            <div class="planned-task-container task-project task-project-days"
                                            :class="(isTodayOrGone(i, date_month, date_year) !== 'goneDay' && task[1].status == 'CLOSED') ? task[1].class + ' task-closed' : task[1].class"
                                            :style="(task[1].taskTypes[0].id == $parent.hoverTaskTypeId) ? {backgroundColor: $parent.taskTypeHoverBg[$parent.hoverTaskTypeId], border: '2px solid ' + $parent.taskTypeHoverBorder[$parent.hoverTaskTypeId]} : {}"
                                            v-on:click.prevent="(project.id == 1) ? false : contextMenu.show($event,'PLANNING',{ planning: task[1], isShared: _isShared },getPlanning, {isClient: _isClient, isShared: _isShared, status: task[1].status, closedAt: task[1].closedAt, startDate: task[1].startDate, endDate: task[1].endDate, projectId: project.id, project: project, selectedDate: getSelectedDay(i + 1, date_month, date_year), task: task[1], tasks: tasks});"
                                            v-on:mouseover="taskHover($event, 'task-' + task[1].project_id + '-' + task[1].id, true)" v-on:mouseleave="taskHover($event, 'task-' + task[1].project_id + '-' + task[1].id, false)">
                                                <span class="dot-button-task" :class="(task[1].task_type == '3D') ? 'd3' : task[1].task_type"></span>
                                                <span class="task-type" :style="{color: 'white'}" :class="(task[1].task_type == '3D') ? 'd3' : task[1].task_type">{{ $t(task[1].task_type) }}</span>
                                                <p class="description-task">{{ task[1].talents[0].firstname + ' ' + task[1].talents[0].lastname }}</p>
                                                <span class="task-actions" v-show="!_isShared && !_isClient && project.id != 1">
                                                    <button class="edit">
                                                        <span class="icon-action icon-edit" v-on:click.stop="Global._setAction('SET_TASK', {project: project, selectedDate: getSelectedDay(i + 1, date_month, date_year), task: task[1], tasks: tasksInProgress, type: 'EDIT', formType: 'days'})"></span>
                                                    </button>
                                                    <button class="delete">
                                                        <span class="icon-action icon-delete" v-on:click.stop="Task._delete(task[1].id, project)"></span> 
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                </div>
                                <div v-show="isDeadlineDay(i + 1, date_month, date_year, project.date_deadline)" class="is-actions"
                                    style="display: flex;flex-direction: column;width: 100%;justify-content: flex-end;gap: 0.5rem;">
                                    <div class="planned-task-container task-project-days deadline-day">
                                        <span class="dot-button-task" :style="{backgroundColor: '#FF83BE'}"></span>
                                        <span class="task-type" :style="{color: '#FF83BE'}">Livraison finale</span>
                                    </div>
                                </div>
                            </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Planning from "../planning/Planning.vue";
    import { VueSelecto } from "vue-selecto";
    export default {
        components: {
            Planning,
            VueSelecto
        },
        props: ["user", "_project", "contextMenu", "_isShared", "_isClient", '_nbDays'],
        data() {
            let date = new Date();
            return {
                planning: {},
                project: {},
                selected_talents_id: [],
                selected_talents_name: [],
                filters: {
                    talents_id: [],
                    projects_id: null,
                    project_name: "",
                    task_types_id: [],
                },
                this_mounth: date.getMonth() + 1,
                date_month: date.getMonth() + 1,
                date_year: date.getFullYear(),
                yesterday: moment().add(-1, "days").format("dddd DD"),
                tomorrow: moment().add(1, "days").format("dddd DD"),
                today: moment().add(0, "days").format("dddd DD"),
                counter_slide: 0,
                translation: 0,
                cell_width: 250,
                plan: [],
                tasksToday: [],
                dayTask: [],
                workingWeek: {},
                calendarDays: [],
                hoverTaskActions: false,
                hoverTask: false,
                isScrolled: true,
                tasks: [],
                deadline_date: false,
                planningDaysContainer: document.querySelector('.planning-days-wrapper'),
                body: document.querySelector('body'),
                selectedDateTask: false,
                selecto: true,
                draggable: true,
                tasksInProgress: [],
                projectTypeTasks: [],
                nbDays: 0,
                typeTaskOpen: false,
                hoverTaskTypeId: false,
                taskTypeColors: {
                    1:"#37ff9f",
                    2:"#01cfbf",
                    3:"#37ff46",
                    4:"#ff4286",
                    5:"#7b9cff",
                    6:"#ffd337",
                    7:"#b78bff",
                    8:"#377bff",
                    9:"#7e7bff",
                    10:"#be6cff",
                    11:"#ff37eb",
                    12:"#37ffdb",
                    13:"#ff4b4b",
                    14:"#ffc672",
                    15:"#ff4b4b"
                },
                taskTypeHoverBg: {1:"#15373A", 2:"#23406B", 3:"#2D4953", 4:"#552460", 5:"#3B3678", 6:"#554150", 7:"#3D1925", 8:"#151D4D", 9:"#231D4D", 10:"#301A4D", 11:"#3D0F49", 12:"#2D4971", 13:"#3D1329", 14:"#3D2C31", 15:"#3D173E"},
                taskTypeHoverBorder: {1:"#269B6D", 2:"#128795", 3:"#32A44D", 4:"#ff4286", 5:"#5B69BB", 6:"#AA8A44", 7:"#AA4944", 8:"#1C3071", 9:"#504CA6", 10:"#7743A6", 11:"#9E239A", 12:"#32A4A6", 13:"#9E2F3A", 14:"#9E7952", 15:"#9E3C7A"},
            };
        },
        created() {
            if (this._project) {
                this.project = this._project;
            }
            if (this._project && this._project.task) {
                this.tasks = this._project.task;
            }
            if (this._project && this._project.tasksInProgress) {
                this.tasksInProgress = this._project.tasksInProgress;
            }
        },
        mounted() {
            this.getPlanning();

            this.setProjectSelect("project");

            //this.setTalentsSelect("talents_id");

            //this.setTaskTypesSelect("task_types_id");

            this.$bus.$on("TASK_ADD_OR_UPDATE", () => {
                this.getPlanning();
            });
            this.$bus.$on("TASK_DATE_UPDATE", () => {
                this.getPlanning();
            });
            this.$bus.$on("APPOINTMENT_ADD_OR_UPDATE", () => {
                this.getPlanning();
            });
            this.$bus.$on('PLANNING_TO_SCROLL', () => {
                this.isScrolled = true;
            });

            if (this._isClient || this._isShared) {
                this.selecto = false;
                this.draggable = false;
            }

            let planningResponsive = window.matchMedia("(max-width: 700px)")
            this.resizePlanningBlock(planningResponsive) // Call listener function at run time
            planningResponsive.addListener(this.resizePlanningBlock) // Attach listener function on state changes
        },

        methods: {
            resizePlanningBlock(x) {
                const planningBlock = document.getElementById("project-detail-planning");
                const planningMonthes = document.getElementById("mobile-planning-monthes");
                const header = document.getElementById("kolab-header");
                if (x.matches && screen && screen.width) { // If media query matches
                    let width = screen.width - 30;
                    planningBlock.style.width = width + 'px';
                    planningMonthes.style.width = width + 'px';
                    if (header && header.style) {
                        header.style.width = screen.width + 'px';
                        header.style.position = 'fixed';
                    }
                }
            },
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
            async getPlanning() {
                var userId = (typeof this.user !== "undefined" && typeof this.user.id !== "undefined") ? this.user.id.toString() : false;
                if(userId) {
                    this.filters.talents_id = Array.from(userId);
                }

                await axios.get("/api/project/"+ this.project.id +"/planning", {
                        params: {
                            id: this.project.id,
                            date_month: this.date_month,
                            date_year: this.date_year,
                        },
                    })
                    .then((res) => {
                    if (res.success === false) {
                    // Todo : Error
                } else {
                    let datas = res.data.datas;
                    this.planning = datas.project_tasks; // Update project task list
                    this.projectTypeTasks = res.data.datas.project_tasks_summary; // Update project task list
                    for (const [key, typeTask] of Object.entries(this.projectTypeTasks)) {
                        typeTask.class = typeTask.task_type.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(" ", "-").toLowerCase();
                    }
                    this.getNbDays();
                    if (this.project.id == 1) {
                        this.getThisWeekTest();
                    } else {
                        this.getThisWeek();
                    }
                }
            })
            .catch((error) => console.log(error));
            },
            getNbDays(){
                if (this.projectTypeTasks) {
                    this.nbDays = 0;
                    for (const [key, value] of Object.entries(this.projectTypeTasks)) {
                        this.nbDays += parseInt(value.nb_days); // value.tasks.length
                    }
                }
            },
            getThisWeek() {
                let i = [];
                let e = [];
                let a = 0;
                if (typeof this.planning !== "undefined") {
                    this.workingWeek = [];
                    Object.keys(this.planning).forEach((day) => {
                        if (day != undefined) {
                            e.push(Object.entries(this.planning[day]));
                                if (e[a] != undefined) {
                                    i.push(e[a]);
                                }
                            if (i[a] != undefined) {
                                i[a].forEach((el) => {
                                    var el = i[a];
                                    el.start_date = moment(el.start_date).format("DD");
                                    el.end_date = moment(el.end_date).format("DD MMMM");
                                    el.hourtixme = moment(el.datetime).format("HH:mm");
                                    el.datetime = moment(el.datetime).format("dddd DD");
                                });
                            }
                            this.workingWeek[moment(day).format("ddd DD")] = i[a];
                            a++;
                        }
                    });
                    this.calendarDays = Object.entries(this.workingWeek);
                }
            },
            getThisWeekTest() {
                let planning = {};
                let startDateDay = new Date().getDate();
                let endDateDay = new Date().getDate();
                var year = new Date().getFullYear();
                let deadlineDay = new Date().getDate();
                let deadlineYear = new Date().getFullYear();
                let monthStart = new Date().getMonth();
                let monthEnd = new Date().getMonth();
                let deadlineMonth = new Date().getMonth() + 1;

                let names = ['Paul', 'Stéfane', 'Youssouph', 'Tiffany', 'Jade', 'Bryan', 'Titouan', 'Jérémy'];
                let taskType = [
                    {'task_type_name': 'project-preparation', 'task_type_id': 15},
                    {'task_type_name': 'editing', 'task_type_id': 2},
                    {'task_type_name': 'artistic-direction', 'task_type_id': 3},
                    {'task_type_name': 'VFX', 'task_type_id': 7},
                    {'task_type_name': 'motion', 'task_type_id': 5}, 
                    {'task_type_name': 'editing', 'task_type_id': 2},
                    {'task_type_name': 'VFX', 'task_type_id': 5},
                    {'task_type_name': 'pre-grading-confo', 'task_type_id': 10},
                    {'task_type_name': 'color-grading', 'task_type_id': 11},
                    {'task_type_name': 'post-grading-confo', 'task_type_id': 9}
                ];
                taskType.forEach(function(taskType, key) {
                    let startDate = new Date(year, monthStart, startDateDay);
                    let endDate = new Date(year, monthEnd, endDateDay);
                    let dates = [];
                    let date = new Date(startDate.getTime());
                    let talentName = names[Math.floor(Math.random() * names.length)];
                    let w = 0;
                    while (date <= endDate) {
                        let incrDate = date.getDate();
                        if (w > 0) {
                            incrDate = date.getDate() + 1;
                        }
                        let monthFormat = date.getMonth() + 1;
                        monthFormat = monthFormat.toString().length == 1 ? '0' + monthFormat : monthFormat;
                        let dayFormat = date.getDate().toString().length == 1 ? '0' + date.getDate() : date.getDate();
                        let dateFormat =  date.getFullYear() + '-' + monthFormat + '-' + dayFormat;
                        dates.push(new Date(date));
                        date.setDate(incrDate);
                        planning[dateFormat] = [];
                        w++;
                    }
                    dates.forEach(function(item, key){
                        let monthFormat = item.getMonth() + 1;
                        monthFormat = monthFormat.toString().length == 1 ? '0' + monthFormat : monthFormat;
                        let dayFormat = item.getDate().toString().length == 1 ? '0' + item.getDate() : item.getDate();
                        let dateFormat =  item.getFullYear() + '-' + monthFormat + '-' + dayFormat;
                        let taskData =
                                {
                                    "accepted": true,
                                    "class":'task-' + key,
                                    "created_at":'test',
                                    "created_by":'test',
                                    "end_date":'test',
                                    "id":'test',
                                    "name":'test',
                                    "note":'test',
                                    "owner":'test',
                                    "profile":'test',
                                    "project_category":'test',
                                    "project_id":26,
                                    "project_name":'TEST',
                                    "start_date":'test',
                                    "status":'test',
                                    "talentId":'test',
                                    "talentName": talentName,
                                    "talent_id":'test',
                                    "talents":[{'firstname' : names[Math.floor(Math.random() * names.length)], 'lastname' : 'TEST'}],
                                    "taskTypes":[{'id': taskType.task_type_id}],
                                    "task_type": taskType.task_type_name,
                                };
                            planning[dateFormat].push(taskData);
                    });
                    switch (key) {
                        case 0: // editing
                        startDateDay = startDateDay + 2;
                        endDateDay = endDateDay + 4;
                        break;
                        case 1: // artistic direction
                        startDateDay = startDateDay + 2;
                        break;
                        case 2: // VFX
                        startDateDay = startDateDay + 3;
                        endDateDay = endDateDay + 4;
                        break;
                        case 3: // motion
                        startDateDay = startDateDay + 2;
                        endDateDay = endDateDay + 2;
                        break;
                        case 4: //editing
                        startDateDay = startDateDay + 1;
                        endDateDay = endDateDay + 1;
                        break;
                        case 5: //VFX
                        startDateDay = startDateDay + 3;
                        endDateDay = endDateDay + 3;
                        break;
                        case 6: //pre confo
                        startDateDay = startDateDay + 2;
                        endDateDay = endDateDay + 1;

                        break;
                        case 7: // grading
                        startDateDay = startDateDay + 1;
                        endDateDay = endDateDay + 1;
                     
                        break;
                        case 8: // post confo
                        startDateDay = startDateDay + 3;
                        endDateDay = endDateDay + 3;
                        break;
                    }

                    let lastMonthDayStart = new Date(year, monthStart, 0).getDate();
                    let lastMonthDayEnd = new Date(year, monthEnd, 0).getDate();

                    if (startDateDay > lastMonthDayStart) {
                        startDateDay = startDateDay - lastMonthDayStart;
                        if (startDateDay == 0) {
                            startDateDay = 1;
                        }
                        monthStart = monthStart + 1;
                        if (monthStart > 12) {
                            monthStart = 1;
                        }
                    }
                    if (endDateDay > lastMonthDayEnd) {
                        endDateDay = endDateDay - lastMonthDayEnd;
                        if (endDateDay == 0) {
                            endDateDay = 1;
                        }
                        monthEnd = monthEnd + 1;
                        deadlineMonth = deadlineMonth + 1;
                        if (monthEnd > 12) {
                            monthEnd = 1;
                        }
                        if (deadlineMonth > 12) {
                            deadlineMonth = 1;
                            deadlineYear = deadlineYear + 1;
                        }
                    }
                });

                deadlineDay = endDateDay + 3;
                this.deadline_date = deadlineMonth + '-' + deadlineDay + '-' + deadlineYear;
                this.project.date_deadline = this.deadline_date;

                let e = [];
                let a = 0;
                if (typeof this.planning !== "undefined") {
                    this.workingWeek = [];
                    Object.keys(this.planning).forEach((day) => {
                        if (day != undefined) {
                            e.push(Object.entries(this.planning[day]));
                            if (planning[day] && typeof planning[day] != 'undefined' && planning[day].length > 0) {
                                let taskDay = [];
                                planning[day].forEach(function(item, key) {
                                    item.start_date = moment(item.start_date).format("DD");
                                    item.end_date = moment(item.end_date).format("DD MMMM");
                                    item.hourtime = moment(item.datetime).format("HH:mm");
                                    item.datetime = moment(item.datetime).format("dddd DD");
                                    taskDay.push(0);
                                    taskDay.push(item);
                                });
                                e[a][0] = taskDay;
                            }
                            this.workingWeek[moment(day).format("dddd DD")] = e[a];
                            a++;
                        }
                    });
                    this.calendarDays = Object.entries(this.workingWeek);
                }
            },
            getDatesInRange(startDate, endDate) {
                const date = new Date(startDate.getTime());
                const dates = [];
                while (date <= endDate) {
                    dates.push(new Date(date));
                    date.setDate(date.getDate() + 1);
                }

                return dates;
            },
            setProjectSelect(name) {
                $(() => {
                    $(".form__task .js-" + name + "-data").select2({
                    placeholder: "Rechercher un projet",
                    language: {
                        searching: function() {
                            return "Chargement...";
                        },
                    },
                    ajax: {
                        url: "/api/project",
                        data: function(params) {
                            return {
                                mode: "search",
                                term: params.term,
                            };
                        },
                        processResults: function(data) {
                            var data = $.map(data.datas, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: data,
                            };
                        },
                    },
                });
                $(".form__task .js-" + name + "-data").on(
                    "select2:select",
                    (e) => {
                    this.getPlanning();
            }
            );
                $(".form__task .js-" + name + "-data").on(
                    "select2:unselect",
                    (e) => {
                    this.getPlanning();
            }
            );
            });
            },
            setTalentsSelect(name) {
                $(() => {
                    $(".form__task .js-" + name + "-data").select2({
                    dropdownCssClass: "multiple-choices filters-dropdown",
                    placeholder: "Talent(s)",
                    closeOnSelect: false,
                    language: {
                        searching: function() {
                            return "Chargement...";
                        },
                    },
                    ajax: {
                        url: "/api/talent?exclude= " + this.user.id,
                        processResults: function(data) {
                            var data = $.map(data.datas, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: data,
                            };
                        },
                    },
                });
                $(".form__task .js-" + name + "-data").on(
                    "select2:select",
                    (e) => {
                    this.getPlanning();
            }
            );
                $(".form__task .js-" + name + "-data").on(
                    "select2:unselect",
                    (e) => {
                    this.getPlanning();
            }
            );
            });
            },
            setTaskTypesSelect(name) {
                $(() => {
                    $(".form__task .js-" + name + "-data").select2({
                    dropdownCssClass: "multiple-choices filters-dropdown",
                    placeholder: "Tâche(s)",
                    closeOnSelect: false,
                    language: {
                        searching: function() {
                            return "Chargement...";
                        },
                    },
                    ajax: {
                        url: "/api/task-type",
                        processResults: function(data) {
                            var data = $.map(data.datas, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: data,
                            };
                        },
                    },
                });

                $(".form__task .js-" + name + "-data").on(
                    "select2:select",
                    (e) => {
                    this.getPlanning();
            }
            );
                $(".form__task .js-" + name + "-data").on(
                    "select2:unselect",
                    (e) => {
                    this.getPlanning();
            }
            );
            });
            },
            taskHover: function(e, taskProjectId, active) {
                if (e.target.getElementsByClassName('task-actions').length > 0 && e.target.getElementsByClassName('task-actions')[0]) {
                    var taskActions = e.target.getElementsByClassName('task-actions')[0];
                    if (active) {
                        taskActions.classList.add('task-actions-hover');
                        this.hoverTaskActions = true;
                    } else {
                        taskActions.classList.remove('task-actions-hover');
                        this.hoverTaskActions = false;
                    }
                }
                if (taskProjectId) {
                    this.hoverTask = taskProjectId;
                }
                if (this.hoverTask) {
                    let allTask = document.querySelectorAll('.task-project-days.' + taskProjectId);
                    for (var i = 0; i < allTask.length; i++) {
                        var task = allTask.item(i);
                        var dayContainer = task.parentElement.parentElement.parentElement;
                        if (active) {
                            task.classList.add('related-task-hover');
                            if (dayContainer.classList.contains('goneDay')) {
                                dayContainer.classList.add('goneDaySelected');
                            }
                        } else {
                            task.classList.remove('related-task-hover');
                            if (dayContainer.classList.contains('goneDay')) {
                                dayContainer.classList.remove('goneDaySelected');
                            }
                        }
                    }
                }
            },
            setPlanningType(type) {
                if (type == 'DAYS') {
                    this.$parent.$parent.$children[2].getPlanning();
                } else {
                    if (this.project.id == 1) {
                        this.$parent.$parent.$children[2].tasksToCalendarOnBoarding();
                    } else {
                        this.$parent.$parent.$children[2].tasksToCalendar();
                    }
                }
                this.$parent.setPlanningType(type);
                this.isScrolled = true;
            },
            isDayGone(i, m, y) {
                var date = new Date();
                m = m - 1; // december become january ?
                if (new Date(y, m, i+1) < date) {
                    return true;
                }
                return false;
            },
            isToday(i, m) {
                var date = new Date();
                if (i == date.getDate() - 1 && m == date.getMonth() + 1) {
                    return 'isToday';
                }
                return i + '-' + m;
            },
            getId(y, i, m) {
                if (m < 10) {
                    m = m.toString().padStart(2, "0");
                }
                return 'days-' + y + '-' + m + '-' + i;
            },
            isTodayOrGone(i, m, y) {
                var date = new Date();
                if (i == date.getDate() - 1 && m == date.getMonth() + 1) {
                    return 'isToday';
                } else if (this.isDayGone(i, m, y)) {
                    return 'goneDay'
                }
                return i + '-' + m;
            },
            scrollToToday() {
                if (this.isScrolled && typeof this.$refs.isToday != "undefined" && this.$refs.isToday.length > 0) {
                    this.$refs.isToday[0].scrollIntoView({behavior: 'smooth', block: 'center'});
                    this.isScrolled = false;
                }
            },
            getSelectedDay(i, m, y) {
                var selectedDate = '0' + m + '/' + i + '/' + y;
                if (m > 9) {
                    selectedDate = m + '/' + i + '/' + y;
                }
                var date = new Date(selectedDate.toString());
                if (date) {
                    return date;
                }
            },
            isDeadlineDay(i, m, y, deadline) {
                var date = this.getSelectedDay(i, m, y);
                var deadline = new Date(deadline);
                if (date.getTime() === deadline.getTime()) {
                    return true;
                }
                return false;
            },
            /*startDrag(evt, item) {
                let taskID = item.taskDateId;
                let projectID = this.project.id;
                evt.dataTransfer.dropEffect = 'move'
                evt.dataTransfer.effectAllowed = 'move'
                evt.dataTransfer.setData('taskID', taskID)
                evt.dataTransfer.setData('projectID', projectID)
                this.selecto = false;
            },
            onDrop(evt) {
                const taskID = evt.dataTransfer.getData('taskID');
                const projectID = evt.dataTransfer.getData('projectID');
                if (typeof evt.path != 'undefined' && typeof evt.path[0] != 'undefined' && typeof evt.path[0].id != 'undefined' && taskID && taskID > 0) {
                    let date = false;
                    for (let i = 1; i < evt.path.length; i++) {
                        if (evt.path[0].id != '') {
                            date = evt.path[0].id;
                            if (date) {
                                date = date.replace('days-', '');
                            }
                            break;
                        }
                        if (evt.path[i].id != '') {
                            date = evt.path[i].id;
                            if (date) {
                                date = date.replace('days-', '');
                            }
                            break;
                        }
                    }
                    axios.put('/api/task/date/' + taskID, {
                        start_date: date,
                        end_date: date,
                        projectId: projectID
                    })
                    .then(res => {
                        if (res === false) {
                            // Todo : Error
                        } else {
                            this.$bus.$emit('TASK_ADD_OR_UPDATE', res.data);
                            this.onDragLeave('days-' + date);
                            this.$bus.$emit("ACTION_CHANGED", {action: "CONGRATS", type: 'TASK_MOVED'});
                            this.selecto = true; 
                        }
                        }).catch(error => console.log(error));
                    }
            },
            onDragOver(id) {
                let element = document.getElementById(id);
                if (!element.classList.contains('onDrop')) {
                    element.classList.add('onDrop');
                }
            },
            onDragLeave(id) {
                let element = document.getElementById(id);
                if (element.classList.contains('onDrop')) {
                    element.classList.remove('onDrop');
                }
            }, // TO USE LATER */

            startDrag(evt, item) {
                let startDragDate = evt.target.id ? evt.target.id : false;
                startDragDate = startDragDate.replace('days-', '');
                let itemStartDate = item.start_date;
                let itemEndDate = item.end_date;
                if (Number.isNaN(new Date(itemStartDate).getTime())) {
                    itemStartDate = moment(itemStartDate).format('YYYY/MM/DD');
                }
                if (Number.isNaN(new Date(itemEndDate).getTime())) {
                    itemEndDate = moment(itemEndDate).format('YYYY/MM/DD');
                }
                let taskStartDate = new Date(itemStartDate);
                let taskEndDate = new Date(itemEndDate);
                let endMonth = taskStartDate.getMonth() + 1;
                let startMonth = taskEndDate.getMonth() + 1;
                let taskID = item.id;
                let projectID = this.project.id;
                evt.dataTransfer.dropEffect = 'move';
                evt.dataTransfer.effectAllowed = 'move';
                evt.dataTransfer.setData('taskID', taskID);
                evt.dataTransfer.setData('projectID', projectID);
                evt.dataTransfer.setData('start', taskStartDate.getFullYear() + '-' + endMonth + '-' + taskStartDate.getDate());
                evt.dataTransfer.setData('end', taskEndDate.getFullYear() + '-' + startMonth + '-' + taskEndDate.getDate());
                evt.dataTransfer.setData('startDragDate', startDragDate);
                this.selecto = false;
                let droppable = document.getElementsByClassName('calendar-drop');
                if (droppable && droppable.length > 0) {
                    for (let i = 0; i < droppable.length; i++) {
                        let drop = droppable[i];
                        drop.style.zIndex = 1;
                    }
                }
                evt.target.parentElement.style.zIndex = 1;
            },
            dragEnd($event) {
                let droppable = document.getElementsByClassName('calendar-drop');
                if (droppable && droppable.length > 0) {
                    for (let i = 0; i < droppable.length; i++) {
                        let drop = droppable[i];
                        drop.style.zIndex = 0;
                    }
                }
            },
            onDrop(evt) {
                let droppable = document.getElementsByClassName('calendar-drop');
                if (droppable && droppable.length > 0) {
                    for (let i = 0; i < droppable.length; i++) {
                        let drop = droppable[i];
                        drop.style.zIndex = 0;
                    }
                }
                const taskID = evt.dataTransfer.getData('taskID');
                const projectID = evt.dataTransfer.getData('projectID');
                let start = evt.dataTransfer.getData('start');
                let end = evt.dataTransfer.getData('end');
                let startDragDate = evt.dataTransfer.getData('startDragDate');
                if ((typeof evt.target != 'undefined' && typeof evt.target.id != 'undefined') && taskID && taskID > 0) {
                    let date = false;
                    if (typeof evt.target != "undefined" && typeof evt.target.id != "undefined") {
                        date = evt.target.id;
                        if (date) {
                            date = date.replace('days-', '');
                        }
                    }

                    axios.put('/api/task/date/' + taskID, {
                        start_date: start,
                        end_date: end,
                        from_date: startDragDate,
                        to_date: date,
                        projectId: projectID
                    })
                    .then(res => {
                        if (res.data.datas == false) {
                            this.$bus.$emit("ACTION_CHANGED", {action: "CONGRATS", type: 'TASK_UNMOVED'});
                            this.onDragLeave();
                        } else {
                            this.$bus.$emit('TASK_ADD_OR_UPDATE', res.data);
                            this.onDragLeave();
                            this.$bus.$emit("ACTION_CHANGED", {action: "CONGRATS", type: 'TASK_MOVED'});
                            this.selecto = true; 
                        }
                        }).catch(error => console.log(error));
                    }
            },
            onDragOver(evt) {
                let dropzone = document.getElementById('dropzone');
                if (dropzone.classList.contains('in')) {
                    dropzone.classList.remove('in');
                }
                
                let elements = document.getElementsByClassName('calendar-drop');
                for (let i = 0; i < elements.length; i++) {
                    if (elements[i].classList.contains('onDrop')) {
                        elements[i].classList.remove('onDrop');
                    }
                }
                evt.target.classList.add('onDrop');
            },
            onDragLeave() {
                let onDrop = document.getElementsByClassName('onDrop');
                for (let i = 0; i < onDrop.length; i++) {
                    if (onDrop[i]) {
                        onDrop[i].classList.remove('onDrop');
                    }
                }
                this.removeTaskHover();
            },

            onSelect(e) {
                e.removed.forEach(function(el, key) {
                    console.log(el);
                    el.children[2].children[0].classList.remove('hover');
                })
                e.selected.forEach(function(el, key) {
                    console.log(el);
                    el.children[2].children[0].classList.add('hover');
                })

                this.selectedDateTask = e.selected;
            },
            onSelectEnd(e) {
                if (this.selectedDateTask && this.selectedDateTask.length > 1 && typeof this.selectedDateTask[0] != undefined && this.selectedDateTask[this.selectedDateTask.length - 1] != undefined) {
                    let start = this.selectedDateTask[0].children[0].id;
                    let end = this.selectedDateTask[this.selectedDateTask.length - 1].children[0].id;
                    start = start.replace('days-', '');
                    end = end.replace('days-', '');
                    if (Number.isNaN(new Date(start).getTime())) {
                        start = moment(start).format('YYYY/MM/DD');
                    }
                    if (Number.isNaN(new Date(end).getTime())) {
                        end = moment(end).format('YYYY/MM/DD');
                    }
                    let startCompare = new Date(start).setHours(23, 59, 59);
                    start = new Date(start);
                    end = new Date(end);
                    let today = new Date().setHours(0, 0, 0);
                    /*if (startCompare < today) {
                        start = new Date();
                        end = new Date();
                    }*/
                    let selectedDate = {start: start, end: end};
                    Vue.prototype.Global._setAction('SET_TASK', {project: this.project, selectedDateTask: selectedDate, type: 'ADD'})
                }
                this.selectedDateTask = [];
                this.removeTaskHover();
            },
            removeTaskHover() {
                document.querySelectorAll('.add-task-btn').forEach(function(el, key) {
                    el.classList.remove('hover');
                })
            },
            openTypeTasks() {
                this.typeTaskOpen = !this.typeTaskOpen;
                var projectTypeTask = document.getElementById("project-type-tasks-mobile");
                if (projectTypeTask) {
                    if (this.typeTaskOpen) {
                        projectTypeTask.classList.add("project-type-tasks-hover");
                    } else {
                        projectTypeTask.classList.remove("project-type-tasks-hover");
                    }
                }
            },
            changeMonth(monthKey) {
                this.date_month = monthKey;
                this.getPlanning();
            },
            log(error) {
                console.log(error);
            }
        },
    };
</script>