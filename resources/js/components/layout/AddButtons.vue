<template>
	<div class="actions-list">
    <button type="button" class="button is-gradient has-arrow" v-on:click="toggle()">Créer</button>
    <div class="actions-list__buttons" v-bind:class="{ 'is-hidden': options == false }">
  		<a href="javascript:;" class="button" v-on:click="Global._setAction('SET_PROJECT', {step: 1})"><span class="icon icon-project"></span> Nouveau projet</a>
  		<a href="javascript:;" class="button" v-on:click="Global._setAction('SET_APPOINTMENT')"><span class="icon icon-calendar"></span> Nouveau RDV</a>
  		<a href="javascript:;" class="button" v-on:click="Global._setAction('SET_TASK', {type: 'ADD'})"><span class="icon icon-task"></span> Nouvelle tâche</a>
  		<a href="javascript:;" class="button" v-on:click="Global._setAction('SET_TALENT')"><span class="icon icon-talent"></span> Nouveau talent</a>
    </div>
	</div>
</template>

<script>
    export default {
    		props: [],

    		data() {
          return {
          	action: null,
            options: false
          }
        },

        mounted() {
        },

        methods: {
          toggle(){
            if(this.options == false){
              this.options = true
            } else {
              this.options = false
            }
          }
      	}
    }
</script>