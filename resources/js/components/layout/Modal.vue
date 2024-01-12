<template>
    <div id="js-popup" class="popup js-popup" v-show="action" :class="pClass" :style="(action == 'SET_TASK') ? {backgroundColor: 'rgb(21, 12, 45)'} : ''" v-on:click.prevent="(action == 'CONGRATS' || action === 'CREATE' || action === 'SET_APPOINTMENTFORM') ? close() : ''">
        <div
            :class="{ 'popup-overlay': noBg }"
            class="popup-overdlay"
        ></div>
        <div :class="{ 'popup-content': noBg, allowScroll: allowScroll, 'popup-congrats': action == 'CONGRATS' }">
            <!-- <button type="button" class="popup__close small-icon-button js-close-popup" v-on:click="action = null">
				<IconCross></IconCross> Close
			</button> -->
            <ProjectForm
                v-if="action == 'SET_PROJECT'"
                :_project="project"
                :_step="step"
                :_workspace="this._workspace"
            ></ProjectForm>
            <GroupForm
                v-if="action == 'SET_GROUP'"
                :_group="element"
                :_workspace="workspace"
            ></GroupForm>
            <transition name="bounce">
                <TaskForm
                    v-if="action == 'SET_TASK'"
                    :_workspace="this._workspace"
                    :_project="project"
                    :_goback="goback"
                    :_selectedDate="selectedDate"
                    :_selectedDateTask="selectedDateTask"
                    :_selectToTalents="selectToTalents"
                    :_tasks="tasks"
                    :_task="task"
                    :type="type"
                    :formType="formType"
                    :_projectId="element"
                ></TaskForm>
            </transition>
            <transition name="bounce">
                <TaskSingleForm
                    v-if="action == 'SET_TASK_SINGLE'"
                    :_task="task"
                    :_project="project"
                ></TaskSingleForm>
            </transition>
            <ExportForm
                v-if="action == 'SET_EXPORT'"
                :_project="project"
                :_exxports="exxports"
                :_type="type"
                :_exxport="exxport"
            ></ExportForm>
            <transition name="bounce">
                <!-- Used to create and edit an appointment -->
                <AppointmentForm
                    v-if="action == 'SET_APPOINTMENTFORM'"
                    :_appointment="appointment"
                ></AppointmentForm>
            </transition>
            <!-- Not used? -->
            <Appointment
                v-if="action == 'SET_APPOINTMENT'"
                :_appointment="appointment"
            ></Appointment>
            <transition name="bounce">
                <TalentForm
                    v-if="action == 'SET_TALENT'"
                    :_talent="talent"
                    :_workspace="this._workspace"
                ></TalentForm>
            </transition>
            <transition name="fadeUpAndDown" mode="out-in">
                <Congrats
                    v-if="action == 'CONGRATS'"
                    :_type="type"
                    :_element="element"
                    :_more="more"
                ></Congrats>
            </transition>
            <AccountManagement
                v-if="action == 'ACCOUNT'"
                :_user="user"
            ></AccountManagement>
            <template v-if="action == 'CREATE'"
            >
                <div
                    class="actions-list__buttons"
                    :style="`z-index: 15; top:`+(getCreateButtonPosition().top+getCreateButtonPosition().height + 5)+`px;left:`+getCreateButtonPosition().left+`px;`"
                    v-on:click.stop
                >
                    <table>
                        <tr>
                            <td class="create-btns">
                                <a
                                    href="javascript:;"
                                    class="create-btn"
                                    v-on:click="Global._setAction('SET_TASK', {type: 'ADD', formType: 'project'})"
                                    style="background: #160d2d; display: block"
                                    data-id="TaskForm"
                                >
                                    <span
                                        class="icon icon-project"
                                        style="margin:0"
                                    ></span
                                    >Un projet
                                </a>
                            </td>
                            <td class="create-btns">
                                <a
                                    href="javascript:;"
                                    class="create-btn"
                                    v-on:click="Global._setAction('SET_TASK', {type: 'ADD', formType: 'task'})"
                                    style="background:#160d2d;display:block;"
                                    data-id="TaskForm"
                                >
                                    <span
                                        class="icon icon-task"
                                        style="margin:0"
                                    ></span
                                    >Une tâche</a
                                >
                            </td>
                        </tr>
                        <tr>
                            <td class="create-btns">
                                <a
                                    href="javascript:;"
                                    class="create-btn"
                                    v-on:click="
                                        Global._setAction('SET_APPOINTMENTFORM')
                                    "
                                    style="background: #160d2d; display: block"
                                >
                                    <span class="picto picto-work"></span>Un
                                    RDV</a
                                >
                            </td>
                            <td class="create-btns">
                                <a
                                    href="javascript:;"
                                    class="create-btn"
                                    v-on:click="Global._setAction('SET_TALENT')"
                                    style="background: #160d2d; display: block; display: relative "
                                    ><span
                                        class="icon icon-talent"
                                        style="margin:0"
                                    ></span
                                    >Un contact</a
                                >
                            </td>
                        </tr>
                    </table>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
export default {
    props: ['_workspace'],

    data() {
        return {
            action: null,
            type: null,
            more: null,
            element: null,
            pClass: null,
            talent: null,
            project: null,
            task: null,
            appointment: null,
            user: null,
            goback: null,
            noBg: true,
            step: null,
            exxports: null,
            selectedDate: null,
            selectedDateTask: null,
            exxport: null,
            allowScroll: false,
            tasks: false,
            task: false,
            talentName: false,
            formType: false,
            id: false,
            workspace: false,
            selectToTalents : false,
        };
    },

    mounted() {
        this.$bus.$on("ACTION_CHANGED", datas => {
            setTimeout(() => {
                this.action = datas.action;
                this.type = datas.type ? datas.type : false;
                this.element = datas.element ? datas.element : false;
                this.more = datas.more ? datas.more : false;
                this.pClass = datas.class ? datas.class : false;
                if (this.action == 'CONGRATS') {
                    this.pClass = 'popup-absolute';
                }
                else if (this.action == "CONGRATS") {
                    this.noBg = false;
                }
                else if (this.action == "CREATE") {
                    $(".popup-content").css("position", "absolute");
                }
                else if (this.action == "SET_TALENT") {
                    $(".popup-content").    css("position","relative");
                }
                else if (this.action == "SET_TASK" || this.action == "CONGRATS") {
                    this.allowScroll = true;
                    $(".popup-content").css("position", "relative");
                } else {
                    this.allowScroll = false;
                    $(".popup-content").css("position","relative");
                    $(".popup").css("background-color", "inherit");
                }
                if (!this.action || (this.action && this.action == "CREATE")) {
                    Vue.prototype.$shortcutActive = true;
                } else {
                    Vue.prototype.$shortcutActive = false;
                }
            }, 10);
            // Reset datas
            this.talent = null;
            this.project = null;
            this.appointment = null;
            this.task = null;
            this.user = null;
            //this.element = null;
            this.goback = datas.goback;
            this.id = datas.id ? datas.id : false;
            if (datas.talent) {
                this.talent = datas.talent;
                this.element = datas.talent;
            }
            if (datas.workspace) {
                this.workspace = datas.workspace;
            }
            if (datas.project) {
                this.project = datas.project;
                this.element = datas.project;
            }
            if (datas.appointment) {
                this.appointment = datas.appointment;
                this.element = datas.appointment;
            }
            if (datas.task) {
                this.task = datas.task;
                this.element = datas.task;
            }
            if (datas.user) {
                this.user = datas.user;
                this.element = datas.user;
            }

            if (datas.tasks) {
                this.tasks = datas.tasks;
                this.element = datas.tasks;
            }

            if (datas.selectedDateTask) {
                this.selectedDateTask = datas.selectedDateTask;
                this.element = datas.selectedDateTask;
            }

            if (datas.task) {
                this.task = datas.task;
                if (datas.talentName) {
                    this.task.talent_name = datas.talentName;
                }
                this.element = datas.task;
            }

            if (datas.step) {
                this.step = datas.step;
                this.element = datas.step;
            }
            if (datas.selectedDate) {
                this.selectedDate = datas.selectedDate;
                this.element = datas.selectedDate;
            }
            if (datas.exxports) {
                this.exxports = datas.exxports;
                this.element = datas.exxports;
            }

            if (datas.exxport) {
                this.exxport = datas.exxport;
                this.element = datas.exxport;
            }
            if (datas.formType) {
                this.formType = datas.formType;
                this.element = datas.formType;
            }

            if (datas.selectToTalents) {
                this.selectToTalents = datas.selectToTalents;
                this.element = datas.selectToTalents;
            }

            // Add body class
            this.setBodyClass();
        });
    },

    beforeDestroy() {
        this.$bus.$off("ACTION_CHANGED");
    },
    methods: {
        getCreateButtonPosition() {
            return (document.getElementById('create_btn')).getBoundingClientRect();
        },
        close() {
            if (this.type == 'TASK_OVER') {
                return false;
            } else {
                if (this.action == 'CONGRATS') {
                    this.$bus.$emit('RESET_CONGRATS_TIMER', {});
                }
                this.$bus.$emit("ACTION_CHANGED", {
                    action: null
                });
                // Remove body class
                this.setBodyClass();
                $(".popup").css("background-color", "inherit");
                // Reset watcher for changes on popup
                window.changeWhenPopupIsOpen = true;
            }
        },
        setBodyClass() {
            setTimeout(() => {
                if (
                    this.action == null ||
                    this.pClass == "task-popin" ||
                    this.action == "CONGRATS"
                ) {
                    $("body").removeClass("popup-is-open");
                    $("body").removeClass("popup-is-open-with-navbar");
                } else {
                    if (this.action === 'CREATE') {
                         $("body").addClass("popup-is-open-with-navbar");
                    } else {
                        $("body").addClass("popup-is-open");
                    }
                }
            }, 10);
        }
    }
};
</script>
