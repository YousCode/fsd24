<template>
    <div v-if="conversation !== null && conversation !== undefined" class="explorer-messenger-conversation" style="padding: 0">
        <!-- Laissez commenter, Alexandre n'en veux pas pour l'instant car pas maquetter
        <div v-show="conversation !== null && conversation.lastMessage.type !== undefined && conversation.lastMessage !== null && conversation.lastMessage.type === 'system_mission_closed' || conversation.lastMessage.type === 'system_mission_canceled' && conversation.lastMessage !== null" class="text-center" style="display: flex;
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
            <div>
                <div style="height: 40px;width: 40px;margin: 0px auto 1rem auto;">
                    <div class="picto-messenger-lock"></div>
                </div>
                <div v-if="conversation.proposition.status === 'closed'">
                    <p>Vous ne pouvez pas accéder</p>
                    <p>à la conversation</p>
                </div>
                <div v-else-if="conversation.proposition.status === 'canceled'">
                    <p>Cette mission a été annulée</p>
                </div>
            </div>
        </div> -->
        <div v-if="!isFetching" class="conversation-message-container" id="message-container" >
            <conversation-day-containerr v-for="(messages, date) in messagesByDay" v-bind:key="date" :date="date"
                                        :messages="messages"
                                        :mission="conversation.proposition.mission"
                                        :conversation='conversation'
                                        :mission-proposition="conversation.proposition" :user="user"
                                        :_currencies="_currencies"
                                        :is-freelance="isFreelance"/>
        </div>
        <div class="conversation-footer">
            <div class="conversation-input-container" style="position: relative">
                <textarea :placeholder="$t('conversation-input')" v-model="messageToPost" @keydown.enter.exact.prevent="postMessage"
                          :style="textAreaStyle" @keyup="resizeTextarea" @keydown="resetInput" ref="messageInput"
                          class="conversation-input-text"/>
                <button @click="postMessage" class="conversation-input-button" style="right: 1rem;">
                    <span class="picto-explorer-send-message" style="height: 18px"/>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import Explorer from "../../../explorer/Explorer";
import ExplorerMessengerCancelMissionPopup from "./ExplorerMessengerCancelMissionPopup";

export default {
    components: {ExplorerMessengerCancelMissionPopup, Explorer},
    props: ['user', 'conversation', 'isFreelance','conversationsList','selectedconversation', '_currencies'],
    data() {
        return {
            messageToPost: "",
            messagesByDay: null,
            isFetching: false,
            isShowQuoteProposeModalActive: false,
            textAreaHeight: '21px',
            pulseMessage:false
        }
    },
    created() {
        this.getConversationMessage();
    },
    mounted() {
        this.scrollDown();
        window.Echo.private('explorer').listen('.new-message',(e)=>{
            this.getConversationMessage()
        });

        window.Echo.private('explorer').listen('.new-link', (e) => {
            this.getConversationMessage()
            this.getLinksUrl();
            if (this.$parent.$children[4]) {
                this.$parent.$children[4].newLink = true;
            }
        });
    },
    computed: {
        textAreaStyle() {
            return {
                'height': this.textAreaHeight
            }
        },
    },
    watch: {
        conversation: function () {
            this.messagesByDay = null;
            this.getConversationMessage();
        }
    },
    methods: {

        resetInput()
        {
            if(this.$refs.messageInput.value.length===0){
                this.$refs.messageInput.style.height = this.textAreaHeight
            }
        },
        getConversations() {
            axios.get("/api/explorer/missions/conversations", {
                params: {}
            }).then(res => {
                this.conversationsList = res.data;
            }).catch(error => console.log(error));
        },

        //scroll to the last message of the conversation smoother
        scrollDown() {
            const scrollingElement = document.getElementById('message-container');
            if(scrollingElement)
            {
                const config = { childList: true };
                const callback = function (mutationsList, observer) {
                    for (let mutation of mutationsList) {
                        if (mutation.type === "childList") {
                            setTimeout(()=> {
                                scrollingElement.scrollTo({top:scrollingElement.scrollHeight,behavior:"smooth"})
                            },1000)
                        }
                    }
                };
                const observer = new MutationObserver(callback);
                observer.observe(scrollingElement, config);
                setTimeout(() => {
                    scrollingElement.scrollTo({top:scrollingElement.scrollHeight,behavior:"smooth"})
                }, 0)
            }
        },
        //handle post message conversation
        postMessage() {
            if (this.conversation !== null && this.conversation !== undefined) {
                axios.post("/api/explorer/missions/conversations/" + this.conversation.id + "/messages", {
                    params: {
                        message: this.messageToPost
                    }
                }).then(res => {
                    if(res.data ===  1){
                        this.$bus.$emit('UPDATE_DRIVE_EXPLORER', ()=>this.getLinksUrl());
                        this.messageToPost = '';
                        this.textAreaHeight = '21px'

                    }
                    this.$bus.$emit("newBubble",(res)=>res.data);
                    this.messageToPost = '';
                    this.textAreaHeight = '21px'
                   // this.$emit('UPDATE_DRIVE', ()=>this.getLinksUrl());
                }).catch(error => console.log(error))
            }
        },

        //get last message from the conversation
        async getConversationMessage() {
            if (this.conversation !== null && this.conversation !== undefined ) {
                let conversationId = this.selectedconversation ? this.selectedconversation.id : this.conversation.id;
                let conversation = this.selectedconversation ? this.selectedconversation : this.conversation;
                 await axios.get("/api/explorer/missions/conversations/" + conversationId + "/messages", {}).then(res => {
                    this.messagesByDay = res.data
                    this.isFetching = false
                    /*if (conversation.lastMessage.type !== "system_mission_closed"){
                        this.scrollDown()
                    }*/
                     this.scrollDown()
                }).catch(error => console.log(error))
            }
        },

        getLinksUrl() {
            if (this.conversation !== null  && this.conversation !== undefined) {
                axios.get("/api/explorer/missions/conversations/" + this.conversation.proposition.drive.id_proposition + "/drive")
                    .then(res => {
                        this.driveLink = res.data.driveLinks
                        this.$bus.$emit('UPDATE_DRIVE_URL_EXPLORER', this.driveLink);
                    }).catch(error => console.log(error));
            }
        },

        showQuoteProposeModal() {
            this.showQuoteProposeModal = true;
        },

        closeQuoteProposeModal() {
            this.showQuoteProposeModal = false;
        },

        resizeTextarea() {
            this.textAreaHeight = `${this.$refs.messageInput.scrollHeight}px`

        }
    },
}

Vue.component('conversation-messagee', {
    template: `
        <div class="conversation-message">
        <a :href="'/explorer/profile/' + message.created_by">
            <div v-if="showInitial || message.createdByAvatar === 'user.jpg'" v-bind:class="[isSelf ? 'talent-initials-container-self' : 'talent-initials-container' ]">
                <span class="talent-initials">{{ getTalentInitials(message.createdByUsername) }}</span>
            </div>
            <img v-else @error="handleImgError" :src="message.createdByAvatar ?'/upload/avatars/' + message.createdByAvatar : getTalentInitials(message.createdByUsername)"
                 v-bind:class="[isSelf ? 'message-profile-picture-self' : 'message-profile-picture' ]"
                 alt="profile"/>
        </a>
        <div class="message-content-container">
            <div v-if="isSelf" class="message-header" v-bind:class="{'self-message' : isSelf }">
            <span class="message-hour" style="margin: 0px 5px 0px 0px;">{{ messageTime }}</span>
                <a :href="'/explorer/profile/' + message.created_by"><span
                    class="message-username">{{ message.createdByUsername.charAt(0).toUpperCase() + message.createdByUsername.slice(1) }}</span></a>
            </div>
            <div v-else class="message-header">
                <a :href="'/explorer/profile/' + message.created_by"><span
                    class="message-username">{{ message.createdByUsername.charAt(0).toUpperCase() + message.createdByUsername.slice(1) }}</span></a>
                    <span class="message-hour">{{ messageTime }}</span>
            </div>
            <div class="message-body" v-bind:class="{'self-message' : isSelf }">
                <p class="message-paragraph m-e_p" v-if="messageType !=='user_drive_link'">{{ message.message }}</p>
                <bubble-mission-summary v-if="messageType=='user_mission_proposal'" :mission="mission"
                                        :mission-proposition="missionProposition" :is-freelance="isFreelance"/>
                <bubble-mission-quote v-if="messageType==='user_quote'" :quote="message.quote"
                                      :mission-proposition="missionProposition" :is-freelance="isFreelance"/>
               <!-- <bubble-drive-link v-if="messageType==='user_drive_link'" :drive-link="message.driveLink"/>-->
                <a v-if="messageType==='user_drive_link'" target="_blank" class="message-bubble-drive-link" :href="message.driveLink.link">
                    <div class="link-miniature" :style="{'background-image': 'url(' + message.driveLink.miniature + ')', backgroundPosition: 'center', backgroundSize:'cover'}">
                    </div>
                    <div class="link-description">
                        <span class="link-name">{{ message.driveLink.name.substring(0,25) + '...'+ message.driveLink.name.substring(message.driveLink.name.length-25,message.driveLink.name.length)}}</span>
                        <span class="link-url">{{ message.driveLink.link.substring(0,25) + '...'+ message.driveLink.link.substring(message.driveLink.link.length-25,message.driveLink.link.length) }}</span>
                    </div>
                </a>
            </div>
        </div>
        </div>`,
    props: ['messageType', 'message', 'mission', 'user', 'missionProposition', 'isFreelance','isDisabled'],
    data() {
        return {
            showInitial: false
        }
    },
    mounted() {
    },
    computed: {
        showButton() {
            return true;
        },
        messageTime() {
            let messageTime = new Date(this.message.created_at);
            return String(messageTime.getHours()).padStart(2, '0') + ":" + String(messageTime.getMinutes()).padStart(2, '0');
        },
        isSelf() {
            return this.message.createdByUsername === this.user.name;
        }
    },
    methods: {
        getTalentInitials(talent) {
            let names = talent.split(' ')
            let res = ''
            names.forEach(element => res += element.charAt(0).toUpperCase());
            return res;
        },
        handleImgError() {
            this.showInitial = true;
        },
    }
});

Vue.component('conversation-system-messageee', {
    template: `
        <div v-if="showMessage" class="conversation-message conversation-system-message" :id="'conversation-system-message-'+systemeType"
             :class="{'conversation-system-message-disabled': isDisabled}">
        <img src="/images/explorer/kolab-messenger.png" class="message-profile-picture" style="max-width: 100%"
             alt="profile"/>
        <div class="message-content-container">
            <div class="message-header">
                <span class="message-username">{{ systemName }}</span>
                <span class="message-hour">{{ messageTime }}</span>
            </div>
            <div class="message-body">
                <p class="message-paragraph">{{ messageText }}</p>
                <button v-if="showButton" class="message-action-button" @click="buttonHandler" type="button" :disabled="isDisabled">
                    {{ buttonText }}
                </button>
            </div>
        </div>
        <explorer-mission-propose-modal v-if="showProposeModal" :user-to-propose="userToSee" @close-modal="closeProposeModal"/>
        <explorer-messenger-quote-propose-modal v-if="showQuoteProposeModal"  @close-modal="closeQuoteProposeModal" :conversation="conversation" :_currencies="_currencies" />

        </div>`,
    props: ['message', 'missionProposition', 'user', 'conversation','isFreelance','quote', '_currencies'],
    data() {
        return {
            systemName: "Kolab",
            showQuoteProposeModal: false,
            showProposeModal:false,
            systeme : this.message.type
        }
    },
    computed: {
        userToSee(){
            return this.missionProposition.freelance
        },
        systemeType(){
            return this.message.type
        },
        showButton() {
            switch (this.message.type) {
                case "system_mission_proposition_waiting_delivery":
                    return true;
                case "system_quote_creation":
                    return true;
                case "system_mission_finished":
                    return this.missionProposition.status === "waiting_closing";
                case "system_mission_quote_paid_not_filled":
                    if (this.isFreelance) {
                        return "waiting_work";
                    }
                    return "waiting_closing"
                case "system_mission_quote_paid":
                    return
                    /*if (this.isFreelance) {
                        return "waiting_work";
                    }
                    return "waiting_closing"*/
                case "system_mission_closed":
                    if (this.message.type && !this.isFreelance) {
                        return true
                    }
                case "system_mission_canceled":
                    if (this.message.type && !this.isFreelance) {
                        return true
                    }
                    break;
                case "system_freelance_quote_info_filled":
                    if (this.message.type && !this.isFreelance ) {
                        return true
                    }else{
                        return true;
                    }
                    break;
                default:
                    return false;
            }
        },
        isDisabled() {
            switch (this.message.type) {
                case "system_mission_closed" :
                    return false;
                case "system_mission_finished" :
                    return this.message.missionPropositionStatus !== "waiting_closing";
                case "system_quote_creation" :
                    return this.message.missionPropositionStatus !== "waiting_quote";
                case "system_mission_quote_paid":
                    return this.message.missionPropositionStatus !== "waiting_work";
                case "system_mission_canceled":
                    return false;
                case "system_mission_quote_paid_not_filled":
                    if (!this.isFreelance) {
                        return this.message.missionPropositionStatus !== "waiting_work";
                    }
                    return this.message.missionPropositionStatus !== "waiting_work";
                case "system_mission_proposition_waiting_delivery":
                    return this.message.missionPropositionStatus !== "waiting_work";
                case "system_mission_taken_by_other":
                    return false;
                case "system_mission_canceled":
                    return this.message.missionPropositionStatus !== "canceled";
                case "system_freelance_quote_info_filled":
                    if (!this.isFreelance) {
                        return this.message.missionPropositionStatus !== 'info_filled';
                    }
                default:
                    return false;
            }
        },
        buttonText() {
            switch (this.message.type) {
                case "system_mission_proposition_waiting_delivery":
                    return 'Envoyer les livrables';
                case "system_quote_creation":
                    return this.$t('send-quote');
                case  "system_mission_finished":
                    return this.$t('close-mission-cta');
                case "system_mission_quote_paid":
                   /* if (this.message.type && !this.isFreelance){
                       // return this.$t('bank-details')
                        return this.$t('close-mission-cta');
                    }
                    return this.$t('close-mission-cta');*/
                case "system_mission_quote_paid_not_filled":
                    if (this.message.type && !this.isFreelance){
                        return this.$t('close-mission-cta');
                    }
                    return this.$t('required-cta-explorer');
                case "system_mission_closed":
                    if (this.message.type && !this.isFreelance) {
                        return this.$t('mission-re-ask')
                    }
                    break;
                case "system_mission_canceled":
                    if (this.message.type && !this.isFreelance) {
                        return this.$t('mission-re-ask')
                    }
                    break;
                case "system_freelance_quote_info_filled":
                    if (this.message.type && !this.isFreelance){
                        return this.$t('close-mission-cta');
                    }else{
                        return "Connect to your Stripe dashboard"
                    }
            }
        },
        messageTime() {
            let messageTime = new Date(this.message.created_at);
            return String(messageTime.getHours()).padStart(2, '0') + ":" + String(messageTime.getMinutes()).padStart(2, '0');
        },
        messageText() {
            switch (this.message.type) {
                case "system_mission_finished" :
                    return "Hello " + this.missionProposition.client.lastname.charAt(0) + this.missionProposition.client.lastname.slice(1) + ", " + this.missionProposition.freelance.firstname + this.$t('close-mission-kolab1')
                case "system_mission_closed" :
                    if (this.user.id === this.missionProposition.client.id) {
                        return this.$t('end-mission-client') + this.missionProposition.client.firstname + " !";
                    } else {
                        return this.$t('end-mission-freelance') + this.missionProposition.freelance.firstname + " !"
                    }
                case "system_quote_creation":
                    return "Hello " + this.missionProposition.freelance.lastname.charAt(0).toUpperCase() + this.missionProposition.freelance.lastname.slice(1) + this.$t('received-mission-message')
                case "system_mission_quote_paid":
                    if (this.user.id === this.missionProposition.client.id) {
                        return this.$t('kolab-congrats-paid-client') + this.missionProposition.freelance.lastname.charAt(0)+ this.missionProposition.freelance.lastname.slice(1) + this.$t('kolab-congrats-paid-client-2');
                    } else {
                        if (this.missionProposition.client.firstname !== "") {
                            return this.$t('kolab-congrats-paid-1') + this.missionProposition.client.lastname.charAt(0).toUpperCase() + this.missionProposition.client.lastname.slice(1) + this.$t('kolab-congrats-paid-2');
                        }
                        return this.$t('kolab-congrats-paid-1') + this.missionProposition.client.name.charAt(0).toUpperCase() + this.missionProposition.client.name.slice(1) + this.$t('kolab-congrats-paid-2');


                    }
                case "system_mission_quote_paid_not_filled":
                    if (this.user.id === this.missionProposition.client.id) {
                        return this.$t('kolab-congrats-paid-client') + this.missionProposition.freelance.lastname.charAt(0)+ this.missionProposition.freelance.lastname.slice(1) + this.$t('kolab-congrats-paid-client-2');
                    } else {
                        if (this.missionProposition.client.firstname !== "") {
                            return this.$t('kolab-congrats-paid-1') + this.missionProposition.client.lastname.charAt(0).toUpperCase() + this.missionProposition.client.lastname.slice(1) + this.$t('kolab-congrats-paid-2');
                        }
                        return this.$t('kolab-congrats-paid-1') + this.missionProposition.client.name.charAt(0).toUpperCase() + this.missionProposition.client.name.slice(1) + this.$t('kolab-congrats-paid-2');
                    }
                case "system_mission_taken_by_other":
                    return this.missionProposition.freelance.firstname + this.$t('mission-accepted-by-other-freelance');
                case "system_mission_canceled":
                    if (this.user.id === this.missionProposition.client.id) {
                        return this.$t('mission-canceled')
                    } else {
                        return this.missionProposition.freelance.lastname.charAt(0).toUpperCase() + this.missionProposition.freelance.lastname.slice(1) + this.$t('mission-canceled-freelance')
                    }
                case "system_freelance_quote_info_filled":
                    if (this.user.id === this.missionProposition.client.id) {
                        return this.$t('congratulation') + this.missionProposition.freelance.lastname.charAt(0).toUpperCase() + this.missionProposition.freelance.lastname.slice(1) + this.$t('bank-details-done');
                    } else {
                        if (this.missionProposition.client.firstname !== "") {
                            return this.$t('congratulation') + this.missionProposition.freelance.lastname.charAt(0).toUpperCase() + this.missionProposition.freelance.lastname.slice(1) + ' ' + this.$t('bank-details-freelance-done')
                        }
                        return false;
                    }
            }
        },
        showMessage() {
            switch (this.message.type) {
                case "system_mission_closed" :
                    return true;
                case "system_mission_finished" :
                    return this.user.id === this.missionProposition.client.id;
                case "system_quote_creation" :
                    return this.user.id === this.missionProposition.freelance.id;
                case "system_mission_quote_paid":
                    return true;
                case "system_mission_canceled":
                    return true;
                case "system_mission_quote_paid_not_filled":
                    return true;
                case "system_freelance_quote_info_filled":
                    return true;

            }
        }
    },
    methods: {
        buttonHandler() {
            switch (this.message.type) {
                case "system_mission_proposition_waiting_delivery":
                    return 'Envoyer les livrables';
                case "system_quote_creation":
                    this.openQuoteProposeModal()
                    break;
                case "system_mission_finished":
                    this.closeMission()
                    break;
                case "system_mission_quote_paid":
                    /*if (this.message.type && !this.isFreelance)
                    {
                        this.closeMission();
                        if(this.message.type === "system_mission_quote_paid_not_filled")
                        {
                            this.$bus.$emit('ACTION_CHANGED', {
                                action: 'CONGRATS',
                                type: 'NOT_CLOSE'
                            });
                            document.querySelector(".conversation-system-message").classList.add('system-error')
                            setTimeout(()=>{
                                document.querySelector(".conversation-system-message").classList.remove('system-error')
                            },6000)
                        }
                    }else{
                        this.stripeAccount()
                    }
                    break;*/
                case "system_mission_quote_paid_not_filled":
                    if (this.message.type && !this.isFreelance)
                    {
                        this.closeMission();
                        if(this.message.type === "system_mission_quote_paid_not_filled")
                        {
                            this.$bus.$emit('ACTION_CHANGED', {
                                action: 'CONGRATS',
                                type: 'NOT_CLOSE'
                            });
                            document.querySelector("#conversation-system-message-system_mission_quote_paid_not_filled").classList.add('system-error')
                            setTimeout(()=>{
                            document.querySelector("#conversation-system-message-system_mission_quote_paid_not_filled").classList.remove('system-error')
                            },6000)
                        }
                    }else{
                        this.stripeAccount()
                    }
                    break;
                case "system_mission_canceled":
                    if (this.message.type && !this.isFreelance) {
                        this.openProposeModal()
                    }
                    break;
                case "system_mission_closed":
                    this.openProposeModal()
                    break;
                case "system_freelance_quote_info_filled":
                    if (this.message.type && !this.isFreelance)
                    {
                        this.closeMission();
                    }else{
                        this.dashboardConnect()
                    }
                    break;
            }
        },
        stripeAccount() {
            window.location.href = "/explorer/messenger/account/" + this.quote.id + "/create";
        },
        openQuoteProposeModal() {
            this.showQuoteProposeModal = true;
        },
        dashboardConnect(){
            //window.location.href = "/explorer/messenger/login/"+ this.quote.id +"/account";
            window.open("/explorer/messenger/login/"+ this.quote.id +"/account", '_blank');
        },
        openProposeModal() {
            this.showProposeModal = true;
        },
        closeProposeModal(){
            this.showProposeModal = false
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
        }
    }
});

Vue.component('conversation-day-containerr', {
    template: `
        <div id="day" class="conversation-day-container">
        <span class="conversation-day-container-date">{{ formatedDate }}</span>
        <hr>
        <div v-for="message in messages">
            <conversation-messagee v-if="message.type.includes('user')" :is-freelance="isFreelance"
                                  :message-type="message.type" :message="message" :is-disabled="isMessageDisabled(message)"
                                  :mission="mission" :user="user" :mission-proposition="missionProposition" :_currencies="_currencies"/>
            <conversation-system-messageee v-else-if="message.type.includes('system')" :user="user" :message="message"
                                           :conversation='conversation'
                                           :quote="message.quote"
                                           :_currencies="_currencies"
                                           :mission-proposition="missionProposition" :is-freelance="isFreelance"/>
        </div>
        </div>
    `,
    props: ['date', "messages", "mission", "user", "missionProposition", "isFreelance", 'conversation', '_currencies'],
    data(){
        return {
            message: this.messages,
            pulseMessage: false,
        }
    },
    computed: {
        formatedDate() {
            let dateToFormat = new Date(this.date);
            let options = {dateStyle: 'full'};
            let dateString = new Intl.DateTimeFormat('en-EN', options).format(dateToFormat);
            return dateString.charAt(0).toUpperCase() + dateString.slice(1);
        },

    },
    mounted() {

        //Handle function for fadeIn previous message with ease
        this.messengerFadeIn()
        /*requestAnimationFrame(() => {
            let input = document.querySelector('.conversation-input-text')
            if(this.message !== null || typeof this.message !== "undefined")
            {
                let testo = this.message.filter(a=>
                {
                    if(a.type === 'system_mission_canceled')
                    {
                        this.pulseMessage = a
                        this.$bus.$emit('cancel',this.pulseMessage)
                    }
                })
            }
            if (this.conversation !== null && this.conversation.lastMessage.type === 'system_mission_closed' || this.pulseMessage.type === 'system_mission_canceled') {
                let container = document.getElementById('message-container');
                let conversation = container.lastElementChild
                let wrapper = conversation.lastElementChild
                let test = wrapper.children
                //container.style.padding = "0"
               // wrapper.style.position = "absolute"
                //wrapper.style.bottom = "5.2rem"
                //wrapper.style.width = "100%"
                //wrapper.style.zIndex = 9999
                test[0].classList.add("pulse")
                test[0].style.width = "99%"
                test[0].style.margin = "auto"
                //input.setAttribute('disabled', '')
            }
            //input.removeAttribute('disabled')

        })*/
    },
    methods: {
        isMessageDisabled(message) {
            return this.conversation.messageToDisableDate > message.created_at;
        },
        messengerFadeIn() {

            let container = document.getElementById('message-container');
            container.addEventListener('scroll', () => {
                let container = document.getElementById('message-container');
                Object.values(container.children).forEach(e => {
                    let distanceToTop = container.offsetTop;
                    let elementHeight = container.offsetHeight;
                    let scrollTop = document.getElementById('message-container').scrollTop;
                    let opacity = 1;
                    let arr = Array.from(e.children)
                    arr.forEach(element => {
                        if (element.offsetTop >= elementHeight) {
                            opacity = 0.7 - (scrollTop - element.offsetTop) / elementHeight
                            element.style.opacity = opacity;

                        } else {
                            opacity = 0.7 - (scrollTop - distanceToTop) / elementHeight;
                            element.style.opacity = opacity;
                        }
                        if (opacity >= 1 && !arr[arr.length - 1]) {
                            element.style.opacity = opacity;
                            element.classList.add("slide-in-messenger")
                        }else{
                            element.style.opacity = 1;
                        }
                    })
                })
            });
        }
    }
});

Vue.component('bubble-mission-summary', {
    props: ['mission', "missionProposition", 'isFreelance'],
    template: `
        <div :class="{'b-s' : !isFreelance}">
        <p style="margin-bottom: 1.4rem;">{{$t('mission-offer-1')}} {{missionProposition.freelance.name.charAt(0).toUpperCase() + missionProposition.freelance.name.slice(1) }}, {{$t('mission-offer-2')}} </p>
        <div class="message-bubble message-bubble-summary" :class="{'message-bubble-button-disabled' : disableButton}">
        <div class="message-bubble-header">
            <div class="mission-summary-icon-container">
                <span class="picto-explorer-mission-summary-colored"></span>
            </div>
            <div class="mission-summary-titles">
                <span class="bubble-mission-name">{{ mission.name }}</span>
                <span class="bubble-mission-type">{{ $t(mission.taskType) }}</span>
            </div>
        </div>
        <div class="message-bubble-body">
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-eur"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Budget</div>
                    <div class="summary-item-value">{{mission.budget }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-calendar"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Deadline</div>
                    <div class="summary-item-value">{{ mission.deadline }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-notes"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">{{$t('mission-description')}}</div>
                    <div class="summary-item-value">{{ mission.description }}
                    </div>
                </div>
            </div>
        </div>
        <button v-if="showCancelButton" :disabled="disableButton" class="message-bubble-button" @click="buttonHandler"
                type="button">
            {{$t('cancel-offer-cta')}}
        </button>
        <explorer-messenger-cancel-mission-popup v-if="showCancelMissionModal" @modal-close="closeCancelMissionModal"
                                                 :mission-proposition="missionProposition"/>
        </div>
        </div>`,
    data() {
        return {
            showCancelMissionModal: false
        }
    },
    computed: {
        showCancelButton() {
            return !this.isFreelance;
        },
        disableButton() {
            return !(this.missionProposition.status === "waiting_quote");
        }
    },
    mounted() {
        this.mission.deadline = Vue.prototype.Global._reformatDate(this.mission.deadline)
    },
    methods: {
        buttonHandler() {
            this.openCancelMissionModal();
        },
        cancelMission() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_proposition_cancel'
                }
            }).then(res => {
            }).catch(error => console.log(error))
        },
        closeCancelMissionModal() {
            this.showCancelMissionModal = false
        },
        openCancelMissionModal() {
            this.showCancelMissionModal = true
        }
    },

})

Vue.component('bubble-mission-quote', {
    props: ['quote', "isFreelance",'mission-proposition'],
    template: `
        <div class="message-bubble message-bubble-quote" style="margin-top: 2rem" :class="{'message-bubble-button-disabled' : disableButton}">
        <div class="message-bubble-header" :style="{justifyContent:justify }">
            <span class="bubble-quote-title">{{$t('quote')}}</span>
            <button v-if="showCancelButton" :disabled="disableButton" class="message-bubble-button m-c_paid" @click="buttonHandler"
                    type="button">
                {{$t('cancel-offer-cta')}}
            </button>
            <explorer-messenger-cancel-mission-popup v-if="showCancelMissionModal" @modal-close="closeCancelMissionModal"
                                                     :mission-proposition="missionProposition"/>
        </div>
        <div class="message-bubble-body">
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-pile"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">{{$t('quote-included')}}</div>
                    <ul>
                        <li v-for="quoteItem in JSON.parse(quote.quote_line)" class="summary-item-value">
                            {{ quoteItem }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-calendar"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">{{$t('quote-delivery')}}</div>
                    <div class="summary-item-value">{{ quoting }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-eur"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">{{$t('quote-price')}}</div>
                    <div class="summary-item-value">{{ quote.price }} {{ quote.currencySymbol }} {{$t('H-T')}}</div>
                </div>
            </div>
            <div v-if="quote.service_fee !== ''" class="summary-item">
                <div class="icon-container"><span class="picto-explorer-shield-colored"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">{{$t('quote-included-fee')}}</div>
                    <div class="summary-item-value">{{ quote.service_fee }} {{ quote.currencySymbol }} {{$t('H-T')}}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-shield-colored"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">{{$t('quote-comments')}}</div>
                    <div class="summary-item-value">{{ quote.comments }}</div>
                </div>
            </div>
        </div>
        <div class="message-bubble-footer" style="padding-right: 10px ">
            <div>
                <span class="quote-total">Total : </span>
                <span class="quote-price">{{ sum(parseInt(quote.price),parseFloat(quote.service_fee)) }} {{ quote.currencySymbol }}  {{$t('H-TT')}}</span>
            </div>
            <span v-if="quote.status === 'PAID' || quote.status === 'INFO_FILLED'" class="quote-status-paid">{{$t('quote-pay-cta-2')}} ✓</span>
            <button v-else-if="!isFreelance && quote.status === 'WAITING_PAYMENT'" @click="payQuote"
                    class="quote-button">{{$t('quote-pay-cta')}}
            </button>
            <span v-else-if="isFreelance && quote.status === 'WAITING_PAYMENT'" class="quote-status-waiting-payment">{{$t('quote-pay-cta-freelance')}}</span>

        </div>
        </div>`,
    mounted() {
        this.quoting = Vue.prototype.Global._reformatDate(this.quote.deadline)
        this.sum()
    },
    computed:{

        disableButton() {
            return this.quote.status === "PAID" || this.quote.status === "INFO_FILLED" || this.quote.status === "CANCELED";
        },
        showCancelButton() {
            return !this.isFreelance;
        },
    },
    methods: {
        payQuote() {
            window.location.href = "/explorer/messenger/quotes/" + this.quote.id + "/checkout";
        },
        sum(a,b){
            return a+b;
        },
        buttonHandler() {
            this.openCancelMissionModal();
        },
        cancelMission() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_proposition_cancel'
                }
            }).then(res => {
            }).catch(error => console.log(error))
        },
        openCancelMissionModal() {
            this.showCancelMissionModal = true
        },
        closeCancelMissionModal() {
            this.showCancelMissionModal = false
        },
    },
    data() {
        return {
            justify:'space-between',
            quoting:null,
            showCancelMissionModal: false
        }
    },

})

Vue.component('bubble-drive-link', {
    template: `
        <a class="message-bubble-drive-link" :href="driveLink.link">
        <div class="link-miniature" :style="{'background': 'url(' + driveLink.miniature + ')', backgroundPosition: 'center', backgroundSize:'cover'}">
        </div>
        <div class="link-description">
            <span class="link-name">{{ driveLink.name }}</span>
            <span class="link-url">{{ driveLink.link }}</span>
        </div>
        </a>`,
    methods: {},
    props: ['driveLink'],
    mounted() {

    },
    data() {
        return {}
    }
})
</script>
