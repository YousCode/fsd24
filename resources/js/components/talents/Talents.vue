
<template>
	<div class="page__talents" id="vue__talents">
		<ContextMenu ref="ContextMenu"></ContextMenu>

		<div class="main-container" style="background: transparent !important; margin-top: 20px;">
			<div class="filters" style="background: #1E1438">
				<div class="row filters-row">
					<div class="search-col" style="width: 100%">
						<div class="filter-item has-picto">
							<span class="filter-picto picto-search"></span>
								<!-- <input type="text" v-model="filters.name" placeholder="Rechercher un talent" class="filter-field search-field" v-on:keyup="getTalents()" /> -->
								<TalentAutocomplete></TalentAutocomplete>
								<button type="button" class="filters-delete is-cross" :disabled='!filters.name' v-on:click="clear('name')"><span class="filters-delete__picto icon-cross"></span></button>
						</div>
					</div>
				</div>
			</div>
			<div class="block talents__block" style="background-color: #2B1C56; padding-top: 20px;padding-bottom: 20px;margin-top: 20px" v-if="talents.length">
				<div style="margin-right: 20px; margin-left: 20px; height: 100%;">
					<div class="table-head" style="border-bottom: 0px;">
						<div class="table-row" style="padding-left: 30px; background: #1E1438; border-radius: 10px; border-bottom: 0px; padding-bottom: 0px">
							<div class="table-data" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; letter-spacing: 0;"><span>Contact</span></div>
							<div class="table-data table-col-medium" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; max-width: 12%; letter-spacing: 0;">Coordonnées</div>
							<div class="table-data table-col-wide" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; max-width: 18%; letter-spacing: 0;">Compétences</div>
							<div class="table-data table-col-small" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; letter-spacing: 0;">Statut</div>
							<div class="table-data" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; letter-spacing: 0;">Références</div>
							<div class="table-data table-col-small" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; letter-spacing: 0;">Localisation</div>
						</div>
					</div>

					<div class="table-head" style="border-bottom: 0px;">
						<div class="table-row create-btn hover-add-contact"  href="javascript:;" v-on:click="Global._setAction('SET_TALENT')">
							<div class="table-data" style="color: #AF9DE2; text-transform: none; display: flex; white-space: nowrap">
								<span style="font-size: 1.5vh; letter-spacing: 0;"> Ajouter un contact </span>
								<span style="font-size: 2.1vh; margin-left: 4.49px;"> + </span>
							</div>
						</div>
					</div>
					
					<div class="table-body" style="height:78%;margin-top: 10px;">
						<div class="table-row-contact-list js-clickable js-clickable-talent" v-for="talent in talents" style="padding-left: 25px; border-radius: 10px; height: 10%; margin-bottom: 10px; height: 60px;">
							<div class="table-data" style="padding-right: 75px;">
								<div style="white-space: nowrap; display: flex">
									<p style="font-size: 0.83vw; font-weight: 700; margin-right: 5px">{{ talent.firstname }} {{ talent.lastname }}</p>
								</div>
								<p style="color: #7665A7; font-size: 0.78vw; font-weight: 400">{{ talent.job ? $t(talent.job.name) : '' }}</p>
							</div>
							<div class="table-data table-col-small" style="max-width: 12%; height: 130%;">
							<!--  <a :href="'tel:' + talent.phone" class="tel">{{ Global._formatPhone(talent.phone) }}</a> -->
							<!-- <p class="c-grey">{{ talent.email }}</p> -->
								<div class="talents__references" style="height: 100%">
									<button type="button" class="button-info-hover js-toggle-button">
										<i class="icon-email-full" alt="" style="width:13px; height:13px"></i>
										<span class="tooltip-text" id="top-email">{{ talent.email}}</span>
									</button>

									<button  type="button" class="button-info-hover js-toggle-button">
										<span v-if="talent.phone != 0">
											<i class="icon icon-phone" alt="" style="width:13px; height:13px;"></i>
											<span class="tooltip-text" id="top-phone">{{ Global._formatPhone(talent.phone) }}</span>
										</span>
										<span v-else>
											<i class="icon icon-phone" alt="" style="width:13px; height:13px;"></i>
											<span class="tooltip-text" style="color: rgb(118, 101, 167);" id="top-phone">{{ 'À renseigner' }}</span>
										</span>
									</button>

								</div>
							</div>
						<!-- 	<div class="hover-text">
								<i class="icon icon-phone" alt="" style="width:13px; height:13px;"></i>
  								<span class="tooltip-text" id="top">{{ Global._formatPhone(talent.phone) }}</span>
							</div> -->
							<!--<div class="table-data table-col-wide js-toggle-wrapper">
								<p v-if="talent.skills.length > 1"><small><strong> {{ talent.skills.length - 1 }} <template v-if="talent.skills.length == 2">compétence</template><template v-else>compétences</template></strong></small></p>
								<div class="table-data__list js-toggle-button">
									<div class="table-data__list-inner">
										<span v-for="(skill, id) in talent.skills" :key="id" class="table-data__list-item">{{ skill.name }}</span>
									</div>
									<div v-if="talent.skills.length > 1" class="table-data__list-all-items js-toggle-content">
										<span v-for="(skill, id) in talent.skills" :key="id" class="table-data__list-item">{{ skill.name }}</span>
									</div>
									<span v-if="talent.skills.length > 1" class="icon icon-down-arrow"></span>
								</div>
							</div>
							-->

							<div class="table-data table-col-medium" style=" justify-content: center; height: 70px; white-space: nowrap;">
								<div style="background: #1E1438; border-radius: 6px; padding: 15px 10px 15px; width: 100%; max-width: 250px; position: relative; display: flex; height: 100%; align-items: center; overflow: hidden;" >
									<span v-for="(skill, id) in talent.skills" :key="id">
										<p v-if="id == talent.skills.length - 1" style="font-size: 0.78vw; font-weight: 400; color: #7665A7">
											{{ $t(skill.name) + '.' }}
										</p>
										<p v-else style="font-size: 0.78vw; font-weight: 400; color: #7665A7">
											{{ $t(skill.name) + ', &nbsp' }}
											<!-- <div type="button" class="button-info-hover js-toggle-button" style="width: 20%; position: absolute; right: 0;
												background-color: #38276A;
												height: 70%;
												margin-top: -22px">
												<h1>+1
												</h1>
												<span class="tooltip-text" style="z-index:2222;" id="top-email">{{ skill.name }}</span>
											</div> -->
										</p>
									</span>
									<div v-if="talent.skills.length < 1">
										<p style="font-size: 0.78vw; font-weight: 400; color: #7665A7">
											{{ 'À renseigner' }}
										</p>
									</div>
                            	</div>
							</div>

							<div class="table-data table-col-small">
								<p style="font-size: 0.83vw; font-weight: 700; width: max-content;">
									{{talent.profile_type}}
								</p>
								<span v-if="talent.profile_type == null">
									<p style="font-size: 0.78vw; font-weight: 400; color: #7665A7">
										{{ 'À renseigner' }}
									</p>
								</span>
							</div>

							<div class="table-data" style="height: 130%">
								<div class="talents__references" style="height: 100%">
									<a :href="talent.profile_url" v-if="talent.profile_url" target="_blank" class="talents__reference c-grey socials-hover" style="width: 100%;height: 95%; auto; justify-content: center;align-items: center;padding: 10px; display: inline-grid; border-radius: 5px; margin: 3px">
										<span class="icon icon-web"  style="width:13px; height:13px;"></span>
									</a>
									<a :href="getInstaUrl(talent)" v-if="talent.instagram_url" target="_blank" class="talents__reference c-grey socials-hover" style="width: 100%;height: 95%;background: #1E1438; justify-content: center;align-items: center;padding: 10px; display: inline-grid; border-radius: 5px; margin: 3px">
										<span class="icon icon-instagram" style="width:13px; height:13px;"></span>
									</a>
									<a :href="getVimeoUrl(talent)" v-if="talent.vimeo_url" target="_blank" class="talents__reference c-grey socials-hover" style="width: 100%;height: 95%;background: #1E1438; justify-content: center;align-items: center;padding: 10px; display: inline-grid; border-radius: 5px; margin: 3px">
										<span class="icon icon-vimeo" style="width:13px; height:13px;"></span>
									</a>
								</div>
							</div>

							<div class="table-data table-col-small" style="max-width: 20%;">
								<p style="font-size: 0.83vw; font-weight: 700;">{{ talent.city }}</p>
								<p style="font-size: 0.78vw; font-weight: 400; color: #7665A7">{{ talent.country }}</p>
								<span v-if="talent.city == 0">
									<p style="font-size: 0.78vw; font-weight: 400; color: #7665A7">
										{{ 'À renseigner' }}
									</p>
								</span>
							</div>

							<div class="show-on-hover-only">
								<div class="table-data table-col-small js-toggle-wrapper" style=" text-align: right; height: 130%;">
									<div class="talents__references" style="height: 100%; text-align: right;">
										<button class="button-info-hover"  v-if="!talent.token_login"  v-on:click.prevent="Talent._edit(talent)" style=" width: 35px;height: 100%;background: #1E1438; justify-content: center;align-items: center;padding: 10px; display: inline-grid; border-radius: 5px; margin: 3px" >
											<span class="icon icon-edit"></span>
										</button>
										<button class="button-info-hover"  v-if="!talent.token_login"  v-on:click.prevent="Talent._delete(talent, getTalents)" style=" width: 35px;height: 100%;background: #1E1438; justify-content: center;align-items: center;padding: 10px; display: inline-grid; border-radius: 5px; margin: 3px">
											<span class="icon icon-delete"></span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div v-else>
				<div class="block talents__block" style="background-color: #2B1C56; padding: 20px;margin-top: 20px">
					<div class="table-head" style="border-bottom: 0px;">
						<div class="table-row" style="padding-left: 30px; background: #1E1438; border-radius: 10px; border-bottom: 0px; padding-bottom: 0px">
							<div class="table-data" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; letter-spacing: 0;"><span>Contact</span></div>
							<div class="table-data table-col-medium" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; max-width: 12%; letter-spacing: 0;">Coordonnées</div>
							<div class="table-data table-col-wide" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; max-width: 20%; letter-spacing: 0;">Compétences</div>
							<div class="table-data table-col-small" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; letter-spacing: 0;">Statut</div>
							<div class="table-data" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; letter-spacing: 0;">Références</div>
							<div class="table-data table-col-small" style="font-size: 0.83vw; color: #AF9DE2; text-transform: none; letter-spacing: 0;">Localisation</div>
						</div>
						<div class="table-row create-btn hover-when-no-contact"  href="javascript:;" v-on:click="Global._setAction('SET_TALENT')">
							<div>
								<img src="../../../images/contact_ui.png" alt="" style="width: 50px; height: 50px; margin-left: -15px;"/>
								<div class="table-data" style="color: #AF9DE2; text-transform: none; display: flex; white-space: nowrap">
									<span style="font-size: 2.1vh; margin-right: 9.49px;"> + </span>
									<span style="font-size: 1.5vh; letter-spacing: 0;"> Ajouter un contact </span>
								</div>
							</div>
						</div>
					</div>
				</div>
    		</div>
		</div>
	</div>
</template>


<script>
	export default {
		props: ['_workspace'],

		data() {
			return {
				workspace: this._workspace,
				all_talents: [],
				talents: [],
				filters: {
					alpha: null,
					name: null,
					date: null,
					jobs: [],
					skills: []
				},
        open_options: null,
			}
		},
		mounted() {
    	// On mounted, get all talents list
    	this.getTalents(true);

    	// Catch event when talent is added or updated
    	this.$bus.$on('TALENT_ADD_OR_UPDATE', () => {
	      this.getTalents(); // Call talents with filters
	    });

    	this.setSkillsSelect();
    	this.setJobsSelect();
    },

    computed: {
    },

    methods: {

    	/**
    	 * getTalents
    	 * @return void
    	 */
		getTalents(all){
			var params = {
				mode: 'contact',
				workspace: this.workspace
			};
 

    	setTimeout(() => {

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
    	 * Set skills select2
    	 */
    	setSkillsSelect(){
    	 	$(() => {
    	 		$('.js-skills-data').select2({
            dropdownCssClass: 'multiple-choices filters-dropdown',
    	 			placeholder: "Compétence(s)",
            closeOnSelect: false,
            debug: true,
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

    	 		$('.js-skills-data').on("select2:select", (e) => { this.getTalents(); });
    	 		$('.js-skills-data').on("select2:unselect", (e) => { this.getTalents(); });
    	 	});
    	},

    	/**
    	 * Set jobs select2
    	 */
    	setJobsSelect(){
    	 	$(() => {
    	 		$('.js-jobs-data').select2({
            dropdownCssClass: 'filters-dropdown',
    	 			placeholder: "Métier(s)",
    	 			//minimumInputLength: 1,
            closeOnSelect: false,
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

					$('.js-jobs-data').on("select2:select", (e) => { this.getTalents(); });
    	 		$('.js-jobs-data').on("select2:unselect", (e) => { this.getTalents(); });
    	 	});
    	},

    	/**
    	 * Enable/Disable active letter
    	 */
    	setActiveLetter(letter){
    		if(this.filters.alpha == letter){
    			this.filters.alpha = null;
    		} else {
    			this.filters.alpha = letter;
    		}

    		this.getTalents();
    	},

    	hasTalentsWithLetter(letter){
    		const startsWith = this.all_talents.filter((talent) => {
    			return talent.firstname.toLowerCase().startsWith(letter.toLowerCase());
    		});

    		return startsWith.length > 0;
    	},

      /**
       * Clear field
       */
      clear(type){
        if(typeof this.filters[type] == 'object'){
            this.filters[type] = [];
        } else {
            this.filters[type] = null;
        }

        if($('.js-'+type+'-data')){
        	$('.js-'+type+'-data').val('').trigger('change');
      	}

        this.getTalents();
      },

      convert(talent){
      	return btoa(talent.id);
      },

      getInstaUrl(talent){
      	var url = talent.instagram_url.replace('https://instagram.com/', '');
      			url = talent.instagram_url.replace('http://instagram.com/', '');
      			url = talent.instagram_url.replace('https://www.instagram.com/', '');
      			// url = talent.instagram_url.replace('http://www.instagram.com/', '');
      	return 'https://www.instagram.com/' + url;
      },

      getVimeoUrl(talent){
      	var url = talent.vimeo_url.replace('https://vimeo.com/', '');
      			url = talent.vimeo_url.replace('http://vimeo.com/', '');
      			url = talent.vimeo_url.replace('https://www.vimeo.com/', '');
      			url = talent.vimeo_url.replace('http://www.vimeo.com/', '');
      	return 'https://www.vimeo.com/' + url;
      },

      getYoutubeUrl(talent){
      	var url = talent.profile_url.replace('https://youtube.com/', '');
      			url = talent.profile_url.replace('http://youtube.com/', '');
      			url = talent.profile_url.replace('https://www.youtube.com/', '');
      			url = talent.profile_url.replace('http://www.youtube.com/', '');
      	return 'https://www.youtube.com/' + url;
      }
    }
}

</script>

