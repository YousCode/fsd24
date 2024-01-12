<template>
    <transition name="bounce">
        <div class="popup js-popup editProject" v-on:click.stop>
            <div class="popup-content">
                <div class="popup-box">
                    <p class="text mb-15">
                        <strong v-if="this.step == 1">Modifier les informations du responsable client</strong>
                        <strong v-else>Modifier les informations du projet</strong>
                    </p>
                    <div class="row" v-show="this.step == 1">
                        <!-- content -->
                        <div class="create-project-wrapper">
                          <div class="create-project">
                            <div class="create-project-field">
                                <label><i class="icon icon-project"></i>Nom</label>
                                <input class="form-field js-required js-search-clients" required v-model="filters.name" type="text" :placeholder="$t('to-fill')" :style="{width: '100%'}"/>
                                <!-- <select v-model="project.client_name" v-select2 id="form-clients-select2" class="form-field js-clients-data js-required" name="client_name" placeholder="A définir"  :style="{width: '100%'}"></select> -->
                            </div>
                            <div class="create-project-field">
                                <label><i class="icon icon-email"></i>Adresse email</label>
                                <input class="form-field js-required" required v-model="project.client_email" type="text" :placeholder="$t('to-fill')" :style="{width: '100%'}"/>
                            </div>
                            <div class="create-project-field">
                                <label><i class="icon icon-phone"></i>Numéro de téléphone</label>
                                <input class="form-field js-required" required v-model="project.client_phone" type="text" :placeholder="$t('to-fill')" :style="{width: '100%'}"/>
                            </div>
                          </div>
                        </div>
                        <!-- content -->
                    </div>
                    <div class="row" v-show="this.step == 2">
                        <!-- content -->
                        <div class="create-project-wrapper">
                            <div class="create-project">
                                <div class="create-project-field">
                                    <label><i class="icon icon-project"></i>Nom de votre projet vidéo*</label>
                                    <input
                                        class="form-field js-required"
                                        required
                                        v-model="project.name"
                                        type="text"
                                        placeholder="Exemple: Spot TV DIOR"
                                        :style="{width: '100%'}"
                                    />
                                </div>
                                <div class="create-project-field badges-category">
                                    <label
                                    ><i class="icon icon-player"></i>Catégorie*</label
                                    >
                                    <button
                                        class="badge"
                                        :class="{
                                        selected: project.category_id === categorie.id
                                    }"
                                    v-for="categorie in projectCategories"
                                    v-on:click="setProjectCategorie(categorie.id)"
                                >
                                    {{ $t(categorie.name) }}
                                </button>
                            </div>
                            <div class="create-project-field">
                                <label><i class="icon icon-calendar"></i>Deadline</label>
                                <v-date-picker v-model="project.date_deadline" mode="date" :popover="{ visibility: 'focus', placement: 'bottom-start' }" color="kpurple" is-dark>
                                  <template v-slot="{ inputValue, inputEvents }">
                                    <input class="form-field js-required" :value="inputValue" v-on="inputEvents" style="width:100%" />
                                  </template>
                                </v-date-picker>
                            </div>
                            <div class="create-project-field">
                                <label><i class="icon icon-project"></i>Nom de la production</label>
                                <input
                                    class="form-field js-required"
                                    v-model="project.production"
                                    type="text"
                                    :placeholder="$t('to-fill')"
                                    :style="{width: '100%'}"
                                />
                            </div>
                          </div>
                        </div>
                      <!-- content -->
                  </div>
                  <div class="btn-container">
                      <template>
                          <button class="confirm-popup__button button is-gradient cancel_btn" v-on:click="close()">
                              Quitter
                          </button>
                      </template>
                      <template>
                          <button class="confirm-popup__button button valid_btn" v-on:click="setProject()">
                              Modifier
                          </button>
                      </template>
                  </div>
              </div>
          </div>
      </div>
  </transition>
</template>

<script>
export default {
    props: ['_project', '_step', '_workspace'],

    data() {
        return {
            dashboard: this.getComponent('Dashboard'),
            step: 1,
            project: {
                id: null,
                workspace_id: this._workspace,
                name: '',
                production: '',
                client: '',
                client_name: '',
                client_phone: '',
                client_email: '',
                responsable_name: '',
                responsable_phone: '',
                responsable_email: '',
                category_id: '',
                date_deadline: new Date(),
                talents: [],
                talents_id: [],
            },
            masks: {
                input: ["L", "YYYY-MM-DD", "YYYY/MM/DD"]
            },
            projectCategories: [],
            error_msg: false,
        filters: {
                name: null,
            },
        }
    },

    created() {
        if (this._project) {
            this.project = this._project;

            if (this._project.client) {
                this.project.client_name = this._project.client.name;
            this.filters.name = this.project.client_name;
        }

            var talents = [];
            this.project.talents.forEach(talent => {
                talents.push(talent.id);
            });
            this.project.talents_id = talents;

            this.project.date_deadline = new Date(this._project.date_deadline);
        }
    },

    mounted() {
        this.getProjectCategory();
        this.setTalentSelect();
        this.setClientSelect();
        this.triggerSelect2();
        if (this._step) {
            this.step = this._step;
        }$(() => {
	    	$(".js-search-clients").autocomplete({
				  source: (request, response) => {
				    $.get('/api/client', {
							term: request.term,
						})
				    .done((data) => {
				    	response(data.datas);
				    });
				  },
				  create: function() {
            $(this).data('ui-autocomplete')._renderItem = (ul, item) => {
            	return $('<li>')
								.data('item.autocomplete', item)
								.append('<a>' + item.name + '</a>')
								.appendTo(ul);
            };
        	},
				  minLength: 2,
				  select: (event, ui) => {
				  	this.filters.name = ui.item.name;
				  	setTimeout(() => { this.$parent.getProjects(); }, 10);
				  },
				  open: (event, ui) => {
				  	console.log('OPEN');
				  	$('.ui-autocomplete').css({
				  		width: $('.ui-autocomplete-input').outerWidth(),
				  	});
				  },
				  close: (event, ui) => {
				  	console.log('CLOSE');
				  }
				});
	    });
    },

    methods: {
        setProject() {
            var errors = Vue.prototype.Global._checkForFields('.js-project-form');
            if (errors > 0) return false;
            if (this.filters.name) {
                this.project.client_name = this.filters.name;
            }
            axios.post("/api/project", {
                project: this.project
            }).then(res => {
                if (res.success === false) {
                    // Error
                } else {
                    let type = (!this.project.id) ? 'PROJECT' : 'EDIT_PROJECT';
                    this.$bus.$emit('PROJECT_ADD_OR_UPDATE', res.data); // Emit add or update project event
                    this.$bus.$emit('ACTION_CHANGED', {action: null}); // Close modal
                    this.$bus.$emit('ACTION_CHANGED', {
                        action: 'CONGRATS',
                        type: type,
                        project: res.data.datas, // created project
                        more: !this.project.id // more action if new project
                    }); // Congrats modal
                }
            }).catch(error => console.log(error));

            /*axios.post("/api/project/conversation", {
                projectId: this.project.id
            }).then(res => {
                if(res.success === false){
                // Error
            } else {
                this.$bus.$emit('PROJECT_ADD_OR_UPDATE', res.data); // Emit add or update project event
                this.$bus.$emit('ACTION_CHANGED', {action: null}); // Close modal

                this.$bus.$emit('ACTION_CHANGED', {
                    action: 'CONGRATS',
                    type: 'PROJECT',
                    project: res.data.datas, // created project
                    more: !this.project.id // more action if new project
                }); // Congrats modal
            }
        }).catch(error => console.log(error));*/
        },

        /**
         * Set Client select2
         */
        setClientSelect() {
            $(() => {
                $('.js-clients-data').select2({
                    selectionCssClass: 'simple-field',
                    dropdownCssClass: 'has-search',
                    placeholder: "Nom du client *",
                    tags: "true",
                    language: {
                        searching: function () {
                            return "Chargement...";
                        }
                    },
                    ajax: {
                        url: '/api/client',
                        processResults: function (data) {
                            var data = $.map(data.datas, function (obj) {
                                obj.id = obj.name;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: data
                            };
                        }
                    }
                });
            });
        },

        /**
         * Set Talent select2
         */
        setTalentSelect() {
            $(() => {
                $('.form__project .js-talents-data').select2({
                    dropdownCssClass: 'multiple-choices',
                    placeholder: "Rechercher un ou plusieurs talent(s)",
                    language: {
                        searching: function () {
                            return "Chargement...";
                        }
                    },
                    ajax: {
                        url: '/api/talent',
                        processResults: function (data) {
                            var data = $.map(data.datas, function (obj) {
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
            });
        },

        /**
         * Fill select2
         * */
        triggerSelect2() {
            this.triggerClientSelect2();
            this.triggerTalentsSelect2();
        },

        /**
         * Fill the job in the select2
         */
        triggerClientSelect2() {
            $(() => {
                let jobsSelect2 = $('#form-clients-select2');

                if (this.project.client) {
                    let option = new Option(this.project.client.name, this.project.client.name, true, true);
                    jobsSelect2.append(option).trigger('change');
                }
            });
        },

        /**
         * Fill the select2 with already selected Skills
         */
        triggerTalentsSelect2() {
            let skillsSelect2 = $('#form-talents-select2');

            this.project.talents.forEach(talent => {
                let option = new Option(talent.name, talent.id, true, true);
                skillsSelect2.append(option).trigger('change');
            });
        },

        async getProjectCategory() {
            await axios.get("/api/project-category", {
                user: this.$root.user,
            }).then(res => {
                if (res.success === false) {
                    // Error
                } else {
                    this.projectCategories = res.data.datas;
                }
            }).catch(error => console.log(error));
        },

        prevStep() {
            this.step--;
        },

        nextStep() {
            var errors = Vue.prototype.Global._checkForFields('.js-project-form', this.step);
            if (errors > 0) {
                this.error_msg = true;
                return false;
            } else {
                this.error_msg = false;
                this.step++;
            }

        },
        setProjectCategorie(id) {
            this.project.category_id = id;
        },
        close() {
            this.$bus.$emit('ACTION_CHANGED', {action: null}); // Close modal
        }
    }
}
</script>


