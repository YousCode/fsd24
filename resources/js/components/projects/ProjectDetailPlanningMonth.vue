<template>
    <div id="project-detail-planning" class="project-detail__planning block" style="background-color: #191032;">
            <VueSelecto
            v-if="selecto"
            :container="body"
            :dragContainer="calendarContainer"
            :selectableTargets='selectableTargets'
            :selectByClick="false"
            :selectFromInside="disableSelecto"
            :continueSelect="false"
            :continueSelectWithoutDeselect="true"
            :toggleContinueSelect='"shift"'
            :keyContainer="calendarContainer"
            :hitRate="0"
            :ratio="0"
            @select="onSelect"
            @selectEnd="onSelectEnd"
            />
            <div id="planning-monthes" class="planning-monthes">
                  <!-- ./Planning header -->
                <div class="row text-center planning">
                    <div class="col-md-3 planning-header-monthes">
                        <p><span class="icon icon-calendar"></span>Planning</p>
                    </div>
                    <div class="col-md-2 planned-days">
                        <button class="inactive-planning" v-on:click="(onboardingEnd) ? setPlanningType('DAYS') : false">Jour</button>
                        <button class="active-planning">Mois</button>
                    </div>
                    <div class="col-md-3 total-planned-header days" v-on:click="openTypeTasks(nb_days)">
                        <p v-if="nb_days > 1">
                            <span class="left">Total planifiés :</span> <span class="right">{{ nb_days }} jours</span>
                            <!-- <span class="picto-days-chevron"></span> -->
                        </p>
                        <p v-else>
                            <span class="left">Total planifié :</span> <span class="right">{{ nb_days }} jour</span>
                            <!-- <span class="picto-days-chevron"></span> -->
                        </p>
                        <div class="project-type-tasks-month">
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
                <!-- Planning calendar -->
                <div id="planning-calendar" class="row">
                    <div class="col-md-12 calendar__view">
                        <v-calendar
                                class="calendar-preview"
                                :attributes="calendarPreview.attributes"
                                :masks="calendarPreview.masks"
                                :to-page="calendarDefaultPage"
                                ref="calendarPreview"
                                is-expanded
                        >
                            <template v-slot:day-content="{ day, attributes }">
                                <div class="calendar-preview__card" :id="getDateId(day.year, day.month, day.day)" :class="isOffProjectDays(day.date) ? 'goneDay' : ''" :style="{'min-height': '110px', 'height': '100%'}">
                                    <div class="calendar-drop" :id="getId(day.year, day.day, day.month)" @drop="onDrop($event)" @dragover.prevent="onDragOver(getId(day.year, day.day, day.month))"  @dragleave.prevent="onDragLeave()"></div>
                                    <span class="calendar-preview__day" @mouseover="dayHover(day.date, _isShared, _isClient)" @mouseleave="hoverDay = false">{{ day.day }} <span v-if="isToday(day.date)" class="pastille-today"></span></span>
                                    <div class="calendar-preview__tasks" :id="'calendar-preview-' + day.year + '-' + day.month + '-' + day.day">
                                        <p v-show="!_isShared && !_isClient && project.id != 1" v-on:click="Global._setAction('SET_TASK', {project: project, selectedDate: day.date, type: 'ADD', formType: 'month'})" class="text-center hide-add-task" :class="hoverDay === day.date ? 'task-hover' : ''" @mouseover="dayHover(day.date, _isShared, _isClient)" @mouseleave="hoverDay = false">+ Ajouter</p>
                                                <p v-for="(attr, index) in attributes"
                                                class="calendar-month-task task-project task-project-month"
                                                :id="getId(day.year, day.day, day.month)"
                                                :key="attr.key"
                                                :class="(!isOffProjectDays(day.date) && attr.customData.task.status == 'CLOSED') ?  attr.customData.class + ' task-closed': attr.customData.class"
                                                :style="(attr.customData.taskTypeId == hoverTaskTypeId) ? {backgroundColor: taskTypeHoverBg[hoverTaskTypeId], border: '2px solid ' + taskTypeHoverBorder[hoverTaskTypeId]} : {}"
                                                v-show="index == 0 || index == 1 || (dayIsInSelectedMore(day))"
                                                v-on:click="(project.id == 1 || attr.customData.class == 'deadline-day') ? false : ContextMenu.show($event,'PLANNING',{ planning: attr.customData.task, isShared: _isShared },getPlanning, {isClient: _isClient, isShared: _isShared, status: attr.customData.taskStatus, closedAt: attr.customData.closedAt, startDate: attr.customData.startDate, endDate: attr.customData.endDate, projectId: project.id, project: project, selectedDate: day.date, task: attr.customData.task, tasks: tasks}), setOpenContextMenu()"
                                                v-on:mouseover="taskHover($event, 'task-' + project.id + '-' + attr.customData.task.id, true)" v-on:mouseleave="taskHover($event, 'task-' + project.id + '-' + attr.customData.task.id, false)"
                                                data-popup="js-popup"
                                                :draggable="attr.customData.class == 'deadline-day' ? false : draggable" @dragstart="startDrag($event, attr)" @dragend="dragEnd($event)"
                                                >
                                                    <span class="task-title" v-if="attr.customData.class == 'deadline-day'">
                                                        <i class="pastille" :style="{backgroundColor: '#FF4286'}" v-if="attr.key !== 99999"></i>
                                                        {{ attr.customData.title }}
                                                    </span>
                                                        <span class="task-title" :class="(attr.customData.typeTaskClass == '3D') ? 'd3' : attr.customData.typeTaskClass" v-else>
                                                        <span class="dot-button-task" :class="(attr.customData.typeTaskClass == '3D') ? 'd3' : attr.customData.typeTaskClass"></span>
                                                            {{ attr.customData.title.length > 15 ? attr.customData.title.substring(0,5) + '...'+  attr.customData.title.substring(attr.customData.title.length-3,attr.customData.title.length) : attr.customData.title }}
                                                        </span>
                                                    <br/><span class="task-talent">{{ attr.customData.talentName }}</span>
                                                    <span class="task-actions" v-show="attr.customData.class != 'deadline-day' && !_isShared && !_isClient && project.id != 1">
                                                        <button class="edit">
                                                            <span class="icon-action icon-edit" v-on:click.stop="Global._setAction('SET_TASK', {project: project, selectedDate: day.date, task: attr.customData.task, tasks: tasksInProgress, type: 'EDIT', formType: 'month'})"></span>
                                                        </button>
                                                        <button class="delete">
                                                            <span class="icon-action icon-delete" v-on:click.stop="Task._delete(attr.customData.task.id, project)"></span>
                                                        </button>
                                                    </span>
                                                </p>
                                        <p class="text-center calendar-month-task see-more-less" v-if="dayIsInSelectedMore(day)" v-on:click="removeSelectedShowMore(day)">
                                            <span>Voir Moins</span>
                                        </p>
                                        <p class="text-center calendar-month-task see-more-less" v-else-if="attributes && attributes.length > 2" v-on:click="addSelectedShowMore(day)">
                                            <span>Voir Plus</span>
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </v-calendar>
                    </div>
                </div>
                <!-- ./Planning calendar -->
            </div>
            <div class="planning-days-mobile">
                <ProjectDetailPlanningDays :_project="project" :_isShared="_isShared" :_isClient="_isClient" :_nbDays="nb_days" />
            </div>
    </div>
</template>

<script>
import { VueSelecto } from "vue-selecto";

    export default {
        props: ['_project', '_talents', 'contextMenu', '_isShared', '_isClient'],
        components: {
            VueSelecto,
        },
        data() {
            return {
                project: this._project,
                planning: {},
                projectTypeTasks: {},
                nb_days: 0,
                today: moment().format('YYYY-MM-DD'),
                calendarDefaultPage: {
                    year: new Date().getFullYear(),
                    month: new Date().getMonth() + 1
                },
                masks: {
                    input: ['L', 'YYYY-MM-DD', 'YYYY/MM/DD'],
                },
                calendarPreview: {
                    masks: {
                        weekdays: 'WWWW'
                    },
                    attributes: []
                },
                attributes_colors: [
                    "#37ffdb",
                    "#b78bff",
                    "#35da45"
                ],
                tasks: [],
                talents: [],
                showMore: false,
                selectedShowMore: [],
                hoverDay: false,
                offProjectDays: false,
                hoverTask: false,
                hoverTaskActions: false,
                typeTaskOpen: false,
                taskDragId: false,
                calendarContainer: document.querySelector('.vc-pane-container'),
                body: document.querySelector('body'),
                date: 'date',
                selectedDateTask: [],
                disableSelecto: true,
                selectableTargets: [".calendar-preview__card"],
                selecto: true,
                draggable: true,
                hoverTaskTypeId: false,
                selectToOnSelect: false,
                ContextMenu: false,
                onboardingEnd: true,
                taskTypeColors: {1:"#37ff9f", 2:"#01cfbf", 3:"#37ff46", 4:"#ff4286", 5:"#7b9cff", 6:"#ffd337", 7:"#b78bff", 8:"#377bff", 9:"#7e7bff", 10:"#be6cff", 11:"#ff37eb", 12:"#37ffdb", 13:"#ff4b4b", 14:"#ffc672", 15:"#ff4b4b"},
                taskTypeHoverBg: {1:"#15373A", 2:"#23406B", 3:"#2D4953", 4:"#552460", 5:"#3B3678", 6:"#554150", 7:"#3D1925", 8:"#151D4D", 9:"#231D4D", 10:"#301A4D", 11:"#3D0F49", 12:"#2D4971", 13:"#3D1329", 14:"#3D2C31", 15:"#3D173E"},
                taskTypeHoverBorder: {1:"#269B6D", 2:"#128795", 3:"#32A44D", 4:"#ff4286", 5:"#5B69BB", 6:"#AA8A44", 7:"#AA4944", 8:"#1C3071", 9:"#504CA6", 10:"#7743A6", 11:"#9E239A", 12:"#32A4A6", 13:"#9E2F3A", 14:"#9E7952", 15:"#9E3C7A"},
                tasksInProgress: [],
            }
        },

        created() {
            if (this.project.task) {
                this.tasks = this.project.task;
                this.tasksInProgress = this.project.tasksInProgress;
            }
            if (this._talents) {
                this.talents = this._talents;
            }
            this.$bus.$on('PLANNING_UPDATE', (tasks) => {
                this.tasks = tasks;
                this.tasksToCalendar();
                this.getPlanning();
            });

            this.$bus.$on('TASK_IN_PROGRESS_UPDATE', (tasksInProgress) => {
                if (tasksInProgress) {
                    this.tasksInProgress = tasksInProgress;
                }
            });
        },
        mounted() {
            if (this.project.id !== 1) {
                this.getPlanning();
            }
            this.ContextMenu = this.$parent.$refs.ContextMenu,
            this.calendarRef = this.$refs.calendarPreview;
            if (this.project.id == 1) {
                this.onboardingEnd = false;
            } else {
                this.tasksToCalendar();
            }

            this.project.format_deadline = new Date(this.project.date_deadline);

            this.offProjectDays = false;
            if (this._isClient || this._isShared) {
                this.selecto = false;
                this.draggable = false;
            }
        },

        methods: {
            async getPlanning() {
                await axios.get('/api/project/' + this.project.id + '/planning').then(res => {
                    if(res.success === false
                ){
                    // Todo : Error
                }
            else
                {
                    this.planning = res.data.datas; // Update project task list
                    this.projectTypeTasks = res.data.datas.project_tasks_summary;
                    for (const [key, typeTask] of Object.entries(this.projectTypeTasks)) {
                        typeTask.class = typeTask.task_type.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(" ", "-").toLowerCase();
                    }
                    this.getNbDays();

                    if (this.project.id == 1) {
                        this.projectTypeTasks = [
                            {'nb_days': 1, 'task_type': 'project-preparation', 'class': 'project-preparation', 'taskTypes': [{'id': 15}]},
                            {'nb_days': 4, 'task_type': 'editing', 'class': 'editing', 'taskTypes': [{'id': 2}]},
                            {'nb_days': 1, 'task_type': 'artistic-direction', 'class': 'artistic-direction', 'taskTypes': [{'id': 3}]},
                            {'nb_days': 4, 'task_type': 'VFX', 'class': 'vfx', 'taskTypes': [{'id': 7}]},
                            {'nb_days': 2, 'task_type': 'motion', 'class': 'motion', 'taskTypes': [{'id': 5}]},
                            {'nb_days': 1, 'task_type': 'pre-grading-confo', 'class': 'pre-grading-confo', 'taskTypes': [{'id': 10}]},
                            {'nb_days': 1, 'task_type': 'color-grading', 'class': 'color-grading', 'taskTypes': [{'id': 11}]},
                            {'nb_days': 1, 'task_type': 'post-grading-confo', 'class': 'post-grading-confo', 'taskTypes': [{'id': 9}]}
                        ];
                        this.nb_days = 17;
                    }
                }
            }).catch(error => console.log(error));
            },
            async updatePlanning() {
                await axios.get("/api/project/" + this.project.id).then(res => {
                    if (res.success === false) {
                    } else {
                        this.$bus.$emit("PLANNING_UPDATE", res.data.datas.task);
                    }
                }).catch(error => console.log(error));
            },
            getNbDays() {
                if (this.planning.project_tasks_summary) {
                    this.nb_days = 0;
                    for (const [key, value] of Object.entries(this.planning.project_tasks_summary)) {
                        this.nb_days += parseInt(value.nb_days); // value.tasks.length
                    }
                }
            },
            setPlanningType(type) {
                this.$parent.$children[3].$children[1].getPlanning();
                this.$parent.$children[3].openTypeTasks();
                this.$parent.type = type;
            },
            isToday(date) {
                const today = new Date();
                today.setHours(0);
                today.setMinutes(0);
                today.setSeconds(0);
                today.setMilliseconds(0);
                return today.getTime() === date.getTime();
            },
            tasksToCalendar() {
                let start_date = false;
                let end_date = false;
                this.calendarPreview.attributes = [];
                this.tasks.forEach(task => {
                    /*task.tasks.sort(function(a,b) {
                        return new Date(a.start_date).getTime() - new Date(b.start_date).getTime() 
                    });
                    if (task.tasks.length > 0) {
                        if (typeof task.tasks[0] !== "undefined" && typeof task.tasks[task.tasks.length - 1] !== "undefined") {
                            start_date = task.tasks[0].end_date;
                            end_date = task.tasks[task.tasks.length - 1].end_date;
                        }
                    }*/
                    start_date = task.start_date;
                    end_date = task.end_date;
                    if (Number.isNaN(new Date(start_date).getTime())) {
                        start_date = moment(start_date).format('YYYY/MM/DD');
                    }
                    if (Number.isNaN(new Date(end_date).getTime())) {
                        end_date = moment(end_date).format('YYYY/MM/DD');
                    }
                    start_date = new Date(start_date);
                    end_date = new Date(end_date);
                    const options = {
                            month: "long",
                            day: "numeric",
                        };
                    let optionsStartDate = {
                            month: "long",
                            day: "numeric",
                        }
                    if (start_date.getMonth() == end_date.getMonth()) {
                        optionsStartDate = {
                            day: "numeric"
                        };
                    }
                    start_date = start_date.toLocaleDateString('fr-FR', optionsStartDate);
                    end_date = end_date.toLocaleDateString('fr-FR', options);
                    //task.tasks.forEach(taskDate => {
                        //var dates = {'end': new Date(taskDate.end_date), 'start': new Date(taskDate.start_date)};
                        let taskStartDate = task.start_date;
                        let taskEndDate = task.end_date;
                        if (Number.isNaN(new Date(taskStartDate).getTime())) {
                            taskStartDate = moment(taskStartDate).format('YYYY/MM/DD');
                        }
                        if (Number.isNaN(new Date(taskEndDate).getTime())) {
                            taskEndDate = moment(taskEndDate).format('YYYY/MM/DD');
                        }
                        var dates = {'end': new Date(taskStartDate), 'start': new Date(taskEndDate)};
                        var user = this.talents[task.talentId[0].user_id];
                        task.task_type = task.taskTypes[0].name;
                        task.project_name = this.project.name;
                        task.created_by = task.owner.name;
                        task.talent_id = [];
                        task.talent_id.push(user);
                        task.dates = dates;
                        //task.taskDateId = taskDate.id;
                        task.task_id = task.id;
                        let dateClosedAt = task.closed_at;
                        if (Number.isNaN(new Date(dateClosedAt).getTime())) {
                            dateClosedAt = moment(dateClosedAt).format('YYYY/MM/DD');
                        }
                        let closed_at = new Date(dateClosedAt);
                        closed_at = closed_at.toLocaleDateString('fr-FR', options);
                        this.calendarPreview.attributes.push({
                            //key: 'task-' + taskDate.id,
                            key: 'task-' + task.id,
                            customData: {
                                title: (task.taskTypes && task.taskTypes[0] && task.taskTypes[0].name) ?  this.$t(task.taskTypes[0].name) || '...' : false,
                                class: 'project-day js-popup-position-button task-' + this.project.id + '-' + task.id,
                                typeTaskClass: (task.taskTypes && task.taskTypes[0] && task.taskTypes[0].name) ? task.taskTypes[0].name : false,
                                talent: user,
                                talentName: task.talentName,
                                color: 'white',
                                //startDay: new Date(taskDate.start_date),
                                //endDay: new Date(taskDate.end_date),
                                startDay: new Date(taskStartDate),
                                endDay: new Date(taskEndDate),
                                task: task,
                                //taskDateId: taskDate.id,
                                taskTypeId: task.taskTypes[0] ? task.taskTypes[0].id : 0,
                                taskStatus: task.status,
                                closedAt: closed_at,
                                startDate: start_date,
                                endDate: end_date,
                            },
                            dates: dates
                        });
                    //});
                });
                if (this.project.date_deadline) {
                    let deadlineDate = new Date(this.project.date_deadline);
                    if (Number.isNaN(deadlineDate.getTime())) {
                        deadlineDate = moment(this.project.date_deadline).format('YYYY/MM/DD');
                    }
                    var dates = {
                        'end': new Date(deadlineDate),
                        'start': new Date(deadlineDate)
                    };
                    this.calendarPreview.attributes.push({
                        key: this.project.id,
                        customData: {
                            title: 'Livraison finale',
                            class: 'deadline-day',
                            talentName: '',
                            color: 'white',
                            task: {'id': 0}
                        },
                        dates: dates
                    });
                }
            },
            tasksToCalendarOnBoarding() {
                let startDateDay = new Date().getDate();
                let endDateDay = new Date().getDate();
                var year = new Date().getFullYear();
                let deadlineDay = new Date().getDate();
                let deadlineMonth = new Date().getMonth() + 1;
                let deadlineYear = new Date().getFullYear();
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
                this.calendarPreview.attributes = [];
                let calendarPreview = this.calendarPreview;
                let project = this.project;
                let monthStart = new Date().getMonth();
                let monthEnd = new Date().getMonth();
                let switchMonth = false;
                /*if (startDateDay == 25) {
                    startDateDay = 1;
                    endDateDay = 1;
                    monthStart = monthStart + 1;
                    monthEnd = monthEnd + 1;
                    switchMonth = true;
                }*/
                let $this = this;
                taskType.forEach(function (item, key) {
                    var task = {};
                    if (monthStart > 12) {
                        monthStart = 1;
                        year = year + 1;
                    }
                    if (monthEnd > 12) {
                        monthEnd = 1;
                        year = year + 1;
                    }
                    var startDate = new Date(year, monthStart, startDateDay);
                    var endDate = new Date(year, monthEnd, endDateDay);
                    var dates = {'end': endDate, 'start': startDate};
                    var user = {'avatar': 'user.jpg', 'city': 'Paris', 'country': 'France', 'firstname': names[Math.floor(Math.random() * names.length)], 'lastname': 'Durant'};
                    task.task_type = item.task_type_name;
                    task.project_name = 'Onboarding';
                    task.created_by = 1;
                    task.talent_id = [];
                    task.talent_id.push(user);
                    task.dates = dates;
                    task.id = 23 + key;
                    calendarPreview.attributes.push({
                        key: 'task-' + task.id,
                        customData: {
                            title: $this.$t(item.task_type_name) || '...',
                            class: 'project-day js-popup-position-button task-' + project.id + '-' + task.id,
                            typeTaskClass: (item.task_type_name) ? item.task_type_name : false,
                            talent: user,
                            talentName: names[Math.floor(Math.random() * names.length)],
                            color: 'white',
                            startDay: new Date(task.start_date),
                            endDay: new Date(task.end_date),
                            task: task,
                            taskTypeId: item.task_type_id
                        },
                        dates: dates
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
                    }
                    if (endDateDay > lastMonthDayEnd) {
                        endDateDay = endDateDay - lastMonthDayEnd;
                        if (endDateDay == 0) {
                            endDateDay = 1;
                        }
                        monthEnd = monthEnd + 1;
                    }
                });

                deadlineDay = startDateDay + 3;
                let thisMonth = new Date().getMonth() + 1;
                if (switchMonth || (thisMonth < (monthStart || monthEnd))) {
                    deadlineMonth = deadlineMonth + 1;
                }
                if (deadlineDay > 30) {
                    deadlineDay = deadlineDay - 30;
                    deadlineMonth = deadlineMonth + 1;
                    if (deadlineMonth > 12) {
                        deadlineMonth = 1;
                        deadlineYear = deadlineYear + 1;
                    }
                }
                var deadline = new Date(deadlineYear, deadlineMonth, deadlineDay);
                this.project.formated_date_deadline = deadline.toLocaleDateString('fr-FR');
                var dates = {'end': new Date(deadline),'start': new Date(deadline)};
                this.calendarPreview.attributes.push({
                    key: this.project.id,
                    customData: {
                        title: 'Livraison finale',
                        class: 'deadline-day',
                        talentName: '',
                        color: 'white',
                        task: {'id': 0}
                    },
                    dates: dates
                });
                this.project.reformat_deadline = Vue.prototype.Global._reformatDate(deadline);
            },
            isOffProjectDays(date) {
                const today = new Date();
                today.setHours(0);
                today.setMinutes(0);
                today.setSeconds(0);
                today.setMilliseconds(0);
                return this.offProjectDays = (date.getTime() < today.getTime()) ? true : false;
            },
            taskHover: function (e, taskProjectId, active) {
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
                    let allTask = document.getElementsByClassName(taskProjectId);
                    for (var i = 0; i < allTask.length; i++) {
                        var task = allTask.item(i);
                        var dayContainer = task.parentElement.parentElement;
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
            dayHover(day, isShared, isClient) {
                this.isOffProjectDays(day);
                this.hoverDay = (this.offProjectDays && this.selectToOnSelect) ? false : day;
                if (isShared || isClient) {
                    this.hoverDay = false;
                }
            },
            openTypeTasks(nbDays = 0) {
                this.typeTaskOpen = !this.typeTaskOpen;
                let toclose = document.getElementById('toclose');
                if (this.typeTaskOpen) {
                    toclose.style.display = "block";
                } else {
                    toclose.style.display = "none";
                }
                var projectTypeTask = document.getElementsByClassName("project-type-tasks-month");
                if (projectTypeTask[0]) {
                    if (this.typeTaskOpen && nbDays > 0) {
                        projectTypeTask[0].classList.add("project-type-tasks-month-hover");
                    } else {
                        projectTypeTask[0].classList.remove("project-type-tasks-month-hover");
                    }
                }
            },
            dayIsInSelectedMore(day) {
                var index = this.selectedShowMore.indexOf(day);
                    if (index !== -1) {
                        return true;
                    }
                return false;
            },
            addSelectedShowMore(day) {
                var index = this.selectedShowMore.indexOf(day);
                if (index === -1) {
                    this.selectedShowMore.push(day);
                }
            },
            removeSelectedShowMore(day) {
                var index = this.selectedShowMore.indexOf(day);
                if (index !== -1) {
                    this.selectedShowMore.splice(index, 1);
                }
            },
            setOpenContextMenu() {
                this.$parent.isContextMenuOpen = true;
            },
            /*startDrag(evt, item) {
                let taskID = item.customData.taskDateId;
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
                if (((typeof evt.path != 'undefined' && typeof evt.path[0] != 'undefined' && typeof evt.path[0].id != 'undefined') || (typeof evt.originalTarget != 'undefined' && typeof evt.originalTarget.id != 'undefined')) && taskID && taskID > 0) {
                    let date = false;
                    if (typeof evt.path != "undefined" && typeof evt.path[0] != "undefined") {
                        for (let i = 1; i < evt.path.length; i++) {
                            if (evt.path[0].id != '') {
                            date = evt.path[0].id;
                            if (date) {
                                date = date.replace('month-', '');
                            }
                            break;
                            }
                            if (evt.path[i].id != '') {
                                date = evt.path[i].id;
                                if (date) {
                                    date = date.replace('month-', '');
                                }
                                break;
                            }
                        }
                    } else if (typeof evt.originalTarget != "undefined" && typeof evt.originalTarget.id != "undefined") {
                        date = evt.originalTarget.id;
                        if (date) {
                            date = date.replace('month-', '');
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
                            this.updatePlanning();
                            this.onDragLeave('month-' + date);
                            this.$bus.$emit("ACTION_CHANGED", {action: "CONGRATS", type: 'TASK_MOVED'});
                            if (this._isClient || this._isShared) {
                                this.selecto = false;
                            } else {
                                this.selecto = true;
                            }
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
            },
            getId(y, i, m) {
                return 'month-' + y + '-' + m + '-' + i;
            },
            onSelect(e) {
                this.removeTaskHover();

                let selectedFirstElId = (typeof e.selected[0] != "undefined") ? e.selected[0].id : false;
                let selectedLastElId = (e.selected.length  && typeof e.selected[e.selected.length - 1] != "undefined") ? e.selected[e.selected.length - 1].id : false;
                if (selectedFirstElId && selectedLastElId) {
                    let splitFirstEl = selectedFirstElId.split('-');
                    let splitLastEl = selectedLastElId.split('-');

                    let firstElDay = typeof splitFirstEl[3] != undefined ? parseInt(splitFirstEl[3]) : false;
                    let lastElDay = typeof splitLastEl[3] != undefined ? parseInt(splitLastEl[3]) : false;

                    let firstElMonth = typeof splitFirstEl[2] != undefined ? parseInt(splitFirstEl[2]) : false;
                    let lastElMonth = typeof splitLastEl[2] != undefined ? parseInt(splitLastEl[2]) : false;

                    let firstElYear = typeof splitFirstEl[1] != undefined ? parseInt(splitFirstEl[1]) : false;
                    let lastElYear = typeof splitLastEl[1] != undefined ? parseInt(splitLastEl[1]) : false;

                    while (firstElDay <= lastElDay) {
                        document.getElementById('month-' + firstElYear + '-' + firstElMonth + '-' + firstElDay).children[1].children[0].classList.add('task-hover');
                        firstElDay++;
                    }
                }
                

                this.selectedDateTask = e.selected;
            },
            onSelectEnd(e) {
                if (this.selectedDateTask.length > 1 && typeof this.selectedDateTask[0] != undefined && this.selectedDateTask[this.selectedDateTask.length - 1] != undefined) {
                    let start = this.selectedDateTask[0].id;
                    let end = this.selectedDateTask[this.selectedDateTask.length - 1].id;
                    start = new Date(start.replace('month-', ''));
                    end = new Date(end.replace('month-', ''));
                    if (start < new Date()) {
                        start = new Date();
                    }
                    let selectedDate = {start: start, end: end};
                    Vue.prototype.Global._setAction('SET_TASK', {project: this.project, selectedDateTask: selectedDate, selectedDate: 'NULL', type: 'ADD'})
                }
                this.selectedDateTask = [];
                this.removeTaskHover();
            },*/ // TO USE LATER

            startDrag(evt, item) {
                let taskID = item.customData.task.id;
                let projectID = this.project.id;
                let startDragDate = evt.target.id ? evt.target.id : false;
                startDragDate = startDragDate.replace('month-', '');
                let endMonth = item.dates[0].end.getMonth() + 1;
                let startMonth = item.dates[0].start.getMonth() + 1;
                evt.dataTransfer.dropEffect = 'move';
                evt.dataTransfer.effectAllowed = 'move';
                evt.dataTransfer.setData('taskID', taskID);
                evt.dataTransfer.setData('projectID', projectID);
                evt.dataTransfer.setData('end', item.dates[0].end.getFullYear() + '-' + endMonth + '-' + item.dates[0].end.getDate());
                evt.dataTransfer.setData('start', item.dates[0].start.getFullYear() + '-' + startMonth + '-' + item.dates[0].start.getDate());
                evt.dataTransfer.setData('startDragDate', startDragDate);
                let droppable = document.getElementsByClassName('calendar-preview__tasks');
                if (droppable && droppable.length > 0) {
                    for (let i = 0; i < droppable.length; i++) {
                        let drop = droppable[i];
                        drop.style.zIndex = -1;
                    }
                }
                evt.target.parentElement.style.zIndex = 0;
                this.selecto = false;
            },
            dragEnd($event) {
                let droppable = document.getElementsByClassName('calendar-preview__tasks');
                if (droppable && droppable.length > 0) {
                    for (let i = 0; i < droppable.length; i++) {
                        let drop = droppable[i];
                        drop.style.zIndex = 0;
                        //drop.style.backgroundColor = 'transparent';
                    }
                }
            },
            onDrop(evt) {
                let taskID = evt.dataTransfer.getData('taskID');
                let projectID = evt.dataTransfer.getData('projectID');
                let start = evt.dataTransfer.getData('start');
                let end = evt.dataTransfer.getData('end');
                let startDragDate = evt.dataTransfer.getData('startDragDate');
                if (((typeof evt.path != 'undefined' && typeof evt.path[0] != 'undefined' && typeof evt.path[0].id != 'undefined') || (typeof evt.target != 'undefined' && typeof evt.target.id != 'undefined')) && taskID && taskID > 0) {
                    let date = false;
                    if (typeof evt.path != "undefined" && typeof evt.path[0] != "undefined") {
                        for (let i = 1; i < evt.path.length; i++) {
                            if (evt.path[0].id != '') {
                            date = evt.path[0].id;
                            if (date) {
                                date = date.replace('month-', '');
                            }
                            break;
                            }
                            if (evt.path[i].id != '') {
                                date = evt.path[i].id;
                                if (date) {
                                    date = date.replace('month-', '');
                                }
                                break;
                            }
                        }
                    } else if (typeof evt.target != "undefined" && typeof evt.target.id != "undefined") {
                        date = evt.target.id;
                        if (date) {
                            date = date.replace('month-', '');
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
                            this.updatePlanning();
                            this.$bus.$emit("ACTION_CHANGED", {action: "CONGRATS", type: 'TASK_MOVED'});
                            if (this._isClient || this._isShared) {
                                this.selecto = false;
                            } else {
                                this.selecto = true;
                            }
                            this.onDragLeave();
                        }
                        }).catch(error => console.log(error));
                    }
            },
            onDragOver(id) {
                let dropzone = document.getElementById('dropzone');
                if (dropzone.classList.contains('in')) {
                    dropzone.classList.remove('in');
                }

                let element = document.getElementById(id);
                if (!element.classList.contains('onDrop')) {
                    element.classList.add('onDrop');
                }
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
            getId(y, i, m) {
                return 'month-' + y + '-' + m + '-' + i;
            },
            onSelect(e) {
                this.removeTaskHover();
                let selectedFirstElId = (typeof e.selected[0] != "undefined") ? e.selected[0].id : false;
                let selectedLastElId = (e.selected.length  && typeof e.selected[e.selected.length - 1] != "undefined") ? e.selected[e.selected.length - 1].id : false;
                if (selectedFirstElId && selectedLastElId) {
                    let splitFirstEl = selectedFirstElId.split('-');
                    let splitLastEl = selectedLastElId.split('-');
                    let firstElDay = typeof splitFirstEl[3] != undefined ? parseInt(splitFirstEl[2]) : false;
                    let lastElDay = typeof splitLastEl[3] != undefined ? parseInt(splitLastEl[2]) : false;

                    let firstElMonth = typeof splitFirstEl[2] != undefined ? parseInt(splitFirstEl[1]) : false;
                    let lastElMonth = typeof splitLastEl[2] != undefined ? parseInt(splitLastEl[1]) : false;

                    let firstElYear = typeof splitFirstEl[1] != undefined ? parseInt(splitFirstEl[0]) : false;
                    let lastElYear = typeof splitLastEl[1] != undefined ? parseInt(splitLastEl[0]) : false;

                    while (firstElDay <= lastElDay) {
                        document.getElementById('calendar-preview-' + firstElYear + '-' + firstElMonth + '-' + firstElDay).children[0].classList.add('task-hover');
                        firstElDay++;
                    }
                }
                this.selectToOnSelect = true;
                this.selectedDateTask = e.selected;
            },
            onSelectEnd(e) {
                if (this.selectedDateTask.length > 1 && typeof this.selectedDateTask[0] != undefined && this.selectedDateTask[this.selectedDateTask.length - 1] != undefined) {
                    let start = this.selectedDateTask[0].id;
                    let end = this.selectedDateTask[this.selectedDateTask.length - 1].id;
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
                    Vue.prototype.Global._setAction('SET_TASK', {project: this.project, selectedDateTask: selectedDate, selectedDate: 'NULL', type: 'ADD'})
                }
                this.selectedDateTask = [];
                this.removeTaskHover();
                this.selectToOnSelect = false;
            },
            removeTaskHover() {
                document.querySelectorAll('.hide-add-task').forEach(function(el, key) {
                    el.classList.remove('task-hover');
                })
            },
            getDateId(year, month, day) {
                if (month < 10) {
                    month = month.toString().padStart(2, "0");
                }
                let date = year + '-' + month + '-' + day;

                return date;
            }
        }
    }
</script>