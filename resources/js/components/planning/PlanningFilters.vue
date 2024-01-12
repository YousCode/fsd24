<template>
    <div class="col">
        <div class="form__planning">
            <div class="filter-item has-picto">
                <span class="filter-picto picto-search"></span>
                <PlanningAutocomplete :_workspace="workspace"></PlanningAutocomplete>  
            </div>
        </div>
        <div class="row">
            <div class="planning-fav left">
                <button class="today" v-on:click="planningToday()">{{ $t('today') }}</button>
            </div>
            <div class="col planning-fav right">
                <button v-show="selected_talents_name.length > 0" :class="(talentIdSelected == selectedTalent.id) ? 'active' : ''" v-for="selectedTalent in selected_talents_name" v-on:click="setPlanningType('MONTH', [selectedTalent.id, selectedTalent.firstname, selectedTalent.lastname])">
                    <span class="color"></span>{{ $t('planning-of') + ' ' + selectedTalent.firstname + ' ' + selectedTalent.lastname }}
                    <span class="icon icon-delete" v-show="talentIdSelected == selectedTalent.id" v-on:click="deleteSearchedUser({id: selectedTalent.id, firstname: selectedTalent.firstname, lastname: selectedTalent.lastname})"></span>
                </button>
                <button v-for="(group, index) in $parent.groups" v-on:click="getGroup(group);" class="favorites" :class="$parent.$parent.activeGroupId == group.id ? 'active' : ''">
                    <span class="color" v-bind:style="group.color ? { 'background': group.color } : ''"></span>
                    {{ group.name }}
                    <span class="icon icon-edit" v-show="$parent.$parent.activeGroupId == group.id" v-on:click="setGroup(group)"></span>
                    <span class="icon icon-delete" v-show="$parent.$parent.activeGroupId == group.id" v-on:click="deleteGroup(group.id)"></span>
                </button>
                <!--<button v-for="(talent, userId) in $parent.selected_talents_name" v-show="userId != user.id" v-on:click="setPlanningType('MONTH', userId)">
                    {{ talent[0].firstname + ' ' + talent[0].lastname }}
                </button>-->
                <button v-on:click="setMyPlanning()" :class="(myPlanning) ? 'active' : ''"><span class="color"></span>Mon planning</button>
                <button v-on:click="setGroup()" class="active"><span class="icon-planning-header"><i class="picto-heart"></i></span>Créer un groupe</button>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    props: ['_filters', '_groups', '_selected_talents_name', '_talentIdSelected', '_user', '_workspace'],

    data(){
    	return {
            workspace: this._workspace,
            filters: {},
            groups: {},
            selected_talents_name: [],
            talentIdSelected: false,
            user: false,
            group: false,
            currentGroup: false,
            myPlanning: true
      }
    },

    beforeMount(){
    },

    mounted(){
        if (this._filters) {
            this.filters = this._filters;
        }
        if (this._groups) {
            this.groups = this._groups;
        }
        if (this._user) {
            this.user = this._user;
        }

        this.$bus.$on('UPDATE_PLANNING', (group) => {
            this.getGroup(group);
        });

        this.$bus.$on('USER_PLANNING_TAB', (data) => {
            if (data && data.firstname && data.lastname && data.id) {
                this.selected_talents_name.push(data);
                this.talentIdSelected = data.id;
            }
        });

        this.$bus.$on('MY_PLANNING_ACTIVE', (data) => { 
            this.myPlanning = data;
        });

        this.$bus.$on('USER_PLANNING_UNACTIVE', () => {
            this.talentIdSelected = false;
            this.myPlanning = false;
        });

        this.$bus.$on('UPDATE_PLANNING_USER_ACTIVE', (activeUserSearchedId) => {
            this.talentIdSelected = activeUserSearchedId;
        });

        this.$bus.$on('REMOVE_SEARCHED_USER', (index) => {
            this.selected_talents_name.splice(index, 1);
            this.setMyPlanning();
        });

        this.$bus.$on('MY_PLANNING', (add) => {
            if (!add) {
                this.setMyPlanning();
            } else {
                this.myPlanning = false;
            }
        });
    },

    methods: {
        setGroup(group) {
            this.$bus.$emit('ACTION_CHANGED', {
                action: 'SET_GROUP',
                element: group,
                workspace: this.workspace
            });
        },
        getGroup(group) {
            this.$bus.$emit('UPDATE_ACTIVE_GROUP', group);
            let loader = document.getElementById('loaderDays');
            loader.style.display = "grid";
            this.setPlanningType('DAYS', group.talents);
            this.$bus.$emit('PLANNING_DAYS_IS_SCROLLED');
            this.$parent.$parent.$children[1].slicePlanning();
            this.$bus.$emit('GET_GROUP', group);
            this.talentIdSelected = false;
            this.$bus.$emit('USER_PLANNING_UNACTIVE');
        },
        deleteGroup(id) {
            axios.delete("/api/group", {data: {id: id}}).then(res => {
                if (res.success === false) {
                } else {
                    this.$bus.$emit('GROUP_UPDATE');
                    this.$bus.$emit('MY_PLANNING');
                    this.$bus.$emit("ACTION_CHANGED", {
                        action: "CONGRATS",
                        type: "GROUP_DELETE",
                    });
                }
            }).catch(error => console.log(error));
        },
        setPlanningType(type, userIdOrTalents, userPlanning = false) {
            if (type == 'DAYS') {
                if (userIdOrTalents.length == 1) {
                    type = 'MONTH';
                    userIdOrTalents = (userIdOrTalents[0] && userIdOrTalents[0].id) ? userIdOrTalents[0].id : this.user.id;
                }
            }
            if (type == 'MONTH') {
                if (userPlanning) {
                    this.$bus.$emit('PLANNING_CALENDAR_MY_PLANNING');
                }
                this.$bus.$emit('UPDATE_PLANNING_TYPE', type);
                this.$bus.$emit("PLANNING_CALENDAR_UPDATE", {
                    talents: [userIdOrTalents],
                    talentData: [{id: userIdOrTalents[0] ? userIdOrTalents[0] : false, firstname: userIdOrTalents[1] ? userIdOrTalents[1] : false, lastname: userIdOrTalents[2] ? userIdOrTalents[2] : false}]
                });
                this.talentIdSelected = userIdOrTalents[0];
                this.$bus.$emit('UPDATE_PLANNING_USER_ACTIVE', this.talentIdSelected);
                this.$bus.$emit('UPDATE_ACTIVE_GROUP', {id: false});
                if (!userPlanning) {
                    this.$bus.$emit('MY_PLANNING_ACTIVE', false);
                }
                
            } else if (type == 'DAYS') {
                this.$parent.$parent.$children[1].setPlanningType(type, userIdOrTalents);
            }
        },
        setMyPlanning() {
            this.setPlanningType('MONTH', [this.user.id, this.user.firstname, this.user.lastname], true); 
            this.$bus.$emit('MY_PLANNING_ACTIVE', true);
            this.$parent.$parent.activeGroupId = false;
            this.$parent.$parent.currentGroup = false;
            this.talentIdSelected = false;
        },
        planningToday() {
            this.$bus.$emit('PLANNING_TODAY');
            if (this.myPlanning) {
                this.$parent.$parent.$children[2].$refs.calendarPreview.move(new Date());
                this.setMyPlanning();
            } else if (this.$parent.$parent.currentGroup) {
                let group = this.$parent.$parent.currentGroup;
                this.setPlanningType('DAYS', group.talents);
            } else if (!this.myPlanning && !this.$parent.$parent.currentGroup) {
                this.$parent.$parent.$children[2].$refs.calendarPreview.move(new Date());
                this.setPlanningType('MONTH', [this.$parent.$parent.$children[2].current_selected_talent_id]);
            }
        },
        hoverElement(elementId, isHover) {
                let element = document.getElementById(elementId);
                if (element) {
                    if (isHover) {
                        element.style.opacity = 1;
                    } else {
                        element.style.opacity = 0;
                    }
                }
            },

            deleteSearchedUser(talent) {
                for (let i = 0; i < this.selected_talents_name.length; i++) {
                    if (talent && talent.id && this.selected_talents_name[i] && this.selected_talents_name[i].id && this.selected_talents_name[i].id == talent.id) {
                        this.$bus.$emit('REMOVE_SEARCHED_USER', i);
                    }
                }
            }
    }
}
</script>