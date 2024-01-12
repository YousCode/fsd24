<template>
    <div class="page_explorer_membership" id="vue__explorer_membership_client" style="height: 100%;">
        <div class="main-container" style="background: none;padding: 0;height: 100%;margin:0;justify-content: space-between;max-width: 100%;">
            <div class="bg-image-left">
                <div class="left-image_calq"></div>
                <img src="/images/explorer/membership-explorer.png">
            </div>
            <div class="form-container">
                <h1>{{$t('explorer-form-request-freelance')}}</h1>
                <p>{{ user.firstname }}{{ $t('explorer-form-request-freelance-subtitle') }}</p>

                <form method="POST" v-on:submit.prevent="postRegistration" class="membership-sub-form">
                    <div class="form-input-group">
                        <div class="input-container">
                            <label for="user-firstname">{{$t('firstname')}} *</label>
                            <input class="membership-input" v-model="formUser.firstname" id="user-firstname"
                                   type="text" name="firstname"/>
                            <div>
                                <span class="text-danger">{{errorFirstname}} </span>
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="user-lastname">{{$t('lastname')}} *</label>
                            <input class="membership-input" v-model="formUser.lastname" id="user-lastname" type="text" name="lastname"/>
                            <div>
                                <span class="text-danger">{{errorLastname}} </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-input-group">
                        <div class="input-container">
                            <label for="user-email">{{$t('email')}} *</label>
                            <input class="membership-input" v-model="formUser.email" id="user-email" type="email" name="email" />
                            <div v-if="errorEmail">
                                <span class="text-danger">{{errorEmail}} </span>
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="user-phone">{{$t('phone')}}*</label>
                            <input class="membership-input" v-model="formUser.phone" id="user-phone" type="tel" name="phone"/>
                            <div>
                                <span class="text-danger">{{errorPhone}} </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-input-group">
                        <div class="input-container">
                            <label for="job-data">{{$t('job')}}*</label>
                            <select v-model="formUser.job" v-select2 class="js-job-data js-select2 js-required"
                                    id="job-data" name="job_id"></select>
                            <div>
                                <span class="text-danger">{{errorJob}} </span>
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="user-portfolio">Portfolio</label>
                            <input class="membership-input" v-model="formUser.website" id="user-portfolio" type="text"  :placeholder="$t('your-website')" name="website"/>
                        </div>

                    </div>

                    <div class="form-input-group" style="margin-bottom: 0;">
                        <div class="input-container" style="width: 100%">
                            <label for="skills-data" style="top: -5px;position: relative;">{{$t('your-skills')}}*</label>
                            <select v-model="formUser.skills" v-select2
                                    class="js-skills-data js-select2 js-required multiple-choices" id="skills-data"
                                    name="skills[]" multiple="multiple"></select>
                        </div>
                    </div>
                    <div v-if="notifmsg">
                        <span class="text-danger">{{notifmsg}} </span>
                    </div>
                    <div class="form-input-group" style="margin-top: 15px;">
                        <div class="input-container" style="width: 100%; margin-top: 5px;">
                            <label for="user-description">Description</label>
                            <textarea v-model="formUser.description" id="user-description" name="description" type="text" placeholder="Description"/>
                        </div>
                    </div>
                    <div v-if="Exception">
                        <span class="text-danger">{{Exception}} </span>
                    </div>
                    <button class="submit-button" v-if="!isSended">{{ $t('explorer-form-request-freelance-send-request') }}</button>
                    <button disabled class="submit-button button-sended" v-else-if="isSended">{{ $t('explorer-form-request-freelance-sent-request') }}</button>
                </form>
            </div>
        </div>
    </div>

</template>
<script>
export default {
    props: ['user'],

    data() {
        return {
            formUser: this.user,
            isSended: false,
            notifmsg: "",
            errorLastname: '',
            errorFirstname: '',
            errorEmail: '',
            errorJob: '',
            errorPhone: '',
            Exception: ''
        }
    },
    mounted() {
        $('body').toggleClass('theme-explorer');
        this.triggerSelect2();
        setTimeout(() => {
            this.setSkillsSelect();
            this.setJobsSelect();
        }, 1000)

    },
    methods: {
        /**
         * Set jobs select2
         */
        setJobsSelect() {
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
        },
        /**
         * Set skills select2
         */
        setSkillsSelect() {
            $('.js-skills-data').select2({
                placeholder: "Choisir une ou plusieurs compétence(s)",
                language: {
                    searching: function () {
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
        },

        /**
         * Fill select2
         * */
        triggerSelect2() {
            this.triggerJobsSelect2();
            this.triggerSkillsSelect2();
        },

        /**
         * Fill the job in the select2
         */
        triggerJobsSelect2() {
            let jobsSelect2 = $('#job-data');

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

        postRegistration: function () {
            axios.post('/api/explorer/register',this.formUser).then(res => {
                if (res.status === 500) {
                    console.log(res.data.Exception)
                } else {
                    setTimeout( ()=>this.isSended = true,
                        3000);
                    setTimeout(()=>window.location.href = "/explorer",5000)
                }
            }).catch(error => {
                if(error.response.status === 500){
                    this.Exception = error.response.data.Exception
                }
                error = error.response;
                let user = this.formUser
                let inputArray = ["lastname","firstname","email","job","skills","phone"]
                Object.keys(user).forEach((item)=>{
                    if(inputArray.includes(item) && error){
                        switch (item){
                            case 'lastname': this.errorLastname = user[item].length === 0 ? error.data.lastname[0] : ''; break;
                            case 'firstname': this.errorFirstname = user[item].length === 0 ? error.data.firstname[0] : ''; break;
                            case 'email': this.errorEmail = user[item].length === 0 ? error.data.email[0] : ''; break;
                            case 'job': this.errorJob = user[item].length === 0 ? error.data.job[0] : ''; break;
                            case 'skills': this.notifmsg = user[item].length === 0 ? error.data.skills[0] : ''; break;
                            case 'phone': this.errorPhone = user[item].length === 0 ? error.data.phone[0] : ''; break;
                        }
                    }
                })
            });
        }
    },
}
</script>