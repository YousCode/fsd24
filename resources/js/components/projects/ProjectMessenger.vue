<template>
    <div class="project-detail-modal project-messenger-conversation explorer-messenger-card explorer-messenger-drive-card project-messenger">
    <input type="hidden" id="fromMessenger" name="fromMessenger" value="false">
        <button type="button" @click="toggleCard" class="card-header">
            <div class="picto-container"><span class="picto-messenger"/></div>
            <h1 class="card-title">Messages <span v-show="!_isShared">({{ conversation.countMessages }})</span></h1>
            <span v-show="newProjectMessage" class="notification"></span>
            <div v-show="_isShared || _isClient" style="height: 30px;width: 30px;padding: 5px;background: #2B1C56;border-radius: 5px;margin-left: 10px;"><span class="picto-messenger-lock"></span></div>
            <div class="card-dropdown-button">
                <span v-if="isShow" class="picto-explorer-dropdown-up"/>
                <span v-else class="picto-explorer-dropdown-down"/>
            </div>
        </button>
        <div v-show="isShow" class="card-body">
            <div v-if="!_isShared && !_isClient">
                <div class="conversation-message-container" id="message-container">
                        <div class="conversation-message" v-show="conversation.proposition.kolab_project_id == 1" v-for="message in messagesOnBoarding">
                                <a href="">
                                    <div v-bind:class="[false ? 'talent-initials-container-self' : 'talent-initials-container']" :style="(message.initials == 'CF') ? {backgroundColor: '#EBFF00'} : {}">
                                        <span class="picto-kolab" v-if="message.initials == 'KB'"></span>
                                        <span class="picto-kolab-client" v-else></span>
                                        <span class="talent-initials">
                                            <!-- {{ message.initials }} -->
                                        </span>
                                    </div>
                                </a>
                                <div class="message-content-container" v-if="conversation.proposition.kolab_project_id == 1">
                                    <div class="message-header" v-bind:class="{'self-message' : false }">
                                        <span class="message-username">{{ message.username }}</span>
                                        <span class="message-hour">{{ message.messageTime }}</span>
                                    </div>
                                    <div class="message-body" v-bind:class="{'self-message' : false }">
                                        <p class="message-paragraph">
                                            {{ message.text }}
                                        </p>
                                    </div>
                                    <div class="message-body" v-if="message.file && files[message.key]" v-bind:class="{'self-message' : false }" :style="{marginBottom: '20px'}">
                                        <a href="" class="drive-item" target="_blank" style="width: auto">
                                            <div class="media-item-miniature" :style="{'background-image': 'url(' + files[message.key].url_view + ')', backgroundPosition: 'center', backgroundSize:'cover'}"></div>
                                            <div class="media-item-infos" :style="{backgroundColor: '#38266B', height: 'auto', width: '200px'}">
                                            <p :style="{fontWeight: '600'}">{{ files[message.key].name.substring(0,15)+".." }}</p>
                                            <p :style="{color: '#7665A7'}">{{ files[message.key].extension.toUpperCase() }}</p>
                                            <a :href="files[message.key].url_download" target="_blank">
                                                <div class="icon-container"><span class="picto-messenger-download"></span></div>
                                            </a>
                                        </div>
                                        </a>
                                     </div>
                                    <div class="message-body" v-if="message.driveLink" v-bind:class="{'self-message' : false }">
                                        <a :href="driveLink[0].link" class="drive-item" target="_blank" :style="{width: 'auto'}">
                                            <div class="drive-item-miniature" :style="{'background-image': 'url(' + driveLink[0].miniature + ')', backgroundPosition: 'center', backgroundSize:'cover'}"></div>
                                            <div class="drive-item-infos" :style="{backgroundColor: '#38266B', height: 'auto'}">
                                                <span class="drive-item-name" :style="{ fontSize: '12px' }">{{ driveLink[0].name.substring(0,30)+".." }}</span>
                                                <span class="drive-item-link" :style="{ fontSize: '12px' }">{{ driveLink[0].link.substring(0,30)+".." }}</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                        </div>
                    <div v-if="conversation.countMessages > 0 || newMessage" v-show="conversation.proposition.kolab_project_id !== 1">
                        <conversation-day-container v-for="(messages, date) in messagesByDay" v-bind:key="date" :date="date" :messages="messages" :mission="conversation.proposition.mission" :conversation='conversation' :mission-proposition="conversation.proposition" :user="user" />
                    </div>
                    <div class="empty-messenger" v-else>
                        <div class="date-head">
                            <div class="conversation-day-container">
                                <div class="conversation-day-container-date">
                                    {{ dateNow }}
                                </div>
                            </div>
                        </div>
                        <div class="empty-messenger-content">
                            <div class="empty-messenger-wrapper"><span class="picto-empty-messenger"></span></div>
                            <div>
                                Aucun message pour le<br>
                                moment
                            </div>
                        </div>
                    </div>
                </div>

                <div class="conversation-footer">
                <div id="media-conversation-container" v-show="filesFromMessenger.length > 0">
                    <div id="preview-container">
                        <div class="img-container" :class="file.type == 'application/pdf' ? 'pdf' : ''" v-for="file in filesFromMessenger">
                            <span class="icon icon-cross" v-on:click="removeFromFilesMessenger(file)"></span>
                            <div v-if="file.type == 'application/pdf'">
                                <div id="media-pdf">
                                    <span class="picto-medias"></span>
                                </div>
                                <p>.pdf</p>
                            </div>
                            <img v-else id="preview-upload" :src="(file.previewUrl) ? file.previewUrl : ''" alt=""/>
                        </div>
                    </div>
                </div>
                <div class="conversation-input-container">
                <input type="text" v-model="messageToPost" @keyup.enter="postMessage" ref="messageInput" class="conversation-input-text" placeholder="Ecrivez votre message ici..." />
                    <button @click="postMessage" class="conversation-input-button">
                        <span class="picto-explorer-send-message" style="height: 13px"/>
                    </button>
                    <div class="medias-conv-container" v-on:click="onClickUpload">
                        <span class="picto-medias-conv"></span>
                    </div>
                </div>
                </div>
            </div>
            <div v-else class="no-items text-center">
                <div>
                    <div style="height: 40px;width: 40px; margin: 0 auto;">
                        <div class="picto-messenger-lock"></div>
                    </div>
                    <p>Vous ne pouvez pas accéder</p>
                    <p>à la conversation</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        components: {},
        props: ['user', 'conversation', '_isShared', '_isClient'],
        data() {
            return {
                messageToPost: "",
                messagesByDay: null,
                textAreaHeight: '30px',
                isShow: true,
                filesFromMessenger: [],
                newProjectMessage: false,
                messagesToAdd: [
                    {'text': 'Bienvenue ! Sur cette page vous pourrez retrouver et rassembler tous les éléments d\'un projet vidéo.', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB'},
                    {'text': 'Du planning à la liste des livrables, en passant par la typo ou le numéro du client.', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB'},
                    {'text': 'Justement en parlant de ça...', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB'},
                    {'text': 'Nous venons de remplir un exemple de liste des livrables et nous avons rajouté le logo pour le projet.', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB', 'key': 2, 'file': true, 'url_download': '/project-file/637fb1245f16b', 'url_view': '/project-file/637fb1245f16b', 'name': 'logo-lorem', 'extension': 'svg'},
                    {'text': '', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB', 'key': 1, 'file': true, 'url_download': '/project-file/637fb1245f16a', 'url_view': '/project-file/637fb1245f16a', 'name': 'Livrables_PUB', 'extension': 'pdf'},
                    {'text': '', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB', 'key': 0, 'file': true, 'url_download': '/project-file/637fb1245f16c', 'url_view': '/project-file/637fb1245f16c', 'name': 'Helvetica', 'extension': 'ttc'},
                    {'text': 'Hello, quelqu\'un peut m\'envoyer un lien Kolab pour voir le planning de post-prod et la liste des livrables pour le projet svp ?', 'username': 'Client ou freelance', 'messageTime': '', 'initials': 'CF'},
                    {'text': 'Pour partager le lien Kolab d\'un projet vidéo, cliquez en haut à droite sur le bouton "Partager". Avez-vous essayé ?', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB'},
                    {'text': '@Client ou freelance, voici le lien Kolab: https://kolab.app/sharing/project/fri733de', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB', 'driveLink': true},
                    {'text': 'Kolab vous permet également de gérer vos projets en créant des tâches pour vous et vos collaborateurs', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB'},
                    {'text': 'Nous venons justement d\'en ajouter plusieurs au calendrier:', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB'},
                    {'text': 'La présentation de cette page est terminée. Encore bienvenue sur Kolab :)', 'username': 'Kolab', 'messageTime': '', 'initials': 'KB'},
                ],
                messagesOnBoarding: [],
                driveLink: false,
                files: false,
                dateNow: false,
                newMessage: false,
            }
        },
        created() {
            this.getConversationMessage();
        },
        mounted() {
            this.setFormatDate();
            if (!this._isShared) {
                this.filesFromMessenger = this.$parent.$children[6].filesToUploadFromMessenger;
                let $this = this;
                const scrollingElement = document.getElementById('message-container');
                setTimeout(() => {
                    scrollingElement.scrollTo({top:scrollingElement.scrollHeight,behavior:"smooth"})
                }, 3000);
                // ONBOARDING PROJECT DETAIL MESSENGER
                if (this.conversation.proposition.kolab_project_id == 1) {
                    var messagesOnBoarding = this.messagesOnBoarding;
                    var parent = this.$parent;
                    var bus = this.$bus;
                    setTimeout(() => {
                        this.messagesToAdd.forEach(function(item, key) {
                        parent.$children[4].newProjectMessage = true;
                        setTimeout(() => {
                            messagesOnBoarding.push(item);
                            $this.conversation.countMessages = $this.conversation.countMessages + 1;
                            if (key == 3 && typeof parent != "undefined" && typeof parent.$children[5] != "undefined" && typeof parent.$children[6] != "undefined") {
                                parent.$children[5].newExport = true;
                                parent.$children[6].newFile = true;
                                bus.$emit('ACTION_CHANGED', {
                                    action: 'CONGRATS',
                                    type: 'ADD_EXXPORT'
                                });
                            } else if (key == 8 && typeof parent != "undefined" && typeof parent.$children[7] != "undefined") {
                                parent.$children[7].newLink = true;
                                bus.$emit('ACTION_CHANGED', {
                                    action: 'CONGRATS',
                                    type: 'ADD_DRIVE'
                                });
                            } else if (key == 10 && typeof parent != "undefined" && typeof parent.$children[2] != "undefined") {
                                parent.$children[2].tasksToCalendarOnBoarding();
                                parent.$children[2].getPlanning();
                                parent.$children[2].onboardingEnd = true;
                            }
                            scrollingElement.scrollTo({top:scrollingElement.scrollHeight,behavior:"smooth"});
                        }, key * 3500);
                    });
                    }, 1 * 3500);
                }
                // ONBOARDING PROJECT DETAIL MESSENGER
            }
            this.getFiles();
            this.getLinks();

            this.$bus.$on("FILES_FROM_MESSENGER", () => {
                this.filesFromMessenger = this.$parent.$children[6].filesToUploadFromMessenger;
            });

            this.$bus.$on('UPDATE_COUNT_MESSAGES', (add) => {
                if (add == 0) {
                    this.conversation.countMessages--;
                    if (this.conversation.countMessages < 0) {
                        this.conversation.countMessages = 0;
                    }
                } else {
                    this.conversation.countMessages++;
                }
            });

            window.Echo.private('kolab').listen('.message-added', (e) => {
                this.newMessage = true;
                setTimeout(() => {
                    this.getConversationMessage();
                    this.scrollDown();
                }, 500)
                this.newProjectMessage = true;
                this.$bus.$emit('UPDATE_COUNT_MESSAGES', 1);
            });

            window.Echo.private('kolab').listen('.drive-deleted', (e) => {
                setTimeout(() => {
                    this.getConversationMessage();
                    this.scrollDown();
                }, 500)
            });

            window.Echo.private('kolab').listen('.file-added', (e) => {
                this.getFiles();
                this.getConversationMessage();
                this.scrollDown();
                this.conversation.countMessages++;
                if (this.$parent.$children[6]) {
                    this.$parent.$children[6].newFile = true;
                }
            });

            window.Echo.private('kolab').listen('.link-added', (e) => {
                this.getLinks();
                this.getConversationMessage();
                this.scrollDown();
                if (this.$parent.$children[7]) {
                    this.$parent.$children[7].newLink = true;
                }
            });
            if (this._isShared) {
                this.isShow = false; 
            }
        },
        computed: {
            textAreaStyle() {
                return {
                    'height': this.textAreaHeight
                }
            }
        },
        watch: {
            conversation: function () {
                this.messagesByDay = null;
                this.getConversationMessage();
            }
        },
        methods: {
            setFormatDate() {
                let dateToFormat = new Date();
                let options = {dateStyle: 'full'};
                let dateString = new Intl.DateTimeFormat('fr-FR', options).format(dateToFormat);
                this.dateNow = dateString.charAt(0).toUpperCase() + dateString.slice(1);
            },
            scrollDown() {
                const scrollingElement = document.getElementById('message-container');
                if (scrollingElement && scrollingElement.children && scrollingElement.children[0] && scrollingElement.children[0].lastChild) {
                    let conversationElement = scrollingElement.children[0].lastChild;
                    const config = { childList: true };

                    const callback = function (mutationsList, observer) {
                    for (let mutation of mutationsList) {
                        if (mutation.type === "childList") {
                            scrollingElement.scrollTo({top:scrollingElement.scrollHeight,behavior:"smooth"});
                        }
                    }};
                    const observer = new MutationObserver(callback);
                    observer.observe(conversationElement, config);
                }
            },
            postMessage() {
                if (this.conversation.proposition.kolab_project_id == 1) {
                    return false;
                }
                var message = this.messageToPost;
                var hasFiles = false;
                var files = false;
                if (this.$parent.$children[6] && this.$parent.$children[6].filesToUploadFromMessenger && this.$parent.$children[6].filesToUploadFromMessenger.length > 0) {
                    hasFiles = true;
                    files = this.$parent.$children[6].filesToUploadFromMessenger;
                }
                if (message.length == 0) {
                    message = "§";
                }
                if (message) {
                    axios.post("/api/explorer/missions/conversations/" + this.conversation.id + "/messages", {
                    params: {
                        message: message,
                        fromProject: true,
                    }
                    }).then(res => {
                        if(hasFiles && files) {
                            var messageId = res.data;
                            axios.post('/project/'+ this.conversation.proposition.kolab_project_id +'/file', {
	    			            project: this.conversation.proposition.kolab_project_id,
	    			            files: files,
                                messageId: messageId
                            }).then(res => {
                                if(res.success === false){
                                    // Error
                                } else {
                                    this.files = res.data.datas;
                                    var mediaAction = "ADD_MEDIA";
                                    this.$bus.$emit('ACTION_CHANGED', {
                                        action: 'CONGRATS',
                                        type: mediaAction
                                    });
                                    this.$parent.$children[6].filesToUploadFromMessenger = [];
                                    this.filesFromMessenger = this.$parent.$children[6].filesToUploadFromMessenger;
                                    this.conversation.countMessages++;
                                }
                            }).catch(error => console.log(error));
                        }

                        this.messageToPost = '';
                        //this.getConversationMessage();
                    }).catch(error => console.log(error));
                }
            },
            onClickUpload() {
                if(document.getElementById("fromMessenger")) {
                    document.getElementById("fromMessenger").value = 1;
                }
			    $('#fileupload').click();
		    },
            async getConversationMessage() {
                if (this.conversation !== null) {
                    await axios.get("/api/explorer/missions/projectConversations/" + this.conversation.id + "/messages", {}).then(res => {
                        this.messagesByDay = res.data;
                    }).catch(error => console.log(error))
                }
            },
            resizeTextarea() {
                this.textAreaHeight = `${this.$refs.messageInput.scrollHeight}px`
            },
            toggleCard() {
                this.$parent.$children[5].isShow = false;
                this.$parent.$children[6].isShow = false;
                this.$parent.$children[7].isShow = false;
                this.isShow = !this.isShow;
            },
            async getFiles(){
                await axios.get('/api/project/'+ this.conversation.proposition.kolab_project_id +'/file')
    			.then(res => {
		      	if (res.success === false) {
		      		// Error
		      	} else {
                    this.$bus.$emit('FILE_ADD_OR_UPDATE', res.data.datas); // Emit add or update talent event
                    this.files = res.data.datas;
		      	}
		      }).catch(error => console.log(error));
    	    },
            async getLinks() {
                await axios.get('/api/project/'+ this.conversation.proposition.kolab_project_id)
                    .then(res => {
                    if(typeof res.data.datas.proposition.drive !== "undefined"){
                        this.driveLink = res.data.datas.proposition.drive.driveLinks;
                            this.$bus.$emit('UPDATE_DRIVE', res.data.datas.proposition.drive); // Emit add or update project event
                    } else {
                        //error
                    }
                }).catch(error => console.log(error));
            },
            objectUrl(file) {
                if (file) {
                    return window.URL.createObjectURL(file);
                }
            },
            removeFromFilesMessenger(file) {
              var index = this.filesFromMessenger.indexOf(file);
                  if (index !== -1) {
                    this.filesFromMessenger.splice(index, 1);
                  }
            },
            log(error) {
                console.log(error);
            }
        },
    }

    Vue.component('conversation-message', {
        template: `
        <div class="conversation-message" :class="{'conversation-message-disabled' : !isDisabled}">
        <a :href="'/explorer/profile/' + message.created_by">
            <div v-if="showInitial"
                 v-bind:class="[isSelf ? 'talent-initials-container-self' : 'talent-initials-container' ]">
                <span class="talent-initials" v-if="message.createdByUsername != ''">{{ getTalentInitials(message.createdByUsername.toUpperCase()) }}</span>
                <span class="talent-initials" v-else-if="message.contactEmail != ''">{{ getTalentInitials(message.contactEmail.toUpperCase()) }}</span>
                <span class="talent-initials" v-else>{{ getTalentInitials('Kolab') }}</span>
            </div>
            <img v-else @error="handleImgError" :src="'/upload/avatars/' + message.createdByAvatar"
                 v-bind:class="[isSelf ? 'message-profile-picture-self' : 'message-profile-picture' ]"
                 alt="profile"/>
        </a>
        <div class="message-content-container">
            <div v-if="isSelf" class="message-header" v-bind:class="{'self-message' : isSelf }">
                <span class="message-hour">{{ messageTime }}</span>
                <a :href="'/explorer/profile/' + message.created_by">
                    <span class="message-username" v-if="message.createdByUsername != ''">{{ message.createdByUsername }}</span>
                    <span class="message-username" v-else-if="message.contactEmail != ''">({{ message.contactEmail }})</span>
                    <span class="message-username" v-else>Kolab</span>
                </a>
            </div>
            <div v-else class="message-header">
                <a :href="'/explorer/profile/' + message.created_by">
                    <span class="message-username" v-if="message.createdByUsername != ''">{{ message.createdByUsername }}</span>
                    <span class="message-username" v-else-if="message.contactEmail != ''">({{ message.contactEmail }})</span>
                    <span class="message-username" v-else>Kolab</span>
                </a>
                <span class="message-hour">{{ messageTime }}</span>
            </div>

            <div v-for="file in message.files" class="message-body" v-if="message.type == 'user_media'" v-bind:class="{'self-message' : isSelf }" :style="{marginBottom: '20px'}">
                <a :href="file.url_view" class="drive-item" target="_blank" :style="{width: 'auto'}">
                    <div class="media-item-miniature" :style="{'display': 'flex', 'background-image': 'url(' + file.url_view + ')', backgroundPosition: 'center', backgroundSize:'cover', 'justify-content' : 'center', 'align-items': 'center'}">
                        <div :style="{'width': '30px', 'height' : '30px'}" v-if="!['gif', 'png', 'jpeg', 'jpg'].includes(file.extension)">
                            <span class="picto-medias"></span>
                        </div>
                    </div>
                        <div class="media-item-infos" :style="{backgroundColor: '#38266B', height: 'auto', width: '200px'}">
                            <p :style="{fontWeight: '600'}">{{ file.name.substring(0,15)+".." }}</p>
                            <p :style="{color: '#7665A7'}">{{ file.extension.toUpperCase() }}</p>
                            <a :href="file.url_download" target="_blank">
                                <div class="icon-container"><span class="picto-messenger-download"></span></div>
                            </a>
                    </div>
                </a>
            </div>

            <div class="message-body" v-if="message.driveLink != null" v-bind:class="{'self-message' : isSelf }">
                <a :href="message.driveLink.link" class="drive-item" target="_blank" :style="{width: 'auto'}">
                    <div class="drive-item-miniature" :style="(message && message.driveLink && message.driveLink.miniature) ? {'background-image': 'url(' + message.driveLink.miniature + ')', backgroundPosition: 'center', backgroundSize:'cover'} : { 'display': 'flex', 'justify-content' : 'center', 'align-items': 'center' }">
                        <div :style="{'width': '30px', 'height': '30px'}" v-if="message && message.driveLink && !message.driveLink.miniature">
                            <span class="picto-drive"></span>
                        </div>
                    </div>
                    <div class="drive-item-infos" :style="{backgroundColor: '#38266B', height: 'auto'}">
                        <span class="drive-item-name" :style="{ fontSize: '12px' }">{{ message.driveLink.name.substring(0,30)+".." }}</span>
                        <span class="drive-item-link" :style="{ fontSize: '12px' }">{{ message.driveLink.link.substring(0,30)+".." }}</span>
                     </div>
                </a>
            </div>
            <div class="message-body" v-else v-bind:class="{'self-message' : isSelf }">
                <p class="message-paragraph" v-show="message.message != '§'">
                    {{ message.message }}
                    <span v-if="message.type == 'user_drive_link' && message.id_drive_link == null"> ({{ $t('drive_deleted') }}) </span>
                </p>
            </div>

        </div>
        </div>`,
        props: ['messageType', 'message', 'mission', 'user', 'missionProposition','isDisabled'],
        data() {
            return {
                showInitial: false
            }
        },
        computed: {
            showButton() {
                return true;
            },
            messageTime() {
                let messageTime = new Date(this.message.created_at);
                let hours = messageTime.getHours();
                let minutes = (messageTime.getMinutes() < 10 ? '0' : '') + messageTime.getMinutes();
                return hours + ":" + minutes;
            },
            isSelf() {
                return this.message.createdByUsername === this.user.name;
            },
            /* isDisabled() {
                 switch (this.message.type) {
                     case 'user_quote':
                         return this.message.missionPropositionStatus !== 'waiting_payment';
                     case 'user_mission_proposal':
                         return this.message.missionPropositionStatus !== 'waiting_quote';
                     default:
                         return false;

                 }
             }*/
        },
        mounted() {
        },
        methods: {
            buttonHandler() {
                console.log("click");
            },
            getTalentInitials(talent) {
                let names = talent.split(' ')

                let res = ''

                names.forEach(element => res += element.charAt(0));

                return res;
            },
            handleImgError() {
                this.showInitial = true;
            }
        }
    });

    Vue.component('conversation-day-container', {
        template: `
        <div class="conversation-day-container">
        <span class="conversation-day-container-date">{{ formatedDate }}</span>
        <div v-for="message in messages">
            <conversation-message :message-type="message.type" :message="message" :is-disabled="isMessageDisabled(message)"
                :mission="mission" :user="user" :mission-proposition="missionProposition"/>
        </div>
        </div>
    `,
        props: ['date', "messages", "mission", "user", "missionProposition", 'conversation'],
        computed: {
            formatedDate() {
                let dateToFormat = new Date(this.date);
                let options = {dateStyle: 'full'};
                let dateString = new Intl.DateTimeFormat('fr-FR', options).format(dateToFormat);
                return dateString.charAt(0).toUpperCase() + dateString.slice(1);
            }
        },
        methods: {
            isMessageDisabled(message) {
                return this.conversation.messageToDisableDate > message.created_at;
            }
        }


    });

    Vue.component('bubble-drive-link', {
        template: `
        <a class="message-bubble-drive-link" :href="driveLink.link">
        <div class="link-miniature">

        </div>
        <div class="link-description">
            <span class="link-name">{{ driveLink.name }}</span>
            <span class="link-url">{{ driveLink.link }}</span>
        </div>
        </a>`,
        methods: {},
        mounted() {
        },
        props: ['driveLink'],
        data() {
            return {}
        }
    })
</script>
