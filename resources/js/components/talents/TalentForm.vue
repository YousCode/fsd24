<template style="display: relative">
	<div style="display: relative" v-on:click.stop>
    
		<form method="POST" v-on:submit.prevent="setTalent" class="js-talent-form" rel="form__talent" style="display: relative" id="myForm">
			<input type="hidden" v-model="talent.id">

      <!-- Step 1 -->
      <div v-show="step == 1" class="popup-box step-1" style="margin-top: 450px; width: 750px; height: 704px; padding-top: 25px; padding-bottom: 25px; padding-right: 35px; padding-left: 55px; max-height:  704px; overflow: hidden">

		<div class="text-center mb-20" style="margin-bottom: 43px; padding-bottom: 35px; padding-top: 10px; border-bottom: 1px solid; border-image: linear-gradient(to right, rgb(65, 25, 181), rgb(30, 20, 59)) 0.5;width: 120%; margin-left: -55px;">
		  <h2 class="main-title c-main-white" style="font-size: 25px; margin-left: -20px">Ajouter un contact</h2>
		</div>
        <div class="form-inner">
		  <div style="display: flex; position: relative; align-items: center;">
			<img src="../../../images/contact_ui.png" alt="" style="width: 40px; height: 40px; margin-left: -15px;"/>
			<label for="" class="form-label" style="color: white; margin-left: 15px; font-size: 15px; margin-bottom: -10px;">Contact</label>
		  </div>
		  <div style="display: flex; margin-top: 15px; width: 90%; margin-left: 30px; justify-content: center;">
			<div class="form-box" style="width: 50%">
				<input type="text" placeholder="Nom de famille*" v-model="talent.name" class="form-field js-required" style="width: 96%;  background-color: #100824; border: 1px solid #1E143B;">
			</div>

			<div class="form-box" style="width: 50%">
				<!-- <label for="" class="form-label"></label> -->
				<input type="text" placeholder="Prénom*" v-model="talent.firstname" class="form-field js-required" style="width: 96%;  background-color: #100824; border: 1px solid #1E143B;">
			</div>
		</div>

		<div style="display: flex; position: relative; align-items: center;">
			<img src="../../../images/contact_ui.png" alt="" style="width: 40px; height: 40px; margin-bottom: -10px; margin-left: -15px;"/>
			<label for="" class="form-label" style="color: white; margin-left: 15px; font-size: 15px; margin-bottom: -10px;">Coordonnées</label>
		  </div>
		<div style="display: flex; margin-top: 15px; width: 90%; margin-left: 30px; justify-content: center;">
          <div class="form-box" style="width: 50%" for="input1">
			  <input type="email" placeholder="E-mail" v-model="talent.email" class="form-field js-required" style="width: 96%; background-color: #100824; border: 1px solid #1E143B;" id="input1" name="input1"	 >
          </div>
		  <div class="form-box" style="width: 50%" for="input2">
			  <input type="phone" placeholder="Numéro de téléphone" v-model="talent.phone" class="form-field" style="width: 96%; background-color: #100824; border: 1px solid #1E143B;" id="input2" name="input2"  >
          </div>
		</div>

		<div style="display: flex; position: relative; align-items: center; margin-top: 20px">
			<img src="../../../images/job_bag.png" alt="" style="width: 40px; height: 40px; margin-bottom: -10px; margin-left: -15px;" />
			<label for="" class="form-label" style="color: white; margin-left: 15px; font-size: 15px; margin-bottom: -10px;">Métier & Compétences</label>
		  </div>
		<div class="form-box" style="margin-top: 15px; width: 90%; margin-left: 30px">
            <div class="form-field-wrapper filter-select has-picto" style="width: 97%">
				<span class="filter-picto picto-explorer-filter-work"></span>
					<select v-model="talent.job_id" class="filter-field js-job-data" v-select2 name="jobs[]"></select>
            </div>
        </div>
		<div class="form-box"  style="margin-top: 15px; width: 90%; margin-left: 30px">
            <div class="form-field-wrapper filter-select has-picto" style="width: 97%;">
				<span class="filter-picto picto-explorer-filter-competence"></span>
              	<select v-model="talent.skills_ids" v-select2 class="filter-field js-skills-data js-select2 " id="skills-data" name="skills[]" multiple="multiple"></select>
            </div>
          </div>

          <p v-if="error_msg" class="form-error" style="margin-bottom: -30px;">Veuillez remplir les champs obligatoires</p>

			<div class="form-footer" style="
				position: relative;
				display: flex;
				border-top: 2px solid #1e143b;
				justify-content: center;
				align-items: center;
				min-height: 5em;
				margin-top: 45px;
				width: 120%;
				margin-left: -80px;">
				<a class="button popup-btn" @click="close()" style="position: initial;
					width: 37%;
					line-height: 2.3em; 
					text-align: center;
					height: 2.5em;
					border-radius: 5px;
					background: rgba(65, 25, 181, 0.3);
					display: initial;
					padding: 0;
					margin: 15px"
				>Quitter</a>
				<button v-show="step == 1" type="button" v-on:click.prevent="nextStep()" class="button popup-btn" style="position: initial;
					width: 37%;
					text-align: center;
					height: 2.5em;
					border-radius: 5px;
					background: #4119b5;
					display: initial;
					padding: 0;">
		<!-- 		<span v-if="!_appointment">Suivant</span>
				<span v-else>Modifier</span>≈ -->
				Suivant
		  	</button>
		   </div>
        </div>
		</div>
								<!-- STEP  2  -->


      <div v-show="step == 2" class="popup-box step-2"  style="margin-top: 450px; width: 750px; height: 765px;  padding-top: 25px; padding-bottom: 25px; padding-right: 35px; padding-left: 55px; max-height: 765px; overflow: hidden;">

		<div class="text-center mb-20" style="margin-bottom: 43px; padding-bottom: 35px; padding-top: 10px; border-bottom: 2px solid rgb(65, 25, 181); width: 120%; margin-left: -55px">
			<h2 class="main-title c-main-white" style="font-size: 25px; margin-left: -38px">Ajouter un contact</h2>
			<div
			class="go-back"
			  v-on:click="goPrev"
			  style="top: 40px"
		  >
			  <svg
				  width="26px"
				  height="26px"
				  viewBox="0 -1 16 34"
				  class="vc-svg-icon"
				  style="margin-right: 0px"
			  >
				  <path
					  data-v-63f7b5ec=""
					  d="M11.196 10c0 0.143-0.071 0.304-0.179 0.411l-7.018 7.018 7.018 7.018c0.107 0.107 0.179 0.268 0.179 0.411s-0.071 0.304-0.179 0.411l-0.893 0.893c-0.107 0.107-0.268 0.179-0.411 0.179s-0.304-0.071-0.411-0.179l-8.321-8.321c-0.107-0.107-0.179-0.268-0.179-0.411s0.071-0.304 0.179-0.411l8.321-8.321c0.107-0.107 0.268-0.179 0.411-0.179s0.304 0.071 0.411 0.179l0.893 0.893c0.107 0.107 0.179 0.25 0.179 0.411z"
				  ></path>
			  </svg>
			  Précédent
		  </div>
		</div>

        <div class="form-inner" style="margin-left: 25px;">
		  <div style="display: flex; position: relative; align-items: center;">
			<img src="../../../images/contact_ui.png" alt="" style="width: 40px; height: 40px; margin-left: -15px;" />
			<label for="" class="form-label" style="color: white; margin-left: 15px; font-size: 15px; margin-bottom: -10px;">Statut</label>
		  </div>
		  <div style="margin-top: 18px;; justify-content: center; width: 100%; padding-left: 37px;">
			<div class="form-box" style="width: 85%;">
				<div class="form-field-wrapper" style="width: 100%">
				<select class="filter-field js-status-data "  name="status" v-select2 v-model="talent.profile_type" style="margin-left: 0px">
					<option value="Freelance">Freelance</option>
					<option value="Post Producteur">Post Producteur</option>
					<option value="Société">Société</option>
				</select>
				</div>
			</div>
		  </div>

		  <div style="display: flex; position: relative; align-items: center;">
			<img src="../../../images/planet.png" alt="" style="width: 40px; height: 40px; margin-left: -15px;" />
			<label for="" class="form-label" style="color: white; margin-left: 15px; font-size: 15px; margin-bottom: -10px;">Réferences</label>
		  </div>
		  <div style=" margin-top: 20px; justify-content: center; width: 100%; padding-left: 37px">
			<div class="form-box" style="width: 85%">
				<div class="form-field-wrapper" style="width: 100%">
					<span class="icon icon-instagram" style="width:13px; height:13px;"></span>
					<input type="social" placeholder="http://instagram.com/kolab.app" v-model="talent.instagram_url" class="form-field" style="width: 100%; padding-left: 35px; background-color: #100824; border: 1px solid #1E143B !important;">
            	</div>
			</div>
			<div class="form-box" style="width: 85%">
				<div class="form-field-wrapper" style="width: 100%">
					<span class="icon icon-vimeo" style="width:13px; height:13px;"></span>
					<input type="social" placeholder="http://vimeo.com/kolab" v-model="talent.vimeo_url" class="form-field" style="width: 100%; padding-left: 35px; background-color: #100824; border: 1px solid #1E143B !important;">
            	</div >
			</div>
			<div class="form-box" style="width: 85%">
				<div class="form-field-wrapper" style="width: 100%">
					<span class="icon icon-web" style="width:13px; height:13px;"></span>
					<input type="social" placeholder="http://kolab.app" v-model="talent.profile_url" class="form-field" style="width: 100%; padding-left: 35px; background-color: #100824; border: 1px solid #1E143B !important;">
            	</div>
			</div>
		</div>

		  <div style="display: flex; position: relative; align-items: center;">
			<img src="../../../images/localisation.png" alt="" style="width: 40px; height: 40px; margin-left: -15px;" />
			<label for="" class="form-label" style="color: white; margin-left: 15px; font-size: 15px; margin-bottom: -10px;">Localisation</label>
		  </div>
		  <div class="form-box" style="display: flex; margin-top: 10px;width: 83%;justify-content: center; margin-left: 28px;">
            <input type="text" placeholder="Ville" v-model="talent.city" class="form-field" style="width: 50%; margin: 10px; background-color: #100824; border: 1px solid #1E143B !important;">
            <input type="text" placeholder="Pays" v-model="talent.country" class="form-field" style="width: 50%; margin: 10px; background-color: #100824; border: 1px solid #1E143B !important;">
          </div>

<!--           <div class="popup-pagination">
            <span class="popup-pagination__item is-active"></span>
            <span class="popup-pagination__item" v-bind:class="{ 'is-active' : step >= 2 }"></span>
          </div>
 -->
          <p v-if="error_msg" class="form-error">Veuillez remplir les champs obligatoires</p>


		  <div class="form-footer" style="
		 	position: relative;
			display: flex;
			border-top: 2px solid #1e143b;
			justify-content: center;
			align-items: center;
			min-height: 5em;
			margin-top: 25px;
			width: 120%;
    		margin-left: -110px;">
		<a class="button popup-btn" @click="close()" style="position: initial;
				width: 37%;
				line-height: 2.3em;
				text-align: center;
				height: 2.5em;
				border-radius: 5px;
				background: rgba(65, 25, 181, 0.3);
				display: initial;
    			padding: 0;
				margin: 15px;"
			>Quitter</a>
		    <button type="submit" class="button popup-btn" style="position: initial;
				width: 37%;
				text-align: center;
				height: 2.5em;
				border-radius: 5px;
				background: #4119b5;      
				display: initial;
    			padding: 0;">
		    <span v-if="!talent.id">Ajouter</span>
			<span v-else>Enregistrer</span>
		</button>
			
		   </div>
        </div>
      </div>
	  


		</form>
	</div>
</template>


<script>

	export default {
		props: ['_talent','_workspace'],

		data() {
			return {
        	step: 1,
			talent: {
					id: '',
					firstname: '',
					name: '',
					email: '',
					phone : '',
					//job : '',
					job_id: [],
					//price : '',
					city : '',
					country : '',
					profile_type : '',
					profile_url : '',
					skills : [],
					skills_ids : [],
          			instagram_url: '',
          			vimeo_url: '',
					workspace_id: this._workspace,
			},
			selectedProject: null,
    		error_msg: false,
			}
		},

		created() {
			if(this._talent) {
				this.talent = this._talent;

				var skills = [];
				this.talent.skills.forEach(skill => {
					skills.push(skill.id);
				});
				this.talent.skills_ids = skills;
			}
		},

		mounted() {
			this.setSkillsSelect();
			this.setJobsSelect();
			this.setStatusSelect();
			this.triggerSelect2();
      this.selectOnChange();
		},

		methods: {

    	/**
    	 * [setTalent description]
    	 */
    	 setTalent(){

    	 	if(this.step == 1){
    	 		this.nextStep
    	 	}

        var errors = Vue.prototype.Global._checkForFields('.js-talent-form');
        if(errors > 0) return false;
		
    	 	axios.post("/api/talent", {talent : this.talent}).then(res => {
    	 		if(res.data.success === false){
	      		//console.log(res.data.datas.message); // Todo : popup error
	      	} else {
	      		this.$bus.$emit('TALENT_ADD_OR_UPDATE', res.data); // Emit add or update talent event
	      		this.$bus.$emit('ACTION_CHANGED', {
            	action: 'CONGRATS',
            	type: 'TALENT'
            }); // Congrats modal
	      	}
	      }).catch(error => console.log(error));
    	 },

    	/**
    	 * Set skills select2
    	 */
    	setSkillsSelect(){
    	 	$('.js-skills-data').select2({
          language: "fr",
          dropdownCssClass: 'multiple-choices',
  	 			placeholder: "Compétence(s)",
  	 			language: {
		        searching: function() {
	            return "Chargement...";
		        }
			    },
  	 			ajax: {
  	 				url: '/api/skill',
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
    	},
		
		goPrev() {
            this.step = 1;
            this.selectedProject = null;
        },

    	setJobsSelect(){
  	 		$('.js-job-data').select2({
        /*   minimumResultsForSearch: -1,
  	 			tags: "true", */
				dropdownCssClass: 'multiple-choices',
  	 			placeholder: "Métier",
  	 			language: {
		        searching: function() {
	            return "Chargement...";
		        }
			    },
  	 			ajax: {
  	 				url: '/api/job',
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
    	},

    	/**
    	 * Set status select2
    	 */
    	setStatusSelect(){
    	 	$(() => {
    	 		$('.js-status-data').select2({
    	 			minimumResultsForSearch: Infinity,
    	 			placeholder: "Statut",
    	 		});
    	 	});
    	},

      /**
       * Fill select2
       * */
       triggerSelect2(){
       	this.triggerJobsSelect2();
       	this.triggerSkillsSelect2();
       },

      /**
       * Fill the job in the select2
       */
       triggerJobsSelect2(){
       	let jobsSelect2 = $('#job-data');

       	if(this.talent.job){
     			let option = new Option(this.$t(this.talent.job.name), this.talent.job.id, true, true);
     			jobsSelect2.append(option).trigger('change');
     		}
       },

	   close() {
            this.$bus.$emit("ACTION_CHANGED", {
                action: null
            }); // Close modal
        },


      /**
       * Fill the select2 with already selected Skills
       */
       triggerSkillsSelect2(){
       	let skillsSelect2 = $('#skills-data');
		const $this = this;
       	this.talent.skills.forEach(skill => {
       		let option = new Option($this.$t(skill.name), skill.id, true, true);
       		skillsSelect2.append(option).trigger('change');
       	});
       },

        nextStep(){
          var errors = Vue.prototype.Global._checkForFields('.js-talent-form', this.step);
          if(errors > 0) {
            this.error_msg = true;
            return false;
          } else {
            this.error_msg = false;
            this.step ++;
          }
        },

        selectOnChange() {
          $('.js-select2').each(function(){
            $(this).on('change', function() {
              if ($(this).val() != ""){
                $(this).parent().addClass('has-value');
              } else {
                $(this).parent().removeClass('has-value');
              }
            });
          });
        }
      }
    }
 </script>
