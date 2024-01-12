<template>
    <div v-if="conversation !== null && drive !== null" class="explorer-messenger-card explorer-messenger-drive-card">
        <button type="button" @click="toggleCard" class="card-header">
            <div class="picto-container"><span class="picto-drive"/></div>
            <h1 class="card-title">{{driveLength ? driveLength + newDrives.length : driveLink.length}} {{$t('links')}}</h1><span v-show="newLink" class="notification"></span>
            <div class="card-dropdown-button">
                <span v-if="isCardOpen" class="picto-explorer-dropdown-up"/>
                <span v-else class="picto-explorer-dropdown-down"/>
            </div>
        </button>
        <div v-show="isCardOpen" class="card-body">
            <!--<div :class="{'d-m_c': proposition.proposition.status === 'canceled' && proposition !== undefined}" v-if="proposition.proposition.status === 'canceled' && proposition !== undefined">
                <div :class="{'d-m_c-l': proposition.proposition.status === 'canceled' }">
                    <div class="picto-messenger-lock"></div>
                </div>
                <div :class="{'text-center' : proposition.proposition.status === 'canceled'&& proposition !== undefined }" v-if="proposition.proposition.status === 'closed' || proposition.proposition.status === 'canceled'">
                    <p :class="{'d-m_c-p' : proposition.proposition.status === 'canceled' && proposition !== undefined}">{{$t('no-access')}}</p>
                 <p :class="{'d-m_c-p' : proposition.proposition.status === 'canceled' && proposition !== undefined}">à cette ressource</p>
                </div>
            </div>-->
            <div v-if="isEmpty && arraySplit.length === 0" class="empty-card">
                <span class="empty-text">{{ $t('no-links') }}</span>
            </div>
            <div v-else id="for-scroll" style="position: relative;height: 165px;overflow: auto;scroll-snap-type: y mandatory;">
                <button v-if="showButton" @click="handleButtonLeft" class="picto-explorer-selector-left drive-items-selector" style="left: 0;"/>
                <button v-if="showButton" @click="handleButtonRight" class="picto-explorer-selector-right drive-items-selector" style="right: 0;"/>

                <div v-for="newDriver in arraySplit" class="drive-items-container">
                        <a class="drive-item" v-for="driver in newDriver" :href="driver.link" target="_blank">
                            <span v-show="newLinkAdded(driver.id)" class="notification" style="top: -5px;position: absolute;right: -3px;"></span>
                            <div class="drive-item-miniature" :style="{'background-image': 'url(' + driver.miniature + ')', backgroundPosition: 'center', backgroundSize:'cover'}"></div>
                            <div class="drive-item-infos">
                                <span class="drive-item-name">{{ driver.name.substring(0,20) + '...'+ driver.name.substring(driver.name.length-20,driver.name.length)}}</span>
                                <span class="drive-item-link">{{ driver.link.substring(0,20) + '...'+ driver.link.substring(driver.link.length-20,driver.link.length) }}</span>
                            </div>
                        </a>

                </div>

                <div>
                    <span/>
                    <span/>
                    <span/>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user', 'drive','conversation'],
    data() {
        return {
            isCardOpen: false,
            currentDriveShow: 0,
            driveLink: null,
            isShow:false,
            newDrive:[],
            newDrives:[],
            currentLink:[],
            newLink:false
        }
    },
    created() {
        if (this.drive) {
            this.driveLink = this.drive;
            if (this.drive.driveLinks) {
                this.currentLink = this.drive.driveLinks.map(a => a.id);
            }
        }
    },

    mounted() {

        window.Echo.private('explorer').listen('.new-link', (e) => {
            this.getLinksUrl();
        });
        this.getLinksUrl()
    },
    computed: {
        isEmpty: function () {
            if (this.drive === null) {
                this.getLinksUrl()
                return true;
            }
            return this.drive.driveLinks.length <= 0;
        },
        driveLength(){
            if (this.drive) {
                this.driveLink = this.drive;
                if (this.drive.driveLinks) {
                    this.currentLink = this.drive.driveLinks.map(a => a.id);
                }
                return this.drive.driveLinks.length
            }
        },
        proposition(){
            if (this.conversation !== null){
                return this.conversation
            }
        },
        arraySplit(){
            let linksToShow = [];
            let both = 2
            let linkToShow = linksToShow.push(this.drive.driveLinks)
            let result = linksToShow[0].reduce((resultArray, item, index) => {
                const newIndex = Math.floor(index/both)
                if(!resultArray[newIndex]) {
                    resultArray[newIndex] = []
                }
                resultArray[newIndex].push(item)
                this.newDrive = resultArray
                return resultArray
            }, [])
            return result
        },
        showButton() {
            return this.drive.driveLinks.length > 2;
        }
    },
    methods: {

        getLinksUrl() {
            if (this.conversation !== null  && this.conversation !== undefined) {
                axios.get("/api/explorer/missions/conversations/" + this.conversation.proposition.drive.id_proposition + "/drive")
                    .then(res => {
                        let moreThanTwo = []
                        this.driveLink = res.data.driveLinks
                        let news = this.driveLink[this.driveLink.length - 1]
                        if(this.arraySplit.length === 0){
                            this.arraySplit.push([news])
                        } else if (this.arraySplit[this.arraySplit.length - 1].length === 2){
                            moreThanTwo = this.arraySplit.push([news])
                        }
                        else{
                            this.arraySplit[this.arraySplit.length - 1].push(news)
                        }
                        this.scrollDown()
                        if (this.driveLink){
                            this.newDrives = this.driveLink.map(a => a.id);
                        }
                        this.newDrives = this.newDrives.filter(x => !this.currentLink.includes(x));
                    }).catch(error => console.log(error));
            }
        },
        toggleCard() {
            this.$parent.$children[2].isCardOpen = false;
            this.$parent.$children[3].isCardOpen = false;
            this.isCardOpen = !this.isCardOpen;
        },
        scrollDown() {
            const scrollingElement = document.getElementById('for-scroll').lastElementChild;
            const config = { childList: true };
            const callback = function (mutationsList, observer) {
                for (let mutation of mutationsList) {
                    if (mutation.type === "childList") {
                        setTimeout(()=> {
                            scrollingElement.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"})
                        },1000)
                    }
                }
            };
            const observer = new MutationObserver(callback);
            observer.observe(scrollingElement, config);
            setTimeout(() => {
                scrollingElement.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"})
            }, 0)
        },
        handleButtonLeft() {
            let futureDriveShow = this.currentDriveShow - 1;
            if (futureDriveShow < 0) {
                futureDriveShow = this.drive.driveLinks.length - 1;
            }
            this.currentDriveShow = futureDriveShow;
        },

        handleButtonRight() {
            let futureDriveShow = this.currentDriveShow + 1;
            if (futureDriveShow > this.drive.driveLinks.length - 1) {
                futureDriveShow = this.drive.driveLinks.length - 2;
            }
            this.currentDriveShow = futureDriveShow;
        },
        newLinkAdded(linkUrl)
        {
            if (this.newDrives.length === 0) {
                return false;
            } else if (this.newDrives.includes(linkUrl)) {
                return true;
            }
        }
    },
}
</script>
