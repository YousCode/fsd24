<template>
    <div class="context-menu" id="box-context-menu">
        <template v-if="menuType == 'CMENU_PROJECT'">
            <ul class="context-menu--items">
                <li class="context-menu--items-element" v-on:click.prevent="Project._edit(project, callback, params)">
                    <span class="icon icon-edit"></span>
                    Modifier le projet
                </li>
                <hr/>
                <li class="context-menu--items-element" v-on:click.prevent="Project._delete(project, callback, params)">
                    <span class="icon icon-delete"></span>
                    Supprimer le projet
                </li>
            </ul>
        </template>
        <transition name="bounce" mode="out-in">
            <template v-if="menuType == 'CMENU_TASK'">
                <template v-if="task.accepted == null && task.talents[0].id == Global.user_id">
                    <ul class="context-menu--items">
                        <li class="context-menu--items-element" v-on:click="Task._setAccepted(task, true, callback, params)">
                            <span class="icon icon-check"></span>
                            Accepter la tâche
                        </li>
                        <li class="context-menu--items-element" v-on:click="Task._setAccepted(task, false, callback, params)">
                            <span class="icon icon-cross"></span>
                            Décliner la tâche
                        </li>
                    </ul>
                </template>
                <template v-else>
                    <ul class="context-menu--items">
                        <template v-if="task.status !== 'CLOSED'" >
                            <li v-show="task.status != 'CLOSED'" class="context-menu--items-element"
                                v-on:click="Global._setAction('SET_TASK', {element: task.project_id, task: task, type: 'EDIT', formType: 'context'}), hideContextMenu()">
                                Modifier la tâche
                            </li>
                            <li class="context-menu--items-element" v-on:click="hideContextMenu(); Task._close(task)">
                                Clôturer la tâche
                            </li>
                        </template>
                        <li class="context-menu--items-element" v-on:click="Project._goTo(project)">
                            Voir le projet
                        </li>
                        <li class="context-menu--items-element" v-on:click="Task._delete(task.id), hideContextMenu()">
                            Supprimer la tâche
                        </li>
                    </ul>
                </template>
            </template>
        </transition>
        <transition name="bounce" mode="out-in">
            <template v-if="menuType == 'CMENU_APPOINTMENT'">
                <ul class="context-menu--items">
                    <li class="context-menu--items-element" style="padding: 1.2rem 1.5rem;" v-on:click.prevent="Appointment._edit(appointment, contextMenuCallback, params), hideContextMenu()">
                        Modifier le RDV
                    </li>
                    <li class="context-menu--items-element" v-on:click.prevent="Appointment._delete(appointment, contextMenuCallback, params), hideContextMenu()">
                        Supprimer le RDV
                    </li>
                </ul>
            </template>
        </transition>
        <transition name="bounce" mode="out-in">
            <template v-if="menuType == 'CMENU_PLANNING'">
                <div class="expand-task" v-if="planning.type == 'APPOINTMENT'">
                    <i class="close-icon"></i>
                    <div class="head-planing">
                        <span  class="dot-button-task RDV" :class="planning.type"></span>
                        <h2>RDV</h2>
                        <p v-if="planning.firstname.length > 25" :title="planning.firstname">
                            {{ planning.firstname.substring(0, 35) + "..." }}
                        </p>
                        <p v-else>{{ planning.firstname }}</p>
                    </div>
                    <h3>Participants</h3>
                    <div class="section-planing" style="padding: 0.5rem 1em; gap: 0; justify-content: flex-start;">
                        <div class="tooltiped" v-for="talent in planning.talents">
                            <img class="rdv-avatars" v-bind:src="'/upload/avatars/' + talent.avatar" v-bind:alt="talent.name" />
                            <span class="tooltiptext">{{ talent.name }}</span>
                        </div>
                    </div>
                    <h3>Date</h3>
                    <div class="section-planing">
                        <i class="date"></i>
                        <p v-if="planning.datetime == tomorrow">
                            Demain à {{ planning.hourtime }}
                        </p>
                        <p v-else>
                            {{ planning.datetime }} à {{ planning.hourtime }}
                        </p>
                    </div>
                    <h3>Infos RDV</h3>
                    <div v-if="planning.location" class="section-planing">
                        <i class="location"></i>
                        <p v-if="planning.location.length > 25" :title="planning.location">
                            {{ planning.location.substring(0, 35) + "..." }}
                        </p>
                        <p v-else>{{ planning.location }}</p>
                    </div>
                    <div v-show="planning.phone" class="section-planing">
                        <i class="phone"></i>
                        <p>{{ planning.phone }}</p>
                    </div>
                    <div v-show="planning.email" class="section-planing">
                        <i class="email"></i>
                        <p v-if="planning.email.length > 25" :title="planning.email">
                            {{ planning.email.substring(0, 35) + "..." }}
                        </p>
                        <p v-else>{{ planning.email }}</p>
                    </div>
                    <h3 v-if="planning.note">Notes</h3>
                    <div v-if="planning.note" class="js-position-wrapper">
                        <div class="section-planing action-button js-position-button" style="cursor: pointer">
                            <i class="note close-no-modal"></i>
                            <p class="close-no-modal">Voir notes</p>
                        </div>
                        <div class="notes js-position-content c-main-grey" style="top:-5px;background:#150C2D;width: 96%;left: 5px;">
                            {{ planning.note }}
                        </div>
                    </div>
                    <div class="Edit-task-planing">
                        <div class="main-btns">
                            <span class="main-btn icon-edit" v-on:click="Appointment._edit(planning), hideContextMenu()"></span>
                            <span v-on:click.prevent="Appointment._delete(appointment), hideContextMenu()" class="main-btn icon-delete"></span>
                        </div>
                    </div>
                </div>
                <div class="expand-task" v-else>
                    <i class="close-icon" v-on:click="hideContextMenu"></i>
                    <div class="head-planing">
                        <span class="dot-button-task" :class="(planning.project_category == 'mission-explorer') ? 'mission-explorer-dot' : planning.task_type"></span>
                        <h2 v-if="planning.project_category == 'mission-explorer'">
                            {{ $t('mission-explorer') }}
                        </h2>
                        <h2 v-else>{{ $t(planning.task_type) }}</h2>
                        <a :href="project && project.url && !isShared ? project.url : '#'">
                            <p v-if="planning.project_name && planning.project_name.length > 25" :title="planning.project_name">
                                {{ planning.project_name.substring(0, 35) + "..." }}
                            </p>
                            <p v-else>{{ planning.project_name }}</p>
                        </a>
                    </div>
                    <h3>Tâche assigné par :</h3>
                    <div class="section-planing">
                        <img src="/images/avatar.png" alt="image du créateur de la tâche" />
                        <p v-if="planning.taskCreator" :title="planning.taskCreator">
                            {{ planning.taskCreator }}
                        </p>
                        <p v-else>{{ planning.created_by }}</p>
                    </div>
                    <h3>Date</h3>
                    <div class="section-planing">
                        <i class="date"></i>
                        <p v-if="params.startDate && params.endDate">
                            Du {{ params.startDate }} au {{ params.endDate }}
                        </p>
                        <p v-else>
                            Du {{ planning.start_date }} au {{ planning.end_date }}
                        </p>
                    </div>
                    <h3 v-if="planning.note">Notes</h3>
                    <div v-if="planning.note" class="js-position-wrapper">
                        <div class="section-planing action-button js-position-button" style="cursor: pointer">
                            <i class="note close-no-modal"></i>
                            <p c lass="close-no-modal">Voir notes</p>
                        </div>
                        <div class="notes js-position-content c-main-grey" style="top:-5px;background:#150C2D;width: 96%;left: 5px;">
                            <div v-for="(note, key) in taskNotes" v-if="typeof note == 'object'" class="notes-wrapper">
                                <span v-on:click="noteCheck(key, !note.state, planning.id, params.isShared)" class="close-no-modal">
                                    <span :class="note.state ? 'note-checkbox checked' : 'note-checkbox'"></span>
                                </span>
                                <span class="close-no-modal">{{ note.note }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="Edit-task-planing">
                        <div class="main-btns" v-if="!params.isClient && !params.isShared">
                            <span v-if="planning.project_category !== 'mission-explorer' && params.status !== 'CLOSED'" class="main-btn icon-edit"
                                v-on:click="hideContextMenu(); Global._setAction('SET_TASK', {project: project, element: project.id, selectedDate: params.selectedDate, task: params.task, tasks: project.tasksInProgress, type: 'EDIT', formType: 'context'})"></span>
                                <span v-if="planning.project_category !== 'mission-explorer' && params.status !== 'CLOSED'" class="main-btn icon-delete"
                                v-on:click="Task._delete(planning.id, contextMenuCallback, params, {deleteParentTask: true, projectId: params.projectId}), hideContextMenu()"></span>
                            <button v-if="planning.project_category !== 'mission-explorer' && params.status !== 'CLOSED'" v-on:click="hideContextMenu(); Task._close(planning)">
                                Clôturer la tâche
                            </button>
                            <p class="closed-task" v-else-if="planning.project_category !== 'mission-explorer' && params.status == 'CLOSED'">
                                Tâche clôturée le {{ params.closedAt }}
                            </p>
                            <button v-else-if="planning.project_category == 'mission-explorer'" @click="goToMessenger()">
                                Aller vers la conversation
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </transition>
        <template v-if="menuType == 'CMENU_TALENT'">
            <ul class="context-menu--items">
                <li class="context-menu--items-element" v-on:click.prevent="Talent._edit(talent, callback, params)">
                    <span class="icon icon-edit"></span>
                    Modifier le talent
                </li>
                <li class="context-menu--items-element" v-on:click.prevent="Talent._delete(talent, callback, params)">
                    <span class="icon icon-delete"></span>
                    Supprimer le talent
                </li>
            </ul>
        </template>
    </div>
</template>

<script>
import Vue from "vue";

export default {
    props: [
        "contextMenuCallback"
    ],

    data() {
        return {
            contextMenu: null,
            contextMenuX: 0,
            contextMenuY: 0,
            menuType: null,
            planning: null,
            project: null,
            task: null,
            appointment: null,
            talent: null,
            relativePosition: null,
            relativeParentPosition: null,
            callback: null,
            params: [],
            taskNotes: false,
            isShared : null,
        };
    },

    mounted() {

    },

    methods: {
        show(e, type, datas, callback, params) {
            if (params.projectId) {
                let projectId = params.projectId;
                this.getProject(projectId);
            }
            //this.resetData();
            let origin = e.target
            let viewportHeight = window.innerHeight;
            let viewportWidth = window.innerWidth;
            this.startFocusOut(origin);
            var rect = e.currentTarget.getBoundingClientRect();
            this.relativeParentPosition = document.getElementById(
                "context_menu_wrapper"
            );
            this.contextMenu = document.getElementById("box-context-menu");
            this.relativePosition = this.relativeParentPosition.getBoundingClientRect();
            let lastKnownScrollPosition = window.scrollY;
            this.isShared = datas.isShared;
            if (type == "PROJECT") {
                this.menuType = "CMENU_PROJECT";
                this.project = datas.project;
            } else if (type == "TASK") {
                if (e.which === 3) {
                    this.contextMenu.style.position = "fixed";
                    this.contextMenu.style.display = "block";
                    this.contextMenu.style.left = e.clientX + "px";
                    this.contextMenu.style.top = e.clientY + "px";
                } else {
                    this.contextMenu.style.position = "relative";
                    this.contextMenu.style.top =
                        rect.top - this.relativePosition.top + 30 + "px";
                    this.contextMenu.style.left =
                        rect.left - this.relativePosition.left + "px";
                }
                this.menuType = "CMENU_TASK";
                this.project = datas.task.project_id;
                this.task = datas.task;
                //    document.getElementById("box").style.display = "block";
            } else if (type == "APPOINTMENT") {
                if (e.which === 3) {
                    this.contextMenu.style.position = "fixed";
                    this.contextMenu.style.display = "block";
                    this.contextMenu.style.left = e.clientX + "px";
                    this.contextMenu.style.top = e.clientY + "px";
                } else {
                    this.contextMenu.style.position = "relative";
                    this.contextMenu.style.top =
                        rect.top - this.relativePosition.top + "px";
                    this.contextMenu.style.left =
                        rect.left - this.relativePosition.left - 138 + "px";
                }
                this.menuType = "CMENU_APPOINTMENT";
                this.appointment = datas.appointment;
                //    document.getElementById("box").style.display = "block";
            } else if (type == "PLANNING") {
                this.menuType = "CMENU_PLANNING";
                this.planning = datas.planning;
                this.centerModal(e, this.planning);
                this.task = datas.planning.task_id;
                if (this.planning.note == '') {
                    this.taskNotes = {};
                } else {
                    this.taskNotes = JSON.parse(this.planning.note);
                }
                //    document.getElementById("box").style.display = "block";
                if (this.planning.project_category !== "mission-explorer") {
                    if (this.planning.note == '') {
                        this.taskNotes = {};
                    } else {
                        this.taskNotes = JSON.parse(this.planning.note);
                    }
                }
                //    document.getElementById("box").style.display = "block";
            } else if (type == "TALENT") {
                this.menuType = "CMENU_TALENT";
                this.talent = datas.talent;
            }

            if (params) {
                this.params = params;
            }
            $(document).on("scroll", () => {
                try {
                    if (
                        type !== undefined &&
                        type !== "PLANNING" &&
                        type !== null
                    ) {
                        this.hideContextMenu();
                        type = null;
                    }
                } catch (error) {
                    console.log(error);
                }
            });
        },
         startFocusOut(origin) {
            $(document).on("click", () => {
                let contextMenutarget = document.querySelector("#box-context-menu")
                let contextMenuOrigin = document.querySelectorAll(".planned-task-container");
                let contextMenuEditAppointment = document.querySelectorAll(".appointment-edit")
                let contextMenuEditTask = document.querySelectorAll(".project-detail__action-button-task")
                var els = [];
                let a = event.target
                while (a !== contextMenutarget.parentNode && a !== null ) {
                    els.unshift(a);
                    a = a.parentNode;
                }
                if(!els.includes(contextMenutarget) ){
                    if(origin !== null) {
                        if (!els.includes(origin) ){
                            if (!els.some(r => Array.from(contextMenuOrigin).includes(r)) && 
                                !els.some(r => Array.from(contextMenuEditAppointment).includes(r))&& 
                                !els.some(r => Array.from(contextMenuEditTask).includes(r))) {
                                this.hideContextMenu();
                            }
                        }
                    }
                }
            });
        },
        centerModal(e, planning) {
            let viewportHeight = window.innerHeight;
            let initialPosition = 0;
            var rect = e.currentTarget.getBoundingClientRect();
            this.relativeParentPosition = document.getElementById("context_menu_wrapper");
            this.contextMenu = document.getElementById("box-context-menu");
            this.contextMenu.style.position = "absolute";
            let lastKnownScrollPosition = window.scrollY;
            if (rect.top + 397 > viewportHeight) {
                if (planning.type == "APPOINTMENT") {
                    this.contextMenu.style.top = viewportHeight - 498.4 + lastKnownScrollPosition + "px";
                } else {
                    this.contextMenu.style.top = rect.top + lastKnownScrollPosition + "px";
                }
            } else {
                this.contextMenu.style.top = rect.top + lastKnownScrollPosition + "px";
                initialPosition = rect.top + lastKnownScrollPosition;
            }
            let rectWidth = (planning.type == 'TASK') ? 100 : rect.width;
            this.contextMenu.style.left = rect.left - this.relativePosition.left + rectWidth + "px";
            addEventListener("scroll", event => {
                let lastKnownScrollPosition = window.scrollY;
                this.contextMenu.style.position = "absolute";
                if (planning.type == "APPOINTMENT") {
                    this.contextMenu.style.top =
                        viewportHeight - 498.4 + lastKnownScrollPosition + "px";
                } else {
                    this.contextMenu.style.top =
                        viewportHeight - 397 + lastKnownScrollPosition + "px";
                }
            });
        },
        goToMessenger() {
            window.location.href = "/explorer/messenger";
        },

        hideContextMenu() {
            this.menuType = null;
            var jsPopup = document.getElementById("js-popup");
            jsPopup.style.top = 0;
            jsPopup.style.left = 0;
            $("body").removeClass("overflow-hidden");
            if ($(".scroll-content").length > 0) {
                $(".scroll-content").removeClass("overflow-hidden");
            }
            $(document).off('click');
        },

        resetData() {
            this.menuType = null;
            this.project = null;
            this.task = null;
            this.appointment = null;
            this.talent = null;
            this.callback = null;
            this.contextMenuX = 0;
            this.contextMenuY = 0;
        },
        noteCheck(key, state, taskId, isShared) {
            if (
                typeof this.taskNotes !== "undefined" &&
                typeof this.taskNotes[key] !== "undefined" && !isShared
            ) {
                this.taskNotes[key].state = state;
                axios
                    .put("/api/task/note/" + taskId, {
                        params: this.taskNotes
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
            }
        },
        getProject(projectId) {
            if (!this.project || (this.project && this.project.id != projectId)) {
                axios.get('/api/project/' + projectId).then(res => {
                    if (typeof res.data.datas !== "undefined") {
                        let project = res.data.datas;
                        this.project = project;
                    } else {
                        //error
                    }
                }).catch(error => console.log(error));
            }
        },
        ucfirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    }
};
</script>
