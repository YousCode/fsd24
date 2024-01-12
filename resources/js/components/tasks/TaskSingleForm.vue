<template>
    <div
        style="position: relative;
top: 50%;
-webkit-transform: translateY(-50%);
-ms-transform: translateY(-50%);
transform: translateY(-50%);
display: flex;"
    >
        <!-- <div class="popup-intro text-center mb-30">
      <h2 class="popup-maintitle">
     		<span v-if="task.id">Modifier la tâche</span>
     		<span v-else>Créer une tâche</span>
      </h2>
    </div> -->
        <form
            method="POST"
            v-on:submit.prevent="setTask"
            rel="form__task_single"
            class="form__task_single"
        >
            <input type="hidden" v-model="task.id" />

            <div class="popup-box">
                <button
                    type="button"
                    class="button icon-button"
                    v-on:click="Task._delete(task)"
                >
                    <span class="icon icon-delete"></span>
                </button>
                <div v-if="task.id" class="form-box">
                    <p class="form-label is-light mb-20">
                        Tâche créée le :<br />
                        <strong>{{
                            moment(task.created_at).format("D MMMM YYYY")
                        }}</strong>
                    </p>
                    <p>
                        <strong>Par : {{ task.created_by }}</strong>
                    </p>
                </div>
                <div class="form-box">
                    <label for="" class="form-label">Type de tâche :</label>
                    <div class="filter-item filter-select has-picto">
                        <span class="filter-picto picto-task"></span>
                        <select
                            v-model="task.task_type_id"
                            v-select2
                            class="filter-field js-task-type-data"
                            id="task-single-type"
                            required
                        ></select>
                    </div>
                </div>
                <div class="form-box">
                    <label for="" class="form-label">Assignée à :</label>
                    <div class="filter-item filter-select has-picto">
                        <span class="filter-picto picto-talent"></span>
                        <select
                            v-model="task.talent_id"
                            v-select2
                            class="filter-field js-talent-data"
                            id="task-single-talent"
                            required
                        ></select>
                    </div>
                </div>
                <div class="form-box">
                    <label for="" class="form-label">Note :</label>
                    <div
                        class="filter-item has-picto notes__form-box js-toggle-wrapper"
                    >
                        <span class="filter-picto picto-note"></span>
                        <div
                            class="form-field js-toggle-button notes__input"
                            v-bind:class="{ 'has-value': task.note }"
                        >
                            <span
                                class="form-field-placeholder js-notes-placeholder"
                                >Ajouter une note</span
                            >
                            <span class="notes__field-value js-notes-value">{{
                                task.note
                            }}</span>
                        </div>
                        <textarea
                            type="text"
                            placeholder="Ex : Travailler sur les retakes pour v3"
                            v-model="task.note"
                            class="form-field notes__textarea js-toggle-content js-notes-textarea no-caps"
                            maxlength="300"
                        ></textarea>
                    </div>
                </div>
                <div class="form-box">
                    <label for="" class="form-label">Date :</label>
                    <div class="filter-item has-picto">
                        <span class="filter-picto picto-calendar"></span>
                        <v-date-picker
                            v-model="task.date"
                            mode="range"
                            :popover="{ visibility: 'click' }"
                            class="filter-field-date"
                            color="kpurple"
                        ></v-date-picker>
                    </div>
                </div>
                <div class="form-box">
                    <label for="" class="form-label">Statut :</label>
                    <select
                        class="filter-field js-status-data"
                        name="status"
                        v-select2
                        v-model="task.status"
                    >
                        <option value="PROGRESS">En cours</option>
                        <option value="CLOSED">Clôturé</option>
                    </select>
                </div>
                <div class="form-box">
                    <button type="submit" class="button is-gradient w-100">
                        Appliquer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: ["_task", "_project", "_taskTalent"],

    data() {
        return {
            task: {
                id: null,
                name: "",
                talent_id: null,
                talent_name: "",
                task_type_id: null,
                task_type_name: "",
                note: null,
                taskTalent: null,
                date: {
                    start: new Date(),
                    end: new Date()
                }
            }
        };
    },

    created() {
        if (this._task.id) {
            this.task = this._task;
            this.task.task_type_id = this.task.taskTypes[0].id;
            //this.task.created_at = moment(this._task.created_at).format("D MMMM YYYY");
            this.task.task_type_name = this.task.taskTypes[0].name;
            if (this.task.talents) {
                this.task.talent_id = this.task.talents[0].id;
                this.task.talent_name = this.task.talents[0].name;
            } else if (this.taskTalent) {
                this.task.talent_id = this.taskTalent.id;
                this.task.talent_name = this.taskTalent.name;
            }
            this.task.date = {
                start: new Date(this.task.start_date),
                end: new Date(this.task.end_date)
            };
        }
    },

    mounted() {
        this.setTaskTypeSelect();
        this.setTalentSelect();
        this.setStatusSelect();
        this.triggerSelect2();
    },
    methods: {
        setTask() {
            var project_id = this.task.project_id
                ? this.task.project_id
                : this._project.id;
            axios
                .post("/api/task", {
                    project: project_id,
                    tasks: [this.task]
                })
                .then(res => {
                    if (res.success === false) {
                        // Error
                    } else {
                        this.$bus.$emit("TASK_ADD_OR_UPDATE", res.data); // Emit add or update talent event
                        this.$bus.$emit("PLANNING_TO_SCROLL");
                        this.$bus.$emit("ACTION_CHANGED", {
                            action: "CONGRATS",
                            type: "EDIT_TASK"
                        });
                        Vue.prototype.Task._get(project_id);
                    }
                })
                .catch(error => console.log(error));
        },

        /**
         * Set TaskType select2
         */
        setTaskTypeSelect() {
            $(() => {
                $(".form__task_single .js-task-type-data").select2({
                    minimumResultsForSearch: -1,
                    dropdownParent: $(".task-popin").length
                        ? $(".task-popin")
                        : null,
                    placeholder: "Sélectionner un type de tâche",
                    ajax: {
                        url: "/api/task-type",
                        language: {
                            searching: function() {
                                return "Chargement...";
                            }
                        },
                        processResults: function(data) {
                            var data = $.map(data.datas, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: data
                            };
                        }
                    }
                });

                $(".js-task-type-data").on("select2:select", e => {
                    this.task.task_type_name = e.params.data.name;
                });
            });
        },

        /**
         * Set TaskType select2
         */
        setTalentSelect() {
            $(() => {
                $(".form__task_single .js-talent-data").select2({
                    dropdownCssClass: "has-search is-grey",
                    dropdownParent: $(".task-popin").length
                        ? $(".task-popin")
                        : null,
                    placeholder: "Sélectionner un talent",
                    language: {
                        searching: function() {
                            return "Chargement...";
                        }
                    },
                    ajax: {
                        url: "/api/talent",
                        processResults: function(data) {
                            var data = $.map(data.datas, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: data
                            };
                        }
                    }
                });

                $(".js-talent-data").on("select2:select", e => {
                    this.task.talent_name = e.params.data.name;
                });
            });
        },

        /**
         * Set status select2
         */
        setStatusSelect() {
            $(() => {
                $(".form__task_single .js-status-data").select2({
                    minimumResultsForSearch: Infinity,
                    placeholder: "Statut"
                });

                $(".form__task_single .js-status-data").on(
                    "select2:select",
                    e => {
                        this.contextMenuCallback(false, this.filters);
                    }
                );
                $(".form__task_single .js-status-data").on(
                    "select2:unselect",
                    e => {
                        this.contextMenuCallback(false, this.filters);
                    }
                );
            });
        },

        /**
         * Fill select2
         * */
        triggerSelect2() {
            this.triggerTaskTypeSelect2();
            this.triggerTalentSelect2();
        },

        /**
         * Fill the task type in the select2
         */
        triggerTaskTypeSelect2() {
            let taskTypeSelect2 = $("#task-single-type");

            if (this.task.task_type_id) {
                let option = new Option(
                    this.task.task_type_name,
                    this.task.task_type_id,
                    true,
                    true
                );
                taskTypeSelect2.append(option).trigger("change");
            }
        },

        /**
         * Fill the talent in the select2
         */
        triggerTalentSelect2() {
            let talentSelect2 = $("#task-single-talent");

            if (this.task.talents) {
                let option = new Option(
                    this.task.talents[0].name,
                    this.task.talents[0].id,
                    true,
                    true
                );
                talentSelect2.append(option).trigger("change");
            }
        }
    }
};
</script>
