<template>
    <div class="page_explorer_messenger" id="vue__explorer_messenger">
        <div id="loader">
            <div style=" height: 33px;width:40px;position: relative;">
                <svg class="circle" viewBox="0 0 50 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M27.8851 13.3985C31.6024 13.3985 34.6158 10.3987 34.6158 6.69829C34.6158 2.99785 31.6024 -0.00195312 27.8851 -0.00195312C24.1678 -0.00195312 21.1543 2.99785 21.1543 6.69829C21.1543 10.3987 24.1678 13.3985 27.8851 13.3985Z" fill="white"/>
                    <path d="M42.3078 21.0564C45.4941 21.0564 48.077 18.4852 48.077 15.3134C48.077 12.1416 45.4941 9.57031 42.3078 9.57031C39.1215 9.57031 36.5386 12.1416 36.5386 15.3134C36.5386 18.4852 39.1215 21.0564 42.3078 21.0564Z" fill="white"/>
                    <path d="M45.1925 36.3706C47.8477 36.3706 50.0002 34.2279 50.0002 31.5847C50.0002 28.9415 47.8477 26.7988 45.1925 26.7988C42.5372 26.7988 40.3848 28.9415 40.3848 31.5847C40.3848 34.2279 42.5372 36.3706 45.1925 36.3706Z" fill="white"/>
                    <path d="M36.538 49.7707C38.6622 49.7707 40.3842 48.0565 40.3842 45.942C40.3842 43.8275 38.6622 42.1133 36.538 42.1133C34.4139 42.1133 32.6919 43.8275 32.6919 45.942C32.6919 48.0565 34.4139 49.7707 36.538 49.7707Z" fill="white"/>
                    <path d="M19.2309 53.5988C21.3551 53.5988 23.0771 51.8847 23.0771 49.7701C23.0771 47.6556 21.3551 45.9414 19.2309 45.9414C17.1067 45.9414 15.3848 47.6556 15.3848 49.7701C15.3848 51.8847 17.1067 53.5988 19.2309 53.5988Z" fill="white"/>
                    <path d="M4.80796 42.1142C6.40108 42.1142 7.69257 40.8285 7.69257 39.2426C7.69257 37.6567 6.40108 36.3711 4.80796 36.3711C3.21483 36.3711 1.92334 37.6567 1.92334 39.2426C1.92334 40.8285 3.21483 42.1142 4.80796 42.1142Z" fill="white"/>
                    <path d="M1.92308 24.8834C2.98516 24.8834 3.84615 24.0263 3.84615 22.969C3.84615 21.9118 2.98516 21.0547 1.92308 21.0547C0.860991 21.0547 0 21.9118 0 22.969C0 24.0263 0.860991 24.8834 1.92308 24.8834Z" fill="white"/>
                    <path d="M11.5383 11.483C12.6004 11.483 13.4614 10.6259 13.4614 9.56865C13.4614 8.51138 12.6004 7.6543 11.5383 7.6543C10.4762 7.6543 9.61523 8.51138 9.61523 9.56865C9.61523 10.6259 10.4762 11.483 11.5383 11.483Z" fill="white"/>
                </svg>
            </div>
        </div>
        <div v-show="conversationsList.length === 0" class="text-center" style="display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 3px;
                width: 100%;
                color: #7665a7;
                cursor: pointer;
                position: absolute;
                backdrop-filter: blur(5px);
                z-index: 99;
                height: 100%;">
            <div v-if="!isFreelance">
                <div style="height: 40px;width: 40px; margin: 0 auto;">
                    <div class="picto-file"></div>
                </div>
                <p :style="{marginTop:top}">Cette page sera accessible lorsque</p>
                <p>vous proposerez une mission à un</p>
                <p>freelance sur Explorer.</p>
            </div>
            <div v-if="isFreelance">
                <div style="height: 40px;width: 40px; margin: 0 auto;">
                    <div class="picto-file"></div>
                </div>
                <p :style="{marginTop:top}">Cette page sera accessible lorsqu'un</p>
                <p>client vous proposera une mission</p>
                <p> sur Explorer.</p>
            </div>
        </div>
        <div class="conversations-list-container">
            <explorer-messenger-conversations-list v-on:update-selected-conversation="handleUpdateSelectedConversation"
                                                   :is-freelance="isFreelance" :user="user"
                                                   :conversations-list="conversationsList"
                                                   :selected-conversation="selectedConversation"/>
        </div>

        <div id="conversation" class="conversation-container">
            <explorer-messenger-conversation :conversation="selectedConversation" :user="user"
                                             :is-freelance="isFreelance"  :drive="selectedDrive" :conversations-list="conversationsList" :selectedconversation="selectedConversation" :_currencies="_currencies" />
        </div>

        <div class="right-card-container">
            <div class="mission-progression-container">
                <explorer-messenger-summary-card :mission="selectedMission" :missionProposition="selectedProposition"/>

            </div>
            <div class="mission-summary-container">
                <explorer-messenger-progression-card :key="componentKey" :mission-proposition="selectedProposition"
                                                     :mission-status="missionStatus" :is-freelance="isFreelance" :conversation="selectedConversation"/>

            </div>
            <div class="mission-drive-container">
                <explorer-messenger-drive-card :conversation="selectedConversation" :drive="selectedDrive" :user="user"/>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user', '_selectedConversationId', '_currencies'],
    data() {
        return {
            selectedConversation: null,
            selectedConversationId: this._selectedConversationId,
            conversationsList: [],
            componentKey:0,
            top: '1.4rem'
        }
    },
     created() {
        this.getConversations();
    },
     mounted() {
        $('body').toggleClass('theme-explorer');
            //this.missionStatus
         //this.$bus.$on('newstatus',()=>this.getConversations())
         /*window.Echo.private('explorer').listen('.new-conversation', (e) => {
             this.getConversations()
         });
         if (this.selectedConversationId) {
            this.getConversation();
         }*/
         window.Echo.private('explorer').listen('.new-conversation', (e) => {
             this.getConversations()
         });
    },
    computed: {
        isFreelance: function () {
            return this.user.role[0].name === "talent";
        },
        selectedMission: function () {
            if (this.selectedConversation !== null && typeof this.selectedConversation !== "undefined") {
                return this.selectedConversation.proposition.mission;
            } else {
                return null;
            }
        },
        missionStatus: function () {
            if (this.selectedConversation !== null && typeof this.selectedConversation !== "undefined") {
                return this.selectedConversation.proposition.status;
            } else {
                return null;
            }
        },
        selectedProposition: function () {
            if (this.selectedConversation !== null && typeof this.selectedConversation !== "undefined") {
                return this.selectedConversation.proposition;
            } else {
                return null;
            }
        },

        selectedDrive: function () {
            if (this.selectedConversation !== null  && typeof this.selectedConversation !== "undefined") {
                return this.selectedConversation.proposition.drive;
            } else {
                return null;
            }
        }
    },
    methods: {
        handleUpdateSelectedConversation(value) {
            this.selectedConversation = value;
        },
        async getConversations() {
            await axios.get("/api/explorer/missions/conversations", {
                params: {}
            }).then(res => {
              document.getElementById('loader').style.display='none'
                this.$bus.$emit('animation',()=>this.updateCurrentProposition())
                this.conversationsList = res.data;
                this.updateCurrentProposition();
            }).catch(error => console.log(error));
        },
        async getConversation() {
            await axios.get("/api/explorer/missions/conversations/" + this.selectedConversationId, {
                params: {}
            }).then(res => {
                if (res.data) {
                    this.selectedConversation = res.data;
                }
            }).catch(error => console.log(error));
        },
        updateCurrentProposition() {
            if (this.selectedConversation === null  && typeof this.selectedConversation !== "undefined") {
                this.selectedConversation = this.conversationsList[0];
            } else {
                if(this.conversationsList.length === 1)
                {
                    this.selectedConversation = this.conversationsList[0];
                    let updatedSelectedConversation = this.conversationsList.find(element => {
                        return element.id === this.selectedConversation.id
                    });
                }
                let updatedSelectedConversation = this.conversationsList.find(element => {
                    return element.id === this.selectedConversation.id
                });
                if (updatedSelectedConversation && updatedSelectedConversation.proposition.status !== this.selectedConversation.proposition.status) {
                    this.selectedConversation = updatedSelectedConversation;
                }
            }
        }
    },

}
</script>
