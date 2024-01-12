<template>
  <div class="exxport-wrapper">
    <div class="popup-intro text-center mb-30 mt-20">
      <h2 class="popup-maintitle">
      	{{ addOrUpdate }} des livrables
      </h2>
    </div>
    <div class="text-center exxport-header">
      <button v-on:click="setState('SELECT')" class="button-exxport select">Sélectionner des livrables</button>
      <button v-on:click="setState(null)" v-show="state == 'SELECT'" class="button-exxport cancel">Annuler</button>
      <span v-if="exxports.length > 1" class="nb-exxport"><span>{{ exxports.length }}</span><span>Livrables en prévision</span></span>
      <span v-else class="nb-exxport"><span>{{ exxports.length }}</span> <span>Livrable en prévision</span></span>
      <button class="button-exxport duplicate" :class="(state == 'SELECT') ? 'active' : ''" v-bind:disabled="state !== 'SELECT'" v-on:click="duplicateExport()"><span class="picto-duplicate" style="width: 18px;margin-right: 10px;"></span>Dupliquer</button>
      <button class="button-exxport delete" :class="(state == 'SELECT') ? 'active' : ''" v-bind:disabled="state !== 'SELECT'" v-on:click="Exxport._delete(selectedExxport)"><span class="picto-delete-export" style="width: 18px;margin-right: 10px;"></span>Supprimer</button>
    </div>
    <form method="POST" v-on:submit.prevent="setExport()" data-url="" class="form__export" rel="form__export">
      <div class="exxport-popup-box is-large" id="export--items">
        <div v-for="(exxport, index) in exxports" v-if="exxports" :key="index" class="exxport-select">
          <div class="wrapper-checkbox exxport-checkbox">
              <input type="checkbox" v-model="selectedExxport" :value="exxport.id ? {'delete' : exxport.id, 'duplicate': exxport} : {'delete' : null, 'duplicate': exxport}" :id="'export-' + index" v-on:click="changeSelect($event, 'export-item-' + index)" />
              <span class="checkbox" v-show="state == 'SELECT'"></span>
          </div>
          <div :id="'export-item-' + index" class="row exxport-item" v-bind:style="(exxport.id > 0 && exxport.id == editedExxportId) ? { 'background-color': '#271558' } : {}">
            <input type="hidden" v-model="exxport.id">
            <div class="col-md-1 exxport-title">
              <strong>Livrable {{ index + 1 }}</strong>
            </div>
            <div class="form-box name">
              <label for="" class="form-label no-dot">Nom</label>
              <input type="text" placeholder="Nom du film" v-model="exxport.name" class="form-field" required>
            </div>

            <div class="form-box duration">
              <label for="" class="form-label no-dot">Durée</label>
              <input type="text" placeholder="00" v-model="exxport.hours" class="form-field small" required>
            </div>
            :
            <div class="form-box duration">
              <label for="" class="form-label no-dot" style="opacity:0;">Durée</label>
              <input type="text" placeholder="00" v-model="exxport.minutes" class="form-field small" required>
            </div>
            :
            <div class="form-box duration">
              <label for="" class="form-label no-dot" style="opacity:0">Durée</label>
              <input type="text" placeholder="00" v-model="exxport.seconds" class="form-field small" required>
            </div>

            <div class="form-box format">
              <label for="" class="form-label no-dot">Résolution</label>
              <input type="text" placeholder="1920" v-model="exxport.width" :data-key="index" class="form-field" required>
            </div>
            x
            <div class="form-box format">
              <label for="" class="form-label no-dot" style="opacity:0;">Résolution</label>
              <input type="text" placeholder="1080" v-model="exxport.height" :data-key="index" class="form-field" required>
            </div>

            <div class="form-box type">
              <label for="" class="form-label no-dot">Sortie</label>
              <select type="text" v-model="exxport.format.id" v-select2 :data-key="index" class="form-field js-format-data" style="width: 120px;" required>
              </select>
            </div>

            <!-- <div class="form-box col-md-2">
              <label for="" class="form-label no-dot">Sortie</label>
              <input type="text" placeholder="Ex : MP4" v-model="exxport.format_raw" class="form-field" required>
            </div> -->

            <div class="form-box lang">
              <label for="" class="form-label no-dot">Langue</label>
              <select type="text" v-model="exxport.language_id" v-select2 :data-key="index" class="form-field js-language-data" style="width: 120px;" required></select>
            </div>
            <div class="form-box exxport__notes">
              <label for="" class="form-label no-dot">Notes</label>
              <p v-on:click.prevent="addOrRemoveFromExxportNotes(exxport)" class="add-notes">
                <span>Ajouter des notes</span>
              </p>
              <div class="exxport__notes--content" v-show="checkExxportNotes(exxport)">
                <div class="exxport__notes__body">
                  <ul>
                      <li>
                        <input type="text" v-model="exxport.notePreview" v-on:keydown.enter.prevent="addNoteToExxport(exxport.index)" placeholder="Ajouter une note ici ...">
                      </li>
                      <li v-for="(n, i) in exxport.notes">
                        <input type="text" v-model="exxport.notes[i]" data-ti="exxport.index" @change="checkExxport">
                        <span class="icon icon-cross" v-on:click="deleteNote(index, i) "  style="position: absolute; right: 10px; top: 15px;"></span>
                      </li>
                  </ul>
                </div>
                <div class="exxport__notes__footer">
                  <span class="exxport__notes__button--close"
                          v-on:click="addOrRemoveFromExxportNotes(exxport)"><i
                          class="icon icon-check"></i>
                  </span>
                </div>
              </div>
            </div>
            <div class="actions">
              <div class="icon" style="width:25px; height: 25px; display: inline-block; margin-right: 15px;">
                <span class="picto-duplicate" v-on:click="duplicateExport(exxport);"></span>
              </div>
              <div class="icon" style="width: 25px; height: 25px; display:inline-block">
                <span class="picto-delete-export-line" v-on:click="removeExport(index); Exxport._deleteOne(exxport, exxports);"></span>
              </div>
            </div>
          </div>
        </div>
        <div v-on:click.prevent="addExport();" class="exxport-add"><span class="icon icon-plus"></span></div>
      </div>
      <div class="exxport-bottom">
        <button v-on:click.prevent="close()" class="button leave">Quitter</button>
        <button class="button confirm">Valider</button>
      </div>
    </form>
  </div>
</template>

<script>
    export default {
    props: ['_project', '_exxport', '_exxports', '_type'],

    data() {
      return {
      	selectedProject: null,
        exxports: [],
        loadedExxports: [],
        exxport: {
          id: null,
          name: '',
          resolution: null,
          format: { id: null, name: null },
          format_raw: null,
          language: { id: null, name: null },
          notePreview: '',
          notes: [],
          hours: '00',
          minutes: '00',
          seconds: '00',
          width: '1920',
          height: '1080',
          index: null
        },
        selectedExxport: [],
        editedExxportId: null,
        type: null,
        state: null,
        addedExport: 0,
        addOrUpdate: 'Ajouter',
        exxportNotes: []
      }
    },

    created() {
    	if(this._project){
    		this.selectedProject = this._project;
    	}
        if (this._exxports) {
            this.exxports = this._exxports;
        }
        if (this._type) {
    	    this.type = this._type.type;
        }
        if (this._exxport && this.type == "ADD") {
            this.exxport = this._exxport;
        }
        if (this._exxport && this.type == "EDIT") {
    	    this.editedExxportId = this._exxport.id;
            this.addOrUpdate = "Modifier";
        }
        if (this.type == "ADD") {
            this.addExport();
            this.setOptions();
            this.addedExport = 1;
        } else if (this.type == "EDIT") {
            this.setOptions();
            this.addedExport = 0;
        }
    },

    mounted() {
        this.$bus.$on('EXPORT_ADD_OR_UPDATE', () => {
            this.getExports();
            this.setOptions();
            this.state = null;
        });
        this.setExportNotes();
    },

    methods: {
        setExport() {
        this.exxports.forEach(function (exxport) {
          exxport.notePreview = "";
          exxport.notes = exxport.notes && exxport.notes.length > 0 ? JSON.stringify(exxport.notes) : null;
        });
        axios.post("/api/export", {
        	project: this.selectedProject.id,
          exports: this.exxports
        }).then(res => {
          if (res.success === false) {
            // Error
          } else {
            var exportAction = 'ADD_EXXPORT';
            if (this.type == 'EDIT') {
              exportAction = 'EDIT_EXXPORT';
            }
            //this.$bus.$emit('EXPORT_ADD_OR_UPDATE', res.data); // Emit add or update talent event
            /*this.$bus.$emit('ACTION_CHANGED', {
              action: 'CONGRATS',
              type: exportAction
            }); */ // Congrats modal
            //setTimeout(() => {
              this.close(res.data.datas);
            //}, 5000);
          }
        }).catch(error => console.log(error));
      },
        getExports() {
            axios.get('/api/export/?project_id=' + this.selectedProject.id)
                .then(res => {
                if (res.success === false) {
                // Todo : Error
            } else {
                this.exxports = res.data.datas;
            }
        }).catch(error => console.log(error));
        },
    	addExport(){
    	  this.addedExport++;
    	  this.exxport.resolution = null;
          this.exxport.format = { id: 1, name: '422 HQ' };
          this.exxport.language = { id: 1, name: 'FR' };
          this.exxport.index = this.exxports.length;
          this.exxport.hours = '00';
          this.exxport.minutes = '00';
          this.exxport.seconds = '00';
          this.exxport.width = '1920';
          this.exxport.height = '1080';
          this.exxport.notes = [];
          this.exxports.push(Object.assign({}, this.exxport));
          this.setOptions();
    	},
        duplicateExport(exxportToDuplicate = null) {
    	    if (!exxportToDuplicate) {
                for (const index in this.selectedExxport) {
                    if (this.selectedExxport[index].duplicate) {
                        this.addedExport++;
                        this.selectedExxport[index].duplicate.notes = [];
                        this.selectedExxport[index].duplicate.id = null;
                        this.exxports.push(Object.assign({}, this.selectedExxport[index].duplicate));
                        this.exxports[this.exxports.length - 1].index = this.exxports.length - 1;
                    }
                }
            } else {
              this.exxports.push(Object.assign({}, exxportToDuplicate));
              this.exxports[this.exxports.length - 1].id = null;
              this.exxports[this.exxports.length - 1].index = this.exxports.length - 1;
              this.exxports[this.exxports.length - 1].notes = [];
              this.addedExport++;
            }
            this.setOptions();
        },
        setOptions(){
            //this.setResolutionSelect();
            this.setFormatSelect();
            this.setLanguageSelect();

            setTimeout(() => {
              //this.triggerResolutionSelect2();
              this.triggerFormatSelect2();
              this.triggerLanguageSelect2();
        }, 10);
        },
    	removeExport(index){
      	  var exxports = this.exxports.splice(index, 1);
      	  this.addedExport--;
          var index = 0;
          exxports.forEach(function (exxport) {
            exxport.notePreview = "";
            exxport.index = index;
              /* if (exxport.notes && exxport.notes.length > 0) {
                exxport.notes = exxport.notes.split('\n');
              } else {
                exxport.notes = [];
              } */
            index++;
          });
      },
      setState(state) {
    	  if (state) {
    	    this.state = state;
        } else {
    	    this.state = null;
        }
      },

    	/**
       * Set Resolution select2
       */
      setResolutionSelect(){
      	var $this = this;

        $(() => {
        	$('.form__export .js-resolution-data').select2({
            dropdownCssClass: 'has-search',
            placeholder: "Ex : 1920x1080",
            ajax: {
              url: '/api/resolution',
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

          /* $('.js-resolution-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
	        	$this.exxports[key].resolution.id = e.params.data.id;
	      		$this.exxports[key].resolution.name = e.params.data.name;
	        });
	    	 	$('.js-resolution-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
	    	 		$this.exxports[key].resolution.id = null;
	      		$this.exxports[key].resolution.name = '';
	    	 	}); */
        });
      },

      /**
       * Set Format select2
       */
      setFormatSelect(){
      	var $this = this;

        $(() => {
        	$('.form__export .js-format-data').select2({
            dropdownCssClass: 'has-search',
            placeholder: "Ex : ProRes 422",
            ajax: {
              url: '/api/format',
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

          $('.js-format-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
	        	$this.exxports[key].format.id = e.params.data.id;
	      		$this.exxports[key].format.name = e.params.data.name;
	        });
	    	 	$('.js-format-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
	    	 		$this.exxports[key].format.id = null;
	      		$this.exxports[key].format.name = '';
	    	 	});
        });
      },

      /**
       * Set Language select2
       */
      setLanguageSelect(){
      	var $this = this;

        $(() => {
        	$('.form__export .js-language-data').select2({
            dropdownCssClass: 'has-search',
            placeholder: "Ex : Anglais",
            ajax: {
              url: '/api/language',
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

          $('.js-language-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
	        	$this.exxports[key].language.id = e.params.data.id;
        		$this.exxports[key].language.name = e.params.data.name;
	        });
	    	 	$('.js-language-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
	    	 		$this.exxports[key].language.id = null;
        		$this.exxports[key].language.name = '';
	    	 	});
        });
      },

      /**
       * Fill the resolution in the select2
       */
      triggerResolutionSelect2(){
      	var $this = this;
       	let resolutionSelect2 = $('.js-resolution-data');

       	resolutionSelect2.each(function(index, item){
       		//console.log('Index', index);
       		//console.log($this.exxports[index]);
       		//console.log($this.exxports[index].resolution.id);
       		if($this.exxports[index].resolution.id){
     				let option = new Option($this.exxports[index].resolution.name, $this.exxports[index].resolution.id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },

      /**
       * Fill the language in the select2
       */
      triggerFormatSelect2(){
      	var $this = this;
       	let formatSelect2 = $('.js-format-data');

       	formatSelect2.each(function(index, item){
       		if($this.exxports[index].format.id){
     				let option = new Option($this.exxports[index].format.name, $this.exxports[index].format.id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },

      /**
       * Fill the language in the select2
       */
      triggerLanguageSelect2(){
      	var $this = this;
       	let languageSelect2 = $('.js-language-data');

       	languageSelect2.each(function(index, item){
       		if($this.exxports[index].language.id){
     				let option = new Option($this.exxports[index].language.name, $this.exxports[index].language.id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },
      close(exportNewIds = false){
        let params = '';
        if (exportNewIds) {
          params = '?newExport=true&exportIds=';
          for (let i = 0; i < exportNewIds.length; i++) {
            if (exportNewIds[i]) {
              let exportId = exportNewIds[i];
              params = params + exportId;
              if (i != exportNewIds.length - 1) {
                params = params + ','
              }
            }
          }
        }
        window.location.href = this._project.url + params;
      },
        addNoteToExxport(index) {
            const idx = this.exxports.findIndex(exxport => exxport.index === index);
            if (this.exxports[idx].notePreview.trim().length > 0) {
                this.exxports[idx].notes.push(this.exxports[idx].notePreview);
                this.exxports[idx].notePreview = '';
            }
        },
        checkExxport: function (event) {
            if (event.target.value.length === 0) {
                for (const i = 0; i < this.exxports.length; i++) {
                    this.exxports[i].notes = this.exxports[i].notes.filter(e => e.length > 0);
                }
            }
        },
        checkExxportNotes(exxport) {
            var index = this.exxportNotes.indexOf(exxport);
            if (index !== -1) {
                return true;
            }
            return false;
        },
        addOrRemoveFromExxportNotes(exxport) {
            var index = this.exxportNotes.indexOf(exxport);
            if (index === -1) {
                this.exxportNotes.push(exxport);
            } else {
                this.exxportNotes.splice(index, 1);
            }
        },
        changeSelect(event, id) {
          var isChecked = event.target.checked;
          var select = false;
          var select = document.getElementById(id);
          if (typeof select !== "undefined") {
            if (isChecked) {
              select.classList.add('export-item-selected');
            } else {
              select.classList.remove('export-item-selected');
            }
          }
        },
        setExportNotes() {
          var index = 0;
          this.exxports.forEach(function (exxport) {
            exxport.notePreview = "";
            exxport.index = index;
              if (exxport.notes && exxport.notes.length > 0) {
                exxport.notes = JSON.parse(exxport.notes);
              } else {
                exxport.notes = [];
              }
            index++;
          });
        },
        deleteNote(exportKey, noteKey) {
            if (
                this.exxports &&
                this.exxports[exportKey] &&
                this.exxports[exportKey].notes
            ) {
                this.exxports[exportKey].notes.splice(noteKey, 1);
            }
        },
        log(error) {
          console.log(error);
        }
    }
}
</script>
