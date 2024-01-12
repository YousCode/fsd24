<template>
<div class="row">
    <div id="loader">
            <div style=" height: 33px;width:40px;position: relative;">
                <svg class="circle" viewBox="0 0 50 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M27.8851 13.3985C31.6024 13.3985 34.6158 10.3987 34.6158 6.69829C34.6158 2.99785 31.6024 -0.00195312 27.8851 -0.00195312C24.1678 -0.00195312 21.1543 2.99785 21.1543 6.69829C21.1543 10.3987 24.1678 13.3985 27.8851 13.3985Z" fill="white"/>
                    <path d="M42.3078 21.0564C45.4941 21.0564 48.077 18.4852 48.077 15.3134C48.077 12.1416 45.4941 9.57031 42.3078 9.57031C39.1215 9.57031 36.5386 12.1416 36.5386 15.3134C36.5386 18.4852 39.1215 21.0564 42.3078 21.0564Z" fill="white"/>
                    <path d="M45.1925 36.3706C47.8477 36.3706 50.0002 34.2279 50.0002 31.5847C50.0002 28.9415 47.8477 26.7988 45.1925 26.7988C42.5372 26.7988 40.3848 28.9415 40.3848 31.5847C40.3848 34.2279 42.5372 36.3706 45.1925 36.3706Z" fill="white"/>
                    <path d="M36.538 49.7707C38.6622 49.7707 40.3842 48.0565 40.3842 45.942C40.3842 43.8275 38.6622 42.1133 36.538 42.1133C34.4139 42.1133 32.6919 43.8275 32.6919 45.942C32.6919 48.0565 34.4139 49.7707 36.538 49.7707Z" fill="white"/>
                    <path d="M19.2309 53.5988C21.3551 53.5988 23.0771 51.8847 23.0771 49.7701C23.0771 47.6556 21.3551 45.9414 19.2309 45.9414C17.1067 45.9414 15.3848 47.6556 15.3848 49.7701C15.3848 51.8847 17.1067 53.5988 19.2309 53.5988Z" fill="white"/>
                    <path d="M4.80796 42.1142C6.40108 42.1142 7.69257 40.8285 7.69257 39.2426C7.69257 37.6567 6.40108 36.3711 4.80796 36.3711C3.21483 36.3711 1.92334 37.6567 1.92334 39.2426C1.92334 40.8285 3.21483 42.1142 4.80796 42.1142Z" fill="white"/>
                    <path d="M1.92308 24.8834C2.98516 24.8834 3.84615 24.0263 3.84615 22.969C3.84615 21.9118 2.98516 21.0547 1.92308 21.0547C0.860991 21.0547 0 21.9118 0 22.969C0 24.0263 0.860991 24.8834 1.92308 24.8834Z" fill="white"/>
                    <path d="M11.5383 11.483C12.6004 11.483 13.4614 10.6259 13.4614 9.56865C13.4614 8.51138 12.6004 7.6543 11.5383 7.6543C10.4762 7.6543 9.61523 8.51138 9.61523 9.56865C9.61523 10.6259 10.4762 11.483 11.5383 11.483Z" fill="white"/>
                </svg>
            </div>
    </div>
    <!-- Filters -->
    <PlanningFilters :_filters="filters" :_groups="groups" :_selected_talents_name="selected_talents_name" :_talentIdSelected="talentIdSelected" :_user="user" :_workspace="workspace"></PlanningFilters>
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
            <div class="calendar-preview__card" :class="isOffProjectDays(day.date) ? 'goneDay' : ''" :style="{'min-height': '110px', 'height': '100%'}">
            <div class="calendar-drop" :id="getId(day.year, day.day, day.month)" @drop="onDrop($event)" @dragover.prevent="onDragOver(getId(day.year, day.day, day.month))" 
                        @dragenter.prevent="onDragOver(getId(day.year, day.day, day.month))"
                        @dragleave.prevent="onDragLeave(getId(day.year, day.day, day.month))"></div>
            <span class="calendar-preview__day" @mouseover="dayHover(day.date)" @mouseleave="hoverDay = false">{{ day.day }} <span v-if="isToday(day.date)" class="pastille-today"></span></span>
            <div class="calendar-preview__tasks" :id="'calendar-preview-' + day.year + '-' + day.month + '-' + day.day">
                <p v-on:click="Global._setAction('SET_TASK', {selectedDate: day.date, type: 'ADD', formType: 'task', selectToTalents: [{'talent_id': current_selected_talent_id, 'talent_name': current_selected_talent_name}]})" class="text-center hide-add-task" :class="hoverDay === day.date ? 'task-hover' : ''" @mouseover="dayHover(day.date)" @mouseleave="hoverDay = false">+ Ajouter</p>
                <p v-for="(attr, index) in attributes"
                    class="calendar-month-task task-project task-project-month"
                    :id="getId(day.year, day.day, day.month)"
                    :key="attr.key"
                    :class="(!isOffProjectDays(day.date) && attr.customData.taskStatus == 'CLOSED') ? attr.customData.class + ' task-closed' : attr.customData.class"
                    :style="{}"
                    v-on:click="contextMenu.show($event,'PLANNING', {planning: attr.customData.task}, getPlanning, 
                    {
                        status: attr.customData.taskStatus, 
                        closedAt: attr.customData.closedAt,
                        startDate: attr.customData.startDate,
                        endDate: attr.customData.endDate,
                        projectId: attr.customData.projectId,
                        selectedDate: day.date,
                        task: attr.customData.task,
                    })"
                    v-if="(attr.customData.type == 'TASK' || 'MISSION EXPLORER') && attr.customData.talent && attr.customData.talent.id == talentIdSelected && (index == 0 || index == 1 || (dayIsInSelectedMore(day)))"
                    v-on:mouseover="taskHover($event, 'task-' + attr.customData.task.project_id + '-' + attr.customData.task.id, true)" v-on:mouseleave="taskHover($event, 'task-' + attr.customData.task.project_id + '-' + attr.customData.task.id, false)"
                    data-popup="js-popup"
                    :draggable="draggable" @dragstart="startDrag($event, attr)"
                >
                    <span class="task-title" :class="(attr.customData.type == 'MISSION EXPLORER') ? 'mission-explorer' : (attr.customData.taskTypeClass == '3D') ? 'd3' : attr.customData.taskTypeClass">
                    <span class="dot-button-task" :class="(attr.customData.type == 'MISSION EXPLORER') ? 'mission-explorer-dot' : (attr.customData.taskTypeClass == '3D') ? 'd3' : attr.customData.taskTypeClass"></span>
                        <template v-if="attr.customData.type == 'TASK'">
                            {{ attr.customData.title.length > 15 ? attr.customData.title.substring(0,5) + '...'+  attr.customData.title.substring(attr.customData.title.length-3,attr.customData.title.length) : attr.customData.title }}
                        </template>
                        <template v-else-if="attr.customData.type == 'MISSION EXPLORER'">
                            {{ $t('mission-explorer') }}
                        </template>
                    </span>
                    <!--<span class="task-title task-mission-explorer" v-else-if="attr.customData.type == 'MISSION EXPLORER'">
                        {{ $t('mission-explorer') }}
                    </span>-->
                    <span class="task-talent">{{ shortProjectName(attr.customData.project_name) }}</span>
                    <span class="task-actions" v-show="attr.customData.class != 'deadline-day' && attr.customData.type == 'TASK'">
                        <span class="icon-action icon-edit" v-on:click.stop="Global._setAction('SET_TASK', {element: attr.customData.projectId, selectedDate: day.date, task: attr.customData.task, type: 'EDIT', formType: 'planning'})"></span>
                        <span class="icon-action icon-delete" v-on:click.stop="Task._delete(attr.customData.task.id, project)"></span>
                    </span>
                </p>
                <p v-else-if="attr.customData.type == 'APPOINTMENT' && (index == 0 || (dayIsInSelectedMore(day)))" class="calendar-month-task task-project task-project-month" :class="attr.customData.class"
                   v-on:mouseover="appointmentHover($event, 'appointment-' + attr.customData.appointment.id, true)" v-on:mouseleave="appointmentHover($event, 'appointment-' + attr.customData.appointment.id, false)" 
                >
                    <i class="pastille" :style="{backgroundColor: '#FF4286', top: '8px'}" v-if="attr.key !== 99999"></i>
                    <span class="task-title" :style="{color: '#FF4286'}">
                        {{ attr.customData.title }}
                    </span>
                    <span class="task-talent">{{ attr.customData.appointment.title.length > 15 ? attr.customData.appointment.title.substring(0,5) + '...'+  attr.customData.appointment.title.substring(attr.customData.appointment.title.length-3,attr.customData.appointment.title.length) : attr.customData.appointment.title }}</span>
                    <span class="appointment-actions">
                        <span class="icon-action icon-edit" v-on:click="Appointment._edit(attr.customData.appointment)"></span>
                        <span class="icon-action icon-delete" v-on:click="Appointment._delete(attr.customData.appointment)"></span>
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
</template>
    
    <script>
    import { VueSelecto } from "vue-selecto";
        export default {
            props: ["user", '_workspace'],
            components: {
                VueSelecto,
            },
            data() {
                let date = new Date();
                return {
                    workspace: this._workspace,
                    planning: {},
                    selected_talents_id: [],
                    selected_talents_name: [],
                    selected_task_types_id: [],
                    current_selected_talent_id: this.user.id,
                    current_selected_talent_name: this.user.firstname + ' ' + this.user.lastname,
                    filters: {
                        talents_id: [],
                        projects_id: null,
                        project_name: "",
                        task_types_id: []
                    },
                    date_month: date.getMonth() + 1,
                    date_year: date.getFullYear(),
                    today: moment().format("YYYY-MM-DD"),
                    calendarPreview: {
                        masks: {
                            weekdays: 'WWWW'
                        },
                        attributes: []
                    },
                    selectedShowMore: [],
                    calendarDefaultPage: {
                        year: new Date().getFullYear(),
                        month: new Date().getMonth() + 1
                    },
                    talentIdSelected: this.user.id,
                    hoverDay: false,
                    draggable: true,
                    groups: [],
                    type: 'MONTH',
                    contextMenu: false,
                    selecto: true,
                    body: document.querySelector('body'),
                    calendarContainer: document.querySelector('.vc-pane-container'),
                    disableSelecto: true,
                    selectableTargets: [".calendar-preview__card"],
                    selectToOnSelect: false,
                };
            },
    
            beforeMount() {},
    
            mounted() {
                this.contextMenu = this.$parent.$refs.ContextMenu;
                this.getPlanning();
    
                this.$bus.$on("TASK_ADD_OR_UPDATE", () => {
                    this.getPlanning();
                });
    
                this.$bus.$on("APPOINTMENT_ADD_OR_UPDATE", () => {
                    this.getPlanning();
                });
    
                this.isMounted = true;
                this.$bus.$on('GROUP_UPDATE', () => {
                    this.getGroups();
                    this.getPlanning();
                });

                this.getGroups();

                this.$bus.$on("PLANNING_CALENDAR_UPDATE_CURRENT_TALENT_ID", (data) => {
                    let talentId = data.talentId ? data.talentId : false;
                    if (talentId) {
                        this.current_selected_talent_id = talentId;
                        this.filters.talent_id = [talentId];
                    }
                });

                this.$bus.$on('PLANNING_CALENDAR_SEARCH_USER', (data) => {
                    if (data.talent) {
                        let talent = data.talent;
                        this.current_selected_talent_id = talent.id;
                        this.current_selected_talent_name = talent.name;
                    }
                });

                this.$bus.$on("PLANNING_CALENDAR_MY_PLANNING", () => {
                    if (this.user) {
                        let user = this.user;
                        this.current_selected_talent_id = user.id;
                        this.current_selected_talent_name = user.name;
                        this.filters.talent_name = "";
                    }
                });

                this.$bus.$on("PLANNING_CALENDAR_UPDATE", (data) => {
                    this.talentIdSelected = data.talents;
                    if (data.talentData) {
                        this.current_selected_talent_id = data.talentData[0].id ? data.talentData[0].id : false;
                        this.current_selected_talent_name = data.talentData[0].firstname && data.talentData[0].lastname ? data.talentData[0].firstname + ' ' + data.talentData[0].lastname : false;
                        this.talentIdSelected = data.talentData[0].id ? data.talentData[0].id : false;
                    }
                    this.getPlanning();
                });

                let leftArrow = document.getElementsByClassName('vc-arrow is-left');
                let rightArrow = document.getElementsByClassName('vc-arrow is-right');
                leftArrow = leftArrow[0] ? leftArrow[0] : false;
                rightArrow = rightArrow[0] ? rightArrow[0] : false;
                rightArrow.addEventListener('click', this.planningSlideUp);
                leftArrow.addEventListener('click', this.planningSlideDown);
            },
    
            computed: {},
    
            methods: {
            planningSlideUp() {
                this.date_month = this.date_month + 1;
                if (this.date_month > 12) {
                    this.date_month = 1;
                    this.date_year = this.date_year + 1
                }
                this.getPlanning();
            },
            planningSlideDown() {
                this.date_month = this.date_month - 1;
                if (this.date_month < 1) {
                    this.date_month = 12;
                    this.date_year = this.date_year - 1
                }
                this.getPlanning();
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
            appointmentHover: function (e, appointmentId, active) {
                if (e.target.getElementsByClassName('appointment-actions').length > 0 && e.target.getElementsByClassName('appointment-actions')[0]) {
                    let appointmentActions = e.target.getElementsByClassName('appointment-actions')[0];
                    if (active) {
                        appointmentActions.classList.add('appointment-actions-hover');
                    } else {
                        appointmentActions.classList.remove('appointment-actions-hover');
                    }
                }
                if (appointmentId) {
                    this.hoverAppointment = appointmentId;
                }
            },
            dayHover(day) {
                this.isOffProjectDays(day);
                this.hoverDay = (this.selectToOnSelect) ? false : day;
            },
            openTypeTasks() {
                this.typeTaskOpen = !this.typeTaskOpen;
                var projectTypeTask = document.getElementsByClassName("project-type-tasks-month");
                if (projectTypeTask[0]) {
                    if (this.typeTaskOpen) {
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
            startDrag(evt, item) {
                let taskID = item.customData.task.id;
                let projectID = item.customData.projectId;
                evt.dataTransfer.dropEffect = 'move';
                evt.dataTransfer.effectAllowed = 'move';
                let startDragDate = evt.target.id ? evt.target.id : false;
                startDragDate = startDragDate.replace('month-', '');
                let endMonth = item.dates[0].end.getMonth() + 1;
                let startMonth = item.dates[0].start.getMonth() + 1;
                evt.dataTransfer.setData('taskID', taskID)
                evt.dataTransfer.setData('projectID', projectID)
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
            onDrop(evt) {
                let droppable = document.getElementsByClassName('calendar-preview__tasks');
                if (droppable && droppable.length > 0) {
                    for (let i = 0; i < droppable.length; i++) {
                        let drop = droppable[i];
                        drop.style.zIndex = 0;
                        //drop.style.backgroundColor = 'transparent';
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
                            date = date.replace('month-', '');
                        }
                                         
                        axios.put('/api/task/date/' + taskID, {
                            start_date: start,
                            end_date: end,
                            from_date: startDragDate,
                            to_date: date,
                            projectId: projectID
                        })
                        .then(res => {
                            if (res === false) {
                                // Todo : Error
                            } else {
                                this.onDragLeave('month-' + date);
                                this.$bus.$emit('TASK_ADD_OR_UPDATE');
                                this.$bus.$emit("ACTION_CHANGED", {action: "CONGRATS", type: 'TASK_MOVED'});
                                this.selecto = true;
                                this.removeTaskHover();
                            }
                        }).catch(error => console.log(error));
                    }
                }
            },
            onDragOver(id) {
                let element = document.getElementById(id);
                if (element && !element.classList.contains('onDrop')) {
                    element.classList.add('onDrop');
                }
            },
            onDragLeave(id) {
                let element = document.getElementById(id);
                if (element && element.classList.contains('onDrop')) {
                    element.classList.remove('onDrop');
                }
            },
            getId(y, i, m) {
                if (m < 10) {
                    m = m.toString().padStart(2, "0");
                }
                return 'month-' + y + '-' + m + '-' + i;
            },
            onSelect(e) {
                this.removeTaskHover();
                let selectedFirstElId = (typeof e.selected[0] != "undefined" && typeof e.selected[0].children[0] != "undefined") ? e.selected[0].children[0].id : false;
                let selectedLastElId = (e.selected.length  && typeof e.selected[e.selected.length - 1] != "undefined" && typeof e.selected[e.selected.length - 1].children[0] != "undefined") ? e.selected[e.selected.length - 1].children[0].id : false;
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
                        document.getElementById('calendar-preview-' + firstElYear + '-' + firstElMonth + '-' + firstElDay).children[0].classList.add('task-hover');
                        firstElDay++;
                    }
                }
                
                this.selectToOnSelect = true;
                this.selectedDateTask = e.selected;
            },
            onSelectEnd(e) {
                if (this.selectedDateTask && this.selectedDateTask.length > 1 && typeof this.selectedDateTask[0] != undefined && this.selectedDateTask[this.selectedDateTask.length - 1] != undefined) {
                    let start = this.selectedDateTask[0].children[0].id;
                    let end = this.selectedDateTask[this.selectedDateTask.length - 1].children[0].id;
                    start = start.replace('month-', '');
                    end = end.replace('month-', '');
                    let startCompare = new Date(start).setHours(23, 59, 59);
                    start = new Date(start);
                    end = new Date(end);
                    let today = new Date().setHours(0, 0, 0);
                    /*if (startCompare < today) {
                        start = new Date();
                        end = new Date();
                    }*/
                    let selectedDate = {start: start, end: end};
                    Vue.prototype.Global._setAction('SET_TASK', {selectedDateTask: selectedDate, selectedDate: 'NULL', type: 'ADD', formType: 'task', selectToTalents: [{'talent_id': this.current_selected_talent_id, 'talent_name': this.current_selected_talent_name}]})
                }
                this.selectedDateTask = [];
                this.removeTaskHover();
                this.selectToOnSelect = false;
            },
            isToday(date) {
                const today = new Date();
                today.setHours(0);
                today.setMinutes(0);
                today.setSeconds(0);
                today.setMilliseconds(0);
                return today.getTime() === date.getTime();
            },
            async getPlanning() {
                    // Check if the current user id is the array
                    var userId = this.user.id.toString();
                    this.filters.talents_id = this.filters.talents_id.filter(
                        item => ![this.user.id].includes(item)
                    );
                    this.filters.talents_id = this.filters.talents_id.filter(
                        item => ![userId].includes(item)
                    );
                    this.filters.talents_id.unshift(this.user.id);
                    this.selected_talents_id = this.filters.talents_id;
                    this.selected_task_types_id = this.filters.task_types_id;
                    this.$bus.$on('PLANNING_TODAY', () => {
                        let date = new Date();
                        this.date_month = date.getMonth() + 1,
                        this.date_year = date.getFullYear();
                    });
                    await axios
                        .get("/api/planning", {
                            params: {
                                workspace: this._workspace,
                                filter_talents_id: [this.current_selected_talent_id],
                                filter_projects_id: this.filters.projects_id,
                                filter_task_types_id: this.filters.task_types_id,
                                date_month: this.date_month,
                                date_year: this.date_year,
                                type: 'PLANNING',
                            }
                        })
                        .then(res => {
                            if (res.success === false) {
                                // Todo : Error
                            } else {
                                let datas = res.data.datas;
                                let start_date = false;
                                let end_date = false;
                                this.planning = datas.planning; // Update project task list
                                this.selected_talents_name = datas.talent_name; // Talent name list
                                this.selected_task_types_name = datas.task_name; // Task name list
                                localStorage.setItem("date_month", this.date_month);
                                localStorage.setItem("date_year", this.date_year);
                                this.calendarPreview.attributes = [];
                                for (const day in this.planning) {
                                    if (typeof this.planning[day] !== undefined) {
                                        for (const userId in this.planning[day]) {
                                            if (this.planning[day][userId].events) {
                                                for (const index in this.planning[day][userId].events) {
                                                    let event = this.planning[day][userId].events[index];
                                                    if (event.type == "TASK" || event.type == "MISSION EXPLORER") {
                                                        start_date = event.start_date;
                                                        end_date = event.end_date;
                                                        if (Number.isNaN(new Date(start_date).getTime())) {
                                                            start_date = moment(start_date).format('YYYY/MM/DD');
                                                        }
                                                        if (Number.isNaN(new Date(end_date).getTime())) {
                                                            end_date = moment(end_date).format('YYYY/MM/DD');
                                                        }
                                                        let dates = {'end': new Date(end_date), 'start': new Date(start_date)};
                                                        let user = this.selected_talents_name[userId][0];
                                                        start_date = new Date(start_date);
                                                        end_date = new Date(end_date);
                                                        const options = {month: "long", day: "numeric"};
                                                        let optionsStartDate = {month: "long", day: "numeric"}
                                                        if (start_date.getMonth() == end_date.getMonth()) {
                                                            optionsStartDate = {day: "numeric"};
                                                        }
                                                        start_date = start_date.toLocaleDateString('fr-FR', optionsStartDate);
                                                        end_date = end_date.toLocaleDateString('fr-FR', options);
                                                        
                                                        this.calendarPreview.attributes.push({
                                                        key: 'task-' + event.id,
                                                        customData: {
                                                            title: this.$t(event.task_type) || '...',
                                                            taskTypeClass: event.task_type,
                                                            class: 'planning project-day js-popup-position-button task-' + event.project_id + '-' + event.id,
                                                            talent: user,
                                                            talentName: user.firstname + '...' + user.lastname,
                                                            color: 'white',
                                                            startDay: new Date(event.start_date),
                                                            endDay: new Date(event.end_date),
                                                            task: event,
                                                            projectId: event.project_id,
                                                            project_name: event.project_name,
                                                            taskDateId: event.id,
                                                            taskStatus: event.status,
                                                            closedAt: event.closed_at,
                                                            taskStatus: event.status,
                                                            startDate: start_date,
                                                            endDate: end_date,
                                                            type: event.type,
                                                        },
                                                        dates: dates
                                                        });
                                                    } else if (event.type == "APPOINTMENT") {
                                                        let appointmentDatetime = event.datetime;
                                                        if (Number.isNaN(new Date(appointmentDatetime).getTime())) {
                                                            appointmentDatetime = moment(appointmentDatetime).format('YYYY/MM/DD');
                                                        }
                                                        let appointmentDate = new Date(appointmentDatetime);
                                                        let dates = {'end': new Date(appointmentDate), 'start': new Date(appointmentDate)};
                                                        let hours = appointmentDate.getHours() < 10 ? '0' + appointmentDate.getHours() : appointmentDate.getHours();
                                                        let minutes = appointmentDate.getMinutes() < 10 ? '0' + appointmentDate.getMinutes() :  appointmentDate.getMinutes();
                                                        let title = 'RDV ' + hours + ':' + minutes;
                                                        let hour = event.firstname;
                                                        this.calendarPreview.attributes.push({
                                                        key: 'appointment-' + event.id,
                                                        customData: {
                                                            class: 'planning project-day js-popup-position-button appointment-' + event.id,
                                                            title: title,
                                                            project_name: hour,
                                                            type: event.type,
                                                            appointment: event,
                                                        },
                                                        dates: dates
                                                        });
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
    
                                // Save talents filter for refresh
                                /*localStorage.setItem(
                                    "filter_talents_id",
                                    JSON.stringify(this.filters.talents_id)
                                );
                                localStorage.setItem(
                                    "selected_talents_id",
                                    JSON.stringify(this.selected_talents_id)
                                );
                                localStorage.setItem(
                                    "selected_talents_name",
                                    JSON.stringify(this.selected_talents_name)
                                );*/
    
                                // Save task filter for refresh
                                /*localStorage.setItem(
                                    "filter_task_types_id",
                                    JSON.stringify(this.filters.task_types_id)
                                );
                                localStorage.setItem(
                                    "selected_task_types_id",
                                    JSON.stringify(this.selected_task_types_id)
                                );
                                localStorage.setItem(
                                    "selected_task_types_name",
                                    JSON.stringify(this.selected_task_types_name)
                                );*/
                                document.getElementById('loader').style.display='none';
                            }
                        })
                        .catch(error => console.log(error));
            },
            /**
            * Clear field
                 */
                 clear(type) {
                    if (typeof this.filters[type] == "object") {
                        this.filters[type] = [];
                    } else {
                        this.filters[type] = null;
    
                        if (type == "project_name") {
                            this.filters["projects_id"] = null;
                        }
                    }
    
                    if ($(".js-" + type + "-data")) {
                        $(".js-" + type + "-data")
                            .val("")
                            .trigger("change");
                    }
    
                    if (type == "talents_id") {
                        $(".js-planning-row").css({
                            transform: ""
                        });
                    }

                    this.talentIdSelected = this.user.id;
    
                    this.getPlanning();
                },
                getGroups(){
                    axios.get("/api/group?workspace=" + this.workspace).then(res => {
                        if(res.success === false){
                            // Error
                        } else {
                        this.groups = res.data.datas;
                    }
                    }).catch(error => console.log(error));
                },
                removeTaskHover() {
                    document.querySelectorAll('.hide-add-task').forEach(function(el, key) {
                        el.classList.remove('task-hover');
                    })
                },
                shortProjectName(projectName) {
                    if (projectName) {
                        return projectName.length > 15 ? projectName.substring(0, 15) + "..." : projectName;
                    }

                    return '';
                }
            }
        };
    </script>