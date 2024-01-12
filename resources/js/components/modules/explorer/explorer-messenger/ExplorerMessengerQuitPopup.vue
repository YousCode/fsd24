<template>
    <div class="modal-mask-responsive">
        <div class="modal-mask-wrapper"></div>
        <div class="modal-wrapper modal-mask-wrapper__wrapper" @click="emitCloseModal">
            <div class="modal-container">
                <div class="explorer-quit-modal">
                    <div class="modal-header text-center">
                        <h1 class="modal-title">Êtes vous sur de vouloir quitter ?</h1>
                        <p>Les informations que vous avez remplies ne seront pas enregistrées.</p>
                    </div>
                    <div class="explorer-modal-button-container">
                        <button class="explorer-modal-button" type="button" @click="emitCancel">Revenir en arrière
                        </button>
                        <button class="explorer-modal-button explorer-modal-quit-button" type="button" @click="emitValidate">Quitter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['conversation'],
    data() {
        return {
            toggle: false,
            action: null,
            title: null,
            text: null,
            confirmText: null,
            button_1: {},
            button_2: {},
            sharedProject: false,
            shareLink: false
        };
    },

    beforeMount() {},

    mounted() {
    },
    computed: {},
    methods: {
        emitCloseModal() {
            this.$emit('close-modal');
        },
        emitValidate() {
            this.$emit('validate');
        },
        emitCancel() {
            this.$emit('cancel');
        }
    },
}
</script>
