<template>
    <transition name="bounce">
        <div class="popup js-popup editProject">
            <div class="popup-content">
                <div class="popup-box" v-on:click.stop>
                    <p class="text mb-15">
                        <strong>{{ $t(addOrEdit) }} un favori <span v-show="addOrEdit == 'add'">au planning</span></strong>
                    </p>
                    <div class="row">
                        <!-- content -->
                          <div class="create-project-wrapper group">
                            <div class="create-project">
                              <div class="create-project-field">
                                  <label>
                                    <i class="icon"><span class="picto-heart"></span></i>Nom de votre groupe*
                                  </label>
                                  <input class="form-field js-required" required v-model="group.name" type="text" placeholder="Nom de votre groupe*" :style="{width: '100%'}"/>
                              </div>
                              <div class="create-project-field" style="text-align: justify;">
                                  <label>
                                    <i class="icon"><span class="picto-group-color-check"></span></i>Choix de la couleur*
                                  </label>
                                  <input class="form-field js-required" required v-model="group.color" type="hidden"/>
                                  <span class="checkbox" style="background: #FFE500" v-on:click="setColor('#FFE500')">
                                    <div class="check-container" v-show="group.color == '#FFE500'"><span class="picto-group-color-check"></span></div>
                                  </span>
                                  <span class="checkbox" style="background: #FF5C00" v-on:click="setColor('#FF5C00')">
                                    <div class="check-container" v-show="group.color == '#FF5C00'"><span class="picto-group-color-check"></span></div>
                                  </span>
                                  <span class="checkbox" style="background: #B50B00" v-on:click="setColor('#B50B00')">
                                    <div class="check-container" v-show="group.color == '#B50B00'"><span class="picto-group-color-check"></span></div>
                                  </span>
                                  <span class="checkbox" style="background: #00DCCF" v-on:click="setColor('#00DCCF')">
                                    <div class="check-container" v-show="group.color == '#00DCCF'"><span class="picto-group-color-check"></span></div>
                                  </span>
                                  <span class="checkbox" style="background: #00FF75" v-on:click="setColor('#00FF75')">
                                    <div class="check-container" v-show="group.color == '#00FF75'"><span class="picto-group-color-check"></span></div>
                                  </span>
                                  <span class="checkbox" style="background: #00A3FF" v-on:click="setColor('#00A3FF')">
                                    <div class="check-container" v-show="group.color == '#00A3FF'"><span class="picto-group-color-check"></span></div>
                                  </span>
                                  <span class="checkbox" style="background: #D135D4" v-on:click="setColor('#D135D4')">
                                    <div class="check-container" v-show="group.color == '#D135D4'"><span class="picto-group-color-check"></span></div>
                                  </span>
                                  <span class="checkbox" style="background: #FFB359" v-on:click="setColor('#FFB359')">
                                    <div class="check-container" v-show="group.color == '#FFB359'"><span class="picto-group-color-check"></span></div>
                                  </span>
                              </div>
                              <span style="font-weight: bold; color: red; text-align: justify;" v-show="errorColor">Veuillez sélectionner une couleur.</span>
                              <div class="create-project-field" id="group-select-talent">
                                  <label>
                                    <i class="icon">
                                      <span class="picto-group-form-talents"></span>
                                    </i>Talents*
                                  </label>
                                <input class="form-field js-search-users" required v-model="filters.name" type="text" placeholder="Rechercher un ou plusieurs/talent(s)" :style="{width: '100%'}"/>
                              </div>
                              <span style="font-weight: bold; color: red; text-align: justify;" v-show="errorTalents">Sélectionner un ou plusieurs talents.</span>
                              <div class="select-talents">
                                <div class="talent" v-for="talent, key in filters.selectedTalentsName">
                                  {{ talent.name }}
                                  <span class="icon icon-cross" v-on:click="removeTalent(talent.id, key)"></span>
                                </div>
                              </div>
                            </div>
                          </div>
                        <!-- content -->
                    </div>
                    <div class="btn-container">
                        <template>
                            <button class="confirm-popup__button button cancel_btn" v-on:click="close()">
                                Quitter
                            </button>
                        </template>
                        <template>
                            <button class="confirm-popup__button button is-gradient valid_btn" v-on:click="setGroup()">
                                {{ $t(addOrEdit) }}
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
          props: ["_group", "_workspace"],
  
          data() {
        return {
            workspace: this._workspace,
            group: {
                name: '',
                color: false,
                talents: [],
                workspace_id: this._workspace
            },
            newGroup: {
                name: '',
                color: false,
                talents: [],
                workspace_id: this._workspace
            },
            filters: {
                selectedTalentsName: []
            },

            talents: false,
            errorColor: false,
            errorTalents: false,
            id: false,
            addOrEdit: 'add'
        }
      },
  
      created() {
        if (this._group) {
          this.addOrEdit = 'edit';
          this.group = this._group;
          let talents = this.group.talents;

          if (talents && talents.length > 0) {
            for (let i = 0; i < talents.length; i++) {
              if (talents[i]) {
                let groupTalent = talents[i];
                this.filters.selectedTalentsName.push({id: groupTalent.id, name: groupTalent.firstname + ' ' + groupTalent.lastname});
              }
            }
          }
        }
      },
  
      mounted() {
        this.getTalents();
        $(() => {
          $(".js-search-users").autocomplete({
            source: (request, response) => {
              $.get('/api/talent', {
                term: request.term,
                mode: 'search',
                workspace: this.workspace
              })
              .done((data) => {
                response(data.datas);
              });
            },
            create: function() {
              $(this).data('ui-autocomplete')._renderItem = (ul, item) => {
                return $('<li>')
                  .data('item.autocomplete', item)
                  .append('<a>' + item.firstname + ' ' + item.lastname + '</a>')
                  .appendTo(ul);
              };
            },
            minLength: 2,
            select: (event, ui) => {
              let addTalent = true;
              for (let i = 0; i < this.group.talents.length; i++) {
                if (this.group.talents[i] && this.group.talents[i].id) {
                  if (this.group.talents[i].id == ui.item.id) {
                    addTalent = false;
                    break;
                  } else if (this.group.talents[i] == ui.item.id) {
                    addTalent = false;
                    break;
                  }
                }
              }
              if (addTalent) {
                this.group.talents.push(ui.item.id);
                this.filters.selectedTalentsName.push({id: ui.item.id, name: ui.item.firstname + ' ' + ui.item.lastname});
                this.filters.name = '';
              }
              //setTimeout(() => { this.getGroup(); }, 10);
            },
            open: (event, ui) => {
              $('.ui-autocomplete').css({
                width: $('#group-select-talent').width(),
              });
            },
            close: (event, ui) => {
            }
          });
	    });
      },
  
      methods: {
          setGroup(){
            var errors = Vue.prototype.Global._checkForFields(".create-project");
            this.errorColor = false;
            this.errorTalents = false;
            if (!this.group.color || this.group.talents.length == 0 || errors > 0) {
              this.errorColor = (!this.group.color ) ? true : false;
              this.errorTalents = (this.group.talents.length == 0) ? true : false;

              return false;
            }
              axios.post("/api/group", { group: this.group }).then(res => {
                if(res.success === false) {
                    // Error
                } else {
                  this.close();
                  let group = res.data.datas;
                  group.talentData = group.talents;
                  group.talents = group.talents.map(x => (x.id) ? x.id: x);
                  let type = (!this.group.id) ? 'GROUP_ADD' : 'GROUP_EDIT';
                  //if (type == 'GROUP_EDIT') {
                    if (group.talents.length > 1) {
                      this.$bus.$emit('PLANNING_DAYS_UPDATE', group);
                      this.$bus.$emit('UPDATE_PLANNING_TYPE', 'DAYS');
                    } else {
                      this.$bus.$emit('PLANNING_CALENDAR_UPDATE', group);
                      this.$bus.$emit('UPDATE_PLANNING_TYPE', 'MONTH');
                    }
                  //} else if (type == 'GROUP_ADD') {
                    this.$bus.$emit('UPDATE_ACTIVE_GROUP', group);
                  //}
                  
                  this.$bus.$emit('GROUP_UPDATE');
                  this.$bus.$emit('MY_PLANNING', true);
                  this.$bus.$emit("ACTION_CHANGED", {
                    action: "CONGRATS",
                    type: type,
                  });
                }
              }).catch(error => console.log(error));
          },
          getTalents(){  
              axios.get("/api/talent", {
                  only_talent: true,
                }).then(res => {
                if(res.success === false){
                    // Error
                } else {
                  this.talents = res.data.datas;
              }
            }).catch(error => console.log(error));
          },
        close() {
          this.$bus.$emit('ACTION_CHANGED', {action: null}); // Close modal
        },
        setColor(color) {
            this.group.color = color;
        },
        removeTalent(talentId, key) {
          let talentKey = -1;
          for (let i = 0; i < this.group.talents.length; i++) {
            if (this.group.talents[i] && this.group.talents[i].id && this.group.talents[i].id == talentId) {
              talentKey = i;
              break;
            }
          }
          if (talentKey !== -1) {
            this.group.talents.splice(talentKey, 1);
            this.filters.selectedTalentsName.splice(key, 1);
          }
        }
        }
    }
  </script>
  
  
  