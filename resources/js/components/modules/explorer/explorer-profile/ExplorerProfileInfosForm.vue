<template>
    <div v-if="isFreelance" class="explorer-form explorer-profile-infos-form row">
        <div class="infos-form col-12">
            <button class="explorer-edit-form-button" @click="handleFormUnlockButton"><span
                style="width: 16.8px; height: 16.8px;" class="picto-explorer-pencil"/></button>
            <form v-on:submit.prevent="patchUser">
                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Informations personnelles*</h2>
                    </div>
                    <div class="input-container">
                        <div class="e-r_f">
                            <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled, 'error': errors.firstname}"
                                   v-model="formUser.firstname" placeholder="Prénom" type="text">
                            <p :class="{'invalid-feedback error-message': errors.lastname}" v-if="errorMessage['firstname']">{{errorMessage['firstname'][0]}}</p>
                        </div>
                        <div class="e-r_f">
                            <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled, 'error': errors.lastname}"
                                   v-model="formUser.lastname" placeholder="Nom" type="text">
                            <p :class="{'invalid-feedback error-message': errors.lastname}" v-if="errorMessage['lastname']">{{errorMessage['lastname'][0]}}</p>
                            </div>
                    </div>
                </div>

                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Coordonnées*</h2>
                    </div>
                    <div class="input-container">
                        <div class="e-r_f">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled, 'error': errors.email}"
                               v-model="formUser.email" placeholder="Votre mail" type="email">
                            <p :class="{'invalid-feedback error-message': errors.email}" v-if="errorMessage['email']">{{errorMessage['email'][0]}}</p>
                        </div>
                        <div class="e-r_f">
                            <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                                   v-model="formUser.phone" placeholder="Votre numéro" type="tel">
                            <p :class="{'invalid-feedback error-message': errors.phone}" v-if="errorMessage['phone']">{{errorMessage['phone'][0]}}</p>
                        </div>
                    </div>
                </div>
                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-job"/></div>
                        <h2>Portfolio & Compétences*</h2>
                    </div>
                    <div class="input-container" style="margin-bottom: 15px;">
                        <div style="width: 49.5%; height: 100%">
                            <select  v-model="formUser.job_id" :disabled="isInputDisabled" v-select2
                                    class="js-job-data js-select2 js-required"
                                    id="job-data" name="job" :class="{'error': errors.job}"></select>
                            <p :class="{'invalid-feedback error-message': errors.job_id}" v-if="errorMessage['job_id']">{{errorMessage['job_id'][0]}}</p>
                        </div>
                        <div class="e-r_f">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.website" placeholder="Site internet" type="text">
                        </div>
                    </div>
                    <div class="input-container">
                        <select v-model="formUser.skillIds" :disabled="isInputDisabled" v-select2
                                class="js-skills-data multiple-choices js-select2 js-required filters-dropdown" id="skills-data"
                                name="skills[]" multiple="multiple" :class="{'error': errors.skills}">
                        </select>
                        <p :class="{'invalid-feedback error-message': errors.skillIds}" v-if="errorMessage['skillIds']">{{errorMessage['skillIds'][0]}}</p>
                    </div>
                </div>
                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Description</h2>
                    </div>
                    <div class="input-container">
                        <textarea :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.description" placeholder="Décrivez-vous" type="text"
                               style="width: 100%"></textarea>
                    </div>
                </div>

                <button :disabled="isInputDisabled" class="submit-button" type="submit" v-if="!formSended">Enregistrer
                </button>
                <button disabled class="submit-button button-sended" v-else-if="formSended">Enregistré</button>
            </form>
        </div>
    </div>

    <div v-else class="explorer-form explorer-profile-infos-form row">
        <div class="infos-form col-12">
            <button class="explorer-edit-form-button" @click="handleFormUnlockButton"><span
                style="width: 16.8px; height: 16.8px;" class="picto-explorer-pencil"/></button>
            <form v-on:submit.prevent="patchUser">
                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Informations personnelles*</h2>
                    </div>
                    <div class="input-container" @click="handleInputUnlock">
                        <div class="e-r_f">
                            <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled, 'error': errors.firstname}"
                                   v-model="formUser.firstname" placeholder="Prénom"
                                   type="text">
                            <p :class="{'invalid-feedback error-message': errors.firstname}" v-if="errorMessage['firstname']">{{errorMessage['firstname'][0]}}</p>
                        </div>
                        <div class="e-r_f">
                            <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled, 'error': errors.lastname}"
                                   v-model="formUser.lastname" placeholder="Nom" type="text">
                            <p :class="{'invalid-feedback error-message': errors.lastname}" v-if="errorMessage['lastname']">{{errorMessage['lastname'][0]}}</p>
                        </div>
                    </div>
                </div>

                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Coordonnées*</h2>
                    </div>
                    <div class="input-container">
                        <div class="e-r_f">
                            <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled, 'error': errors.email}"
                                   v-model="formUser.email" placeholder="Votre mail"
                                   type="email" @click="handleInputUnlock">
                            <p :class="{'invalid-feedback error-message': errors.email}" v-if="errorMessage['email']">{{errorMessage['email'][0]}}</p>
                        </div>
                        <div class="e-r_f">
                            <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                                   v-model="formUser.phone" placeholder="Votre numéro"
                                   type="tel" @click="handleInputUnlock">
                            <p :class="{'invalid-feedback error-message': errors.phone}" v-if="errorMessage['phone']">{{errorMessage['phone'][0]}}</p>
                        </div>
                    </div>
                </div>

                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-job"/></div>
                        <h2>Métier & Société</h2>
                    </div>
                    <div class="input-container">
                        <div class="e-r_f">
                        <select v-model="formUser.job_id" :disabled="isInputDisabled"
                                 class="js-job-data js-select2 js-required"
                                 id="client-job-data" name="job" ></select>
                            <p :class="{'invalid-feedback error-message': errors.skillIds}" v-if="errorMessage['skillIds']">{{errorMessage['skillIds'][0]}}</p>
                        </div>
                        <div class="e-r_f">
                            <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                                   v-model="formUser.company" placeholder="Société" type="text" @click="handleInputUnlock">
                        </div>
                    </div>
                </div>

                <button :disabled="isInputDisabled" class="submit-button" type="submit" v-if="!formSended">Enregistrer
                </button>
                <button disabled class="submit-button button-sended" v-else-if="formSended">Enregistré</button>
            </form>
        </div>
    </div>
</template>
<script>

import messages from '../../../../i18n/messages'

export default {
    props: ['user', 'isFreelance', 'isSelf'],

    data() {
        return {
            formUser: {...this.user},
            formSended: false,
            isInputDisabled: true,
            firstname : '',
            lastname : '',
            email : '',
            skillIds : '',
            job_id : '',
            phone : '',
            errorMessage:'',
            errors: {
                firstname: false,
                lastname: false,
                email: false,
                skillIds:false,
                job_id:false,
                phone:false
            }
        }
    },
    created()
    {
        this.handleInputUnlock()
    },
    mounted() {
        this.triggerSelect2();
        this.setSelect2();
        this.handleInputUnlock()
        setTimeout(() => {
        }, 100);

    },
    methods: {
        unlockForm() {
            this.isInputDisabled = false;
            this.formSended = false
        },
        lockForm() {
            this.isInputDisabled = true;
        },
        handleFormUnlockButton() {
            this.isInputDisabled ? this.unlockForm() : this.lockForm();
        },
        handleInputUnlock(){
            let input = document.querySelectorAll('input[disabled="disabled"]')
            let container= document.querySelectorAll('.input-container')
            if(input.length > 0){
                input.forEach(element=>{
                    container.forEach(item=>{
                        item.addEventListener("click", ()=> {
                            if(this.isInputDisabled){
                                this.unlockForm()
                            } ;
                        })
                        }
                    )
                })
            }
        },
        /**
         * Set jobs select2
         */
        setJobsSelect() {
            if(this.formUser.job)
            {
                $('.js-job-data').select2({
                    minimumResultsForSearch: -1,
                    tags: "true",
                    placeholder: "Choisir un métier",
                    language: {
                        searching: function () {
                            return "Chargement...";
                        }
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
            }
        },
        /**
         * Set skills select2
         */
        setSkillsSelect() {
            $('.js-skills-data').select2({
                dropdownCssClass: 'multiple-choices',
                placeholder: "Choisir une ou plusieurs compétence(s)",
                language: {
                    searching: function () {
                        return "Chargement...";
                    }
                },
                allowClear: false,
                closeOnSelect: false,
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
        },

        setSelect2() {
            if (this.isFreelance) {
                this.setSkillsSelect();
                this.setJobsSelect();
            }else{
                this.setJobsSelect();
            }
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

        /**
         * Fill the job in the select2
         */
        triggerJobsSelect2() {
            let jobsSelect2 = $('#client-job-data');

            if (this.isFreelance) {
                jobsSelect2 = $('#job-data');
            }

            if (this.formUser.job) {
                let option = new Option(this.$t(this.formUser.job.name), this.formUser.job.id, true, true);
                jobsSelect2.append(option).trigger('change');
            }
        },

        /**
         * Fill the select2 with already selected Skills
         */
        triggerSkillsSelect2() {
            let skillsSelect2 = $('#skills-data');
            this.formUser.skills.forEach(skill => {
                let option = new Option(this.$t(skill.name), skill.id, true, true);
                skillsSelect2.append(option).trigger('change');
            });
        },

        patchUser() {
            axios.patch('/api/user', {
                user: this.formUser
            }).then(res => {
                if (res.data.error) {
                    console.log(res.data.errors)
                    if (typeof res.data.errors == 'object') {
                        this.resetErrors();
                        let errors = res.data.errors;
                        this.errorMessage = errors
                        for (const [key, value] of Object.entries(errors)) {
                            this.errors[key] = true;
                        }
                    }
                } else {
                    this.resetErrors();
                    this.formSended = true;
                    this.errorMessage = null;
                    this.lockForm();
                    this.emitFormUpdated();
                }
            }).catch(error => {
                this.errorMessage = error.response.data.message
                console.log(error)
            });
        },

        emitFormUpdated() {
            this.$emit('form-updated')
        },
        resetErrors() {
            this.errors = {
                firstname: false,
                lastname: false,
                email: false,
                skillIds : false,
                job_id : false,
                phone : false,
            }
        }
    }
}
</script>
