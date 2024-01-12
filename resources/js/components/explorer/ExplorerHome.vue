<template>
    <div class="page_explorer_home" id="vue__explorer_home">
        <div class="main-content">

            <h1 v-if="isFreelance" class="words">{{ $t('explorer-lock-main-title') }}<br>
                {{$t('explorer-lock-main-title-1')}}<span>{{$t('explorer-lock-main-title-2')}} <span id="change"
                ><typical
                    class="blink"
                    :steps="[$t('job-1') ,500, $t('job-2') ,500, $t('job-3') ,500, $t('job-4') ,500,$t('job-5'),500,$t('job-6'),500,]"
                    :loop="Infinity"
                    :wrapper="'span'"
                >
        </typical
        ></span><br></span> {{$t('explorer-lock-main-title-3')}}
            </h1>
            <h1 v-else>{{$t('explorer-home-client-title')}}<br>

                <span>{{$t('explorer-home-client-title-1')}} <span id="change"
                ><typical
                    class="blink"
                    :steps="[$t('job-1') ,500, $t('job-2') ,500, $t('job-3') ,500, $t('job-4') ,500,$t('job-5'),500,$t('job-6'),500,]"
                    :loop="Infinity"
                    :wrapper="'span'"
                >
        </typical
        ></span> {{$t('explorer-home-client-title-2')}} <br>{{ $t('explorer-home-client-title-3') }}</span> {{ $t('explorer-home-client-title-4') }}
            </h1>
            <div  class="input-container" style="margin-bottom: 12px;">
                <Transition name="doubt">
                    <div v-if="show" class="n-e_h" @click="redirectToMembership" >{{$t('explorer-lock-access-request')}}</div>
                </Transition>
                <input v-model="accessCode" class="password-input" type="text"
                       :placeholder="$t('explorer-lock-access-placeholder')"/>
                <button class="validate-button" @click="sendForm" type="button">{{$t('confirm-cta')}}</button>
            </div>
            <p v-if="errorMessage !== null" style="color: red">{{ errorMessage }}</p>
            <div v-if="show" @click="displayingPage" class="inscription-link">{{$t('explorer-lock-access-already')}}</div>
            <a v-else href="/explorer/membership" class="inscription-link">{{$t('explorer-lock-access-get')}}</a>
        </div>

        <div class="gradient"/>
    </div>
</template>
<script lang="ts">
import typical from "vue-typical";

export default {
    components:{
        typical,
    },
    props: ['user'],
    data() {
        return {
            accessCode: '',
            errorMessage: null,
            slot:'',
            show: true
        }
    },

    mounted() {
        // tap your code here

    },
    computed: {
        isFreelance: function () {
            return this.user.role[0].name === "talent";
        },
        animation(){
            setInterval(this.randomGenerator
            ,3000)
        }

    },
    methods: {
        transition(){
            document.querySelector(".n-e_h").style.width = "0%"
        },
        mouseleave: function(){
            document.querySelector(".n-e_h").style.width = "100%"
        },
        sendForm() {
            axios.post('/api/explorer/unlock-access', {
                params: {
                    access_code: this.accessCode
                }
            }).then(res => {
                window.location.href = "/explorer";
            }).catch(error => this.handleError(error));
        },
        redirectToMembership(){
            window.location.href = "/explorer/membership";
        },
        displayingPage(){
            this.transition()
            this.show = false;
        },
        handleError(error) {
            let response = error.response;

            if (response.status === 403) {
                if (response.data === 'Explorer Registration not validated') {
                    this.errorMessage = "Votre inscription en cours de validation, merci de patienter";
                } else if (response.data === "Explorer access code not valid") {
                    this.errorMessage = "Code d’accès incorrect. Veuillez réessayer ou attendre que votre demande d’accès soit traitée. "
                }else{
                    this.errorMessage = 'Aucune demande d\'accès n\'a été faite avec ce compte. Veuillez cliquez sur "Obtenir un code d\'accès".';
                }
            }
            if (response.status === 500) {
                this.errorMessage = "Une erreur est survenue, veuillez nous contacter";
            }
        },
        homeAnimation()
        {
            /*const jobs = ["freelances","étalonneurs","motions" ,"designers", "3D","VFX Artists"]
            let count = 0
            let handle = setInterval(() => {
                document.getElementById("change").innerHTML = jobs[count];
                count = (count + 1) % jobs.length
            }, 4000)
            console.log(handle)
            let newWords = ''
            newWords= this.randomGenerator()
            this.slot = newWords
            this.slot = handle*/
        },

        randomGenerator: function ()
        {
            //will be used on the future not for mvp but more efficient
            let jobs = ["freelances","étalonneurs","motions" ,"designers", "3D","VFX Artists"]
            let rndIdx = Math.floor(Math.random() * jobs.length);
            let handle=setInterval(()=> {
                document.getElementById("change").innerHTML = jobs[rndIdx] ?? "freelances"
                rndIdx = (rndIdx + 1) % jobs.length},3000)
            this.slot = handle
            return jobs[rndIdx];
        }
    }

}
</script>

