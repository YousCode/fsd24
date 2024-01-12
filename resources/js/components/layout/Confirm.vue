<template>
    <transition name="bounce">
        <div
            class="popup js-popup"
            :class="sharedProject ? 'sharedProject' : 'confirm-popup'"
            v-if="action && action != 'CLOSE'"
            v-on:click="close()"
        >
            <div class="popup-content">
                <div class="popup-box" v-on:click.stop>
                    <!--            <p class="mb-15">
                    <span class="icon icon-alert"></span>
                    <strong>{{ title }}</strong>
                </p>
                <p class="text c-main-grey mb-15">{{ text }}</p> 
				-->
                    <p class="text mb-15">
                        <template v-if="sharedProject">
                            <strong> {{ title }}</strong>
                        </template>
                        <template v-else>
                            <strong>{{ confirmText }}</strong>
                            <i v-if="confirmText2">{{ confirmText2 }}</i>
                        </template>
                    </p>
                    <div class="share-container row" v-show="sharedProject">
                        <div class="col-md-1">
                            <div class="share-link-container">
                                <span class="picto-drive"></span>
                            </div>
                        </div>
                        <div class="col-md-11 share-content-container">
                            <div class="share-content">
                                <h2>{{ text }}</h2>
                                <div class="form-box">
                                    <div class="form-field">
                                        <input type="text" placeholder="" class="share-link" v-model="shareLink" />
                                        <button v-on:click.self.prevent="copyShareLink()">Copier le lien</button>
                                    </div>
                                    <div class="sharing-infos">{{ confirmText }}<br> {{ confirmText2 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-container">
                        <template v-if="button_1 && !sharedProject">
                            <button
                                v-on:click="
                                    doIt(button_1.method, button_1.args)
                                "
                                :class="[
                                    { cancel_btn: toggle },
                                    button_1.class
                                ]"
                                class="confirm-popup__button button valid_btn"
                            >
                                {{ button_1.title }}
                            </button>
                        </template>

                        <template v-if="button_2">
                            <button
                                v-on:click="
                                    doIt(
                                        button_2.method,
                                        button_2.args,
                                        (toggle = false)
                                    )
                                "
                                @mouseenter="toggle = !toggle"
                                @mouseleave="toggle = !toggle"
                                :class="button_2.class"
                                class="confirm-popup__button button is-gradient cancel_btn"
                            >
                                {{ button_2.title }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    props: [],

    data() {
        return {
            toggle: false,
            action: null,
            title: null,
            text: null,
            confirmText: null,
            confirmText2: null,
            button_1: {},
            button_2: {},
            sharedProject: false,
            shareLink: false
        };
    },

    beforeMount() {},

    mounted() {
        this.$bus.$on("CONFIRM", datas => {
            this.title = datas.title ? datas.title : null;
            this.text = datas.text ? datas.text : null;
            this.confirmText = datas.confirmText ? datas.confirmText : null;
            this.confirmText2 = datas.confirmText2 ? datas.confirmText2 : null;
            this.sharedProject = datas.sharedProject ? datas.sharedProject : false;
            this.shareLink = datas.shareLink ? datas.shareLink : false;
            // Actions
            this.button_1 = datas.button_1 ? datas.button_1 : null;
            this.button_2 = datas.button_2 ? datas.button_2 : null;

            this.action = datas.action ? datas.action : "OPEN";

            // Add body class
            if (this.action != 'TASK_OVER') {
                this.setBodyClass();
            }
        });
    },

    methods: {
        hideform() {
            $("div.popup.js-popup").css("display", "none");
        },
        showForm() {
            $("div.popup.js-popup").css("display", "grid");
        },
        doIt(method, args) {
            if (method) {
                method(args);
            }

            this.close();
        },
        close() {
            if (this.action !== 'TASK_OVER') {
                this.hideform();
            }

            this.action = "CLOSE";

            // Remove body class
            this.setBodyClass();
        },

        copyShareLink() {
            if (this.shareLink) {
                navigator.clipboard.writeText(this.shareLink).then(() => {
                    this.$bus.$emit('ACTION_CHANGED', {
            	        action: 'CONGRATS',
            	        type: 'COPY_SHARE_LINK'
                    }); 
                });
            }
            this.close();
        },

        setBodyClass() {
            if (this.action == "CLOSE") {
                $("body").removeClass("alert-is-open");
            } else {
                $("body").addClass("alert-is-open");
                this.hideform();
            }
        },
        style() {
            toggle = !toggle;
        }
    }
};
</script>
