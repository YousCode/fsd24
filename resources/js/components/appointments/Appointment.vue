<template>
    <div class="popup-wrapper">
        <div class="popup-intro text-center mb-30">
          <h2 class="popup-maintitle">
          	<span v-if="!selectedTalent">Création de rendez-vous</span>
          	<span v-if="selectedTalent">{{ selectedTalent.name }}</span>
          </h2>
          <p class="popup-maintext">Ces rendez-vous seront automatiquement créés dans la page Dashboard et Planning. Une fois créé, il sera possible de le modifier plus tard.</p>
        </div>
        <form method="POST" v-on:submit.prevent="setAppointment" data-url="" class="form__appointment" rel="form__appointment">
            <div class="popup-box-rdv is-small text-center pre-task-form" v-show="step == 1">
              <div class="row mb-20">
                <div class="col-md-6">
              	 <div v-on:click="createAppointment()" class="box-button"><span class="picto picto-rdv"></span> Créer<br> <strong>pour moi </strong></div>
                </div>
                <div class="col-md-6">
              	 <div v-on:click="searchTalent = true;" class="box-button js-toggleclass"><span class="picto picto-rdv-talent"></span> Créer<br> <strong>pour un talent</strong></div>
                </div>
              </div>
              <div v-show="searchTalent">
                <div class="form-box text-left mb-0">
                  <label for="" class="form-label no-dot">Talent</label>
                  <div class="d-flex">
                    <div class="form-field-wrapper">
                      <span class="icon icon-search"></span>
                      <select type="text" v-model="appointment.talents_id" v-select2  id="form-talents-select" class="form-field js-talents-data" name="talents_id[]"></select>
                    </div>
                    <button type="button" v-on:click="createAppointment()" class="pre-task-form__button button is-gradient" >Appliquer</button>
                  </div>
                </div>

            	</div>
            </div>
        </form>
    </div>
</template>

<script>

//@todo: any use of this component?
export default {
    props: ['_appointments', '_goback','users'],

    data() {
      return {
    		step: 1,
    		searchTalent: false,
        selectedTalent: null,
        
    		talents: [],
        appointment: {
          id: null,
          name: '',
          talents:'',
          talents_id: null,
          talent_name: '',
          task_type_id: null,
          task_type_name: '',
          note: null,
          date: {
              start: new Date(),
              end: new Date()
          },
        }
      }
    },

    created() {
    	if(this._appointment){
    		this.selectedAppointment = this._appointment;
    		this.step = 2;
    	}
    },

    mounted() {
    	// For creation (Different pour l'update)
      this.getNotes();
      this.setTalentSelect();
    },

    // computed: {
    //     isDisabled: function(){
    //       var noProject = this.selectedProject.length == 0;

    //       var isEmpty = noProject;

    //       // if(isEmpty){
    //       //   this.getTalents();
    //       // }

    //      return isEmpty;
    //     }
    // },

    methods: {
      
      createAppointment(){
      	this.$bus.$emit('ACTION_CHANGED', {action: 'SET_APPOINTMENTFORM'}); // Close modal
      },
      
      getTalents(all){
          var params = {};
          setTimeout(() => {
	      	
	      		if(this.filters.name) params.filter_name = this.filters.name;
	      		axios.get("/api/talent", {
	      			params: params
	      		}).then(res => {
	      			if(res.success === false){
	  	      		// Todo : Error
	  	      	} else {
	  	      		this.talents = res.data.datas; // Update talents list
	  	      		if(all) this.all_talents = res.data.datas; // Set all talents for first time
	  	      	}
	  	      }).catch(error => console.log(error));
	      	}, 10);
    	},
          

      /**
       * Set Talent select2
       */
      setTalentSelect(){
        var $this = this;
        $(() => {
          $('.form__appointment .js-talents-data').select2({
            dropdownCssClass: 'multiple-choices',
            placeholder: "Rechercher un talent",
            language: {
			        searching: function() {
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
          $('.js-talent-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
        		$this.appointment[key].talent_name = e.params.data.name;
	        });
	    	 	$('.js-talent-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
        		$this.appointment[key].talent_name = '';
	    	 	});
        });
      },

      /**
       * Fill the talent in the select2
       */
      triggerTalentSelect2(){
      	var $this = this;
       	let talentSelect2 = $('.js-talents-data');

       	talentSelect2.each(function(index, item){
       		if($this.appointment[index].talents_id){
     				let option = new Option($this.appointment[index].talent_name, $this.appointment[index].talents_id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },

      getNotes() {
        $(document).on('click', (e) => {
          $('.js-notes-textarea').each(function(){

            if ($(this).is(':visible') && !e.target.matches('.js-toggle-content, .js-toggle-content *')) {
              var $notesField = $(this).parent().find('.js-notes-value');

              if($(this).val() !== '') {
                var notes = $(this).val();

                $notesField.parent().addClass('has-value');
                $notesField.html(notes);

                if($(this).val().length > 36) {
                  $notesField.addClass('is-cut');
                } else {
                  $notesField.removeClass('is-cut');
                }
              } else {
                $notesField.parent().removeClass('has-value');
                $notesField.html('');
              }
            }

          });
        });
      },

      setNotes(){
      	$('.js-notes-textarea').each(function(){
	      		var $notesField = $(this).parent().find('.js-notes-value');

	      		if($(this).val() !== '') {
	            var notes = $(this).val();

	            $notesField.parent().addClass('has-value');
	            $notesField.html(notes);

	            if($(this).val().length > 36) {
	              $notesField.addClass('is-cut');
	            } else {
	              $notesField.removeClass('is-cut');
	            }
	          } else {
	            $notesField.parent().removeClass('has-value');
	            $notesField.html('');
	          }
      	});
      },

    	nextStep(){
    		var errors = Vue.prototype.Global._checkForFields('.js-project-form', this.step);
        if(errors > 0) {
          this.error_msg = true;
          return false;
        } else {
          this.error_msg = false;
          this.step ++;
        }

		},

      goBack(){
      	this.tasks = [];
      	this.addTask();
      	this.step = 1;
      }
    }
}
</script>