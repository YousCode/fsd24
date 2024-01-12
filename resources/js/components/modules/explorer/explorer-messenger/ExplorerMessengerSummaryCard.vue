<template>
    <div class="explorer-messenger-card explorer-messenger-summary-card">
        <button type="button" @click="toggleCard" class="card-header">
            <div class="picto-container"><span class="picto-explorer-infos"/></div>
            <h1 class="card-title">{{ $t('mission-summary-test') }}</h1>
            <div class="card-dropdown-button">
                <span v-if="isCardOpen" class="picto-explorer-dropdown-up"/>
                <span v-else class="picto-explorer-dropdown-down"/>
            </div>
        </button>
        <div v-show="isCardOpen" class="card-body" style="display: flex;flex-direction: column;margin-bottom: 1rem;">
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-pile"/></div>
                <div class="summary-item-infos">
                    <div v-if="missionInfos.name" class="summary-item-title">{{  missionInfos.name }}</div>
                    <div v-else class="summary-item-title">Mission</div>
                    <div v-if="proposition" class="summary-item-value" >{{ $t(missionInfos.taskType) }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-eur"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Budget</div>
                    <div v-if="proposition" class="summary-item-value" >{{ missionInfos.budget }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-calendar"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Deadline</div>
                    <div v-if="proposition" class="summary-item-value" >{{ missionInfos.deadline }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-notes"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">{{$t('mission-description')}}</div>
                    <div v-if="proposition" class="summary-item-value" >{{missionInfos.description }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['mission','missionProposition'],
    data() {
        return {
            isCardOpen: true
        }
    },

    mounted() {

    },
    computed: {
        missionInfos() {
            if (this.mission === null) {
                return {
                    name: '',
                    type: '',
                    budget: '',
                    deadline: '',
                    description: ''
                };
            }
            return this.mission
        },
        proposition(){
            if (this.missionProposition !== null){
                return this.missionProposition
            }
        }
    },
    methods: {
        toggleCard() {
            this.$parent.$children[3].isCardOpen = true;
            this.$parent.$children[4].isCardOpen = false;
            this.isCardOpen = !this.isCardOpen;
        }
    },
}
</script>
