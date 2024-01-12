<template>
    <div class="register_login-wrapper js-first-login">
        <div v-if="isFreelance" class="explorer-form explorer-profile-infos-form row" style="background-color: rgba(5, 1, 14, 0); padding-top: 0; ">
            <div class="infos-form col-12">
                <div class="register-wrapper_wrapper">
                    <form class="register_login-form" v-on:submit.prevent="patchUser" v-if="isFreelance">
                        <p class="welcome-screen__register">Hello {{ formUser.lastname +" " + formUser.firstname }} !</p>
                        <div class="main-profile_register">
                            <input hidden id="file" ref="file" name="file" type="file" @change="changeProfilePicture">
                            <div class="profile-image_register">
                                <span v-if="showImgPlaceholder" class="user-initials" >{{ getUserInitial(userToSee) }}</span>
                                <img class="image_register" v-else :src="'/upload/avatars/' + userToSee.avatar" style="max-width: 100%" alt="profile"
                                     @error="onImgError">
                            </div>
                            <div v-if="isSelfProfile" class="edit-profile-image-container" style="display: flex;align-items: center;">
                                    <a @click="selectFile" class="edit-profile-image_register">Modifier la photo de profil</a>
                            </div>
                        </div>
                        <div class="infos-form-group">
                            <div class="explorer-form-main-label">
                                <div class=" icon-register"><span class="picto-explorer-user"/></div>
                                <h2>Informations personnelles*</h2>
                            </div>
                            <div class="input-container_register">
                                <input  class="border-register" v-model="formUser.firstname" placeholder="Prénom" type="text">
                                <input class="border-register" v-model="formUser.lastname" placeholder="Nom" type="text">
                            </div>
                        </div>

                        <div class="infos-form-group">
                            <div class="explorer-form-main-label">
                                <div class="icon-register"><span class="picto-explorer-user"/></div>
                                <h2>Coordonnées*</h2>
                            </div>
                            <div>
                                <div class="input-container_register">
                                    <input class="border-register"
                                           v-model="formUser.email" placeholder="Votre mail" type="email">
                                    <input class="border-register"
                                           v-model="formUser.phone" placeholder="Votre numéro" type="tel">
                                </div>
                            </div>
                        </div>
                        <div class="infos-form-group">
                            <div class="explorer-form-main-label">
                                <div class="icon-register"><span class="picto-explorer-job"/></div>
                                <h2>Métier & Compétences</h2>
                            </div>
                            <div class="input-container_register" style="margin-bottom: 15px;">
                                <div style="width: 49.5%;">
                                    <select v-model="formUser.job_id" v-select2
                                            class="js-jobs-data js-select2 js-required"
                                            id="job-data" name="job[]" multiple="multiple"></select>
                                </div>
                                <div style="width: 49.5%;">
                                    <input class="border-register" v-model="formUser.website" placeholder="Portfolio" style="width: 100%;margin:0;" type="text">
                                </div>
                            </div>
                                <div class="input-container_register">
                                    <select  v-model="formUser.skills_ids" v-select2
                                            class="js-skills-data js-select2 js-required" id="skills-data"
                                            name="skills[]" multiple="multiple"></select>
                                </div>
                        </div>
                        <div class="infos-form-group">
                            <div class="explorer-form-main-label">
                                <div class="icon-register"><span class="picto-explorer-user"/></div>
                                <h2>Description</h2>
                            </div>
                            <div class=" no-padding-form">
                                <textarea class="border-register"
                                          v-model="formUser.description" placeholder="Décrivez-vous en quelques lignes..." type="text"
                                          style="width: 100%; margin-left: 66px;"></textarea>
                            </div>
                        </div>
                        <div class="explorer-form-main-label">
                            <div class="icon-register"><span class="picto-explorer-lock"/></div>
                            <h2>Mot de passe*</h2>
                        </div>
                        <div class="input-container_register">
                            <label>Nouveau mot de passe - 8 caractères minimum avec au moins une Majuscule et 1 symbole</label>
                            <input v-model="newPassword" class="password-input border-register" placeholder="Votre nouveau mot de passe*" type="password">
                        </div>
                        <div class="input-container_register ">
                            <label>Confirmez le nouveau mot de passe</label>
                            <input v-model="newPasswordConfirm" class="password-input border-register" placeholder="Confirmez votre nouveau mot de passe*" type="password">
                            <p style="color:red;">{{errorMessage}}</p>
                        </div>
                        <div class="check-label">
                            <input id="talent-register_checkbox" type="checkbox" name="talent-register_checkbox" style="appearance: unset;">
                            <label class="label_check" for="talent-register_checkbox" style="margin-left: 10px;">Je déclare avoir lu et accepté le <span class="info">Condition générale d'utilisation</span> et la <span class="info">Politique de confidentialité.</span></label>
                        </div>
                        <button class="submit-button" type="submit" >Allons-y !</button>

                    </form>
                </div>
            </div>
        </div>

        <div v-else class="explorer-form explorer-profile-infos-form row" style="background-color: rgba(5, 1, 14, 0);padding-top: 0;">
            <div class="infos-form col-12">
                <form class="register_login-form" v-on:submit.prevent="patchUser" :is-self-profile="isSelfProfile" >
                    <p class="welcome-screen__title">Hello {{ user.firstname }} !</p>
                    <div class="main-profile-resume" style="height: 100%;">
                        <input hidden id="file" ref="file" name="file" type="file" @change="changeProfilePicture">
                        <div class="profile-image_register">
                            <span v-if="showImgPlaceholder" class="user-initials">{{ getUserInitial(userToSee) }}</span>
                            <img class="image_register" v-else :src="'/upload/avatars/' + userToSee.avatar" style="max-width: 100%" alt="profile"
                                 @error="onImgError">
                        </div>
                        <div v-if="isSelfProfile" class="edit-profile-image-container" style="display: flex;align-items: center;">
                            <a @click="selectFile" class="edit-profile-image_register">Modifier la photo de profil</a>
                        </div>
                    </div>

                    <div class="infos-form-group">
                        <div class="explorer-form-main-label">
                            <div class="icon-register"><span class="picto-explorer-user"/></div>
                            <h2>Informations personnelles*</h2>
                        </div>
                        <div class="no-padding-form">
                            <input class="border-register" v-model="formUser.firstname" placeholder="Prénom" type="text">
                            <input class="border-register" v-model="formUser.lastname" placeholder="Nom" type="text">
                        </div>
                    </div>

                    <div class="infos-form-group">
                        <div class="explorer-form-main-label">
                            <div class="icon-register"><span class="picto-explorer-user"/></div>
                            <h2>Coordonnées*</h2>
                        </div>
                        <div class="no-padding-form">
                            <input  class="border-register"
                                   v-model="formUser.email" placeholder="Votre mail"
                                   type="email">
                            <input class="border-register"
                                   v-model="formUser.phone" placeholder="Votre numéro"
                                   type="tel">
                        </div>
                    </div>

                    <div class="infos-form-group no-padding-form" >
                        <div class="explorer-form-main-label">
                            <div class="icon-register"><span class="picto-explorer-job"/></div>
                            <h2>Métier & Société</h2>
                        </div>
                        <div class=" no-padding-form">
                            <input class="border-register"
                                   v-model="formUser.client_job" placeholder="Votre métier" type="text">
                            <input class="border-register"
                                   v-model="formUser.company" placeholder="Nom de la société (facultatif)" type="text">
                        </div>
                    </div>
                    <div class=" no-padding-form" >
                        <label>Mot de passe actuel</label>
                        <input v-model="currentPassword" class="password-input" placeholder="Entrez votre mot de passe actuel" type="password">
                    </div>
                    <div class=" no-padding-form" >
                        <label>Nouveau mot de passe - 8 caractères minimum avec au moins une Majuscule et 1 symbole</label>
                        <input v-model="newPassword" class="border-register password-input" placeholder="Votre nouveau mot de passe*" type="password">
                    </div>
                    <div class=" no-padding-form ">
                        <label>Confirmez le nouveau mot de passe</label>
                        <input v-model="newPasswordConfirm" class="border-register password-input" placeholder="Confirmez votre nouveau mot de passe*" type="password">
                        <p style="color:red;">{{errorMessage}}</p>
                    </div>
                    <div>
                        <input id="horns" type="checkbox" name="horns" >
                        <label for="horns">Je déclare avoir lu et accepté le Condition générale d'utilisation et la Politique de confidentialité.</label>
                    </div>
                    <button class="submit-button" type="submit" v-if="!formSended" @click="patchUser">Enregistrer</button>
                    <button class="submit-button button-sended" v-else-if="formSended">Enregistré</button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
export default {

    props: [ 'user', 'isSelf','userToSee','role'],
    data() {
        return {

            formUser: {...this.user,
            "job_id": [],
            "skills_ids":[]},
            formSended: false,
            isInputDisabled: true,
            currentPassword : '',
            newPassword : '',
            newPasswordConfirm : '',
            isSended : false,
            errorMessage : null,
            showImgPlaceholder: false,
        }
    },
    computed: {
        isFreelance(){
            return this.user.role.name = "talent"
        },
        isSelfProfile() {
            return this.user.id === this.userToSee.id
        },
    },
    mounted() {
        this.getJobs();
        this.getSkills();
        this.triggerSelect2();
        this.setSelect2();

        setTimeout(() => {

        }, 100);
    },

    methods: {
        getSkills(){
            var params = {};

            setTimeout(() => {
                axios.get("/api/skill", {
                    params: params
                }).then(res => {
                    if(res.success === false){
                        // Todo : Error
                    } else {
                        this.skill = res.data.datas; // Update skills list
                    }
                }).catch(error => console.log(error));
            }, 10);
        },
        getJobs(){
            var params = {};
            setTimeout(() => {
                axios.get("/api/job", {
                    params: params
                }).then(res => {
                    if(res.success == false){

                    }else{
                        this.jobs = res.data.datas;
                    }
                }).catch(error =>console.log(error))
            },10);
        },

        /**
         * Set skills select2
         */
        setSkillsSelect(){
            $(() => {
                $('.js-skills-data').select2({
                    dropdownCssClass: 'multiple-choices filters-dropdown',
                    placeholder: "Votre / Vos compétence(s) principale(s)*",
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

                $('.js-skills-data').on("select2:select", (e) => { this.getSkills(); });
            });
        },

        setSelect2() {
            if (this.isFreelance) {
                this.setSkillsSelect();
            }
            this.setJobsSelect();
        },
        /**
         * Fill select2
         * */
        triggerSelect2() {
            if (this.isFreelance) {
                this.triggerSkillsSelect2();
            }
            this.triggerJobsSelect2();
        },
        getUserInitial(talent) {
            return talent.firstname.charAt(0) + talent.lastname.charAt(0);
        },
        onImgError() {
            this.showImgPlaceholder = !this.showImgPlaceholder;
        },
        addPortfolioModalOpen() {
            this.showAddPortfolioModal = true;
            $('body').toggleClass('body-modal');
        },
        addPortfolioModalClose() {
            this.showAddPortfolioModal = false;
            $('body').toggleClass('body-modal');
        },
        proposeMissionModalOpen() {
            this.showProposeMissionModal = true;
            $('body').toggleClass('body-modal');
        },
        proposeMissionModalClose() {
            this.showProposeMissionModal = false;
            $('body').toggleClass('body-modal');
        },
        emitProfileUpdated() {
            this.$emit('profile-updated')
        },
        changeProfilePicture() {
            // Get the selected file
            let files = $('#file')[0].files;

            if (files.length > 0) {

                let fd = new FormData();

                // Append data
                fd.append('file', files[0]);

                axios.post("/api/user/picture-change", fd, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).then(res => {
                        this.emitProfileUpdated();
                    }
                )
            }
        },
        selectFile() {
            let fileInputElement = this.$refs.file;
            fileInputElement.click();
        },

        lockForm() {
            this.isInputDisabled = true;
        },

        postPasswordChange() {
            axios.patch('/api/user/password-change', {
                old_password : this.currentPassword,
                new_password : this.newPassword,
                confirm_password : this.newPasswordConfirm,
            }).then(res => {
                if (res.success === true) {
                    this.isSended = true;
                    this.errorMessage = null;
                }
            }).catch(error => {
                this.errorMessage = error.response.data.message
            });
        },
        /**
         * Set jobs select2
         */
        setJobsSelect(){
            $(() => {
                $('.js-jobs-data').select2({
                    dropdownCssClass: 'multiple-choices filters-dropdown',
                    placeholder: "Votre métier*",
                    //minimumInputLength: 1,
                    closeOnSelect: false,
                    language: {
                        searching: function() { return "Chargement..."; }
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

                $('.js-jobs-data').on("select2:select", (e) => { this.getJobs(); });

            });
        },

        updateUserInfos() {
            axios.get('/api/talent/' + this.user.id).then(
                res => {
                    this.updatedUser = res.data.datas;
                }
            )
        },

        /**
         * Fill the job in the select2
         */
        triggerJobsSelect2() {
            let jobsSelect2 = $('#client-job-data');

            if (this.isFreelance) {
                jobsSelect2 = $('#job-data');
            }

            if (this.formUser.job) {
                let option = new Option(this.formUser.job.name, this.formUser.job.id, true, true);
                jobsSelect2.append(option).trigger('change');
            }
        },

        /**
         * Fill the select2 with already selected Skills
         */
        triggerSkillsSelect2() {
            let skillsSelect2 = $('#skills-data');

            this.formUser.skills.forEach(skill => {
                let option = new Option(skill.name, skill.id, true, true);
                skillsSelect2.append(option).trigger('change');
            });
        },

        patchUser() {
            axios.patch('api/user', {
                user: this.formUser
            }).then(res => {
                this.formSended = true;
                this.postPasswordChange();
                this.errorMessage = null;
                this.emitFormUpdated();
            }).catch(error => {
                console.log(error)
                this.errorMessage = error.response.data.message
            });
        },

        emitFormUpdated() {
            this.$emit('form-updated')
        }
    }
}
</script>
