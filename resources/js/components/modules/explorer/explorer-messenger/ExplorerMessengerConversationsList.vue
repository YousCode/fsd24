<template>
    <div class="explorer-messenger-conversation-list">
        <div class="conversations-list-header">
            <a class="go-back-button" href="/explorer">
                <span class="picto-explorer-selector-left-colored"/>
            </a>
            <h1 class="conversation-list-title">Conversations</h1>
        </div>

        <div style="overflow: auto;height: 80vh;">
            <transition-group name="new-conversation" >
            <explorer-messenger-conversation-item
                v-for="conversation in conversationsList"
                :selectedConv = 'selectedConversation'
                :conversation='conversation'
                :conversationLists = "conversationsList"
                v-on:update-selected-conversation="emitSelectedConversation(conversation)"
                :is-freelance="isFreelance"
                :user="user"
                :key="conversation.id"
                :convId="conversation.id"
                :is-active="conversation.id===selectedConversation.id"
                :messages="messages" :conversationList="newConversation" :proposition="newProposition" class="new-conversation-items"></explorer-messenger-conversation-item>
            </transition-group>

        </div>
    </div>
</template>
<script>
export default {
    props: ['user', 'conversationsList', 'selectedConversation', 'isFreelance','messages'],
    data(){
        return {
            newConversation: false,
            newProposition:null
        }
    },
    mounted() {
        window.Echo.private('explorer').listen('.new-conversation',(e)=>{
            this.getConversationMessage()
        });

    },
    methods: {
        emitSelectedConversation(conversation) {
            this.$emit('update-selected-conversation', conversation);
        },
        getConversationMessage() {
            if (this.conversationsList !== null ) {
                axios.get("/api/explorer/missions/conversations", {}).then(res => {
                   //this.conversationsList.unshift(res.data[0])
                    this.$bus.$on('newconv',()=>this.conversationsList.unshift(res.data[0]))
                    this.newConversation = true
                    this.newProposition= res.data[0].id_proposition
                    this.isNew = true
                }).catch(error => console.log(error))
            }
        }
    }
}

Vue.component('explorer-messenger-conversation-item', {
    template: `
        <button @click="handleSelectConversation" type="button" class="explorer-messenger-conversation-list-item" :class="{'explorer-messenger-conversation-list-item-active' : isActive}">
        <div v-if="showInitialClient || showInitialFreelance " class="talent-initials-container">
            <span v-if="isFreelance" class="talent-initials">{{ getTalentInitials(conversation.proposition.client)}}</span>
            <span v-else class="talent-initials">{{ getTalentInitials(conversation.proposition.freelance)}}</span>
        </div>
        <img v-else @error="handleImgError" :src="isFreelance ? '/upload/avatars/' + conversation.proposition.client.avatar : '/upload/avatars/' + conversation.proposition.freelance.avatar "
             class="profile-image-container"
             alt="profile">
        <div class="mission-infos-container">
            <span v-if="isFreelance" class="mission-contact">{{ conversation.proposition.client.name.charAt(0).toUpperCase() + conversation.proposition.client.name.slice(1) }}</span>
            <a v-else class="mission-contact" :href="'/explorer/profile/' + conversation.proposition.freelance.id">{{ conversation.proposition.freelance.name.charAt(0).toUpperCase() + conversation.proposition.freelance.name.slice(1) }}</a>
            <span class="mission-name">{{ conversation.proposition.mission.name }}</span>
        </div>
        <span class="mission-last-activity">{{time}} ago</span>
        <span>
            <span v-show="showNotification(convId)" class="mission-notification-dot"></span>
        </span>
        </button>`,
    methods: {
        handleSelectConversation() {
            this.emitSelectedConversation();
            this.patchConversation();
        },
        emitSelectedConversation(conversation) {
            this.$emit('update-selected-conversation', conversation);
        },
        showNotification(convId) {
            return this.ids.includes(convId)
        },
         patchConversation() {
             axios.patch("/api/explorer/missions/conversations/" + this.conversation.id, {
                params: {
                    patch_type: 'update_last_check'
                }
            }).then(res => {
                 setTimeout(()=>{
                     this.markAsRead()
                 },1000)
            }).catch(error => console.log(error))
        },
        getLists() {
            if(this.conversationsList !== null && this.user) {
                axios.get("/api/explorer/missions/conv", {}).then(res => {
                    if (typeof res.data == 'object') {
                        res.data = Object.values(res.data);
                    }
                    this.ids = res.data.map(id=>id);
                }).catch(error => console.log(error))
            }
        },
        updateTime(){
            if (this.lastMessenger === null )
            {
                this.time = new Date()
                let today = this.time
                const endDate = new Date(this.conversation.lastMessage.created_at);
                const days = parseInt(Math.abs(endDate - today) / (1000 * 60 * 60 * 24));
                const hours = parseInt(Math.abs(endDate - today) / (1000 * 60 * 60) % 24);
                const minutes = parseInt(Math.abs(endDate.getTime() - today.getTime()) / (1000 * 60) % 60);
                const seconds = parseInt(Math.abs(endDate.getTime() - today.getTime()) / (1000) % 60);

                if (days > 0) {
                    if (days > 1) {
                        return this.time = days + ' days';
                    }
                    return this.time = days + ' day';
                }

                if (hours > 0) {
                    if (hours > 1) {
                        return this.time = hours + " " + this.$t('hours');
                    }
                    return this.time = hours + " " + this.$t('hour');
                }

                if (minutes > 0) {
                    if (minutes > 1) {
                        return this.time = minutes + " " + this.$t('minutes');
                    }
                    return this.time = minutes + " " + this.$t('minute');
                }

                if (seconds >= 0) {
                    if (seconds >= 1) {
                        return this.time = seconds + " " + this.$t('seconds');
                    }
                    return this.time = seconds + " " + this.$t('second');
                }

            }else{
                this.time = new Date()
                const today = this.time;
                const endDate = new Date(this.lastMessenger.created_at);
                const days = parseInt(Math.abs(endDate - today) / (1000 * 60 * 60 * 24));
                const hours = parseInt(Math.abs(endDate - today) / (1000 * 60 * 60) % 24);
                const minutes = parseInt(Math.abs(endDate.getTime() - today.getTime()) / (1000 * 60) % 60);
                const seconds = parseInt(Math.abs(endDate.getTime() - today.getTime()) / (1000) % 60);

                if (days > 0) {
                    if (days > 1) {
                        return this.time = days + " " + " jours";
                    }
                    return this.time = days + " " + " jour";
                }

                if (hours > 0) {
                    if (hours > 1) {
                        return this.time = hours + " " + " heures";
                    }
                    return this.time = hours + " " + " heure";
                }

                if (minutes > 0) {
                    if (minutes > 1) {
                        return this.time = minutes + " " + " minutes";
                    }
                    return this.time = minutes + " " + " minute";
                }

                if (seconds >= 0) {
                    if (seconds >= 1) {
                        return this.time = seconds + " " + " secondes";
                    }
                    return this.time = seconds + " " + " seconde";
                }
            }
        },
         getConversationList() {
            if (this.conversation !== null) {
                  axios.get("/api/explorer/missions/conversations/"+ this.conversation.id + "/messages", {}).then(res => {
                    let result = res.data
                    let arr = []
                    Object.values(result).forEach(item=>{
                        arr.push(item.length)
                        //let test = new Date().toString()
                        //let today = new Date(item.lastMessage.updated_at).toString();
                        Object.values(item).forEach(message=>{
                            let test = new Date()
                            let less= test.setSeconds(0,0)
                            let date = new Date(message.updated_at)
                            let rest = date.setSeconds(0,0)
                            if ((less === rest && this.conversation.id === message.id_conversation) && item.length){
                                this.updateLast = rest
                                this.freelance = message.freelance
                                this.client= message.client
                                this.lastMessenger = message
                                this.test = message.mark_as_read
                                this.dateNow = less
                                this.$bus.$emit('sendedBy',()=>message.created_by)
                                this.hasSend = message.created_by
                                this.isProposition = message.id_proposition
                                this.isConversationMessage = this.conversation.id
                            }
                        })
                    })
                }).catch(error => console.log(error))
            }
        },
        getTalentInitials(talent) {
            return talent.firstname.charAt(0).toUpperCase() + talent.lastname.charAt(0).toUpperCase();
        },
        handleImgError() {
            this.showInitial = true;
        },

        markAsRead(){
            axios.post("/api/explorer/missions/conversations/" + this.conversation.id + "/markAsRead", {}).then(res => {
                this.$bus.$emit('read',res)
            }).catch(error => console.log(error))
        }
    },
    data() {
        return {
            showInitialClient: this.conversation.proposition.client.avatar === 'user.jpg',
            showInitialFreelance: this.conversation.proposition.freelance.avatar === 'user.jpg',
            newMessage:null,
            message:null,
            currentNotif:[],
            hasSend: "",
            isProposition:null,
            updateLast:null,
            isConversationMessage:false,
            dateNow:null,
            read:0,
            freelance:"",
            client:"",
            lastMessenger: null,
            ids:[],
            notify: [],
            last: this.lastActivity,
            time:null
        }
    },
    props: ['isActive', 'isFreelance','user','conversation','conversationList','proposition','selectedConv','conversationLists','convId'],
     mounted() {
         setInterval(()=>this.updateTime(), 1000);
        if(this.isActive){
            this.handleSelectConversation()
            this.getLists()
        }
         window.Echo.private('explorer').listen('.new-message',(e)=>{
             this.updateTime()
         });
         if(this.user)
         {
             this.$bus.$on('UPDATE_DRIVE_URL_EXPLORER', () => {
                 this.getLists()
             });
             this.$bus.$on('read',()=>{
                 if(this.isActive)
                 {
                     this.getLists()
                 }
             })
             this.updateTime()
             this.getLists()
             window.Echo.private('explorer').listen('.new-message', (e) => {
                 this.getLists()
             });
         }
         window.Echo.private('explorer').listen('.new-update',(e)=>{
             this.getConversationList()
         });
         window.Echo.private('explorer').listen('.new-conversation',(e)=>{
             this.getConversationList()
         });
    },
    computed: {
        isNew() {
            if (this.isFreelance && this.conversation.last_freelancer_check) {
                if (this.isFreelance) {
                    if (this.conversation.proposition.freelance.name !== this.conversation.lastMessage.createdByUsername) {
                        return Date.parse(this.conversation.last_freelancer_check) < Date.parse(this.conversation.lastMessage.created_at);
                    }
                }
            } else {
                if (!this.isFreelance) {
                    if (this.conversation.proposition.client.name !== this.conversation.lastMessage.createdByUsername) {
                        if (this.conversation.last_client_check) {
                            return Date.parse(this.conversation.last_client_check) < Date.parse(this.conversation.lastMessage.created_at);
                        }
                    }
                }
            }
            return false;
        }
    }
});
</script>
