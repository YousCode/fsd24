<template>
    <div class="modal-mask-responsive">
        <div class="modal-mask-wrapper"></div>
        <div class="modal-wrapper modal-mask-wrapper__wrapper" @click="emitCloseModal">
            <div class="modal-container">
                <div class="explorer-quit-modal" style="width: 780px; height: 430px">
                    <div class="modal-header text-center">
                        <h1 class="modal-title">{{$t('mission-deleted-popup-title1')}}<br/>
                            {{ missionProposition.freelance.firstname }} {{$t('mission-deleted-popup-title2')}}</h1>
                    </div>
                    <div class="explorer-modal-button-container">
                        <button class="explorer-modal-button" type="button" @click="cancelProposition">{{$t('mission-deleted-confirm-cta')}}
                        </button>
                        <button class="explorer-modal-button explorer-modal-quit-button" type="button"
                                @click="emitCloseModal">{{$t('mission-deleted-cancel-cta')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['missionProposition'],
    data() {
        return {}
    },
    mounted() {
    },

    computed: {},
    methods: {
        emitCloseModal() {
            this.$emit("modal-close");
        },

        cancelProposition() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_proposition_cancel'
                }
            }).then(res => {
                this.emitCloseModal();
            }).catch(error => console.log(error))
        },
        cancelFullMission() {
            axios.patch("/api/explorer/missions/propositions/mission/" + this.missionProposition.mission.id, {
                params: {
                    patch_type: 'mission_full_cancel'
                }
            }).then(res => {
                this.emitCloseModal();
            }).catch(error => console.log(error))
        }
    },
}
</script>