<template>
    <!-- Wrapper -->
    <div class="projects-wrapper" id="vue__projects">
        <ContextMenu ref="ContextMenu"></ContextMenu>

        <!-- Container -->
        <div class="main-container">
            <div class="projects__view-container">
                <div class="projects__view-wrapper">
                    <!-- View -->
                    <div class="js-projects-list projects__view">
                        <!--	<AddButtons></AddButtons>-->

                        <!-- Filters -->
                        <div class="filters">
                            <div class="row filters-row">
                                <div class="search-col">
                                    <div class="filter-item has-picto">
                                        <span class="filter-picto picto-search">
                                    </span>
                                        <ProjectAutocomplete
                                        :_workspace="_workspace">
                                        </ProjectAutocomplete>
                                        <button type="button"
                                            class="filters-delete is-cross"
                                            :disabled="!filters.name"
                                            v-on:click="clear('name')">
                                            <span class="filters-delete__picto icon-cross">
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <div class="filters-col">
                                    <!--<p class="filters-text"><span class="filters-main-icon icon icon-filters"></span> Filtrer par</p>-->
                                    <div class="filter-col">
                                        <div class="filter-item filter-select has-picto">
                                            <span class="filter-picto picto-categories" >
                                            </span>
                                            <select type="text"
                                                v-model="filters.categories"
                                                v-select2
                                                class="filter-field js-categories-data"
                                                name="categories[]"
                                                multiple="multiple"
                                                v-bind:style="{ background:filters.categories.bgColor, border: '1.42px solid ' +  filters.categories.bgColor,}">
                                             </select>
                                            <button type="button"
                                                class="filters-delete"
                                                :disabled="filters.categories.length == 0"
                                                v-on:click="clear('categories')" >
                                                <span class="filters-delete__picto icon icon-refresh"
                                                    style="color: white">
                                            </span>
                                            </button>
                                        </div>
                                    </div>
                                        <div class="filter-col">
                                        <div class="filter-item filter-select has-picto">
                                         <span class="filter-picto picto-order"
                                                style="color: white">
                                        </span>
                                            <select type="text"
                                                v-model="filters.sortby"
                                                v-select2
                                                class="filter-field js-sortby-data"
                                                name="sortby">
                                                <option value="SORT_RECENT_TO_OLDER"  >
                                                    Du plus récent au plus
                                                    ancien
                                                </option>
                                                <option value="SORT_OLDER_TO_RECENT">
                                                    Du plus ancien au plus
                                                    récent
                                                </option>
                                            </select>
                                            <button type="button"
                                                class="filters-delete"
                                                :disabled="!filters.sortby"
                                                v-on:click="clear('sortby', '')" >
                                                <span class="filters-delete__picto icon icon-refresh"
                                                 style="color: white">
                                            </span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- <div class="filter-col">
                                        <div class="filter-item filter-select has-picto">
                                            <span class="filter-picto picto-status">
                                        </span>
                                            <select type="text"
                                                v-model="filters.status"
                                                v-select2
                                                class="filter-field js-status-data"
                                                name="status">
                                                <option value="STATUS_ALL">
                                                    Tous
                                                </option>
                                                <option value="STATUS_PROGRESS">
                                                    En cours
                                                </option>
                                                <option value="STATUS_CLOSED">
                                                    Terminé
                                                </option>
                                            </select>
                                            <button type="button"
                                                class="filters-delete"
                                                :disabled="!filters.status"
                                                v-on:click="clear('status', '')" >
                                                <span class="filters-delete__picto icon icon-refresh"
                                                    style="color: white" >
                                                </span>
                                            </button>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <!-- ./Filters -->

                        <!-- List -->
                        <div class="projects-list-container">
                            <div id="loader" v-if="isLoaded">
                                <div style="height: 33px;width:40px;position: relative;">
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
                            <div v-if="kanban" v-for="(projects, projectStep) in kanban" class="col-4">
                                <div class="project-title" :class="projectStep">
                                    <div class="left">{{ $t(projectStep) }}</div>
                                    <div class="right">{{ projects.length }}</div>
                                </div>
                                <div :id="'project-' + projectStep.toLowerCase()" class="projects-container" @drop="onDrop($event)" @dragover.prevent="onDragOver('project-' + projectStep.toLowerCase())"  @dragleave.prevent="onDragLeave('project-' + projectStep.toLowerCase())">
                                    <div :id="projectStep.toLowerCase()" class="drop-project"></div>
                                    <div v-if="projectStep == 'WAITING'" v-on:click="createProject()" class="create-project" :style="(projectStep == 'WAITING') ? {height: '120px'} : ''"><span class="icon icon-plus"></span> {{ $t('create-project') }}</div>
                                    <div class="project-item" v-for="(project, index) in projects" :data-id="project.id" :draggable="isDraggable(projectStep, project)" @dragstart="startDrag($event, project)" :data-from="projectStep.toLowerCase()" :data-title="project.name" v-on:mouseover="projectHover('add-task-' + project.id, true)" v-on:mouseleave="projectHover('add-task-' + project.id, false)" :style="projectStep == 'WAITING' ? {height: '170px'} : {}">
                                        <div class="header">
                                            <span class="category">{{ $t(project.categoryName) }}</span>
                                            <span v-if="projectStep != 'CLOSED'" class="deadline" :class="projectStep != 'IN_PROGRESS' ? 'done' : ''"
                                                  v-on:mouseenter="projectHover('deadline-' + project.id, true)" v-on:mouseleave="projectHover('deadline-' + project.id, false)">
                                                  <span class="icon icon-calendar"></span>{{ project.formattedDateDeadline }}</span>
                                        </div>
                                        <div class="body">
                                            <span :class="projectStep != 'IN_PROGRESS' ? 'done title' : 'title'" v-on:click="redirectToProject(project.url)">{{ project.name }}</span>
                                            <span v-show="showNotification(project.id)" class="update-notification"></span>
                                            <div v-if="projectStep == 'IN_PROGRESS'" class="in-progress">
                                                <div class="in-progress-task-type">
                                                    {{ $t('in-progress') }}
                                                    <span v-if="project.id != 1 && project.activeTaskType && project.activeTaskType.length > 0" v-for="(typeTask, i) in project.activeTaskType" class="task-type" :class="(typeTask && typeTask) == '3D' ? 'd3' : typeTask">
                                                        {{ $t(typeTask) }}
                                                    </span>
                                                    <span v-if="project.id == 1 && onboardingTaskType && onboardingTaskType.length > 0" v-for="(typeTask, i) in onboardingTaskType" class="task-type" :class="(typeTask && typeTask) == '3D' ? 'd3' : typeTask">
                                                        {{ $t(typeTask) }}
                                                    </span>
                                                    <span v-if="project.id != 1" :id="'add-task-' + project.id" class="add-task" v-on:click="Global._setAction('SET_TASK', {project: project, type: 'ADD', formType: 'projects'})">
                                                        <span class="icon icon-plus"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <span v-if="projectStep == 'WAITING'" :id="'add-task-' + project.id" class="add-task-waiting" v-on:click="Global._setAction('SET_TASK', {project: project, type: 'ADD', formType: 'projects'})">{{ $t('add-task') }}</span>
                                        </div>
                                        <div class="project-card__progress" v-if="projectStep == 'IN_PROGRESS'" style="background: #48367c;" v-on:mouseenter="projectHover('days-left-' + project.id, true)" v-on:mouseleave="projectHover('days-left-' + project.id, false)">
                                            <span class="project-card__progress-bar" :style="'width:' + getProjectProgressBar(project) +'%' "
                                                  v-bind:class="{'is-full': getProjectProgressBar(project) == 100, }" >
                                             </span>
                                        </div>
                                        <div :id="'days-left-' + project.id" class="days-left-hover">
                                            <div style="width: 20px; height: 20px; margin-right: 10px;"><span class="picto-clock"></span></div>
                                            {{ $t('days-left', {diffDays : project.deadlineDiffInDays}) }}
                                        </div>
                                        <div :id="'deadline-' + project.id" class="deadline-hover">
                                            {{ $t('deadline-deliver', {deadline : project.formattedDateDeadline}) }}
                                        </div>
                                    </div>
                                    <div v-if="projectStep == 'IN_PROGRESS' && kanban[projectStep] && kanban[projectStep].length == 1 && kanban['WAITING'] && kanban['WAITING'].length == 0 && kanban['CLOSED'] && kanban['CLOSED'].length == 0" v-on:click="createProject()" class="create-project"><span class="icon icon-plus"></span> {{ $t('create-project') }}</div>
                                </div>
                            </div>
                        </div>
                        <!-- ./List -->
                    </div>
                    <!-- ./View -->
                </div>
            </div>
        </div>
        <!-- ./Container -->
    </div>
    <!-- Wrapper -->
</template>

<script>

export default {
    props: ['_user', '_workspace', '_workspace_owner'],

    data() {
        return {
            selectedProject: null,
            projects: [],
            filters: {
                name: null,
                projects_id: null,
                sortby: '',
                status: '',
                categories: []
            },
            isProjectView: false,
            kanban: {'WAITING': [], 'IN_PROGRESS' : [], 'CLOSED' : []},
            projectUpdated: [],
            isLoaded: true,
            onboardingTaskType: [],
        }
    },

    mounted() {
        this.setCategoriesSelect();
        this.setSortbySelect();
        this.getProjects();
        window.Echo.private('kolab').listen('.update-projects', (e) => {
            this.getProjects();
        });
        this.$bus.$on("UPDATE_PROJECT_STEP", (data) => {
            let projectId = false;
            if (data && data.projectId) {
                projectId = data.projectId;
                this.projectUpdated.push(projectId);
            }
        });
        this.$bus.$on("UPDATE_PROJECT_LOAD", (data) => {
            let time = 0;
            if (!data.isLoaded) {
                time = 500;
            }
            setTimeout(() => {
                this.isLoaded = data.isLoaded;
            }, time)
        });
        this.onboardingTaskType = ['project-preparation', 'editing', 'artistic-direction', 'VFX', 'Motion', 'color-grading'];
    },

    computed: {},

    methods: {
        /**
         * getProjects
         * @return void
         */
        async getProjects() {
            var params = {};
            params.all = true;
            if (this.filters.name) params.filter_name = this.filters.name;
            if (this.filters.categories.length > 0) {
                params.filter_categories = this.filters.categories;
            }
            if (this.filters.sortby) params.filter_sortby = this.filters.sortby;
            await axios.get("/api/projects/kanban", {
                params: params
            }).then((res) => {
                if (res.statusText && res.statusText == 'OK' && res.data) {
                    this.kanban = res.data;
                    this.isLoaded = false;
                    /*if (projects) {
                        const $this = this;
                        for (let i = 0; i < projects.length; i++) {
                            if ((projects[i].category && projects[i].category.name && projects[i].proposition) && ((projects[i].category.name != "mission-explorer") || (projects[i].category.name == "mission-explorer" && (projects[i].proposition.client_id == this._user.id || projects[i].proposition.freelance_id == this._user.id)))) {
                                switch (projects[i].project_step) {
                                    case 'WAITING':
                                        this.kanban['WAITING'].push(projects[i]);
                                    case 'IN_PROGRESS':
                                        this.kanban['IN_PROGRESS'].push(projects[i]);
                                    case 'CLOSED':
                                        this.kanban['CLOSED'].push(projects[i]);
                                }
                            }
                        }
                    }*/
                    
                }
            }).catch((error) => console.log(error));
        },
       
        IsYourProjectExplorer(project) {
        if (project.mission !== 'explorer') {
            return false;
        }
        const freelancerId = this.currentFreelancerId;
        const clientId = this.currentClientId;
        return (
            freelancerId === project.proposal.freelancerId &&
            clientId === project.clientId
        );
        },
        /**
         * Set Project select2
         */
        setProjectSelect(name) {
            $(() => {
                $(".js-" + name + "-data").select2({
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
                                term: params.term,
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

                $(".js-" + name + "-data").on("select2:select", (e) => {
                    this.getProjects();
                });
                $(".js-" + name + "-data").on("select2:unselect", (e) => {
                    this.getProjects();
                });
            });
        }
        ,

        /**
         * Set Categories select2
         */
        setCategoriesSelect() {
            $(() => {
                $(".js-categories-data").select2({
                    dropdownCssClass: "multiple-choices filters-dropdown",
                    placeholder: "Catégorie(s)",
                    closeOnSelect: false,
                    language: {
                        searching: function () {
                            return "Chargement...";
                        },
                    },
                    ajax: {
                        url: "/api/project-category",
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

            $(".js-categories-data").on("select2:select", (e) => {
                this.getProjects();
            });
            $(".js-categories-data").on("select2:unselect", (e) => {
                this.getProjects();
            });
        }
        ,

        /**
         * Set Sortby select2
         */
        setSortbySelect() {
            $(() => {
                $(".js-sortby-data").select2({
                    dropdownCssClass: "filters-dropdown",
                    minimumResultsForSearch: Infinity,
                    placeholder: "Deadline",
                });
            });

            $(".js-sortby-data").on("select2:select", (e) => {
                this.getProjects();
            });
            $(".js-sortby-data").on("select2:unselect", (e) => {
                this.getProjects();
            });
        }
        ,

        /**
         * Set Status select2
         */
        setStatusSelect() {
            $(() => {
                $(".js-status-data").select2({
                    dropdownCssClass: "filters-dropdown",
                    minimumResultsForSearch: Infinity,
                    placeholder: "Statut",
                });
            });

            $(".js-status-data").on("select2:select", (e) => {
                this.getProjects();
            });
            $(".js-status-data").on("select2:unselect", (e) => {
                this.getProjects();
            });
        }
        ,

        /**
         * Clear field
         */
        clear(type, value = null) {
            if (typeof this.filters[type] == "object") {
                this.filters[type] = [];
            } else {
                this.filters[type] = value;
            }

            if ($(".js-" + type + "-data")) {
                $(".js-" + type + "-data")
                    .val("")
                    .trigger("change");
            }

            this.getProjects();
        },
        redirectToProject(url) {
            window.location.href = url;
        },
        createProject() {
            Vue.prototype.Global._setAction('SET_TASK', {type: 'ADD', formType: 'task'});
        },
        projectHover(hoverId, active) {
            let hoverElement = document.getElementById(hoverId);
            if (hoverElement && hoverElement.style) {
                if (active) {
                    hoverElement.style.opacity = 1;
                } else {
                    hoverElement.style.opacity = 0;
                }
            }
        },
        startDrag(evt, project) {
            let projectID = project.id;
            let from = evt.target.dataset.from;
            let projectTitle = evt.target.dataset.title;
            let projectItem = document.getElementsByClassName('project-item');
            if (projectItem) {
                for (let i = 0; i < projectItem.length; i++) {
                    if (projectItem[i]) {
                        if (projectItem[i].dataset && projectItem[i].dataset.id && projectItem[i].dataset.id == projectID) {
                            continue;
                        }
                        projectItem[i].style.zIndex = -1;
                    }
                }
            }
            evt.dataTransfer.dropEffect = 'move';
            evt.dataTransfer.effectAllowed = 'move';
            evt.dataTransfer.setData('projectID', projectID);
            evt.dataTransfer.setData('projectTitle', projectTitle);
            evt.dataTransfer.setData('from', from);
        },
        onDrop(evt) {
            const from = evt.dataTransfer.getData('from');
            const projectID = evt.dataTransfer.getData('projectID');
            const projectTitle = evt.dataTransfer.getData('projectTitle');
            let dropContainers = document.getElementsByClassName('projects-container');
            for (let i = 0; i < dropContainers.length; i++) {
                if (dropContainers[i].classList.contains('onDrop')) {
                    dropContainers[i].classList.remove('onDrop');
                }
            }
            let projectItem = document.getElementsByClassName('project-item');
            if (projectItem) {
                for (let i = 0; i < projectItem.length; i++) {
                    if (projectItem[i]) {
                        projectItem[i].style.zIndex = 0;
                    }
                }
            }
            if (from && projectID && evt && evt.target && evt.target.id) {
                let to = evt.target.id;
                if (from == 'in_progress' && to == 'closed') {
                    Vue.prototype.$bus.$emit('CONFIRM', {
                        confirmText: 'Toutes les tâches de ce projet vont être clôturées ?',
                        confirmText2: projectTitle,
                        button_1: {
                            title: 'Confirmer',
                            class: '',
                            method: Vue.prototype.Project._confirmClosed,
                            args: { from, to, projectID }
                        },
                        button_2: {
                            title: 'Annuler',
                        }
                    });
                } else if (from == 'closed' && to == 'in_progress') {
                    Vue.prototype.$bus.$emit('CONFIRM', {
                        confirmText: 'Pour déplacer ce projet, il faut créer une tâche.',
                        confirmText2: projectTitle,
                        button_1: {
                            title: 'Créer une tâche pour ce projet',
                            class: '',
                            method: Vue.prototype.Project._addTask,
                            args: { projectID }
                        },
                        button_2: {
                            title: 'Annuler',
                        }
                    });
                }
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
        showNotification(projectId) {
            if (this.projectUpdated.length == 0) {
                return false;
            } else if (this.projectUpdated.includes(projectId)) {
                return true;
            }

            return false;
        },
        isDraggable(projectStep, project) {
            let projectOwnerId = project.created_by;
            let projectId = project.id;
            if (projectStep == 'WAITING' || projectId == 1) {
                return false;
            }
            if (this._user && this._user.id && ((this._workspace && this._workspace_owner && this._workspace_owner.id && this._user.id == this._workspace_owner.id) || (projectOwnerId && this._user.id == projectOwnerId) || this._user.is_admin)) {
                return true;
            }
            return false;
        }
    },
};
</script>
