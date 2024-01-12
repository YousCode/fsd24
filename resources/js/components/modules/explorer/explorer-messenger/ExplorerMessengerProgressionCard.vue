<template>
    <div class="explorer-messenger-card explorer-messenger-progression-card">
        <button type="button" @click="toggleCard" class="card-header">
            <div class="picto-container"><span class="picto-explorer-shield"/></div>
            <h1 class="card-title">{{$t('mission-steps')}}</h1>
            <div class="card-dropdown-button">
                <span v-if="isCardOpen" class="picto-explorer-dropdown-up"/>
                <span v-else class="picto-explorer-dropdown-down"/>
            </div>
        </button>
        <div v-if="isFreelance" v-show="isCardOpen" class="card-body" id="cardtest">
            <div class="progression-item list-complete-item" @click="openQuote" :class="{ active: activeStep===1 }">
                <span class="step-number">1</span>
                <span class="step-description">
                    {{$t('mission-step1-free')}}
                </span>
            </div>
            <div class="progression-item" :class="{ active: activeStep===2 }">
                <span class="step-number">2</span>
                <span class="step-description">
                    {{$t('mission-step2-free')}} <br/> {{ $t('mission-step2-2free') }}
                </span>
            </div>
            <div class="progression-item" :class="{ active: activeStep===3 }">
                <span class="step-number">3</span>
                <span class="step-description">
                    {{$t('mission-step3-free')}} <br/> {{ $t('mission-step3-3free') }}
                </span>
            </div>
        </div>
        <div v-else v-show="isCardOpen" class="card-body">
            <div class="progression-item" :class="{ active: activeStep===1 }">
                <span class="step-number">1</span>
                <span class="step-description">
                    {{ $t('mission-step1') }}<br/>
                    {{ $t('mission-step1-break') }}
                </span>
            </div>
            <div class="progression-item" :class="{ active: activeStep===2 }">
                <span class="step-number">2</span>
                <span class="step-description">
                    {{ $t('mission-step2') }}<br/>
                    {{ $t('mission-step2-break') }}
                </span>
            </div>
            <div class="progression-item" :class="{ active: activeStep===3 }">
                <span class="step-number">3</span>
                <span class="step-description">
                    {{ $t('mission-step3') }}<br/>
                    {{ $t('mission-step3-break') }}
                </span>
            </div>
            <button v-if="activeStep===4" type="button" @click="closeMission" class="progression-item" :class="{ active: activeStep===4 }">
                <span class="step-number">4</span>
                <span class="step-description">
                    {{ $t('mission-step4') }}<br/>
                    {{ $t('mission-step4-break') }}
                </span>
            </button>
            <div v-else class="progression-item" :class="{ active: activeStep===4 }">
                <span class="step-number">4</span>
                <span class="step-description">
                    {{ $t('mission-step4') }}<br/>
                    {{ $t('mission-step4-break') }}
                </span>
            </div>
        </div>
        <explorer-messenger-quote-propose-modal v-if="showQuoteProposeModal"  @close-modal="closeQuoteProposeModal" :conversation="conversation"/>
    </div>
</template>
<script>
export default {
    props: ['user', 'missionStatus', 'isFreelance','missionProposition','conversation'],
    data() {
        return {
            isCardOpen: true,
            status:null,
            showQuoteProposeModal: false,
        }
    },
    created(){
        setTimeout(()=>{
            this.status = this.missionStatus
        },2000)
    },

    mounted: function () {
        window.Echo.private('explorer').listen('.new-message', (e) => {
            this.getStatusMission()
        });
        if(this.missionProposition !== null && this.missionProposition.id !== null)
        {
            this.getStatusMission()
        }

    },
    computed: {
        activeStep: function () {
            switch (this.status) {
                case 'waiting_quote' :
                    return 1;
                case 'waiting_payment' :
                    return 2;
                case 'waiting_work' :
                    return this.isFreelance ? 2 : 3;
                case 'info_filled':
                    return this.isFreelance ? 2 : 3;
                case 'waiting_closing' :
                    return this.isFreelance ? 3 : 4;
                default :
                    return 0;
            }
        },
        proposition: function(){
            return this.missionProposition
        }
    },
    methods: {
        toggleCard() {
            this.$parent.$children[2].isCardOpen = true;
            this.$parent.$children[4].isCardOpen = false;
            this.isCardOpen = !this.isCardOpen;
        },
        openQuote(){
          this.showQuoteProposeModal = true
        },
        closeQuoteProposeModal() {
            this.showQuoteProposeModal = false;
        },
        closeMission() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_proposition_close'
                }
            }).then(res => {


            }).catch(error => console.log(error))
        },
        getStatusMission() {
            axios.get("/api/explorer/missions/conversations", {
                params: {}
            }).then(res => {
                res.data.forEach((e)=>{
                    if(this.missionProposition.id === e.proposition.id)
                    {
                        this.status=e.proposition.status;
                        //this.missionStatus = e.proposition.status
                    }
                })
            }).catch(error => console.log(error));
        },
    },
}
</script>
