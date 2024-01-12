<template>
    <div class="page_explorer_membership" id="vue__explorer_membership_client" style="height: 100%;">
        <div class="main-container" style="background: none;padding: 0;height: 100%;margin:0;justify-content: space-between;max-width: 100%;">
            <div class="bg-image-left">
                <div class="left-image_calq"></div>
                <img src="/images/explorer/membership-explorer.png">
            </div>
            <div class="form-container">
                <h1>{{$t('explorer-form-request-client')}} <br>{{$t('explorer-form-request-client-1')}}</h1>
                <p> {{$t('explorer-form-request-client-subtitle')}}</p>

                <form method="POST" v-on:submit.prevent="postRegistration" class="membership-sub-form">
                    <div>
                        <label for="text-project-desc">{{$t('explorer-form-request-client-description')}} *</label>
                        <textarea @keydown="errorProjectDescription=''" v-model="projectDescription" id="text-project-desc" :placeholder="$t('explorer-form-request-client-placeholder')" name="project_description"></textarea>
                    </div>
                    <div v-if="errorProjectDescription">
                        <span class="text-danger">{{errorProjectDescription}} </span>
                    </div>
                    <div>
                        <label for="select-budget">{{ $t('explorer-form-request-client-budget') }}</label>
                        <select @click="errorBudget=''" v-model="budget" id="select-budget" name="budget">
                            <option value="">Votre budjet*</option>
                            <option> &lt; de 100€</option>
                            <option>Entre 100 et 500€</option>
                            <option>Entre 500 et 1000€</option>
                            <option> > de 1000€</option>
                        </select>
                    </div>

                    <div v-if="errorBudget">
                        <span class="text-danger">{{errorBudget}} </span>
                    </div>
                    <div v-if="Exception">
                        <span class="text-danger">{{Exception}} </span>
                    </div>
                    <button class="submit-button" v-if="!isSended">{{$t('explorer-form-request-client-send-request')}}</button>
                    <button disabled class="submit-button button-sended" v-else-if="isSended">{{$t('explorer-form-request-client-sent-request')}}</button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>


export default {
    props: [],

    data() {
        return {
            projectDescription: null,
            budget: null,
            isSended: false,
            errorBudget: '',
            errorProjectDescription: '',
            Exception: ''
        }
    },

    mounted() {
        $('body').toggleClass('theme-explorer');
    },

    methods: {
        postRegistration() {
            axios.post('/api/explorer/register', {
                project_description: this.projectDescription,
                budget: this.budget
            }).then(res => {
                if (res.status === 400) {
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
                if(error){
                    error = error.response;
                    if (typeof error.data.budget !== 'undefined' && error.status !== 500) {
                        if(error.data.budget.length > 0){
                            this.errorBudget = error.data.budget[0];
                        }
                    }
                    if (typeof error.data.project_description !== 'undefined' && error.status !== 500) {
                        if(error.data.project_description.length > 0)
                            this.errorProjectDescription = error.data.project_description[0];
                    }
                }
            });
        }
    },
}
</script>
