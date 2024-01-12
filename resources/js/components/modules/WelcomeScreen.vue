<template>
	<div class="welcome-screen js-welcome-screen">
    <div class="welcome-screen__inner">
      <div class="logo-wrapper border-none">
          <img src="/images/ui/kolab-logo-black.png" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
          <img src="/images/ui/logo-kolab.png" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
      </div>

      <p class="welcome-screen__title">Hello {{ user.firstname }} !</p>
    </div>
  </div>
</template>

<script>
    export default {
        props: ['user'],

    		data() {
          return {
          }
        },

        beforeMount() {
      	},

        mounted() {
        },

        created () {
        },

        methods: {

      	}
    }
</script>
