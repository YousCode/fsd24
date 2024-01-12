<template>
    <div class="popup-wrapper" id="create-task" v-on:click.stop>
        <div class="popup-intro text-center mb-30">
            <div class="go-back" v-on:click="goBackNew" v-if="project.name && step > 1 && projectChoice === 'new'">
                <svg width="26px" height="26px" viewBox="0 -1 16 34" class="vc-svg-icon">
                    <path data-v-63f7b5ec="" d="M11.196 10c0 0.143-0.071 0.304-0.179 0.411l-7.018 7.018 7.018 7.018c0.107 0.107 0.179 0.268 0.179 0.411s-0.071 0.304-0.179 0.411l-0.893 0.893c-0.107 0.107-0.268 0.179-0.411 0.179s-0.304-0.071-0.411-0.179l-8.321-8.321c-0.107-0.107-0.179-0.268-0.179-0.411s0.071-0.304 0.179-0.411l8.321-8.321c0.107-0.107 0.268-0.179 0.411-0.179s0.304 0.071 0.411 0.179l0.893 0.893c0.107 0.107 0.179 0.25 0.179 0.411z"></path>
                </svg>
                Précédent
            </div>
            <h2 class="popup-maintitle" v-if="formType === 'task'" v-show="step === 1">
                <span>Créer une ou plusieurs tâches</span>
            </h2>
            <h2 class="popup-maintitle" v-if="formType === 'project'">
                <span v-if="!selectedProject">Créer votre projet</span>
                <span v-if="selectedProject">{{ selectedProject.name }}</span>
            </h2>
            <h2 class="popup-maintitle" v-else-if="type == 'EDIT'">
                Modifier la tâche
            </h2>
        </div>
        <div data-url="" class="form__task" rel="form__task">
            <hr class="grey-separator" />
            <div class="popup-first-choice" v-if="!selectedProject">
                <div class="popup-first-choice-item" v-on:click="toggleProjectChoice('exists')" ref="exists-task" v-show="formType === 'task'" v-bind:class="{ selected: projectChoice === 'exists' }" v-if="formType === 'task'">
                    <p>Pour un projet<br />existant</p>
                </div>
                <div class="popup-first-choice-item" v-if="formType === 'project'" v-show="(formType === 'project', toggleProjectChoice('new'))" :class="{ selected: projectChoice === 'new' }">
                    <p>Pour un nouveau<br />projet</p>
                </div>
                <div class="popup-first-choice-item" v-if="formType === 'task'" v-on:click="toggleProjectChoice('new')" v-bind:class="{ selected: projectChoice === 'new' }">
                    <p>Pour un nouveau<br />projet</p>
                </div>
            </div>
            <div class="find-project" v-show="projectChoice === 'exists' || (selectedProject !== null && !project.name)">
                <div class="form-box text-left mb-0">
                    <div class="d-flex">
                        <div class="form-field-wrapper">
                            <span class="icon icon-search" :style="{color: this.selectedProject && this.selectedProject.id !== null ? 'white' : ''}"></span>
                            <select v-select2 class="filter-field js-project-data" name="project"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="find-project" v-show="project.name && step > 1 && projectChoice === 'new'">
                <div class="form-box text-left mb-0">
                    <div class="d-flex">
                        <div class="form-field-wrapper">
                            <span class="icon icon-search" :style="{color: this.selectedProject && this.selectedProject.id !== null ? 'white' : ''}"></span>
                            <select disabled="disabled" v-model="project.id" v-select2 class="filter-field js-fakeproject-data" name="fakeproject">
                                <option selected="selected" value="default">
                                    {{ project.name || "default" }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="create-project-wrapper"
                v-show="projectChoice === 'new' && selectedProject === null">
                <div class="create-project">
                    <div class="create-project-field">  <div class="create-project-field badges-category">
                        <label>
                            <i class="icon icon-project"></i>Nom de votre projet vidéo*</label>
                        <input class="form-field js-required" required v-model="project.name" type="text" placeholder="Exemple: Spot TV DIOR" />
                        <label><i class="icon icon-player"></i>Catégorie*</label>
                        <button class="badge" :class="{ selected: project.category_id === categorie.id}" v-for="categorie in projectCategories" v-if="categorie.name !== 'Mission Explorer'" v-on:click="setProjectCategorie(categorie.id)">
                            {{ categorie.name }}
                        </button>
                    </div>
                    </div>

                    <div class="create-project-field">
                        <label><i class="icon icon-calendar"></i>Deadline</label>
                        <v-date-picker v-model="project.date_deadline" color="kpurple" is-dark>
                            <template v-slot="{ inputValue, inputEvents }">
                                <input class="form-field js-required" :value="inputValue" v-on="inputEvents" />
                            </template>
                        </v-date-picker>
                    </div>
                </div>
            </div>

            <div class="add-task-field is-large row" id="task--items" v-show="step == 2">
                <div class="col-12 col-xl-8 task__list">
                    <h3 class="col-12">Liste des tâches</h3>
                    <multiselect
                        @select="talentSelected"
                        @remove="talentRemoved"
                        :internal-search="false"
                        v-model="talents_values"
                        :options="talents"
                        :multiple="true"
                        :close-on-select="false"
                        :clear-on-select="true"
                        placeholder="Assigner une nouvelle tâche"
                        label="name"
                        track-by="id"
                        selectLabel=""
                        deselectLabel=""
                        class="notag js-talent-multiple wrapper-relative add-talent-button"
                    >
                        <template slot="selection" slot-scope="{ values, search, isOpen }">
                            <span v-if="values.length > 0" class="multiselect__placeholder">Assigner une nouvelle tâche à</span>
                        </template>
                        <template slot="option" slot-scope="props">
                            <div class="option__content">
                                <span class="option__name">{{ props.option.name }}</span>
                            </div>
                        </template>
                    </multiselect>
                    <div class="form__talents_task">
                        <div
                            v-for="(task, taskKey) in tasks"
                            v-if="tasks"
                            :key="task.index"
                            class="talent_task"
                            :id="'talent_task-' + task.index"
                            :data-task="task.index"
                            :style="type == 'EDIT' && task.id == taskSelected.id ? { backgroundColor: 'rgb(81, 31, 224)' } : {}"
                            @click="selectTask(task)"
                            >
                            <p class="task__talent" v-if="type == 'ADD'">
                                <i class="icon icon-plus add-icon"
                                    v-on:click="addTask({ talent_id: task.talent_id, talent_name: task.talent_name, task_type_id: task.task_type_id, task_type_name: task.task_type_name, task: task, duplicate: true}); updatePreviewCalendar();">
                                </i>
                                    {{ task.talent_name }}
                            </p>
                            <p class="task__talent" v-else-if="type == 'EDIT'">
                                <select
                                    @change="updatePreviewCalendar($event, task.index)"
                                    v-model="task.talent_id"
                                    v-select2
                                    :data-key="task.index"
                                    :id="task.id ? 'task-form-user-edit-' + task.id : 'new-task-form-user-' + task.index"
                                    class="form-field js-task-user-data"
                                    required
                                ></select>
                            </p>
                            <div class="task__type" @click="closeNotes(); closeDatePickers();">
                                <select
                                    @change="updatePreviewCalendar($event, task.index)"
                                    v-model="task.task_type_id"
                                    v-select2
                                    :data-key="task.index"
                                    :id="task.id ? 'task-form-edit-' + task.id : 'new-task-form-' + task.index"
                                    class="form-field js-task-type-data"
                                    required
                                ></select>
                            </div>
                            <div class="task__date" @click="closeNotes();">
                                <v-date-picker
                                    @input="updatePreviewCalendar"
                                    mode="date"
                                    is-range
                                    :masks="masks"
                                    :popover="{
                                        visibility: 'focus',
                                        placement: $screens({
                                            default: calendarPosition(tasks, taskKey),
                                        }),
                                    }"
                                    v-model="task.dates"
                                    color="kpurple"
                                    is-dark
                                    :columns="$screens({ default: 2 })"
                                    ref="datepicker"
                                >
                                    <template v-slot="{ inputValue, inputEvents }">
                                        <input :value="convertDatePickerToString(task.start_date, task.end_date)" v-on="inputEvents.start" />
                                    </template>
                                </v-date-picker>
                            </div>
                            <div class="task__notes">
                                <span class="task__notes--placeholder" v-on:click="showNote(task, taskKey); closeDatePicker();">
                                    <span
                                        v-if="task.notes && task.notes.length > 0">
                                        {{ task.notes.slice(0, task.notes.length).map((a) => a.note && a.note.length > 10 ? a.note.slice(0, 10) + "..." : a.note).join(" | ") }}
                                    </span>
                                    <span v-else>Ajouter une note</span>
                                </span>
                                <div class="task__notes--content" v-show="task.noteShow">
                                    <div class="task__notes__body">
                                        <ul>
                                            <li>
                                                <input type="text" v-model="task.notePreview" v-on:keyup.enter="addNoteToTask(task)" placeholder="Ajouter une note ici ..."/>
                                            </li>
                                            <li v-for="(n, i) in task.notes">
                                                <input type="text" v-model="task.notes[i].note" data-ti="task.index" @change="checkTask" />
                                                <span class="icon icon-cross" v-on:click="deleteNote(taskKey, i)" style="position: absolute; right: 10px; top: 15px;"></span>
                                            </li>
                                            <li class="example" v-if="task.notes.length < 4"></li>
                                            <li class="example" v-if="task.notes.length < 3"></li>
                                            <li class="example" v-if="task.notes.length < 2"></li>
                                            <li class="example" v-if="task.notes.length < 1"></li>
                                        </ul>
                                    </div>
                                    <div class="task__notes__footer">
                                        <button class="task__notes__button--close" v-on:click="showNote(task, taskKey); addNoteToTask(task)">
                                            <i class="icon icon-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button v-show="type == 'ADD'" type="button" class="task__remove" v-on:click="removeTaskByIndex(task.index)">
                                <span class="icon icon-cross"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-4 calendar__view">
                    <h3>Calendrier</h3>
                    <v-calendar class="calendar-preview" :attributes="calendarPreview.attributes" :masks="calendarPreview.masks" :to-page="calendarDefaultPage" ref="calendarPreview" is-expanded>
                        <template v-slot:day-content="{ day, attributes }">
                            <div class="calendar-preview__card">
                                <span class="calendar-preview__day">{{ day.day }}
                                    <span v-if="isToday(day.date)" class="pastille-today"></span>
                                </span>
                                <div class="calendar-preview__tasks">
                                    <p v-for="attr in attributes" :key="attr.key" :class="attr.customData.class == '3D' ? 'd3 ' + 'task-preview task-preview-' + attr.key  : attr.customData.class + ' ' + 'task-preview task-preview-' + attr.key" style="padding-left: 15px;" :style="(attr.key == taskSelected.index) ? { backgroundColor: '#511FE0' } : ''">
                                        <i class="dot-button-task" :class="attr.customData.class == '3D' ? 'd3' : attr.customData.class" v-if="attr.key !== 99999"></i>
                                        {{ attr.customData.title.length > 15 ? attr.customData.title.substring(0, 5) + "..." + attr.customData.title.substring(attr.customData.title.length - 3, attr.customData.title.length) : attr.customData.title }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </v-calendar>
                </div>
            </div>

            <div class="form__buttons">
                <button type="button" v-on:click="close()" class="close_popup">
                    Quitter
                </button>
                <button type="button" v-on:click="createTasks" v-if="step == 2" class="confirm_tasks selected">
                    Valider
                </button>
                <button type="button" v-on:click="fakeAddProject()" v-if="projectChoice === 'new' && step < 2" class="confirm_tasks selected">
                    Suivant
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";

export default {
    props: [
        "_workspace",
        "_project",
        "_goback",
        "_selectedDate",
        "_task",
        "_tasks",
        "type",
        "_selectedDateTask",
        "formType",
        "_selectToTalents",
        "_projectId"
    ],
    components: {
        Multiselect,
    },
    data() {
        return {
            calendarDefaultPage: {
                year: new Date().getFullYear(),
                month: new Date().getMonth() + 1,
            },
            cntIndex: 0,
            project: {
                id: null,
                workspace_id: this._workspace,
                name: null,
                production: "",
                client: "",
                client_name: this.$t('to-fill'),
                client_phone: "",
                client_email: "",
                responsable_name: "",
                responsable_phone: this.$t('to-fill'),
                responsable_email: this.$t('to-fill'),
                category_id: "",
                date_deadline: null,
                talents: [],
                talents_id: [],
            },
            projectCategories: [],
            step: 1,
            searchProject: false,
            createProject: false,
            projectChoice: "",
            selectedProject: null,
            taskSelected: false,
            tasks: [],
            talents: [],
            talents_values: [],
            masks: {
                input: ["L", "YYYY-MM-DD", "YYYY/MM/DD"],
            },
            calendarPreview: {
                masks: {
                    weekdays: "WWW",
                },
                attributes: [],
            },
            task_default: {
                id: null,
                name: "",
                talent_id: null,
                talent_name: "",
                task_type_id: null,
                task_type_name: "",
                notes: [],
                dates: {
                    start: new Date(),
                    end: new Date(),
                },
                index: 0,
                notePreview: "",
                noteShow: false,
                createdBy: "",
            },
            attributes_colors: ["#37ffdb", "#b78bff", "#35da45"],
            taskTypes: [],
            selectToTalents: this._selectToTalents,
            projectId: this._projectId,
            selectedDate: this._selectedDate,
            projectLoaded : false,
            taskTypeClass : {},
            openDatepicker: [],
        };
    },
    created() {
        if (this._project) {
            this.selectedProject = this._project;
            this.project = this._project;
            this.step = 2;
        }
    },

    mounted() {
        if (this.projectId) {
            this.getProject(this.projectId);
            this.step = 2;
        }
        if (this.type == "EDIT") {
            this.selectedDate = null;
                if (this.selectedProject == null) {
                    this.selectedProject = this.project;
                }
                let tasksDate = [];
                let tasks = this._tasks;
                if (!tasks) {
                    if (this.projectId == this.project.id) {
                        tasks = this.project.task;
                    }
                }
                if (tasks) {
                    let index = 0;
                    let $this = this;
                    tasks.forEach(function (task, key) {
                        let talent = task.talent_id
                            ? task.talent_id[0]
                            : task.talentId;
                        //task.tasks.forEach(function (taskDate, taskKey) {
                            //task.push({
                                //id: taskDate.id,
                                task.id = task.id;
                                task.task_id = task.id;
                                task.index = index++;
                                task.talent_id = talent && talent[0] && talent[0]['user_id'] ? talent[0]['user_id'] : false;
                                task.talent_name = task.talentName;
                                task.task_type_id = task.taskTypes[0]
                                    ? task.taskTypes[0].id
                                    : false;
                                task.task_type_name = task.taskTypes[0]
                                    ? task.taskTypes[0].name
                                    : false;
                                let taskStartDate = task.start_date;
                                let taskEndDate = task.end_date;
                                if (Number.isNaN(new Date(taskStartDate).getTime())) {
                                    taskStartDate = moment(taskStartDate).format('YYYY/MM/DD');
                                }
                                if (Number.isNaN(new Date(taskEndDate).getTime())) {
                                    taskEndDate = moment(taskEndDate).format('YYYY/MM/DD');
                                }
                                task.dates = {
                                    //start: new Date(taskDate.start_date),
                                    //end: new Date(taskDate.end_date),
                                    start: new Date(taskStartDate),
                                    end: new Date(taskEndDate),
                                };
                                task.noteShow = false;
                                task.notes = task.note
                                    ? JSON.parse(task.note)
                                    : [];
                                /*notes: taskDate.note
                                ? JSON.parse(taskDate.note)
                                : [],*/
                                task.notePreview = "";
                                $this.addTask(task);
                            //});
                        //});
                    });
                    if (this._task) {
                        this.taskSelected = this._task;
                        let selectedTaskIndex = 0;
                        this.tasks.map((e) => {
                            if ($this.taskSelected.id == e.id) {
                                selectedTaskIndex = e.index;
                            }
                        });
                        this.taskSelected.index = selectedTaskIndex;
                        this.taskSelected.task_type_id = this.taskSelected.taskTypes[0] ? this.taskSelected.taskTypes[0].id : false;
                        this.taskSelected.task_type_name = this.taskSelected.taskTypes[0] ? this.taskSelected.taskTypes[0].name : false;
                    }
                    //this.tasks = tasksDate;
                    this.setTaskTypeSelect();
                    this.updatePreviewCalendar();
                }
        } else if (this.type == "ADD") {
            this.tasks = [];
            this.taskSelected = false;
            this.setTaskTypeSelect();
        }
        if (this.selectToTalents && this.type == "ADD") {
            const $this = this;
            this.selectToTalents.forEach(function (talent) {
                $this.addTask({
                    talent_id: talent.talent_id,
                    talent_name: talent.talent_name,
                });
            });
        }

        this.setProjectSelect();
        this.getTalents();
        this.getProjectCategories();
        this.getTaskTypes();
        this.removeDotsOnWeekDays();
        if (this.formType == "task") {
            this.toggleProjectChoice("exists");
        }
        this.calendarRef = this.$refs.calendarPreview;
        const _this = this;
        $(() => {
            $(".popup-overlay, .popup-content").on("click", function (e) {
                var container = $(".task__notes");
                if (
                    !container.is(e.target) &&
                    container.has(e.target).length === 0
                ) {
                    if (_this.tasks.length > 0) {
                        _this.tasks.forEach(function (task) {
                            const el_inputs = $(
                                '[data-task="' +
                                    task.index +
                                    '"] .task__notes input'
                            );
                            el_inputs.each(function (index, el) {
                                const val = $(el).val();
                                if (
                                    !task.notes.includes(val) &&
                                    val.length > 0
                                ) {
                                    task.notes.push(val);
                                    task.notePreview = "";
                                }
                            });
                            //task.noteShow = false;
                        });
                    }
                }
            });

            $(document).on("click", ".talent_task", function (e) {
                $(".talent_task").removeClass("selected");
                $(this).addClass("selected");
                _this.updatePreviewCalendar();
            });
        });
        //const calendar = this.$refs.calendarPreview;
    },

    methods: {
        checkTask: function (event) {
            if (event.target.value.length === 0) {
                for (const i = 0; i < this.tasks.length; i++) {
                    this.tasks[i].notes = this.tasks[i].notes.filter(
                        (e) => e.length > 0
                    );
                }
            }
        },
        removeDotsOnWeekDays: function () {
            $(() => {
                $(".vc-weekday").each(function () {
                    $(this).text($(this).text().replace(/\./g, ""));
                });
            });
        },
        isToday(date) {
            const today = new Date();
            today.setHours(0);
            today.setMinutes(0);
            today.setSeconds(0);
            today.setMilliseconds(0);
            return today.getTime() === date.getTime();
        },
        convertDatePickerToString(start, end) {
            if (
                start &&
                end &&
                typeof start != undefined &&
                typeof end != undefined &&
                start.length > 0 &&
                end.length > 0
            ) {
                if (Number.isNaN(new Date(start).getTime())) {
                    start = moment(start).format('YYYY/MM/DD');
                }
                if (Number.isNaN(new Date(end).getTime())) {
                    end = moment(end).format('YYYY/MM/DD');
                }
                let dateStart = new Date(start);
                let dateEnd = new Date(end);
                const months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août",  "Septmbre", "Octobre", "Novembre", "Décembre"];
                let dateMonthEnd = dateEnd.getMonth();
                let dateDayStart = dateStart.getDate();
                let dateDayEnd = dateEnd.getDate();
                let month = (months[dateMonthEnd]) ? months[dateMonthEnd] : this.$t('january');
                return (
                    "Du " +
                    dateDayStart +
                    " au " +
                    dateDayEnd +
                    " " +
                    month
                );
            }
        },
        createTasks() {
            Vue.prototype.$bus.$emit("UPDATE_PROJECT_LOAD", {isLoaded: true});
            $(".add-talent-button").removeClass("error");
            $(`[data-task] .select2-selection--single`).removeClass("error");
            if (this.tasks.length < 1) {
                $(".add-talent-button").addClass("error");
                return;
            }
            let errors = 0;
            this.tasks.forEach((e) => {
                if (e.task_type_id === null) {
                    $(
                        `[data-task="${e.index}"] .select2-selection--single`
                    ).addClass("error");
                    console.log(
                        $(`[data-task="${e.index}"] .select2-selection--single`)
                    );
                    console.log(
                        `[data-task="${e.index}"] .select2-selection--single`
                    );
                    errors++;
                }
                if (e.dates) {
                    e.dates.end.setHours(0);
                    if (e.dates.end.getDate() == e.dates.start.getDate()) {
                        e.dates.end.setHours(19);
                    }
                }
            });
            if (errors > 0) {
                return;
            } else {
                this.close();
            }
            const tasksToSend = this.tasks;
            tasksToSend.map((element) => {
                element.date = {
                    start: new moment(element.dates.start).format(
                        "YYYY-MM-DD HH:mm:ss"
                    ),
                    end: new moment(element.dates.end).format(
                        "YYYY-MM-DD HH:mm:ss"
                    ),
                };
                element.note = element.notes && element.notes.length > 0 ? JSON.stringify(element.notes) : null;
                element.createdBy = Vue.prototype.Global.user_id;
                delete element.dates;
                delete element.index;
                delete element.notePreview;
                delete element.noteShow;
                delete element.notes;
                return element;
            });
            if (this.selectedProject.id === this.project.id) {
                const p = this.project;
                p.date_deadline = new moment(p.date_deadline).format(
                    "YYYY-MM-DD"
                );
                axios
                    .post("/api/project", {
                        project: p,
                    })
                    .then((res) => {
                        this.project.url = res.data.datas.url;
                        if (res.data.success === false) {
                            // Error
                            console.log(res);
                        } else {
                            axios
                                .post("/api/project/conversation", {
                                    projectId: res.data.datas.id,
                                })
                                .then((res) => {
                                    if (res.success === false) {
                                        // Error
                                    } else {
                                    }
                                })
                                .catch((error) => console.log(error));

                            axios
                                .post("/api/task", {
                                    project: res.data.datas.id,
                                    tasks: tasksToSend,
                                    edit: this.type == "EDIT" ? true : false,
                                    formType: this.formType,
                                })
                                .then((res) => {
                                    if (res.data.success === false) {
                                        // Error
                                    } else {
                                        var typeAddTask = "ADD_TASK_ONE";
                                        if (tasksToSend.length > 1) {
                                            typeAddTask = "MULTIPLE_ADD_TASK";
                                        }
                                        if (this.type == "EDIT") {
                                            typeAddTask = "EDIT_TASK";
                                            this.$bus.$emit("DASHBOARD_UPDATE_TASK");
                                        }
                                        this.$bus.$emit(
                                            "TASK_ADD_OR_UPDATE",
                                            res.data
                                        ); // Emit add or update talent event
                                        this.$bus.$emit("PLANNING_TO_SCROLL");
                                        this.getTasks(this.project.id);
                                        this.$bus.$emit("ACTION_CHANGED", {
                                            action: "CONGRATS",
                                            type: typeAddTask,
                                        }); // Congrats modal
                                        if (this.type !== "EDIT" && (!this.selectToTalents && (!['days', 'month', 'projects'].includes(this.formType)))){
                                            window.location.href = this.project.url;
                                        }
                                        if (this.formType && this.formType == 'projects' && this.project && this.project.id) {
                                            this.$bus.$emit("UPDATE_PROJECT_STEP", {projectId: Number(this.project.id)});
                                        }
                                        this.getProject(this.project.id, true);
                                    }
                                })
                                .catch((error) => console.log(error));
                        }
                    })
                    .catch((error) => console.log(error));
            } else {
                axios
                    .post("/api/task", {
                        project: this.selectedProject.id,
                        edit: this.type == "EDIT" ? true : false,
                        tasks: tasksToSend,
                    })
                    .then((res) => {

                        if (res.data.success === false) {
                            // Error
                            console.log(res);
                        } else {
                            var typeAddTask = "ADD_TASK_ONE";
                            if (tasksToSend.length > 1) {
                                typeAddTask = "MULTIPLE_ADD_TASK";
                            }
                            if (this.selectedProject && this.selectedProject.id) {
                                this.getTasks(this.selectedProject.id);
                            }
                            if (this.type == "EDIT") {
                                typeAddTask = "EDIT_TASK";
                                this.$bus.$emit("DASHBOARD_UPDATE_TASK");
                            }
                            this.$bus.$emit("TASK_ADD_OR_UPDATE", res.data); // Emit add or update talent event
                            this.$bus.$emit("ACTION_CHANGED", {
                                action: "CONGRATS",
                                type: typeAddTask,
                                element: this.tasksToSend,
                            }); // Congrats modal
                          }
                    })
                    .catch((error) => console.log(error));
            }
        },
        fakeAddProject() {
            var errors =
                Vue.prototype.Global._checkForFields(".create-project");
            if (errors > 0) return false;

            if (this.project.category_id === "") {
                $(
                    '<p class="error-msg js-category-error" style="padding-left: 20px;">Veuillez choisir une catégorie</p>'
                ).insertAfter(".badges-category");
                return false;
            } else {
                $(".js-category-error").remove();
            }

            this.selectedProject = this.project;

            $(
                ".form__task .js-fakeproject-data + .select2-container .select2-selection__rendered"
            ).text(this.project.name);
            this.step = 2;
        },
        setProjectCategorie(id) {
            this.project.category_id = id;
        },
        getProjectCategories() {
            axios
                .get("/api/project-category")
                .then((res) => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        this.projectCategories = res.data.datas.map((e) => {
                            return { id: e.id, name: e.name };
                        });
                    }
                })
                .catch((error) => console.log(error));
        },
        getTaskTypes() {
            axios
                .get("/api/task-type")
                .then((res) => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        const $this = this;
                        if (res.data.datas) {
                        let taskTypes = res.data.datas;
                        for (let i = 0; i < taskTypes.length; i++) {
                            $this.taskTypeClass[taskTypes[i].id] = taskTypes[i].class;
                        }
                        this.taskTypes = taskTypes.map((e) => {
                            return { id: e.id, name: e.name };
                        });
                        }
                    }
                })
                .catch((error) => console.log(error));
        },
        updatePreviewCalendar(event, taskKey) {
            const $this = this;
            if (event) {
                if (typeof event.target !== "undefined" && event.target.options[event.target.options.selectedIndex]) {
                    let task = this.tasks[taskKey];
                    task.task_type_name = event.target.options[event.target.options.selectedIndex].text;
                    task.task_type_value =  event.target.options[event.target.options.selectedIndex].value;
                }
            }
            this.calendarPreview.attributes = [];
            setTimeout(async () => {
                var default_date = this.tasks[0]
                    ? this.tasks[0].dates.start
                    : new Date();

                this.tasks.forEach((task) => {
                    let title = task.task_type_name;
                    let taskTypeClass = $this.taskTypeClass[task.task_type_value];
                    if ($this.type == "EDIT") {
                        title = $this.$t(task.task_type_name);
                        taskTypeClass = task.task_type_name;
                    }
                    if (default_date.getTime() > task.dates.start.getTime()) {
                        default_date = task.dates.start;
                    }
                    this.calendarPreview.attributes.push({
                        key: task.index,
                        customData: {
                            id: task.id,
                            title: title || $this.$t('to-fill'),
                            class: taskTypeClass,
                        },
                        dates: task.dates,
                    });
                    task.start_date = new Date(task.dates.start).toString();
                    task.end_date = new Date(task.dates.end).toString();
                });
                if (this.selectedProject && this.selectedProject.date_deadline) {
                    let selectedProjectDeadline = this.selectedProject.date_deadline;
                    if (Number.isNaN(new Date(selectedProjectDeadline).getTime())) {
                        selectedProjectDeadline = moment(selectedProjectDeadline).format('YYYY/MM/DD');
                    }
                    this.calendarPreview.attributes.push({
                        key: 99999,
                        customData: {
                            title: $this.$t('delivery'),
                            class: "item-livraison",
                        },
                        dates: selectedProjectDeadline,
                    });
                }
                if (default_date !== new Date()) {
                    /* await this.calendarRef.move({
                        year: default_date.getFullYear(),
                        month: default_date.getMonth() + 1
                    }); */
                }
                //console.log(this.calendarPreview.attributes);
            }, 10);
        },
        addNoteToTask(_task) {
            let tasks = this.tasks;
            let idx = tasks.findIndex((task) => task.index === _task.index);
            if (this.type == 'EDIT' && tasks && _task) {
                idx = tasks.indexOf(_task);
            }
            var noteAction = {};
            if (_task.notePreview.trim().length > 0) {
                noteAction["note"] = _task.notePreview;
                noteAction["state"] = false;
                _task.notes.push(noteAction);
                _task.notePreview = "";
            }
        },
        talentSelected(selectedOption, id) {
            this.addTask({
                talent_id: selectedOption.id,
                talent_name: selectedOption.name,
            });
            this.updatePreviewCalendar();
        },
        talentRemoved(removedOption, id) {
            this.removeTaskByTalentId(removedOption.id);
            this.updatePreviewCalendar();
        },
        removeTalentById(talent_id) {
            this.talents = this.talents.filter(
                (talent) => talent.id != talent_id
            );
            this.updatePreviewCalendar();
        },
        removeTaskByTalentId(talentId) {
            this.tasks = this.tasks.filter(
                (task) => task.talent_id != talentId
            );
            this.updatePreviewCalendar();
        },
        removeTaskByIndex(index) {
            const task = this.tasks.find((task) => task.index === index);
            this.tasks = this.tasks.filter((task) => task.index != index);

            const exists = this.tasks.find(
                (t) => t.talent_id == task.talent_id
            );
            if (!exists) {
                this.talents_values = this.talents_values.filter(
                    (talent) => talent.id != task.talent_id
                );
            }
            this.updatePreviewCalendar();
        },
        setTalents() {
            $(() => {
                $(".js-talent-data").select2({
                    dropdownCssClass: "multiple-choices filters-dropdown",
                    placeholder:
                        "Rechercher un ou plusieurs talent(s) pour ajouter une tâche",
                    closeOnSelect: false,
                    type: "select:unselect",
                    debug: true,
                    language: {
                        searching: function () {
                            return "Chargement...";
                        },
                    },
                    ajax: {
                        url: "/api/talent",
                        processResults: function (data) {
                            var data = $.map(data.datas, function (obj) {
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

                $(".js-talent-data").on("select2:select", (e) => {
                    this.addTask({
                        talent_id: e.params.data.id,
                        talent_name: e.params.data.name,
                    });
                    this.updatePreviewCalendar();
                });
                $(".js-talent-data").on("select2:unselect", (e) => {
                    this.removeTaskByTalentId(e.params.data.id);
                    this.removeTalentById(e.params.data.id.toString());
                });
            });
        },
        close() {
            this.$bus.$emit("ACTION_CHANGED", { action: null });
        },
        getTalents() {
            const params = { workspace: this._workspace };

            setTimeout(() => {
                axios
                    .get("/api/talent", {
                        params: params,
                    })
                    .then((res) => {
                        if (res.success === false) {
                            // Todo : Error
                        } else {
                            this.talents = res.data.datas.map((e) => {
                                return { id: e.id, name: e.name };
                            }); // Update talents list
                        }
                    })
                    .catch((error) => console.log(error));
            }, 10);
        },

        addTask(params) {
            const taskAppend = this.task_default;
            if (this._selectedDateTask) {
                taskAppend.dates.start = this._selectedDateTask.start;
                taskAppend.dates.end = this._selectedDateTask.end;
            }
            if (this.selectedDate && this.selectedDate != "NULL") {
                taskAppend.dates.start = this.selectedDate;
                taskAppend.dates.end = this.selectedDate;
            }

            taskAppend.talent_id = params.talent_id || null;
            taskAppend.talent_name = params.talent_name || "";
            if (!params.duplicate) {
                taskAppend.task_type_id = params.task_type_id || null;
                taskAppend.task_type_name = params.task_type_name || "";
            }
            taskAppend.notes = params.notes || [];
            if (params.dates) {
                taskAppend.dates = params.dates;
            }
            taskAppend.notePreview = params.notePreview || "";
            taskAppend.id = params.id || null;
            taskAppend.index = this.cntIndex;
            this.cntIndex++;
            this.tasks.push(Object.assign({}, taskAppend));
            this.setTaskTypeSelect();
                setTimeout(() => {
                    this.setNotes();
                }, 10);
        },

        /**
         * Set TaskType select2
         */
        setTaskTypeSelect() {
            var $this = this;
            var type = this.type;
            if (type == "ADD") {
                $(() => {
                    $(".form__task .js-task-type-data").select2({
                        //dropdownCssClass: "has-search no-search task-dropdown",
                        placeholder: "Choisir une tâche",
                        language: {
                            searching: function () {
                                return "Chargement...";
                            },
                        },
                        ajax: {
                            url: "/api/task-type",
                            processResults: function (data) {
                                var data = $.map(data.datas, function (obj) {
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
                });
            } else if (type == "EDIT") {
                let workspaceId = this._workspace;
                $(() => {
                    $(".form__task .js-task-type-data").select2({
                        //dropdownCssClass: "has-search no-search task-dropdown",
                        placeholder: "Choisir une tâche",
                        language: {
                            searching: function () {
                                return "Chargement...";
                            },
                        },
                        ajax: {
                            url: "/api/task-type",
                            processResults: function (data) {
                                var data = $.map(data.datas, function (obj) {
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
                    this.tasks.forEach(function (task, index) {
                        let taskForm = $("#task-form-edit-" + task.id);
                        let taskUserForm = $("#task-form-user-edit-" + task.id);
                        if (!task.id) {
                            taskUserForm = $("#new-task-form-user-" + index);
                        }
                        taskForm.select2({
                            dropdownCssClass:
                                "has-search no-search task-dropdown",
                            placeholder: task.task_type_name,
                            language: {
                                searching: function () {
                                    return "Chargement...";
                                },
                            },
                            ajax: {
                                url: "/api/task-type",
                                processResults: function (data) {
                                    var data = $.map(
                                        data.datas,
                                        function (obj) {
                                            obj.id = obj.id;
                                            obj.text = obj.name;
                                            return obj;
                                        }
                                    );

                                    return {
                                        results: data,
                                    };
                                },
                            },
                        });

                        taskUserForm.select2({
                            dropdownCssClass:
                                "has-search no-search task-dropdown",
                            placeholder: task.talent_name,
                            language: {
                                searching: function () {
                                    return "Chargement...";
                                },
                            },
                            ajax: {
                                url: "/api/talent?workspace=" + workspaceId,
                                processResults: function (data) {
                                    var data = $.map(
                                        data.datas,
                                        function (obj) {
                                            obj.id = obj.id;
                                            obj.text = obj.name;
                                            return obj;
                                        }
                                    );
                                    return {
                                        results: data,
                                    };
                                },
                            },
                        });

                        let option = new Option(
                            $this.$t(task.task_type_name),
                            task.task_type_id,
                            true,
                            true
                        );

                        let optionName = new Option(
                            task.talent_name,
                            task.talent_id,
                            true,
                            true
                        );

                        taskForm.append(option).trigger("change");
                        taskUserForm.append(optionName).trigger("change");
                    });
                });
            }

            $(".js-task-type-data").on("select2:opening", function (e) {
                /* $(".talent_task").removeClass("selected");
                    $(this)
                        .parents(".talent_task")
                        .addClass("selected"); */

                var key = $(this).attr("data-key");
                const idx = $this.tasks.findIndex((e) => {
                    return e.index === parseInt(key);
                });

                $this.updatePreviewCalendar();
            });

            $(".js-task-type-data").on("select2:select", function (e) {
                var key = $(this).attr("data-key");
                const idx = $this.tasks.findIndex((e) => {
                    return e.index === parseInt(key);
                });
                if (
                    $this.tasks[idx] &&
                    $this.tasks[idx].task_type_name &&
                    typeof $this.tasks[idx].task_type_name !== "undefined"
                ) {
                    $this.tasks[idx].task_type_name = e.params.data.name;
                }
            });
        },

        /**
         * Fill the task type in the select2
         */
        triggerTaskTypeSelect2() {
            var $this = this;
            let taskTypeSelect2 = $(".js-task-type-data");
            taskTypeSelect2.each((index, item)=> {
                if ($this.tasks[index].task_type_id) {
                    let taskType = $this.tasksTypes.find(
                        (item) =>
                            item.task_type_id ===
                            $this.tasks[index].task_type_id
                    );
                    if(taskType)
                    {
                        let option = new Option(
                            taskType.name,
                            $this.tasks[index].task_type_id,
                            true,
                            true
                        );
                        $(this).append(option).trigger("change");
                    }
                }
            });
        },

        /**
         * Set Project select2
         */
        setProjectSelect() {
            const $this = this;
            let workspace = this._workspace;
            $(() => {
                $(".form__task .js-project-data").select2({
                    //dropdownCssClass: "has-search",
                    placeholder: "Rechercher un projet",
                    language: {
                        searching: function () {
                            return "Chargement...";
                        },
                    },
                    ajax: {
                        url: "/api/project",
                        data: function (params) {
                            return {
                                mode: "search",
                                workspace: workspace,
                                noExplorer: true,
                            };
                        },
                        processResults: function (data) {
                            var data = $.map(data.datas, function (obj) {
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

                $(".form__task .js-fakeproject-data").select2({
                    dropdownCssClass: "has-search",
                    placeholder: "Rechercher un projet",
                    language: {
                        searching: function () {
                            return "Chargement...";
                        },
                    },
                });
                $(".form__task .js-fakeproject-data").val("default").change();
            });

            $(".js-project-data").on("select2:select", (e) => {
                this.selectedProject = e.params.data;
                this.step = 2;
                this.updatePreviewCalendar();
            });
            $(".js-project-data").on("select2:unselect", (e) => {
                this.selectedProject = null;
            });
        },

        getNotes() {
            $(document).on("click", (e) => {
                $(".js-notes-textarea").each(function () {
                    if (
                        $(this).is(":visible") &&
                        !e.target.matches(
                            ".js-toggle-content, .js-toggle-content *"
                        )
                    ) {
                        var $notesField = $(this)
                            .parent()
                            .find(".js-notes-value");

                        if ($(this).val() !== "") {
                            var notes = $(this).val();

                            $notesField.parent().addClass("has-value");
                            $notesField.html(notes);

                            if ($(this).val().length > 36) {
                                $notesField.addClass("is-cut");
                            } else {
                                $notesField.removeClass("is-cut");
                            }
                        } else {
                            $notesField.parent().removeClass("has-value");
                            $notesField.html("");
                        }
                    }
                });
            });
        },

        setNotes() {
            $(".js-notes-textarea").each(function () {
                var $notesField = $(this).parent().find(".js-notes-value");

                if ($(this).val() !== "") {
                    var notes = $(this).val();

                    $notesField.parent().addClass("has-value");
                    $notesField.html(notes);

                    if ($(this).val().length > 36) {
                        $notesField.addClass("is-cut");
                    } else {
                        $notesField.removeClass("is-cut");
                    }
                } else {
                    $notesField.parent().removeClass("has-value");
                    $notesField.html("");
                }
            });
        },

        goBackNew() {
            this.step = 1;
            this.selectedProject = null;
        },

        goBack() {
            this.tasks = [];
            this.addTask();
            this.step = 1;
        },

        toggleProjectChoice(type) {
            this.projectChoice = type;
            this.step = 1;
            this.selectedProject = null;
        },
        getTasks(projectId) {
            axios
                .get("/api/project/" + projectId)
                .then((res) => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        this.$bus.$emit("PLANNING_UPDATE", res.data.datas.task);
                    }
                })
                .catch((error) => console.log(error));
        },
        deleteNote(taskKey, noteKey) {
            let tasks = this.tasks;
            if (
                tasks &&
                tasks[taskKey] &&
                tasks[taskKey].notes
            ) {
                this.tasks[taskKey].notes.splice(noteKey, 1);
            }
        },
        getProject(projectId, force = false) {
            let $this = this;
            if (this.project.id != projectId || force) {
                axios.get('/api/project/' + projectId).then(res => {
                    if (typeof res.data.datas !== "undefined") {
                        this.tasks = [];
                        let project = res.data.datas;
                        this.project = project;
                        let tasksInProgress = res.data.datas.tasksInProgress;
                        if (tasksInProgress) {
                            this.$bus.$emit("TASK_IN_PROGRESS_UPDATE", tasksInProgress);
                        }
                        //////////////////////////////////////
                        if (this.type == "EDIT") {
                            this.selectedDate = null;
                            if (this.selectedProject == null) {
                                this.selectedProject = this.project;
                            }
                            let tasksDate = [];
                            let tasks = this._tasks;
                            if (!tasks) {
                                if (this.projectId == this.project.id) {
                                    tasks = this.project.tasksInProgress;
                                }
                            }
                            if (tasks) {
                                let index = 0;
                                tasks.forEach(function (task, key) {
                                    let taskStartDate = task.start_date;
                                    let taskEndDate = task.end_date;
                                    if (Number.isNaN(new Date(taskStartDate).getTime())) {
                                        taskStartDate = moment(taskStartDate).format('YYYY/MM/DD');
                                    }
                                    if (Number.isNaN(new Date(taskEndDate).getTime())) {
                                        taskEndDate = moment(taskEndDate).format('YYYY/MM/DD');
                                    }
                                    let talent = task.talent_id
                                        ? task.talent_id[0]
                                        : task.talentId;
                                    //task.tasks.forEach(function (taskDate, taskKey) {
                                        //task.push({
                                            //id: taskDate.id,
                                            task.id = task.id;
                                            task.task_id = task.id;
                                            task.index = index++;
                                            task.talent_id = talent ? talent.id : false;
                                            task.talent_name = task.talentName;
                                            task.task_type_id = task.taskTypes[0]
                                                ? task.taskTypes[0].id
                                                : false;
                                            task.task_type_name = task.taskTypes[0]
                                                ? task.taskTypes[0].name
                                                : false;
                                            task.dates = {
                                                //start: new Date(taskDate.start_date),
                                                //end: new Date(taskDate.end_date),
                                                start: new Date(taskStartDate),
                                                end: new Date(taskEndDate),
                                            };
                                            task.noteShow = false;
                                            task.notes = task.note
                                                ? JSON.parse(task.note)
                                                : [];
                                            /*notes: taskDate.note
                                            ? JSON.parse(taskDate.note)
                                            : [],*/
                                            task.notePreview = "";
                                            $this.addTask(task);
                                        //});
                                    //});
                                });
                                if (this._task) {
                                    this.taskSelected = this._task;
                                    let selectedTaskIndex = 0;
                                    this.tasks.map((e) => {
                                        if ($this.taskSelected.id == e.id) {
                                            selectedTaskIndex = e.index;
                                        }
                                    });
                                    this.taskSelected.index = selectedTaskIndex;
                                    this.taskSelected.task_type_id = this.taskSelected.taskTypes[0] ? this.taskSelected.taskTypes[0].id : false;
                                    this.taskSelected.task_type_name = this.taskSelected.taskTypes[0] ? this.taskSelected.taskTypes[0].name : false;
                                }
                                //this.tasks = tasksDate;
                                this.setTaskTypeSelect();
                                this.updatePreviewCalendar();
                            }
                        }
                        //////////////////////////////////////
                    } else {
                        //error
                    }
                }).catch(error => console.log(error));
            }
        },
        showNote(task, taskKey) {
            let allNotes = document.getElementsByClassName('task__notes--content');
            for (let i = 0; i < allNotes.length; i++) {
                allNotes[i].style.display = "none";
            }
            let notesLimit = 3;
            let topLimit = 2;
            let taskElement = allNotes[taskKey];
            if (allNotes.length > 5) {
                notesLimit = allNotes.length - 2;
                topLimit = notesLimit - 2;
            }
            if (allNotes.length > notesLimit && (taskKey > topLimit && taskKey < allNotes.length)) {
                taskElement.style.top = "auto";
                taskElement.style.bottom = "45px";
            } else {
                taskElement.style.top = "45px";
                taskElement.style.bottom = "auto";
            }

            task.noteShow = !task.noteShow;
        },
        calendarPosition(tasks, taskKey) {
            let position = 'auto';
            let taskLimit = 3;
            let topLimit = 2;
            if (tasks.length > 5) {
                taskLimit = tasks.length - 2;
                topLimit = taskLimit - 2;
            }
            if (tasks.length > taskLimit && (taskKey > topLimit && taskKey < taskLimit)) {
                position = 'top-end';
            } else {
                position = 'bottom-end';
            }

            return position;
        },
        selectTask(task) {
            let taskPreview = document.getElementsByClassName('task-preview');
            let selectedTaskPreview = document.getElementsByClassName('task-preview-' + task.index);
            let talentTask = document.getElementsByClassName('talent_task');
            let talentTaskSelected = document.getElementById('talent_task-' + task.index);
            for (let i = 0; i < talentTask.length; i++) {
                talentTask[i].style.backgroundColor = "transparent";
            }
            talentTaskSelected.style.backgroundColor = "#511FE0";
            for (let i = 0; i < taskPreview.length; i++) {
                taskPreview[i].style.backgroundColor = "transparent";
            }
            for (let i = 0; i < selectedTaskPreview.length; i++) {
                selectedTaskPreview[i].style.backgroundColor = "#511FE0";
            }

        },
        closeDatePicker() {

        },
        closeNotes() {
            let allNotes = document.getElementsByClassName('task__notes--content');
            for (let i = 0; i < allNotes.length; i++) {
                allNotes[i].style.display = "none";
            }
        },
        closeDatePickers(){
            let datepickers = this.$refs.datepicker;
            for (let i = 0; i < datepickers.length; i++) {
                datepickers[i].hidePopover();
            }
        }
    },
};
</script>
