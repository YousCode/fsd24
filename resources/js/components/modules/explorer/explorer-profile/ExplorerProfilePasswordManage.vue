<template>
    <div class="explorer-profile-password-manage row">
        <div class="password-manage-form col-12">
            <form class="explorer-form" method="post" v-on:submit.prevent="postPasswordChange">
                <div class="explorer-form-main-label">
                    <div class="icon-container"><span class="picto-explorer-lock"/></div>
                    <h2>Mot de passe*</h2>
                </div>

                <div class="input-container m-t_2">
                    <label>Mot de passe actuel</label>
                    <input v-model="currentPassword" class="password-input" placeholder="Entrez votre mot de passe actuel" @keydown="oldPasswordError=''" type="password">
                    <p style="color:red;">{{oldPasswordError}}</p>
                </div>
                <div class="input-container m-t_2">
                    <label>Nouveau mot de passe - 8 caractères minimum avec au moins une Majuscule et 1 symbole</label>
                    <input v-model="newPassword" class="password-input" placeholder="Nouveau mot de passe" type="password" @keydown="errorMessage=''">
                    <p style="color:red;">{{errorMessage}}</p>
                </div>
                <div class="input-container m-t_2">
                    <label>Confirmez le nouveau mot de passe</label>
                    <input v-model="newPasswordConfirm" class="password-input" placeholder="Confirmez votre nouveau mot de passe" type="password" @keydown="confirmPasswordError=''">
                    <p style="color:red;">{{confirmPasswordError}}</p>
                </div>
                <div class="input-container m-t_2">
                    <button v-if="!isSended" class="submit-button" type="submit">Modifier le mot de passe</button>
                    <button disabled class="submit-button button-sended" v-else-if="isSended"> <svg width="14" height="11" style="margin-right: 6px" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M6.15572 10.2953C5.80067 10.6769 5.19708 10.679 4.8394 10.2999L0.594738 5.8014C0.262496 5.44929 0.267479 4.89769 0.606028 4.55164L0.886902 4.26454C1.24344 3.9001 1.83095 3.90339 2.18339 4.2718L5.47029 7.70769L12.0681 0.666187C12.4116 0.299572 12.9885 0.284295 13.3509 0.632214L13.6593 0.928301C14.0148 1.26949 14.0306 1.83275 13.695 2.19342L6.15572 10.2953Z" fill="white"/> </svg>Mot de passé édité !</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            currentPassword : '',
            newPassword : '',
            newPasswordConfirm : '',
            isSended : false,
            errorMessage : null,
            confirmPasswordError: null,
            oldPasswordError:null

        }
    },
    created() {

    },
    mounted() {
    },
    methods: {
        postPasswordChange() {
            axios.patch('/api/user/password-change', {
                old_password : this.currentPassword,
                new_password : this.newPassword,
                confirm_password : this.newPasswordConfirm,
            }).then(res => {
                if (res.status === 200) {
                    this.isSended = true;
                    this.errorMessage = null;
                }
            }).catch(error => {
                let passwordError = document.querySelectorAll(".password-input")
                if(error) {

                    if (typeof error.response.data.message !== 'undefined') {
                        if(error.response.data.message.old_password){
                            this.oldPasswordError = error.response.data.message.old_password[0]
                            passwordError[0].classList.add("is-invalid")
                            passwordError[0].classList.add("field-error")
                        }else{
                            passwordError[0].classList.remove("is-invalid")
                            passwordError[0].classList.remove("field-error")
                        }
                        if(error.response.data.message.confirm_password)
                        {
                            this.confirmPasswordError = error.response.data.message.confirm_password[0]
                            passwordError[2].classList.add("is-invalid")
                            passwordError[2].classList.add("field-error")
                        }else{
                            passwordError[2].classList.remove("is-invalid")
                            passwordError[2].classList.remove("field-error")
                        }
                        if(error.response.data.message.new_password){
                            this.errorMessage = error.response.data.message.new_password[0]
                            passwordError[1].classList.add("is-invalid")
                            passwordError[1].classList.add("field-error")
                        }else{
                            passwordError[1].classList.remove("is-invalid")
                            passwordError[1].classList.remove("field-error")
                        }

                    }
                }
                //this.errorMessage = error.response.data.message
            });
        },
    },
}
</script>
