<!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
<template>
    <!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
    <div v-if="_type == 'TASK_DELETE'" class="notif-wrapper">
        <div class="notif-txt">
            <span>La tâche a bien été supprimée !</span>
            <span id="timer">{{ timer }}s</span>
        </div>
    </div>
    <div v-else-if="_type == 'TASK_ADD_OR_UPDATE'" class="notif-wrapper">
        <div class="notif-txt">
            <span v-if="_element && _element.id"
                >La tâche a bien été modifiée !</span
            >
            <span v-else>La tâche a bien été ajoutée !</span>
            <span id="timer">{{ timer }}s</span>
        </div>
    </div>
    <div v-else-if="_type == 'TASK_CLOSE'" class="notif-wrapper">
        <div class="notif-txt">
            <span>La tâche a bien été clôturée !</span>
            <span id="timer">{{ timer }}s</span>
        </div>
    </div>
    <div v-else-if="_type == 'TASK_OVER'" class="notif-wrapper">
        <div class="notif-txt-with-btn">
            <span
                >Voulez vous clôturer cette tâche ? (Elle le sera
                automatiquement à la fin de la journée)</span
            >
            <div>
                <span
                    class="btn-notif"
                    v-on:click="Task._close(_element, true)"
                    >Clôturer</span
                >
                <span class="btn-notif" v-on:click="ignoreClosingTask()">Ne pas clôturer</span>
            </div>
        </div>
    </div>
    <div class="notif-wrapper" v-else-if="_type == 'APPOINTMENT_ADD_OR_UPDATE'">
        <div class="notif-txt">
            <span v-if="_element && _element.id"
                >Le RDV a bien été modifié !
            </span>
            <span v-else>Le RDV a bien été ajouté !</span>
            <span id="timer">{{ timer }}s</span>
        </div>
    </div>
    <div v-else-if="_type == 'APPOINTMENT_DELETE'" class="notif-wrapper">
        <div class="notif-txt">
            <span>Le RDV a bien été supprimé !</span>
            <span id="timer">{{ timer }}s</span>
        </div>
    </div>
    <div
        v-else-if="
            _type == 'ADD_TASK_ONE' ||
                'MULTIPLE_ADD_TASK' ||
                'DELETE_TASK' ||
                'EDIT_TASK' ||
                'MULTIPLE_DELETE_TASK' ||
                'MUTLIPLE_ADD_MEDIA' ||
                'ADD_MEDIA' ||
                'DELETE_MEDIA' ||
                'ADD_EXXPORT' ||
                'EDIT_EXXPORT' ||
                'DELETE_EXXPORT' ||
                'ADD_DRIVE' ||
                'DELETE_DRIVE' ||
                'FILE_TOO_BIG' ||
                'COPY_SHARE_LINK' ||
                'EDIT_PROJECT' ||
                'GROUP_EDIT' ||
                'GROUP_DELETE' ||
                'GROUP_ADD' ||
                'DELETE_PORTFOLIO'||
                'NOT_CLOSE' ||
                'PROJECT_MOVED'
        "
        class="notif-wrapper"
        :style="_type == 'FILE_TOO_BIG' ? 'background: #D2004C' : '' || _type === 'FILE_ERROR'|| _type === 'HUGE_FILE' || _type ==='FILE_TYPE' || _type==='NOT_CLOSE' ? 'background:#FF0000' : '' "
    >
        <div class="notif-txt">
            <span v-if="_type == 'MULTIPLE_ADD_TASK'"
                >Les tâches ont bien été ajouté !</span
            >
            <span v-else-if="_type == 'ADD_TASK_ONE'"
                >La tâche a bien été ajouté !</span
            >
            <span v-else-if="_type == 'DELETE_TASK'"
                >La tâche a bien été supprimé !</span
            >
            <span v-else-if="_type == 'DELETE_PORTFOLIO'"
            >Le projet a bien été supprimé !</span
            >
            <span v-else-if="_type == 'HUGE_FILE'"
            >La photo excède 2 mo.</span
            >
            <span v-else-if="_type == 'FILE_TYPE'"
            >Le fichier doit être de type: png,jpg,jpeg</span
            >
            <span v-else-if="_type == 'FILE_ERROR'"
            >Attention votre fichier est inapproprié ou mal formaté !</span
            >
            <span v-else-if="_type == 'EDIT_TASK'"
                >La tâche a bien été modifié !</span
            >
            <span v-else-if="_type == 'MULTIPLE_DELETE_TASK'"
                >Les tâches ont bien été supprimé !</span
            >
            <span v-else-if="_type == 'ADD_MEDIA'"
                >Le fichier a bien été ajouté !</span
            >
            <span v-else-if="_type == 'DELETE_MEDIA'"
                >Le fichier a bien été supprimé !</span
            >
            <span v-else-if="_type == 'ADD_EXXPORT'"
                >L'élément a bien été ajouté !</span
            >
            <span v-else-if="_type == 'EDIT_EXXPORT'"
                >Les éléments ont bien été modifié !</span
            >
            <span v-else-if="_type == 'NOT_CLOSE'"
            >Vous ne pourrez pas clôturer cette mission tant que le freelance n'aura pas remplis ses informations bancaires !</span
            >
            <span v-else-if="_type == 'DELETE_EXXPORT'"
                >L'élément a bien été supprimé !</span
            >
            <span v-else-if="_type == 'DELETE_DRIVE'"
                >Le lien a bien été supprimé !</span
            >
            <span v-else-if="_type == 'ADD_DRIVE'"
                >Le lien a bien été ajouté !</span
            >
            <span v-else-if="_type == 'FILE_TOO_BIG'"
                >L'élément n'a pas pu être ajouté car il dépasse 4 Mo</span
            >
            <span v-else-if="_type == 'COPY_SHARE_LINK'"
                >Lien copié !</span
            >
            <span v-else-if="_type == 'TASK_MOVED'"
                >La tâche a bien été déplacée !</span
            >
            <span v-else-if="_type == 'TASK_UNMOVED'"
                >La tâche n'a pas pu être déplacée !</span
            >
            <span v-else-if="_type == 'UPDATE_EXPORT'"
                >Le livrable a été mis à jour !</span
            >
            <span v-else-if="_type == 'EDIT_PROJECT'"
                >Le projet a été modifié avec succès !</span
            >
            <span v-else-if="_type == 'GROUP_ADD'"
                >Le groupe a été ajouté !</span
            >
            <span v-else-if="_type == 'GROUP_EDIT'"
                >Le groupe a été édité !</span
            >
            <span v-else-if="_type == 'GROUP_DELETE'"
                >Le groupe a été supprimé !</span
            >
            <span v-else-if="_type == 'PROJECT_MOVED'"
                >Le projet a bien été déplacé !</span
            >
            <span id="timer">{{ timer }}s</span>
        </div>
    </div>
    <div v-else-if="_type == 'DELETE_EXXPORT'" class="notif-wrapper">
        <div class="notif-txt">
            <span>L'élément a bien été supprimé !</span>
            <span id="timer">{{ timer }}s</span>
        </div>
    </div>
    <!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
    <div v-else class="congrats">
        <!-- <span class="congrats__overlay" v-on:click="close()"></span> -->
        <div class="popup-intro text-center mb-30" v-if="_element">
            <h2 class="popup-maintitle">{{ _element.name }}</h2>
        </div>

        <div class="block has-bg">
            <!-- <div class="block__bck-image">
	        <img src="/images/project-created.jpg" class="img-fluid block__image" alt="" width="" height="">
	    </div> -->
            <div class="congrats__content js-congrats-content text-center">
                <h3 class="medium-title mb-30">
                    <template v-if="_type == 'PROJECT'">
                        <span v-if="_element && _element.id"
                            >Le projet a été modifié avec succès !</span
                        >
                        <span v-else>Le projet a été créé avec succès !</span>
                    </template>
                    <template v-if="_type == 'TASK'">
                        Vos tâches ont été créées avec succès !
                    </template>
                    <template v-if="_type == 'APPOINTMENT'">
                        <span v-if="_element && _element.id"
                            >Votre rendez-vous a été modifié avec succès !</span
                        >
                        <span v-else
                            >Votre rendez-vous a été créé avec succès !</span
                        >
                    </template>
                    <template v-if="_type == 'TALENT'">
                        <span v-if="_element && _element.id"
                            >Ce talent a été modifié avec succès !</span
                        >
                        <span v-else>Ce talent a été créé avec succès !</span>
                    </template>
                    <template v-if="_type == 'EXPORT'">
                        Vos exports ont été créés avec succès !
                    </template>
                </h3>

                <p class="congrats__text text c-main-grey">
                    <template v-if="_type == 'PROJECT'"
                        >Vous trouverez le projet dans la page
                        <strong>Projet</strong>.</template
                    >
                    <template v-if="_type == 'TASK'"
                        >Ces tâches ont été créées dans la page
                        <strong>Dashboard</strong> et dans la page
                        <strong>Planning</strong>.</template
                    >
                    <template v-if="_type == 'APPOINTMENT'">
                        <span v-if="_element && _element.id"
                            >Le rendez-vous a été modifié dans la page
                            <strong>Dashboard</strong>.</span
                        >
                        <span v-else
                            >Le rendez-vous a été crée dans la page
                            <strong>Dashboard</strong>.</span
                        >
                    </template>
                    <template v-if="_type == 'TALENT'">
                        <span v-if="_element && _element.id"
                            >Ce talent a bien été modifié dans la page
                            <strong>Talents</strong>.</span
                        >
                        <span v-else
                            >Ce talent a bien été créé dans la page
                            <strong>Talents</strong>.</span
                        >
                    </template>
                    <template v-if="_type == 'EXPORT'">Vous trouverez vos exports dans le
                    <strong>détail de votre projet</strong>.</template>
                </p>

                <span class="picto picto-big-check"></span>
            </div>

            <div v-if="_more" class="create-more text-center is-hidden">
                <p class="text c-main-grey mb-30">
                    Vous pouvez aussi créer
                    <strong>un rendez-vous, une ou plusieurs tâche(s)</strong><br/>
                    et <strong>ajouter un ou plusieurs export(s)</strong> pour
                    ce projet.
                </p>
                <div class="row">
                    <div class="col-md-6 mb-15">
                        <div v-on:click="addAppointment()" class="box-button">
                            <span class="picto picto-calendar"></span
                            >Créer<br />
                            <strong>un rendez-vous</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-15">
                        <div v-on:click="addTask()" class="box-button">
                            <span class="picto picto-task"></span>Créer<br />
                            <strong>une ou plusieurs tâche(s)</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-15">
                        <div v-on:click="addExport()" class="box-button">
                            <span class="picto picto-play"></span>Ajouter<br />
                            <strong>un ou plusieurs livrable(s)</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-15">
                        <div v-on:click="addProject()" class="box-button">
                            <span class="picto picto-projects"></span
                            >Créer<br />
                            <strong>un nouveau projet</strong>
                        </div>
                    </div>
                </div>

                <button
                    v-on:click="close()"
                    class="action-link is-white popup-button is-next">
                    Passer et retourner sur la page
                    <span class="icon icon-long-arrow"></span>
                </button>
            </div>
        </div>

        <!-- <button v-on:click="close()">Fermer</button> -->
    </div>
</template>

<script>
export default {
    props: ["_type", "_element", "_more"],

    data() {
        return {
            timer: 5,
            myVar: setInterval(this.myTimer, 1000),
            reset: false,
        };
    },

    created() {},

    mounted() {
        this.closeOnClick();
        this.countDown();
        this.reset = false;
        this.$bus.$on('RESET_CONGRATS_TIMER', () => {
            this.reset = true;
        });
        if (this._type == "PROJECT") {
            setTimeout(() => {
                if (this._element.id) {
                    this.$bus.$emit("ACTION_CHANGED", {
                        action: null
                    });
                } else {
                    $(".js-congrats-content").hide();
                    $(".create-more").fadeIn("300");
                }
            }, 2000);
        }
    },

    methods: {
        countDown() {
            this.myVar;
        },
        myTimer() {
            if (this._type == 'TASK_OVER') {
                this.timer = 5000;
                if (this._element.isLast) {
                    this.timer = 5;
                }
            }
            this.timer = this.timer - 1;
            if (this.timer == 0 && !this.reset) {
                clearInterval(this.myVar);
                this.close();
            }
        },
        addAppointment() {
            this.$bus.$emit("ACTION_CHANGED", {
                action: "SET_APPOINTMENT"
            });
        },
        addTask() {
            this.$bus.$emit("ACTION_CHANGED", {
                action: "SET_TASK",
                project: this._element,
                goback: false,
                type: 'ADD'
            });
        },
        addExport() {
            this.$bus.$emit("ACTION_CHANGED", {
                action: "SET_EXPORT",
                project: this._element
            });
        },

        addProject() {
            this.$bus.$emit("ACTION_CHANGED", {
                action: "SET_PROJECT"
            });
        },
        closeOnClick() {
            $("document").on("click", "body", function() {
                this.$bus.$emit("ACTION_CHANGED", {
                    action: null
                }); // Close modal
            });
        },
        close() {
            this.$bus.$emit("ACTION_CHANGED", {
                action: null
            }); // Close modal
        },
        ignoreClosingTask() {
            this.close();
            this.$bus.$emit("RESET_TASK_TO_CLOSE");
        }
    }
};
</script>
