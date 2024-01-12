<template>
    <div style="position: relative;">
            <span v-show="notifications > 0">
                <span id="message" :class="notifications > 0 ? 'home-notification' : '' ">
                    <span v-if=" notifications < 9 && notifications !== 0">{{notifications}}</span>
                    <span v-else-if="notifications > 9 ">9+</span>
                </span>
            </span>
        <!--<span id="message" class="home-notification" style="display: none"></span>-->
        <button type="button" class="button js-toggle-button h-r" style="width: 35px;height: 35px;background: #2B1C56;justify-content: center;display: flex;align-items: center;padding: 0;"><img src="/images/Subtract.png" class="h-r_i"  alt="" style="width:13px; height:13px;">
        </button>
    </div>

</template>

<script>
    export default {
        props: ['user','conversation'],

        data() {
            return {
                notifications: [],
                lastMessage:'',
                conversationLists: null,
                hasSend:null,
                newMessage: false,
                conv: null,
                client:null,
                freelance:null,
                createdBy:null,
                isNew : 'home-notification',
                freelanceNotif: [],
                clientNotif:[]
            }
        },

        mounted() {
            if(typeof this.notifications !== 'undefined')
            {
                this.getNotifications();
            }
            this.$bus.$on('read',()=>{
                if(typeof this.notifications !== 'undefined')
                    this.getNotifications();
            })
            this.$bus.$on("newBubble",(res)=>{
                this.getConversations()
            });

            window.Echo.private('explorer').listen('.new-link', (e) => {
                this.getNotifications()
            })
            if (this.conversation !== null) {
                window.Echo.private('explorer-notification').listen('.new-notification',(e)=>{
                    //this.getConversation()
                    this.getNotifications()
                });
                window.Echo.private('explorer').listen('.new-message',(e)=>{
                    //this.getConversation()
                    this.getNotifications()
                });
                window.Echo.private('explorer').listen('.new-conversation',(e)=>{
                    this.getNotifications()
                });
                window.Echo.private('explorer').listen('.new-update',(e)=>{
                    this.getNotifications()
                });
            }
        },


        methods: {
        	getNotifications() {
                if(this.notifications !== null && typeof this.notifications !== 'undefined'){
                  axios.get('/api/notification',
                  {
                      params: {}
                  }).then(res => {
                      this.notifications = res.data;
                  }).catch(error => console.log(error));
                }
                return true;
	        },
            async getConversations() {
                await axios.get("/api/explorer/missions/conversations", {
                    params: {}
                }).then(res => {
                    document.getElementById('loader').style.display='none'
                    this.conversationsList = res.data;
                    this.newMessage = true
                }).catch(error => console.log(error));
            },
      	}
    }
</script>
