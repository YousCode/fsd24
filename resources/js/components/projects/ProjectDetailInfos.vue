<template>
    <div class="project-detail__main-info col-md-12">
        <div class="infos-background" v-on:click="showMobileInfos = !showMobileInfos" v-show="showMobileInfos"></div>
        <!-- Header -->
        <div class="project-detail__main-info__box project-detail__main-info__header text-center">
            <h2 class="main-title">
                <div class="title-indent desktop" v-on:click="openKanban()">
                    <div class="title-container">
                        <h1 id="title-project" v-on:mouseover="addActive(true)" v-on:mouseleave="addActive(false)">{{ project.name }}</h1>
                        <div id="caret-title" class="caret-title" v-show="!_isClient && !_isShared" v-on:mouseover="addActive(true)" v-on:mouseleave="addActive(false)">
                            <span class="caret-container">
                                <div class="picto-caret-down"></div>
                            </span>
                        </div>
                        <div id="caret-title-mobile" class="caret-title mobile">
                            <span class="caret-container">
                                <div class="picto-caret-down"></div>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="title-indent mobile" v-on:click="showMobileInfos = !showMobileInfos">
                    <div class="title-container">
                        <h1 id="title-project">{{ project.name }}</h1>
                        <div id="caret-title-mobile" class="caret-title mobile">
                            <span class="caret-container">
                                <div class="picto-caret-down"></div>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="share-container" :style="_isShared || _isClient ? {right: '5px'} : {}" v-on:click="openSharingModal(project)"><span class="picto-share-project"></span></div>
                <div class="delete-container" v-show="(!_isShared && !_isClient && project.id != 1)" v-on:click="Project._delete(project)"><span class="picto-delete-project"></span></div>
            </h2>
            <p class="text title-indent last-edited">{{ $t('last-project-edit', {updateDate: projectLastUpdate}) }}</p>
            <div class="kanban" v-show="showKanban">
                <div class="kanban-header">
                    <div class="col-md-4 cta-container" v-on:click="changeKanban('waiting')"><p id="cta-waiting" class="cta waiting unactive">{{ $t('waiting-project') }} ({{ kanban && kanban.WAITING && kanban.WAITING.length > 0 ? kanban.WAITING.length : 0 }})</p></div>
                    <div class="col-md-4 cta-container" v-on:click="changeKanban('in_progress')"><p id="cta-in-progress" class="cta in-progress">{{ $t('current-project') }} ({{ kanban && kanban.IN_PROGRESS && kanban.IN_PROGRESS.length > 0 ? kanban.IN_PROGRESS.length : 0 }})</p></div>
                    <div class="col-md-4 cta-container" v-on:click="changeKanban('closed')"><p id="cta-closed" class="cta closed unactive">{{ $t('closed-project') }} ({{ kanban && kanban.CLOSED && kanban.CLOSED.length > 0 ? kanban.CLOSED.length : 0 }})</p></div>
                </div>
                <div class="kanban-content">
                    <div v-if="activeKanban.length > 0">
                        <a :href="project.url" v-for="project in activeKanban">
                            <p class="has-project">
                                <a :href="project.url">{{ project.name }}</a>
                                <span><a :href="project.url">{{ $t('see') }}</a></span>
                            </p>
                        </a>
                    </div>
                    <div class="has-no-project" v-else>
                        <div class="has-no-project-content">
                            <div style="height: 30px; margin-bottom: 10px;">
                                <span class="picto-open-folder"></span>
                            </div>
                            <div class="has-no-project-text" v-if="kanbanType == 'waiting'">
                                <h2>Aucun projet en attente <br> pour l'instant</h2>
                                Un projet en attente est<br>
                                un projet qui ne contient<br>
                                pas de tâches en cours.
                            </div>
                            <div class="has-no-project-text" v-else-if="kanbanType == 'closed'">
                                <h2>Aucun projet clôturé <br> pour le moment.</h2>
                                Clôturer toutes les<br>
                                tâches d'un projet pour<br>
                                le voir s'afficher ici.
                            </div>
                            <div class="has-no-project-text" v-else>
                                <h2>Rien pour le moment.</h2>
                                Créez de nouveau projet pour<br>
                                pour les voir s'afficher dans cette<br>
                                fenêtre
                            </div>
                        </div>
                    </div>
    
                </div>
            </div>
        </div>
        <!-- ./Header -->

        <div class="row" v-on:click="isShow = !isShow" style="cursor:pointer; width: 100%; margin:auto;">
            <div class="col-md-12 more-infos">
                <div style="width:12px; height: 12px; margin-right: 10px; margin-bottom: 2px;"><span class="picto-information"></span></div><span>{{ $t('see-project-informations') }}</span>
            </div>
        </div>
        <!-- Infos + Exports -->
        <div class="row custom-row main-infos" v-show="isShow">
            <div class="col-md-4">
                <div class="row more-infos-left-lead">
                    <p>{{ $t('project-created-by') }} <strong>{{ project.creator ? project.creator : 'Onboarding' }}</strong></p>
                    <p>Le <span style="color:white; font-weight:600;">{{ project.reformat_created_at }}</span></p>
                </div>
                <div class="row more-infos-left-talents">
                    <div v-if="talents.length == 0" style="color:white; font-weight:600;">Pas de participant</div>
                    <div v-else class="avatar-user-list" style="display:flex; align-items: center;">
                        <span>{{ $t('project-members') }}: </span>
                        <ul>
                            <li v-for="talent in talents" class="tooltiped">
                                <img class="project-talents-img" v-bind:src="talent.profilePicture" v-bind:alt="talent.name" v-if="talent.profilePicture"/>
                                <div class="profile-image_register profile-register" v-else>
                                    <div class="image_register register_profile">
                                        <span class="user-initials">{{ talent.initials }}</span>
                                    </div>
                                </div>
                                <span class="tooltiptext">{{ talent.name }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 more-infos-center">
                <a class="project-detail__main-info__button" v-show="!_isShared && !_isClient" v-on:click="editProject(project, 1)">
                    <span class="icon icon-edit" style="color: #7665A7;"></span>
                </a>
                <div class="project-infos row">
                    <div class="col-sm-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-client-name"></span></div>
                    </div>
                    <div class="col-sm-11">
                        <p><strong>{{ $t('project-client-name') }}</strong></p>
                        <p v-if="project.client && project.client.name">{{ project.client.name }}</p>
                        <p v-else>{{ $t('to-fill') }}</p>
                    </div>
                </div>
                <div class="project-infos row">
                    <div class="col-sm-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-client-email"></span></div>
                    </div>
                    <div class="col-sm-11">
                        <p><strong>{{ $t('mail') }}</strong></p>
                        <p v-if="project.client_email"><a :href="'mailto:' + project.client_email">{{ project.client_email }}</a></p>
                        <p v-else>{{ $t('to-fill') }}</p>
                    </div>
                </div>
                <div class="project-infos row">
                    <div class="col-sm-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-client-phone"></span></div>
                    </div>
                    <div class="col-sm-11">
                        <p><strong>{{ $t('phone') }}</strong></p>
                        <p v-if="project.client_phone">{{ project.client_phone }}</p>
                        <p v-else>{{ $t('to-fill') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 more-infos-right">
                <a class="project-detail__main-info__button" v-show="!_isShared && !_isClient" v-on:click="editProject(project, 2)">
                    <span class="icon icon-edit" style="color: #7665A7;"></span>
                </a>
                <div class="project-infos row">
                    <div class="col-sm-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-project-deadline"></span></div>
                    </div>
                    <div class="col-sm-11">
                        <p><strong>{{ $t('project-deadline') }}</strong></p>
                        <p v-if="project.formated_date_deadline">{{ project.formated_date_deadline }}</p>
                        <p v-else>{{ $t('empty-field') }}</p>
                    </div>
                </div>
                <div class="project-infos row" v-if="project.category">
                    <div class="col-sm-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-project-category"></span></div>
                    </div>
                    <div class="col-sm-11">
                        <p><strong>{{ $t('category') }}</strong></p>
                        <p v-if="project.category.name">{{ $t(project.category.name) }}</p>
                        <p v-else>{{ $t('empty-field') }}</p>
                    </div>
                </div>
                <div class="project-infos row">
                    <div class="col-sm-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-project-production"></span></div>
                    </div>
                    <div class="col-sm-11">
                        <p><strong>{{ $t('production') }}</strong></p>
                        <p v-if="project.production">{{ project.production }}</p>
                        <p v-else>{{ $t('to-fill') }}</p>
                    </div>
                </div>
            </div>
            <!-- ./Infos -->
        </div>
        <div class="row custom-row main-infos main-infos-mobile" v-show="showMobileInfos">
            <div class="col-md-12">
                <div class="row more-infos-left-lead">
                    <div class="col-2">
                        <img :src="'/upload/avatars/' + project.creatorAvatar" />
                    </div>
                    <div class="col">
                        <p>{{ $t('project-created-by') }} <strong>{{ project.creator ? project.creator : 'Onboarding' }}</strong></p>
                        <p>Le <span style="color:white; font-weight:600;">{{ project.reformat_created_at }}</span></p>
                    </div>
                </div>
                <div class="share-container" :style="_isShared || _isClient ? {right: '5px'} : {}" v-on:click="copyShareLink()"><span class="picto-share-project"></span></div>
            </div>
            <div class="col-md-12 more-infos-center">
                <a class="project-detail__main-info__button" v-show="!_isShared && !_isClient" v-on:click="editProject(project, 1)">
                    <span class="icon icon-edit" style="color: #7665A7;"></span>
                </a>
                <div class="project-infos row">
                    <div class="col-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-client-name"></span></div>
                    </div>
                    <div class="col-10">
                        <p><strong>{{ $t('project-client-name') }}</strong></p>
                        <p v-if="project.client && project.client.name">{{ project.client.name }}</p>
                        <p v-else>{{ $t('to-fill') }}</p>
                    </div>
                </div>
                <div class="project-infos row">
                    <div class="col-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-client-email"></span></div>
                    </div>
                    <div class="col-10">
                        <p><strong>{{ $t('mail') }}</strong></p>
                        <p v-if="project.client_email"><a :href="'mailto:' + project.client_email">{{ project.client_email }}</a></p>
                        <p v-else>{{ $t('to-fill') }}</p>
                    </div>
                </div>
                <div class="project-infos row">
                    <div class="col-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-client-phone"></span></div>
                    </div>
                    <div class="col-10">
                        <p><strong>{{ $t('phone') }}</strong></p>
                        <p v-if="project.client_phone">{{ project.client_phone }}</p>
                        <p v-else>{{ $t('to-fill') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-12 more-infos-right">
                <a class="project-detail__main-info__button" v-show="!_isShared && !_isClient" v-on:click="editProject(project, 2)">
                    <span class="icon icon-edit" style="color: #7665A7;"></span>
                </a>
                <div class="project-infos row">
                    <div class="col-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-project-deadline"></span></div>
                    </div>
                    <div class="col-10">
                        <p><strong>{{ $t('project-deadline') }}</strong></p>
                        <p v-if="project.formated_date_deadline">{{ project.formated_date_deadline }}</p>
                        <p v-else>{{ $t('empty-field') }}</p>
                    </div>
                </div>
                <div class="project-infos row" v-if="project.category">
                    <div class="col-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-project-category"></span></div>
                    </div>
                    <div class="col-10">
                        <p><strong>{{ $t('category') }}</strong></p>
                        <p v-if="project.category.name">{{ $t(project.category.name) }}</p>
                        <p v-else>{{ $t('empty-field') }}</p>
                    </div>
                </div>
                <div class="project-infos row">
                    <div class="col-1">
                        <div style="width: 30px; height: 30px;"><span class="picto-project-production"></span></div>
                    </div>
                    <div class="col-10">
                        <p><strong>{{ $t('production') }}</strong></p>
                        <p v-if="project.production">{{ project.production }}</p>
                        <p v-else>{{ $t('to-fill') }}</p>
                    </div>
                </div>
            </div>
            <!-- ./Infos -->
        </div>
        <!-- ./Infos + Exports -->

    </div>
</template>

<script>
    export default {
        props: ['_project', '_talents', '_isShared', '_isClient'],

        data() {
            return {
                activeKanban: [],
                project: this._project,
                talents: this._talents,
                isShow: false,
                showKanban: false,
                projectLastUpdate: false,
                kanbanType: 'in_progress',
                showMobileInfos: false,
                kanban: {'WAITING': [], 'PROGRESS' : [], 'CLOSED': []},
            }
        },

        created() {
            if (this._project) {
                this.project = this._project;
                this.project.reformat_updated_at = Vue.prototype.Global._reformatDate(this.project.updated_at, true);
                this.project.reformat_created_at = Vue.prototype.Global._reformatDate(this.project.created_at);
                this.project.reformat_deadline = Vue.prototype.Global._reformatDate(this.project.date_deadline);
            }
        },

        mounted() {
            this.projectLastUpdate = this.project.reformat_updated_at;
            if (this.project.id == 1) {
                this.talents = [
                    {'avatar':'user.jpg','name':'Jérémy', 'profilePicture': '/upload/avatars/user.jpg'},
                    {'avatar':'user.jpg','name':'Titouan', 'profilePicture': '/upload/avatars/user.jpg'},
                    {'avatar':'user.jpg','name':'Youssouph', 'profilePicture': '/upload/avatars/user.jpg'},
                ];
                let date = new Date();
                let day = date.getDate();
                let month = date.getMonth() + 1;
                let year = date.getFullYear();
                this.projectLastUpdate = Vue.prototype.Global._reformatDate(month + '-' + day + '-' + year);
            }

            this.$bus.$on('PROJECT_ADD_OR_UPDATE', (project) => {
                this.project = project.datas;
                this.project.formated_date_deadline = Vue.prototype.Global._convertDate(this.project.date_deadline);
            })

            this.kanban = this.getKanban();
        },

        methods: {
            /**
             * Edit project
             * @param  Project projects
             * @return void
             */
            editProject(project, step){
                this.$bus.$emit('ACTION_CHANGED', {
                    action: 'SET_PROJECT',
                    project: project,
                    step: step
                });
            },
            openSharingModal(project) {
                this.$bus.$emit('CONFIRM', {
                    title: 'Partager le projet avec des personnes',
                    text: 'Obtenir le lien de partage',
                    confirmText: 'En partageant ce lien, les utilisateurs pourront ajouter des médias et des liens,',
                    confirmText2: 'mais n\'accèderont pas à la messagerie.',
                    sharedProject: true,
                    shareLink: project.share_link ? project.share_link : '',
                    button_2: {
                        title: 'Quitter',
                    }
  		        });
            },
            openKanban() {
                if (!this._isClient && !this._isShared) {
                    this.showKanban = !this.showKanban;
                    let titleProject = document.querySelector("#title-project");
                    let titleCaret = document.querySelector("#caret-title");
                    let toclose = document.getElementById('toclose');
                    if (this.showKanban) {
                        toclose.style.display = "block";
                    } else {
                        toclose.style.display = "none";
                    }
                    if (titleProject && titleCaret) {
                        if (this.showKanban) {
                            if (!titleProject.classList.contains('active') && !titleCaret.classList.contains('active')) {
                                titleProject.classList.add('active');
                                titleCaret.classList.add('active');
                            }
                        } else {
                            if (titleProject.classList.contains('active') && titleCaret.classList.contains('active')) {
                                titleProject.classList.remove('active');
                                titleCaret.classList.remove('active');
                            }
                        }
                    }
                }
            },
            changeKanban(step) {
                let waitingCTA = document.querySelector("#cta-waiting");
                let inProgressCTA = document.querySelector("#cta-in-progress");
                let closedCTA = document.querySelector("#cta-closed");
                switch(step) {
                    case 'waiting':
                        this.activeKanban = this.kanban && this.kanban.WAITING ? this.kanban.WAITING : [];
                        this.kanbanType = 'waiting';
                        waitingCTA.classList.remove('unactive');
                        inProgressCTA.classList.add('unactive');
                        closedCTA.classList.add('unactive');
                        break;
                    case 'in_progress':
                        this.activeKanban = this.kanban && this.kanban.IN_PROGRESS ? this.kanban.IN_PROGRESS : [];
                        this.kanbanType = 'in_progress';
                        waitingCTA.classList.add('unactive');
                        inProgressCTA.classList.remove('unactive');
                        closedCTA.classList.add('unactive');
                        break;
                    case 'closed':
                        this.activeKanban = this.kanban && this.kanban.CLOSED ? this.kanban.CLOSED : [];
                        this.kanbanType = 'closed';
                        waitingCTA.classList.add('unactive');
                        inProgressCTA.classList.add('unactive');
                        closedCTA.classList.remove('unactive');
                        break;
                }
            },
            addActive(active) {
                let titleProject = document.getElementById('title-project');
                let caretTitle = document.getElementById('caret-title');
                if (active && titleProject && caretTitle && !titleProject.classList.contains('active') && !caretTitle.classList.contains('active')) {
                    titleProject.classList.add('active');
                    caretTitle.classList.add('active');
                } else {
                    titleProject.classList.remove('active');
                    caretTitle.classList.remove('active');
                }
            },
            addActiveMobile() {
                this.showMobileInfos = !this.showMobileInfos;
            },
            copyShareLink() {
                if (this.project.share_link) {
                    navigator.clipboard.writeText(this.project.share_link).then(() => {
                        this.$bus.$emit('ACTION_CHANGED', {
                            action: 'CONGRATS',
                            type: 'COPY_SHARE_LINK'
                        }); 
                    });
                }
            },
            async getKanban() {
                var params = {};
                await axios.get("/api/projects/kanban", {
                    params: params
                }).then((res) => {
                    if (res.statusText && res.statusText == 'OK' && res.data) {
                        this.kanban = res.data;
                        if (this.kanban) {
                            this.activeKanban = this.kanban.IN_PROGRESS;
                        }
                    }
                }).catch((error) => console.log(error));
            },
        }
    }
</script>