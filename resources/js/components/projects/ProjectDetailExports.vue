<template>
  <div class="project-detail-modal block project-messenger-conversation explorer-messenger-card explorer-messenger-drive-card">
    <button type="button" v-on:click="toggleExport()" class="card-header">
      <div class="picto-container"><span class="picto-exports"/></div>
      <h1 class="card-title">Liste des livrables ({{ exxports.length }})</h1> <span v-show="newExport" class="notification"></span>
      <div class="card-dropdown-button">
        <span v-if="isShow" class="picto-explorer-dropdown-up"/>
        <span v-else class="picto-explorer-dropdown-down"/>
      </div>
    </button>

    <!--<button type="button" class="button project-detail__action-button is-violet js-toggle-button">
      <span class="dot-button"></span>
      <span class="dot-button"></span>
      <span class="dot-button"></span>
    </button>

    <div class="actions-box js-toggle-content">
      <ul class="context-menu&#45;&#45;items">
        <li class="context-menu&#45;&#45;items-element" v-on:click="Global._setAction('SET_EXPORT', {project: project})"><span class="icon icon-plus"></span> Ajouter un livrable</li>
        <li class="context-menu&#45;&#45;items-element" v-on:click="setState('DELETE')"><span class="icon icon-delete"></span> Supprimer un ou des livrable(s)</li>
      </ul>
    </div>-->

    <div v-show="isShow" class="card-body">
      <!-- Exports -->
      <div class="project-detail__main-info__box project-detail__export-box">
         <div v-if="exxports.length == 0" class="no-items">
          <a :href="(!_isShared) ? project.url_exports : '#'">
            <div class="text-center" v-if="!_isShared && !_isClient">
              <span class="picto-exports-empty"></span>
              <p>Ajouter des livrables</p>
            </div>
            <div class="text-center" v-else>
              <span class="picto-exports-empty"></span>
              <p>Pas de livrables disponible</p>
            </div>
          </a>
        </div>
        <div v-else class="simple-table dark-bckg is-small">
          <div class="simple-table__header">
            <div class="simple-table__row project-export-header">
              <div class="simple-table__td td-40 name"><p>Nom</p></div>
              <div class="simple-table__td td-10 duration "><p>Durée</p></div>
              <div class="simple-table__td td-20  format_raw"><p>Rés.</p></div>
              <div class="simple-table__td td-20 format_name"><p>Sortie</p></div>
              <div class="simple-table__td td-10 language"><p>Lan.</p></div>
            </div>
          </div>
          <a :href="project.url_exports">
            <p class="context-menu--items-element text-center add-export" v-show="!_isShared && !_isClient">
              <a :href="project.url_exports"><span class="icon icon-plus"></span> Ajouter un livrable</a>
            </p>
          </a>
          <div class="simple-table__body export-content">
            <div class="simple-table__row export" v-for="(exxport, key) in exxports">
                <span class="notification row-notification" v-show="showRowNotification(exxport.id)"></span>
                 <div v-bind:id="'export-' + exxport.id" class="export-actions" v-show="!_isShared && !_isClient">
                  <button class="exxport edit">
                    <a :href="project.url_exports + '?export_id=' + exxport.id"><span class="icon icon-edit"></span></a>
                  </button>
                  <button class="exxport delete">
                    <span class="icon icon-delete" v-on:click="Exxport._delete([exxport])"></span>
                  </button>
                </div>
                <div v-if="exxport.notes.length > 0" v-bind:id="'export-notes-' + exxport.id" v-on:click="addOrRemoveFromExxportNotes(exxport)" class="export-notes" :style="(exxport.notes.length > 0) ? {} : {opacity: 0.5}">
                  <div style="width: 15px; height: 15px;"><span class="picto-notes"></span></div>
                </div>
                <div v-bind:id="'export-notes-content-' + exxport.id" class="export-notes-content" v-show="checkExxportNotes(exxport) && exxport.notes.length > 0" :style="_isShared ? {right: '50px'} : {}">
                  <ul>
                    <li v-for="note in exxport.notes">
                      {{ note }}
                    </li>
                  </ul>
                </div>
                <div class="wrapper-checkbox" v-show="!_isShared && !_isClient" v-on:click="updateExport(exxport.id, exxport.is_closed)">
                  <input type="checkbox" v-model="exxport.is_closed" :value="exxport.id" :id="'export-' + exxport.id" />
                  <span class="checkbox"></span>
                </div>
                <div class="simple-table__td td-40 name">
                <label :for="'export-' + exxport.id" v-if="state == 'DELETE'">
                  <span :title="exxport.name">{{ exxport.name }}</span>
                </label>
                <p v-else>
                  <span :title="exxport.name" 
                  :class="{ 'hovered': hovered }" 
                  v-text="hovered ? exxport.name : exxport.name.length > 5 ? exxport.name.substring(0,5)+'\n...'+ exxport.name.substring(exxport.name.length-3,exxport.name.length) : exxport.name"></span>
                  </p>
                  </div>
              <div class="simple-table__td td-10 duration "><p>{{ exxport.duration }}</p></div>
                <div class="simple-table__td td-20 format_raw"><p>{{ exxport.width }} <br> {{ exxport.height }}</p></div>
                <div class="simple-table__td td-20 format_name"><p>{{ exxport.format.name }}</p></div>
                <div class="simple-table__td td-10 language"><p>{{ exxport.language.name }}</p></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- ./Exports -->
  </div>
  <!-- 
      <div class="col-md-8 col-custom-small">

          &lt;!&ndash; Header &ndash;&gt;
      <div class="project-detail__main-info__box is-light project-detail__export-box-header js-toggle-wrapper">
        <h2 class="main-title mb-05">
            <span class="icon icon-player"></span> Livrables
        </h2>
        <p class="text c-grey-b title-indent">{{ exxports.length }} <template v-if="exxports.length < 2">livrable</template> <template v-else>livrables</template></p>

        <div v-if="state == 'DELETE'" class="delete-actions">
          <button type="button" class="action-link" v-on:click="state = null">Annuler</button>
          <button :disabled="deletedItems.length == 0" v-on:click="Exxport._delete(deletedItems)" class="button icon-button">
              <span class="icon icon-delete"></span>
          </button>
        </div>

        <button type="button" class="button project-detail__action-button is-violet js-toggle-button">
          <span class="dot-button"></span>
          <span class="dot-button"></span>
          <span class="dot-button"></span>
        </button>

        <div class="actions-box js-toggle-content">
          <ul class="context-menu&#45;&#45;items">
            <li class="context-menu&#45;&#45;items-element" v-on:click="Global._setAction('SET_EXPORT', {project: project})"><span class="icon icon-plus"></span> Ajouter un livrable</li>
            <li class="context-menu&#45;&#45;items-element" v-on:click="setState('DELETE')"><span class="icon icon-delete"></span> Supprimer un ou des livrable(s)</li>
          </ul>
        </div>

      </div>
      &lt;!&ndash; ./Header &ndash;&gt;

      &lt;!&ndash; Exports &ndash;&gt;
      <div class="project-detail__main-info__box is-light project-detail__export-box">
        <div class="simple-table dark-bckg is-small">
          <div class="simple-table__header">
            <div class="simple-table__row">
              <div class="simple-table__td td-40"><p>Film</p></div>
              <div class="simple-table__td td-10"><p>Durée</p></div>
              <div class="simple-table__td td-20"><p>Résolution</p></div>
              <div class="simple-table__td td-20"><p>Sortie</p></div>
              <div class="simple-table__td td-10"><p><span class="icon icon-language"></span></p></div>
            </div>
          </div>
          <div class="simple-table__body">
            <div class="simple-table__row" v-for="exxport in exxports">
                <div class="wrapper-checkbox" v-if="state == 'DELETE'">
                <input type="checkbox" v-model="deletedItems" :value="exxport.id" :id="'export-' + exxport.id" />
                <span class="checkbox"></span>
              </div>
              <div class="simple-table__td td-40 name">
                <label :for="'export-' + exxport.id" v-if="state == 'DELETE'">{{ exxport.name }}</label>
                <p v-else>{{ exxport.name }}</p>
              </div>
              <div class="simple-table__td td-10"><p>{{ exxport.duration }}</p></div>
              <div class="simple-table__td td-20"><p>{{ exxport.resolution.name }}</p></div>
              <div class="simple-table__td td-20"><p>{{ exxport.format_raw }}</p></div>
              <div class="simple-table__td td-10"><p>{{ exxport.language.name }}</p></div>
            </div>
          </div>
        </div>
      </div>
      &lt;!&ndash; ./Exports &ndash;&gt;

    </div>
  -->
</template>

<script>
    export default {
        props: ['_project', '_isShared', '_isClient', '_path'],
        data() {
            return {
                project: this._project,
                exxports: [],
                state: null,
                deletedItems: [],
                isShow: false,
                exxportNotes: [],
                newExport: false,
                currentExportsIds: [],
		            newExportsIds: [],
                routeReferer: false,
                path: false,
                hovered: false,
            }
        },

        created() {
          if (this._path) {
            this.path = this._path;
          }
        },

        mounted() {
            this.getExports();
            this.$bus.$on('EXPORT_ADD_OR_UPDATE', () => {
                this.getExports();
                this.state = null;
            });
            window.Echo.private('kolab').listen('.export-added', (e) => {
              this.getExports(true);
              this.newExport = true;
            });
              document.addEventListener('click', (event) => {
              this.handleOutsideClick(event);
            });

            if (this._isShared) {
              this.isShow = true; 
            }
            let queryString = window.location.search;
            let urlParams = new URLSearchParams(queryString);
            //
            if (this.path && this.path.previousUrl && this.path.previousUrl == this.project.url_exports_uri) {
              if (urlParams.has('newExport') && urlParams.get('newExport')) {
              this.newExport = true;
              }
              if (urlParams.has('exportIds') && urlParams.get('exportIds')) {
                let exportIds = urlParams.get('exportIds');
                if (exportIds) {
                  exportIds = exportIds.split(',');
                  for(let index = 0; index < exportIds.length; index++) {
                    if (typeof exportIds[index] != "undefined") {
                      let exportId = exportIds[index];
                      exportIds[index] = parseInt(exportId);
                    }
                  }
                  this.newExportsIds = exportIds;
                }
              }
            }
        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleOutsideClick);
          },
        methods: {
            async getExports(isUpdated = false){
                await axios.get('/api/export/?project_id=' + this.project.id)
                    .then(res => {
                    if (res.success === false) {
                    // Todo : Error
                } else {
                    this.exxports = res.data.datas.exports;
                    for(let index = 0; index < this.exxports.length; index++) {
                      if (typeof this.exxports[index] != "undefined") {
                        this.exxports[index].notes = (this.exxports[index].notes == '') ? '' : JSON.parse(this.exxports[index].notes);
                      }
                    }
                    if (!isUpdated) {
                      this.currentExportsIds = this.exxports.map(a => a.id);
                    } else {
                      this.newExportsIds = this.exxports.map(a => a.id);
                      this.newExportsIds = this.newExportsIds.filter(x => !this.currentExportsIds.includes(x));
                    }
                }
            }).catch(error => console.log(error));
            },
            handleOutsideClick(event) {
            const clickedElement = event.target;
            const isInsideExportNotes = clickedElement.closest('.export-notes');
            if (!isInsideExportNotes) {
                this.exxportNotes = [];
            }},
            onHover: function() {
                this.hovered = true;
            },
            setState(value){
                this.state = value;
                $('.js-toggle-content').fadeOut();
            },
            exportHover: function(exportId, active, isShared, isClient) {
                var exportElement = document.getElementById('export-' + exportId);
                var exportNotes = document.getElementById('export-notes-' + exportId);
                    if (active && !isShared && !isClient) {
                        exportElement.classList.add('export-actions-hover');
                        exportNotes.classList.add('export-notes-hover');
                    } else {
                        this.exxportNotes = [];
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
                  this.handleClick();
            },
            toggleExport() {
              this.$parent.$children[4].isShow = false;
              this.$parent.$children[6].isShow = false;
              this.$parent.$children[7].isShow = false;
              this.isShow = !this.isShow;
            },
            clearNewExport(exportId) {
              if (this.newExportIds) {
                var index = this.newExportIds.indexOf(exportId);
                if (index) {
                  var newExportIds = this.newExportIds.splice(index, 1);
                  window.sessionStorage.setItem('newExportIds', JSON.stringify(newExportIds));
                } 
              }
            },
            showRowNotification(exxportId) {
              if (this.newExportsIds.length == 0) {
                return false;
              } else if (this.newExportsIds.includes(exxportId)) {
                return true;
              }

              return false;
            },
            updateExport(exportId, isClosed) {
              axios.post('/api/export/updateStatus', {exportId: exportId, status: isClosed}).then(res => {
                if (res.data.datas) {
                  /*this.$bus.$emit('ACTION_CHANGED', {
                    action: 'CONGRATS',
                    type: 'UPDATE_EXPORT'
                  });*/
                }
              }).catch(error => console.log(error));
            }
        },
    }
</script>