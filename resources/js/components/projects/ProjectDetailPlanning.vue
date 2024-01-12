<template>
  <div id="context_menu_wrapper" class="project-detail__planning block">
    <ContextMenu ref="ContextMenu"></ContextMenu>
    <!-- Planning header -->
    <div class="row">
      <div class="col-md-12 text-center">
        <div class="mb-30">
          <div class="mb-05 planning-header"><span class="icon icon-calendar"></span>{{ $t('planning') }}</div>
        </div>
      </div>
    </div>
    <!-- Planning calendar -->
    <div id="planning-calendar" class="row">
      <div class="col-md-3">
        <div class="total-planned-header month" v-on:click.prevent="openTypeTasks()">
            <span class="left">{{ $t('total') }} {{ nb_days > 1 ? $t('planneds') : $t('planned') }} : </span> <span style="margin-left: 5px;">{{ nb_days }} {{ nb_days > 1 ? $t('days') : $t('day') }}</span>
            <div style="width: 10px; position: absolute; right: 30px;" class="picto-days-chevron"></div>
        </div>
       <div class="project-type-tasks">
           <div class="total-planned" :class="(taskType.class == '3d' ? 'd3' : taskType.class)" v-on:mouseover="(typeTaskOpen) ? hoverTaskTypeId = taskType.taskTypes[0].id : false" v-on:mouseleave="hoverTaskTypeId = false" v-for="taskType in projectTypeTasks" >
               <span class="left"><span class="dot-button-task" :class="taskType.task_type == '3D' ? 'd3' : taskType.task_type" :style="{marginRight: '5px'}"></span>{{ $t(taskType.task_type) }}</span>
               <span class="right">{{ taskType.nb_days }} {{ taskType.nb_days > 1 ? $t('days') : $t('day') }}</span>
           </div>
       </div>
      </div>
      <div class="col" style="z-index: 1;">
        <ProjectDetailPlanningDays :_project="project" :user="user" :contextMenu="$refs.ContextMenu" :_isShared="_isShared" :_isClient="_isClient" />
      </div>
    </div>
    <!-- ./Planning calendar -->

  </div>
</template>

<script>
export default {
    props: ['_project', 'user', '_isShared', '_isClient'],

    data() {
      return {
        project: this._project,
        planning: {},
        projectTypeTasks: {},
        nb_days: 0,
        today: moment().format('YYYY-MM-DD'),
        typeTaskOpen: false,
        hoverTaskTypeId: false,
        taskTypeColors: {
          1:"#37ff9f",
          2:"#01cfbf",
          3:"#37ff46",
          4:"#ff4286",
          5:"#7b9cff",
          6:"#ffd337",
          7:"#b78bff",
          8:"#377bff",
          9:"#7e7bff",
          10:"#be6cff",
          11:"#ff37eb",
          12:"#37ffdb",
          13:"#ff4b4b",
          14:"#ffc672",
          15:"#ff4b4b"
       },
       taskTypeHoverBg: {1:"#15373A", 2:"#23406B", 3:"#2D4953", 4:"#552460", 5:"#3B3678", 6:"#554150", 7:"#3D1925", 8:"#151D4D", 9:"#231D4D", 10:"#301A4D", 11:"#3D0F49", 12:"#2D4971", 13:"#3D1329", 14:"#3D2C31", 15:"#3D173E"},
       taskTypeHoverBorder: {1:"#269B6D", 2:"#128795", 3:"#32A44D", 4:"#ff4286", 5:"#5B69BB", 6:"#AA8A44", 7:"#AA4944", 8:"#1C3071", 9:"#504CA6", 10:"#7743A6", 11:"#9E239A", 12:"#32A4A6", 13:"#9E2F3A", 14:"#9E7952", 15:"#9E3C7A"},
      }
    },

    created() {
    },

    mounted() {
      this.getPlanning();
      //this.openTypeTasks();
      this.$bus.$on('TASK_ADD_OR_UPDATE', () => {
      	this.getPlanning();
      });
    },

    methods: {
      async getPlanning(){
        await axios.get('/api/project/' + this.project.id + '/planning').then(res => {
          if (res.success === false) {
              // Todo : Error
          } else {
              this.planning = res.data.datas; // Update project task list
              this.projectTypeTasks = res.data.datas.project_tasks_summary; // Update project task list
                for (const [key, typeTask] of Object.entries(this.projectTypeTasks)) {
                    typeTask.class = typeTask.task_type.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(" ", "-").toLowerCase();
                }
                this.getNbDays();

                if (this.project.id == 1) {
                  this.projectTypeTasks = [
                            {'nb_days': 1, 'task_type': 'project-preparation', 'class': 'project-preparation', 'taskTypes': [{'id': 15}]},
                            {'nb_days': 4, 'task_type': 'editing', 'class': 'editing', 'taskTypes': [{'id': 2}]},
                            {'nb_days': 1, 'task_type': 'artistic-direction', 'class': 'artistic-direction', 'taskTypes': [{'id': 3}]},
                            {'nb_days': 4, 'task_type': 'VFX', 'class': 'vfx', 'taskTypes': [{'id': 7}]},
                            {'nb_days': 2, 'task_type': 'motion', 'class': 'motion', 'taskTypes': [{'id': 5}]},
                            {'nb_days': 1, 'task_type': 'pre-grading-confo', 'class': 'pre-grading-confo', 'taskTypes': [{'id': 10}]},
                            {'nb_days': 1, 'task_type': 'color-grading', 'class': 'color-grading', 'taskTypes': [{'id': 11}]},
                            {'nb_days': 1, 'task_type': 'post-grading-confo', 'class': 'post-grading-confo', 'taskTypes': [{'id': 9}]}
                        ];
                this.nb_days = 17;
              }
          }
        }).catch(error => console.log(error));
      },

      getNbDays(){
          if (this.planning.project_tasks_summary) {
              this.nb_days = 0;
              for (const [key, value] of Object.entries(this.planning.project_tasks_summary)) {
                  this.nb_days += parseInt(value.nb_days); // value.tasks.length
              }
          }
      },
      setPlanningType(type) {
        this.$parent.type = type;
      },
      openTypeTasks() {
          this.typeTaskOpen = !this.typeTaskOpen;
          var projectTypeTask = document.getElementsByClassName("project-type-tasks");
          if (projectTypeTask[0]) {
              if (this.typeTaskOpen) {
                  projectTypeTask[0].classList.add("project-type-tasks-hover");
              } else {
                  projectTypeTask[0].classList.remove("project-type-tasks-hover");
              }
          }
      }
    }
}
</script>