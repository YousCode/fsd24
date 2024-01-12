<template>
<div class="js-planning row" v-on:mouseover="scrollTo()">
    <div id="loaderDays">
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
    <PlanningFilters :_filters="filters" :_groups="groups" :_selected_talents_name="selected_talents_name" :_user="user" :_workspace="workspace"></PlanningFilters>
    <!-- ./Filters -->
    <!-- Header -->
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
    <div class="col-12">
        <div class="planning-header">
        <span v-show="this.filters.talents_id.length > 4 && this.sliceStart > 0" class="planning-arrow prev" v-on:click="calendarSlide('DOWN')">
            <span class="picto-planning-prev"></span>
        </span>
        <!-- Date picker -->
            <div class="col-date head">
                <div class="col-date-content">
                    <span class="planning-arrow head prev" v-on:click="handleDateChange('DOWN')">
                        <span class="picto-planning-prev"></span>
                    </span>
                    <!-- <button v-on:click="handleDateChange('DOWN')" class="is-prev"><span class="sr-only">Précédent</span></button> -->
                    <span class="date">
                        {{ date_month | monthLabel }} {{ date_year }}
                    </span>
                    <!-- <button v-on:click="handleDateChange('UP')" class="is-next"><span class="sr-only">Suivant</span></button> -->
                    <span class="planning-arrow head next" v-on:click="handleDateChange('UP')">
                        <span class="picto-planning-next"></span>
                    </span>
                </div>
            </div>
            <!-- Talent name -->
            <template v-for="talent_id, key in selected_talents_id">
                <div class="col head-talent" :class="key == selected_talents_id.length - 1 ? 'last' : (key == 0) ? 'first' : ''" v-on:mouseover="hoverElement('delete-user-' + talent_id, true)" v-on:mouseleave="hoverElement('delete-user-' + talent_id, false)">
                    <p v-if="selected_talents_name[talent_id] && selected_talents_name[talent_id][0]" v-on:click="goToUserPlanning(selected_talents_name[talent_id][0])">
                            {{ selected_talents_name[talent_id][0].firstname }}
                            {{ selected_talents_name[talent_id][0].lastname | truncate(3, ".") }}
                    </p>
                    <div :id="'delete-user-' + talent_id" class="icon" v-on:click="editGroup(talent_id)">
                        <span class="picto-delete-export-line"></span>
                    </div>
                </div>
            </template>
                <span v-show="this.filters.talents_id.length > 4 && this.sliceEnd < this.filters.talents_id.length" class="planning-arrow next" v-on:click="calendarSlide('UP')">
                    <span class="picto-planning-next"></span>
                </span>
    </div>
<!-- ./Header -->
    <div class="planning-container">
<!-- Planning -->
        <div class="planning-content" :data-day="moment(date).format('dddd')" :ref="getPlanningClass(date, true)" :class="getPlanningClass(date, false)" v-for="(planningByTalent, date) in planning">
            <div class="col-date date-content" v-bind:class="{ 'is-current': date == today }" v-on:click="highlightedRow($event)">
                {{ ucfirst(moment(date).format("dddd Do MMMM")) }}
                <span class="dot-button-task" v-show="date == today"></span>
            </div>
            <div :id="'task-' + talent_id + '-' + moment(date).format('Y-MM-DD')"
                 :data-drop-date="moment(date).format('Y-MM-DD')"
                 :data-drop-talent-id="talent_id"
                 :data-drop-talent-name="(selected_talents_name[talent_id] && selected_talents_name[talent_id][0]) ? selected_talents_name[talent_id][0].firstname + ' ' + selected_talents_name[talent_id][0].lastname : ''"
                 class="col task-col" 
                 :class="'task-' + moment(date).format('Y-MM-DD')"
                 v-for="talent_id in selected_talents_id"
                 v-on:mouseover="hoverElement('add-' + talent_id + '-' + moment(date).format('D'), true)" v-on:mouseleave="hoverElement('add-' + talent_id + '-' + moment(date).format('D'), false)"
                 >
                <button :id="(moment(date).format('D')) ? 'add-' + talent_id + '-' + moment(date).format('D') : ''" v-on:click.stop="Global._setAction('SET_TASK', {selectedDate: new Date(date), type: 'ADD', formType: 'task', selectToTalents: [{talent_id: talent_id, talent_name: selected_talents_name[talent_id][0].firstname + ' ' + selected_talents_name[talent_id][0].lastname}]})" 
                v-on:mouseover="hoverElement('add-' + talent_id + '-' + moment(date).format('D'), true)" v-on:mouseleave="hoverElement('add-' + talent_id + '-' + moment(date).format('D'), false)" 
                class="add-task">
                    + Ajouter
                </button>
                <div class="calendar-drop"
                    :id="'calendar-drop-' + talent_id + '-' + moment(date).format('Y-MM-DD')"
                    :data-drop-date="moment(date).format('Y-MM-DD')"
                    :data-drop-talent-id="talent_id"
                    :data-drop-talent-name="(selected_talents_name[talent_id] && selected_talents_name[talent_id][0]) ? selected_talents_name[talent_id][0].firstname + ' ' + selected_talents_name[talent_id][0].lastname : ''"
                    @drop="onDrop($event)" @dragover.prevent="onDragOver('task-' + talent_id + '-' + moment(date).format('Y-MM-DD'))" 
                    @dragenter.prevent="onDragOver('task-' + talent_id + '-' + moment(date).format('Y-MM-DD'))"
                    @dragleave.prevent="onDragLeave('task-' + talent_id + '-' + moment(date).format('Y-MM-DD'))">
                </div>
                <div v-if="planningByTalent[talent_id] && planningByTalent[talent_id].events">
                    <template v-for="event in planningByTalent[talent_id].events">
                        <div v-if="['TASK', 'MISSION EXPLORER'].includes(event.type)" class="event" 
                            :class="(getPlanningClass(date, false) !== 'old' && event.status == 'CLOSED') ? 'task-' + event.id + '-' + event.talent_id + ' task-closed' : 'task-' + event.id + '-' + event.talent_id"
                            v-on:mouseover="activeElement($event, 'task-' + event.id + '-' + event.talent_id, true);" v-on:mouseleave="activeElement($event, 'task-' + event.id + '-' + event.talent_id, false);"
                            v-on:click="contextMenu.show($event,'PLANNING',{ planning: event }, getPlanning, {status: event.status, closedAt: event.closed_at, startDate: event.formattedStartDate, endDate: event.formattedEndDate, projectId: event.project_id, selectedDate: new Date(date), task: event})"
                            :data-drag-date="moment(date).format('Y-MM-DD')" :data-drag-talent-id="event.talent_id" :draggable="draggable" @dragstart="startDrag($event, event)">
                            <span class="dot-button-task" :class="(event.type == 'MISSION EXPLORER') ? 'mission-explorer-dot' : (event.task_type == '3D') ? 'd3' : event.task_type"></span>
                            <p v-if="event.type == 'TASK'" class="task__type" :class="event.task_type">{{ $t(event.task_type) }}</p>
                            <p v-else-if="event.type == 'MISSION EXPLORER'" class="task__type mission-explorer">{{ $t('mission-explorer') }}</p>
                            <p class="task__name">{{ shortProjectName(event.project_name)}}</p>
                            <div v-if="event.type == 'TASK'" :id="'event-actions-' + event.project_id + '-' + event.id" class="event-actions">
                                <span class="icon icon-edit" v-on:click.stop="Global._setAction('SET_TASK', {element: event.project_id, selectedDate: new Date(date), task: event, type: 'EDIT', formType: 'planning'})"></span>
                                <span class="icon icon-delete" v-on:click.stop="Task._delete(event.id, event.project)"></span>
                            </div>
                        </div>
                        <div v-else-if="event.type == 'APPOINTMENT'" class="event appointment"
                            v-on:mouseover="hoverElement('event-actions-' + event.id, true)" v-on:mouseleave="hoverElement('event-actions-' + event.id, false)"
                            v-on:contextmenu.prevent="$refs.ContextMenu.show($event, 'APPOINTMENT', {appointment: event}, getPlanning)">
                            <span class="dot-button-task"></span>
                            <p class="task__type"><span class="color">RDV</span> - {{ shortProjectName(event.title) }}</p>
                            <p class="task__name">{{ event.job }}</p>
                            <div :id="'event-actions-' + event.id" class="event-actions appointment">
                                <span class="icon icon-edit" v-on:click="Appointment._edit(event)"></span>
                                <span class="icon icon-delete" v-on:click="Appointment._delete(event)"></span>
                            </div>
                            <div class="appointment-hours">
                                {{ getAppointmentHours(event) }}
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
<!-- ./Planning -->
    </div>
</div>
</template>

<script>
    import { VueSelecto } from "vue-selecto";
    export default {
        props: ["user", "_workspace"],
        components: {
                VueSelecto,
        },
        data() {
            let date = new Date();
            return {
                workspace: this._workspace,
                planning: {},
                selected_talents_id: [],
                full_talents_id: [],
                selected_talents_name: [],
                full_talents_name: [],
                selected_task_types_id: [],
                filters: {
                    talents_id: [],
                    projects_id: null,
                    project_name: "",
                    task_types_id: []
                },
                date_month: date.getMonth() + 1,
                date_year: date.getFullYear(),
                today: moment().format("YYYY-MM-DD"),
                counter_slide: 0,
                translation: 0,
                cell_width: 250,
                planning_update: false,
                groups: [],
                group: false,
                type: 'DAYS',
                contextMenu: false,
                isScrolled: true,
                draggable: true,
                sliceStart: 0,
                sliceEnd: 4,
                selecto: true,
                body: document.querySelector('body'),
                calendarContainer: document.querySelector('.planning-container'),
                disableSelecto: true,
                selectableTargets: [".task-col"],
                selectToOnSelect: false,
            };
        },

        beforeMount() {},

        mounted() {
            this.contextMenu = this.$parent.$refs.ContextMenu;

            this.$bus.$on("TASK_ADD_OR_UPDATE", () => {
                this.getPlanning();
            });

            this.$bus.$on('GET_GROUP', (data) => {
                this.group = data;
                this.$children[0].myPlanning = false;
            })

            this.$bus.$on("PLANNING_DAYS_UPDATE", (data) => {
                if (data && data.talents) {
                    this.filters.talents_id = data.talents;
                }
                this.planning_update = true;
                this.getPlanning();
            });

            this.$bus.$on('GROUP_UPDATE', () => {
                this.getGroups();
                this.getPlanning();
            });

            this.$bus.$on("APPOINTMENT_ADD_OR_UPDATE", () => {
                this.getPlanning();
            });

            this.$bus.$on("PLANNING_DAYS_IS_SCROLLED", () => {
                this.isScrolled = true;
            });

            this.isMounted = true;

            this.getGroups();
        },

        computed: {},

        methods: {
            async getPlanning() {
                this.selected_talents_id = this.filters.talents_id.sort();
                this.full_talents_id = this.filters.talents_id.sort();
                if (this.selected_talents_id.length > 4) {
                    this.selected_talents_id = this.selected_talents_id.slice(this.sliceStart, this.sliceEnd);
                }
                this.selected_task_types_id = this.filters.task_types_id;
                this.$bus.$on('PLANNING_TODAY', () => {
                    let date = new Date();
                    this.date_month = date.getMonth() + 1,
                    this.date_year = date.getFullYear();
                    this.isScrolled = true;
                });
                if (this.filters.talents_id.length > 0) {
                    await axios.get("/api/planning", {
                        params: {
                            workspace: this._workspace,
                            filter_talents_id: this.filters.talents_id,
                            filter_projects_id: this.filters.projects_id,
                            filter_task_types_id: this.filters.task_types_id,
                            date_month: this.date_month,
                            date_year: this.date_year,
                            type: 'PLANNING'
                        }
                    })
                    .then(res => {
                        if (res.success === false) {
                            // Todo : Error
                        } else {
                            let datas = res.data.datas;                            
                            this.planning = datas.planning; // Update project task list
                            this.selected_talents_name = datas.talent_name; // Talent name list
                            this.full_talents_name = datas.talent_name; // Talent name list
                            this.selected_task_types_name = datas.task_name; // Task name class

                            //this.slicePlanning();
                            document.getElementById('loaderDays').style.display='none';
                        }
                    })
                    .catch(error => console.log(error));
                }
            },
            /**
             * Handle date change.
             */
            handleDateChange(type) {
                switch (type) {
                    case "UP":
                        this.date_month++;
                        if (this.date_month > 12) {
                            this.date_year++;
                            this.date_month = 1;
                        }
                        break;
                    case "DOWN":
                        this.date_month--;
                        if (this.date_month < 1) {
                            this.date_year--;
                            this.date_month = 12;
                        }
                        break;
                }
                this.isScrolled = true;

                this.getPlanning();
            },

            handleSlide(type) {
                var rightPos =
                    $(".js-planning-header-wrapper").offset().left +
                    $(".js-planning-header-wrapper").outerWidth();
                var rowRightPos =
                    $(".js-planning-row-talents").offset().left +
                    $(".js-planning-row-talents").outerWidth();

                switch (type) {
                    case "PREV":
                        if (this.counter_slide > 0) {
                            this.counter_slide--;
                            this.translation = this.translation + this.cell_width;
                        }
                        break;
                    case "NEXT":
                        if (rowRightPos > rightPos) {
                            this.counter_slide++;
                            this.translation = this.translation - this.cell_width;
                        }
                        break;
                }

                $(".js-planning-row")
                    .stop()
                    .css({
                        transform: "translate(" + this.translation + "px, 0)"
                    });
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
            editGroup(talentId) {
                let currentTalentIds = this.filters.talents_id;
                let talentIdToDelete = false;
                if (currentTalentIds.indexOf(talentId) !== -1) {
                    talentIdToDelete = currentTalentIds.indexOf(talentId);
                    currentTalentIds.splice(talentIdToDelete, 1);
                    this.group.talents = currentTalentIds;
                    axios.post("/api/group", { group: this.group }).then(res => {
                    if(res.success === false) {
                        // Error
                    } else {
                        let type = (!this.group.id) ? 'GROUP_ADD' : 'GROUP_EDIT';
                        let group = res.data.datas;
                        group.talents = group.talents.map(x => x.id);
                        if (group.talents.length > 1) {
                            this.$bus.$emit('PLANNING_DAYS_UPDATE', group);
                            this.$parent.type = 'DAYS';
                        } else {
                            this.$bus.$emit('PLANNING_CALENDAR_UPDATE', [
                                group.talents[0]
                            ]);
                            this.$parent.type = 'MONTH';
                        }
                        this.$bus.$emit('GROUP_UPDATE');
                        this.$bus.$emit("ACTION_CHANGED", {
                            action: "CONGRATS",
                            type: type,
                        });
                    }
                    }).catch(error => console.log(error));
                }
            },
            setPlanningType(type, talents) {
                let planningUpdate = "PLANNING_DAYS_UPDATE";
                this.$parent.type = type;
                talents = talents.map(x => x.id);
                this.$bus.$emit(planningUpdate, {
                    talents: talents
                });
            },
            hoverElement(elementId, isHover) {
                let element = document.getElementById(elementId);
                if (element) {
                    if (isHover || this.selectToOnSelect) {
                        element.style.opacity = 1;
                    } else {
                        element.style.opacity = 0;
                    }
                }
            },
            getAppointmentHours(appointment) {
                let appointmentDate = new Date(appointment.datetime);
                if (appointmentDate) {
                    let hours = appointmentDate.getHours() < 10 ? '0' + appointmentDate.getHours() : appointmentDate.getHours();
                    let minutes = appointmentDate.getMinutes() < 10 ? '0' + appointmentDate.getMinutes() :  appointmentDate.getMinutes();
                    if (hours && minutes) {
                        return hours + ':' + minutes;
                    }
                }
                return '';
            },
            isOld(date) {
                let today = new Date();
                let todayDay = today.getDate();
                let todayMonth = today.getMonth();
                let planningDate = new Date(date);
                let planningDateDay = planningDate.getDate();
                let planningDateMonth = planningDate.getMonth();
                if ((planningDateDay == todayDay) && (todayMonth == planningDateMonth)) {
                    return false;
                } else if (planningDate < today) {
                    return true;
                } else {
                    return false;
                }
            },
            getPlanningClass(date, isRef)
            {   
                let classToAdd = '';
                let today = new Date();
                let todayDay = today.getDate();
                let todayMonth = today.getMonth();
                let planningDate = new Date(date);
                let planningDateDay = planningDate.getDate();
                let planningDateMonth = planningDate.getMonth();

                planningDate = planningDate.setHours(0,0,0);
                if (planningDateDay % 2 == 0 && !isRef) {
                    classToAdd = classToAdd + 'bg'
                }
                if ((planningDateDay == todayDay) && (todayMonth == planningDateMonth)) {
                    classToAdd = classToAdd + ' today';
                } else if (planningDate < today) {
                    classToAdd = classToAdd + ' old';
                } else {
                    classToAdd = classToAdd + ' next';
                }

                if (isRef) {
                    classToAdd = classToAdd.trim();
                }

                return classToAdd;
            },
            startDrag(evt, item) {
                // taskId
                let taskID = item.id ? item.id: false;
                // projectId
                let projectID = item.project_id ? item.project_id : false;
                // startdragdate
                let startDragDate = (evt.target && evt.target.attributes[0]) ? evt.target.attributes[0].value : false;
                // task start date
                let startDate = item.start_date ? moment(item.start_date).format('Y-MM-DD') : false;
                // task end date
                let endDate = item.end_date ? moment(item.end_date).format('Y-MM-DD') : false;
                // drag talent id
                let dragTalentId = item.talent_id ? item.talent_id : false;
                evt.dataTransfer.setData('taskID', taskID)
                evt.dataTransfer.setData('projectID', projectID)
                evt.dataTransfer.setData('end', endDate);
                evt.dataTransfer.setData('start', startDate);
                evt.dataTransfer.setData('startDragDate', startDragDate);
                evt.dataTransfer.setData('dragTalentId', dragTalentId);
                this.selecto = false;
                let droppable = document.getElementsByClassName('calendar-drop');
                if (droppable && droppable.length > 0) {
                    for (let i = 0; i < droppable.length; i++) {
                        let drop = droppable[i];
                        drop.style.zIndex = 1;
                    }
                }
                let calendarDrop = document.getElementById('calendar-drop-' + dragTalentId + '-' + startDragDate);
                if (calendarDrop) {
                    calendarDrop.style.zIndex = -1;
                }
            },
            onDrop(evt) {
                let droppable = document.getElementsByClassName('calendar-drop');
                if (droppable && droppable.length > 0) {
                    for (let i = 0; i < droppable.length; i++) {
                        let drop = droppable[i];
                        drop.style.zIndex = -1;
                    }
                }
                const taskID = evt.dataTransfer.getData('taskID');
                const projectID = evt.dataTransfer.getData('projectID');
                let start = evt.dataTransfer.getData('start');
                let end = evt.dataTransfer.getData('end');
                let startDragDate = evt.dataTransfer.getData('startDragDate');
                let dragTalentId = evt.dataTransfer.getData('dragTalentId');
                let attributes = evt.target.attributes;
                let dropDate = (attributes && attributes[1] && attributes[1].name == "data-drop-date") ? attributes[1].value : false;
                let talentId = (attributes && attributes[2] && attributes[2].name == "data-drop-talent-id") ? attributes[2].value : false;
                let changeTalentId = (talentId != dragTalentId) ? talentId : false;
                if (dropDate && taskID && taskID > 0) {
                    axios.put('/api/task/date/' + taskID, {
                        start_date: start,
                        end_date: end,
                        from_date: startDragDate,
                        to_date: dropDate,
                        projectId: projectID,
                        talentId: changeTalentId
                    })
                    .then(res => {
                        if (res === false) {
                                // Todo : Error
                        } else {
                            this.$bus.$emit('TASK_ADD_OR_UPDATE');
                            this.onDragLeaveRemoveAll();
                            this.$bus.$emit("ACTION_CHANGED", {action: "CONGRATS", type: 'TASK_MOVED'});
                            this.removeTaskHover();
                            this.selectToOnSelect = false;
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
            },
            onDragLeaveRemoveAll()
            {
                let onDrop = document.getElementsByClassName('onDrop');
                for (let i = 0; i < onDrop.length; i++) {
                    if (onDrop[i]) {
                        onDrop[i].classList.remove('onDrop');
                    }
                }
            },
            scrollTo() {
                if (this.isScrolled && typeof this.$refs.today != "undefined" && this.$refs.today.length > 0) {
                    this.$refs.today[0].scrollIntoView({behavior: 'smooth', block: 'center'});
                    this.isScrolled = false;
                }
            },
            calendarSlide(type) {
                if (type == 'UP') {
                    this.sliceStart = this.sliceStart + 1;
                    this.sliceEnd = this.sliceEnd + 1;
                } else if (type == 'DOWN') {
                    this.sliceStart = this.sliceStart - 1;
                    this.sliceEnd = this.sliceEnd - 1;
                    if (this.sliceStart < 0) {
                        this.sliceStart = 0;
                    }
                    if (this.sliceEnd < 0) {
                        this.sliceEnd = 0;
                    }
                }

                if (this.sliceEnd > this.filters.talents_id.length) {
                    this.sliceEnd = this.filters.talents_id.length;
                    this.sliceStart = this.filters.talents_id.length - 4;
                }

                if (this.sliceEnd < 4) {
                    this.sliceEnd = 4;
                    this.sliceStart = 0;
                }

                this.slicePlanning();
            },
            slicePlanning() {
                this.selected_talents_id = this.full_talents_id.slice(this.sliceStart, this.sliceEnd);
            },
            onSelect(e) {
                this.removeTaskHover();

                e.selected.forEach(function(task) {
                    task.children[0].style.opacity = 1;
                })
                
                this.selectToOnSelect = true;
                this.selectedDateTask = e.selected;
            },
            activeElement(e, eventClassName, active) {
                let events = document.getElementsByClassName(eventClassName);
                if (events) {
                    for (let i = 0; i < events.length; i++) {
                        if (events[i] && !events[i].classList.contains('hover') && active) {
                            events[i].classList.add('hover');
                            if (e.target.getElementsByClassName('event-actions')[0]) {
                                e.target.getElementsByClassName('event-actions')[0].style.opacity = 1;
                            }
                        } else {
                            events[i].classList.remove('hover');
                            if (e.target.getElementsByClassName('event-actions')[0]) {
                                e.target.getElementsByClassName('event-actions')[0].style.opacity = 0;
                            }
                        }
                    }
                }
            },
            onSelectEnd(e) {
                let addTasks = [];
                let talent = false;
                let selectedDate = false;
                if (this.selectedDateTask) {
                    this.selectedDateTask.forEach(function(selected) {
                        if (selected.attributes[2] && selected.attributes[2].nodeName == 'data-drop-talent-id' && selected.attributes[3] && selected.attributes[3].nodeName == 'data-drop-talent-name') {
                            talent = {talent_id: selected.attributes[2].value, talent_name: selected.attributes[3].value};
                            addTasks.push(talent);
                        }
                    });
                    const ids = addTasks.map(o => o.talent_id)
                    addTasks = addTasks.filter(({talent_id}, index) => !ids.includes(talent_id, index + 1));

                    if (this.selectedDateTask && this.selectedDateTask.length > 1 && typeof this.selectedDateTask[0] != undefined && this.selectedDateTask[this.selectedDateTask.length - 1] != undefined) {
                        let start = (this.selectedDateTask[0].attributes[1] && this.selectedDateTask[0].attributes[1].nodeName == 'data-drop-date') ? this.selectedDateTask[0].attributes[1].value : false;
                        let end = (this.selectedDateTask[this.selectedDateTask.length - 1].attributes[1] && this.selectedDateTask[this.selectedDateTask.length - 1].attributes[1].nodeName) ? this.selectedDateTask[this.selectedDateTask.length - 1].attributes[1].value : false;
                        let startCompare = new Date(start).setHours(23, 59, 59);
                        start = new Date(start);
                        end = new Date(end);
                        let today = new Date().setHours(0, 0, 0);
                        /*if (startCompare < today) {
                            start = new Date();
                            end = new Date();
                        }*/
                        selectedDate = {start: start, end: end};
                    }

                    if (selectedDate && addTasks.length > 0) {
                        Vue.prototype.Global._setAction('SET_TASK', {selectedDateTask: selectedDate, selectedDate: 'NULL', type: 'ADD', formType: 'task', selectToTalents: addTasks})
                    }
                }
                this.selectedDateTask = [];
                this.removeTaskHover();
                this.selectToOnSelect = false;
            },
            removeTaskHover() {
                document.querySelectorAll('.add-task').forEach(function(el, key) {
                    el.style.opacity = 0;
                })
            },
            ucfirst(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            },
            shortProjectName(projectName) {
                if (projectName) {
                    return projectName.length > 15 ? projectName.substring(0, 15) + "..." : projectName;
                }

                return '';
            },
            goToUserPlanning(selectedTalent) {
                if (selectedTalent && this.$children && this.$children[0]) {
                    this.$children[0].setPlanningType('MONTH', [selectedTalent.id, selectedTalent.firstname, selectedTalent.lastname]);
                    this.$bus.$emit('USER_PLANNING_TAB', {id: selectedTalent.id, firstname: selectedTalent.firstname, lastname: selectedTalent.lastname});
                    this.$parent.activeGroupId = false;
                    this.$parent.currentGroup = false;
                }
                
            }
        }
    };
</script>