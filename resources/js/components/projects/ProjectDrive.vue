<template>
    <div class="project-detail-modal explorer-messenger-card explorer-messenger-drive-card">
        <button type="button" @click="toggleCard" class="card-header">
            <div class="picto-container"><span class="picto-drive" /></div>
            <h1 class="card-title">Liens ({{ drive.driveLinks.length }})</h1><span v-show="newLink" class="notification"></span>
            <div class="card-dropdown-button">
                <span v-if="isShow" class="picto-explorer-dropdown-up" />
                <span v-else class="picto-explorer-dropdown-down" />
            </div>
        </button>
        <div v-show="isShow" class="card-body" style="padding-top: 5px;">
            <div v-if="isEmpty || drive === null" class="no-items" style="height:325px; margin-top: 10px;">
                <div class="text-center">
                    <p>Saisir une URL</p>
                    <span class="picto-drive-empty" style="margin-top: 10px;"></span>
                </div>
            </div>
            <div v-else style="position: relative">
                <!-- <button v-if="showButton" @click="handleButtonLeft" class="picto-explorer-selector-left drive-items-selector" style="left: 0;"/>
                <button v-if="showButton" @click="handleButtonRight" class="picto-explorer-selector-right drive-items-selector" style="right: 0;"/> -->

                <div class="drive-items-container row">
                    <div class="col-md-6" v-for="driveLink in drive.driveLinks"
                        v-on:mouseover="linkHover(driveLink.id, true, _isShared, _isClient, user, driveLink.created_by)"
                        v-on:mouseleave="linkHover(driveLink.id, false, _isShared, _isClient, user, driveLink.created_by)"
                        :style="{ }">
                        <span class="icon-action icon-delete" v-bind:id="'link-' + driveLink.id" v-on:click="Drive._delete([driveLink.id]);"></span>
                        <span class="notification row-notification" v-show="showRowNotification(driveLink.id)"></span>
                        <a :href="driveLink.link" class="drive-item" target="_blank">
                            <div class="drive-item-miniature"
                                :style="(driveLink.miniature) ? { backgroundImage: `url(${driveLink.miniature})`, backgroundPosition: 'center', backgroundSize: 'cover' } : {}">
                                <div style="width: 30px; height: 30px;" v-show="!driveLink.miniature">
                                    <div class="picto-drive"></div>
                                </div>
                            </div>
                            <div class="drive-item-infos">
                                <span class="drive-item-name">{{ driveLink.name }}</span>
                                <span class="drive-item-link">{{ driveLink.link }}</span>
                            </div>
                        </a>
                    </div>
                </div>
                <div>
                    <span />
                    <span />
                    <span />
                </div>
            </div>
            <div class="project-messenger-conversation">
                <div class="conversation-footer">
                    <div class="conversation-input-container">
                        <input type="text" v-model="driveToPost" @keyup.enter="postDrive" :style="textAreaStyle"
                            ref="messageInput" class="conversation-input-text"
                            placeholder="Ajouter un lien URL (WeTransfer, Smash, Vimeo)" />
                        <button @click="postDrive" class="drive-input-button">
                            <span class="icon icon-plus"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user', '_drive', '_conversation', '_isShared', '_isClient'],
    data() {
        return {
            isShow: false,
            currentDriveShow: 0,
            driveToPost: "",
            textAreaHeight: '20px',
            conversation: null,
            drive: null,
            newLink: false,
            currentLinkIds: [],
            newLinkIds: []
        }
    },
    mounted() {
        this.$bus.$on('UPDATE_DRIVE', () => {
            //this.drive = drive; // Call projects with filters
            this.getDrives();
        });

        window.Echo.private('kolab').listen('.drive-deleted', (e) => {
            this.getDrives();
            this.newLink = true;
        });
    },
    created() {
        if (this._conversation) {
            this.conversation = this._conversation;
        }
        if (this._drive) {
            this.drive = this._drive;
            if (this.drive.driveLinks) {
                this.currentLinkIds = this.drive.driveLinks.map(a => a.id);
            }
        }
    },
    computed: {
        isEmpty: function () {
            if (this.drive === null) {
                return true;
            }
            return this.drive.driveLinks.length <= 0;
        },
        showButton() {
            return this.drive.driveLinks.length > 2;
        },
        textAreaStyle() {
            return {
                'height': this.textAreaHeight
            }
        }
    },
    methods: {
        toggleCard() {
            this.$parent.$children[4].isShow = false;
            this.$parent.$children[5].isShow = false;
            this.$parent.$children[6].isShow = false;
            this.isShow = !this.isShow;
            if (this.newLink) {
                this.newLink = !this.newLink;
            }
            //this.newLink = false;
        },
        handleButtonLeft() {
            let futureDriveShow = this.currentDriveShow - 1;
            if (futureDriveShow < 0) {
                futureDriveShow = this.drive.driveLinks.length - 2;
            }
            this.currentDriveShow = futureDriveShow;
        },
        handleButtonRight() {
            let futureDriveShow = this.currentDriveShow + 1;
            if (futureDriveShow > this.drive.driveLinks.length - 1) {
                futureDriveShow = this.drive.driveLinks.length = 0;
            }
            this.currentDriveShow = futureDriveShow;
        },
        postDrive() {
            let contactId = localStorage.getItem('shared_project_contact_id');
            axios.post("/api/explorer/missions/conversations/" + this.conversation.id + "/postDrive", {
                params: {
                    message: this.driveToPost,
                    contactId: contactId ? contactId : false,
                    isShared: this._isShared
                }
            }).then(res => {
                if (res.data == 'no_link') {
                    console.log('pas de lien');
                } else {
                    this.getDrives();
                    this.driveToPost = '';
                    this.$bus.$emit('ACTION_CHANGED', {
                        action: 'CONGRATS',
                        type: 'ADD_DRIVE'
                    });
                    this.newLink = true;
                    var objDiv = document.getElementById("project-drive-items-container");
                    objDiv.scrollTop = objDiv.scrollHeight;
                }
            }).catch(error => console.log(error))
        },
        resizeTextarea() {
            this.textAreaHeight = `${this.$refs.messageInput.scrollHeight}px`
        },
        async getDrives() {
            await axios.get('/api/project/' + this.conversation.proposition.kolab_project_id)
                .then(res => {
                    if (typeof res.data.datas.proposition.drive !== "undefined") {
                        this.drive = res.data.datas.proposition.drive;
                        if (this.drive.driveLinks) {
                            this.newLinkIds = this.drive.driveLinks.map(a => a.id);
                        }
                        this.newLinkIds = this.newLinkIds.filter(x => !this.currentLinkIds.includes(x));
                    } else {
                        //error
                    }
                }).catch(error => console.log(error));
        },
        linkHover: function (linkId, active, isShared, isClient, user, createdById) {
            let email = false;
            let owner = false;
            let contactId = false;
            if (isShared && localStorage.getItem('shared_project_email') && localStorage.getItem('shared_project_contact_id')) {
                email = localStorage.getItem('shared_project_email');
                contactId = localStorage.getItem('shared_project_contact_id');
            }
            var linkElement = document.getElementById('link-' + linkId);
            if ((active && !isShared && !isClient) || (active && isClient && user.id == createdById) || (active && isShared && parseInt(contactId) == createdById)) {
                linkElement.classList.add('icon-hover');
            } else {
                linkElement.classList.remove('icon-hover');
            }
        },
        showRowNotification(driveLinkId) {
            if (this.newLinkIds.length == 0) {
                return false;
            } else if (this.newLinkIds.includes(driveLinkId)) {
                return true;
            }

            return false;
        }
    },
}

</script>
