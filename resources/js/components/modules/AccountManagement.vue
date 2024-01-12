<template>
    <div>
        <!-- Header -->
        <div class="popup-intro text-center mb-30">
          <h2 class="popup-maintitle">Mon compte</h2>
        </div>
        <!-- ./Header -->

        <!-- Form -->
        <form method="POST" class="" v-on:submit.prevent="updateAccount()">
            <div class="popup-box">
                <div class="form-box">
                  <label for="" class="form-label">Nom</label>
                  <input type="text" placeholder="Prénom" v-model="user.lastname" required class="form-field">
                </div>

                <div class="form-box">
                  <label for="" class="form-label">Prénom</label>
                  <input type="text" placeholder="Prénom" v-model="user.firstname" required class="form-field">
                </div>

                <div class="form-box">
                  <label for="" class="form-label">Changer de mot de passe</label>
                  <input type="password" placeholder="Nouveau mot de passe" class="form-field" v-model="password">
                </div>

                <div class="form-box">
                  <label for="" class="form-label">Confirmation du mot de passe</label>
                  <input type="password" placeholder="Confirmation du mot de passe" class="form-field" v-model="password_confirm">
                </div>
            </div>

            <button type="submit" class="button is-gradient popup-button">Enregistrer</button>
        </form>
    </div>
</template>

<script>
export default {
    props: ['_user'],

    data() {
        return {
            user: this._user,
            password: '',
            password_confirm : ''
        }
    },

    beforeMount() {
    },

    created(){
    },

    mounted() {
    },

    methods: {
    	updateAccount(){
    		if(this.password.length > 0){
	    		if(this.password.length < 6){
	    			alert('Veuillez saisir un mot de passe de plus de 6 caractères'); return false;
	    		}

	    		if(this.password != this.password_confirm){
	    			alert('Vos mots de passes sont différents'); return false;
	    		}
	    	}

    		axios.patch("/api/talent", {
	    		'user_id': this.user.id,
	    		'firstname': this.user.firstname,
	    		'lastname': this.user.lastname,
	    		'password': this.password
	    	})
				.then(res => {
	  			if(res.success === false){
	      		// Todo : Error
	      	} else {
	      		window.location.reload();
	      	}
	      }).catch(error => console.log(error));
    	}
    }
}
</script>
