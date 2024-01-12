<template>
    <div class="modal-mask-responsive">
        <div class="modal-mask-wrapper"></div>
        <div class="modal-wrapper modal-mask-wrapper__wrapper" v-on:click="emitCloseModal" >
            <div class="module__explorer_project_detail" id="vue__explorer_project_detail">
                <div id="explorer_m-c" class="main-container row text-center" style="background: none;">
                    <div class="col-12">
                        <h1 style="margin: auto;">{{ project.name }}</h1>
                        <span class="project-date">{{ projectDate }}</span>
                        <p>
                            {{ project.description }}
                        </p>
                        <div class="project-main-media-container">
                            <button type="button" @click="toggleMedia('left')" class="media-selector">
                                <span style="width: 25px; height: 41px" class="picto-explorer-project-selector-left"/>
                            </button>
                            <div class="project-main-media" style="overflow: hidden;display: flex;width: 100%;margin:auto;justify-content: center;">
                                <explorer-youtube-player v-if="videoOrigin === 'youtube' && !showSelectedMedia" :video-url=" video_url"/>
                                <explorer-vimeo-player v-else-if="videoOrigin === 'vimeo' && !showSelectedMedia" :video-url=" video_url"/>
                                <img v-if="showSelectedMedia || video_url === null" class="hum" style="width:100%;object-fit: contain;height: 500px;" :src="selectedMedia.path" alt="Selected image from carousel">
                            </div>
                            <button type="button" @click="toggleMedia('right')" class="media-selector">
                                <span style="width: 25px; height: 41px" class="picto-explorer-project-selector-right"/>
                            </button>
                        </div>

                        <div class="project-carousel-container">
                            <div class="om" id="p-c_c" style="transform: translate3d(0px, 0px, 0px);">
                                <!--<explorer-mini-yt-player :class="{'activedd':selectedMedia === video_url && !showSelectedMedia}"  class="lost" v-if="videoOrigin === 'youtube' " :video-url="video_url"/>
                                <explorer-mini-vim-player :class="{'activedd':selectedMedia === video_url && !showSelectedMedia}" class="lost" v-else-if="videoOrigin === 'vimeo'" :video-url="video_url"/>-->
                                <!--<img v-if="project.video_url_image" class="project-carousel-item"  v-on:click="setSelectedMedia(project.video_url_image)" :src="project.video_url_image">-->
                                <div v-for="(media,index) in carousel" class="timeline" :id="'project-carousel-'+`${index}`" :class="{'activedd': selectedMedia.path === media.path && timeline}" :data-key="index">
                                <img class="project-carousel-item" :src="media.path" alt="Carousel slider item" :data-index="index"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['project'],
    data() {
        return {
            video_url: this.project.video_url,
            carousel:[],
            index:0,
            selectedMedia: this.project.video_url !== null ? this.project.video_url : this.project.medias[0],
            showSelectedMedia: false,
            prevIndex:0,
            projectDate:null,
            timeline:false,
            currentPos:null,
            prevPos:null,
            firstClick:false,
        }
    },
    computed: {
        videoOrigin() {
            if (this.video_url !== null) {
                if (this.video_url.search('youtu') !== -1) {
                    return 'youtube';
                } else if (this.video_url.search('vimeo') !== -1) {
                    return 'vimeo';
                } else {
                    return 'error';
                }
            }
        }
    },
    mounted() {
        if (this.videoOrigin === 'error') {
            this.showSelectedMedia = true;
        }
        if(this.video_url){
            this.carousel.unshift({path : this.project.video_url_image})
        }
        let idl = document.querySelector("#p-c_c")
        setTimeout(()=>{

            let container = document.querySelector('.project-carousel-container')
            let containerPortfolio = document.querySelector("#p-c_c")
            let init = container.getBoundingClientRect().left + (container.getBoundingClientRect().width / 2)
            let currentPos = 0;
            let prevPos = 0
            let item = document.querySelectorAll(".timeline")

            item.forEach((element,index)=>{
                let coef = (this.carousel.length > 1) ? this.carousel.length - 1 : this.carousel.length;
                this.currentPos = (this.carousel && this.carousel.length) ? coef * item[this.index].getBoundingClientRect().width/2 : false;
                prevPos =  item[index].getBoundingClientRect().left + (item[index].getBoundingClientRect().width/2)
                containerPortfolio.style.transform = `translate3d(${this.currentPos}px,0,0)`
                let dataIndex = element.dataset.index = index + 1

                /*element.addEventListener('click',(e)=>{
                    e.preventDefault()
                    prevPos =  item[this.index].getBoundingClientRect().left + (item[this.index].getBoundingClientRect().width/2)
                    if(!this.firstClick){
                        this.firstClick = true;
                        this.prevPos = prevPos
                        this.prevIndex = index == 0 ? 0 : index - 1;
                    } else {
                        this.prevIndex = this.index;
                        this.prevPos = item[this.prevIndex].getBoundingClientRect().left + (item[this.prevIndex].getBoundingClientRect().width/2)
                    }
                    this.index = index
                    if (this.carousel.length > 1) {
                        if(this.index > this.prevIndex) { // next
                            currentPos = this.currentPos - ((this.index - this.prevIndex) * item[this.index].getBoundingClientRect().width);
                        }else { // previous
                            currentPos = this.currentPos + (-(this.index - this.prevIndex) * item[this.index].getBoundingClientRect().width);
                        }
                        this.currentPos = currentPos;
                    }
                    containerPortfolio.style.transform ='translate3d('+currentPos+'px,0,0)'
                })*/
            })
            idl.firstChild.classList.add('activedd')
    },200)
        if(this.project.medias !== null)
        {
            this.project.medias.forEach(element=>this.carousel.push(element))
        }
        let date = new Date(this.project.created_at).toISOString().replace('T', ' ').replace('Z', '')
        this.projectDate = Vue.prototype.Global._reformatDate(date)
    },
    methods: {
        setSelectedMedia(media,index) {
            this.showSelectedMedia = true;
            if(this.showSelectedMedia)
            {
                this.timeline = true
                let newIndex = null;
                let idl = document.querySelector("#p-c_c")
                newIndex = index
                if(this.project.video_url !== null)
                {
                    if (this.selectedMedia.path === this.carousel[0].path) {
                        newIndex = 0
                    }
                    idl.firstChild.classList.remove('activedd')
                }
                this.selectedMedia = media;
                if(this.selectedMedia === this.carousel[0]){
                    this.showSelectedMedia = false;
                    this.selectedMedia = this.video_url
                    idl.firstChild.classList.add('activedd')
                }
            }
        },
        emitCloseModal(event) {
            if (event.target.className === "modal-wrapper modal-mask-wrapper__wrapper") {
                this.$emit('close-modal');
            }
        },
        toggleMedia(way) {
            let idl = document.querySelector("#p-c_c")
            this.showSelectedMedia = true
            let item = document.querySelectorAll(".timeline");
            let coef = (this.carousel.length > 1) ? this.carousel.length - 1 : this.carousel.length;
            let defaultPosition = (this.carousel && this.carousel.length) ? coef * 75 : false;
            if (this.showSelectedMedia) {
                let newIndex = this.carousel.findIndex(element => element === this.selectedMedia);
                if (newIndex == -1 || newIndex >= this.carousel.length) {
                    newIndex = 0
                }
                if (way === "right") {
                    newIndex++;
                    if(this.carousel.length > 1 )
                    {
                        idl.firstChild.classList.remove('activedd')
                    }
                    this.timeline = true
                    if (newIndex >= this.carousel.length) {
                        this.index = 0
                        this.selectedMedia = this.project.video_url
                        if(this.selectedMedia === this.video_url)
                        {
                            this.showSelectedMedia = false;
                        }
                        this.currentPos = defaultPosition;
                        idl.style.transform ='translate3d('+(this.currentPos) +'px,0,0)';
                    } else {
                        this.index = newIndex;
                        if (item[newIndex] && this.timeline) {
                            this.currentPos = this.currentPos - item[newIndex].getBoundingClientRect().width;
                            idl.style.transform ='translate3d('+(this.currentPos) +'px,0,0)';
                        }
                    }
                    this.selectedMedia = this.carousel[this.index]
                } else {
                    newIndex--;
                    this.timeline = true
                    if (newIndex < 0) {
                        this.index = this.carousel.length - 1
                        newIndex = this.carousel.length - 1
                        this.currentPos = defaultPosition
                        idl.firstChild.classList.remove('activedd')
                        idl.style.transform ='translate3d('+((this.currentPos - (newIndex * item[newIndex].getBoundingClientRect().width))) +'px,0,0)';
                        this.selectedMedia = this.carousel[this.index];
                    } else {
                        this.currentPos = defaultPosition - (newIndex * item[newIndex].getBoundingClientRect().width)
                        if (item[newIndex] && this.timeline) {
                            idl.style.transform ='translate3d('+ ((this.currentPos)) +'px,0,0)';
                            this.selectedMedia = this.carousel[newIndex];
                        }
                    }
                }
            } else {
                this.showSelectedMedia = true;
            }
        }
    },
}


Vue.component('explorer-youtube-player', {
    props: ['videoUrl'],
    data() {
    },
    computed: {
        embedUrl() {
            let youtubeId;
            if (this.videoUrl.search('embed/') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('embed/') + 6, this.videoUrl.length)
            } else if (this.videoUrl.search('v=') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('v=') + 2, this.videoUrl.length)
            } else if (this.videoUrl.search('youtu.be/') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('youtu.be/') + 9, this.videoUrl.length)
            } else {
                console.log('Youtube Video Link Error')
            }

            return 'https://www.youtube.com/embed/' + youtubeId
        }
    },
    methods: {},
    template: `
        <div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content: center;" class="hum">
        <iframe width="900px" height="500px" :src="embedUrl"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
    `
});

Vue.component('explorer-vimeo-player', {
    props: ['videoUrl'],
    data() {
    },
    computed: {
        embedUrl() {
            let vimeoId;

            if (this.videoUrl.search('vimeo.com/') !== -1) {
                vimeoId = this.videoUrl.slice(this.videoUrl.search('vimeo.com/') + 10, this.videoUrl.length)
            }

            return "https://player.vimeo.com/video/" + vimeoId + "?title=0&byline=0&portrait=0\""
        }
    },
    methods: {},
    template: `
        <div style="width: 100%;height: 100%" class="hum">
        <iframe
            :src="embedUrl"
            width="900px" height="500px" frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
        </div>
    `
});

Vue.component('explorer-mini-yt-player', {
    props: ['videoUrl'],
    data() {
    },
    computed: {
        async embedUrl() {
            let youtubeId;
            if (this.videoUrl.search('embed/') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('embed/') + 6, this.videoUrl.length)
            } else if (this.videoUrl.search('v=') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('v=') + 2, this.videoUrl.length)
            } else if (this.videoUrl.search('youtu.be/') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('youtu.be/') + 9, this.videoUrl.length)
            } else {
                console.log('Youtube Video Link Error')
            }

            return 'https://www.youtube.com/embed/' + youtubeId
        }
    },
    methods: {},
    template: `
        <div style="transform: scale(0.5);opacity: 0.4;">
        <iframe width="239.72px" height="134px" :src="embedUrl" style="z-index: 9;position: relative;"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
    `
});

Vue.component('explorer-mini-vim-player', {
    props: ['videoUrl'],
    data() {
    },
    computed: {
        async embedUrl() {
            let vimeoId;

            if (this.videoUrl.search('vimeo.com/') !== -1) {
                vimeoId = this.videoUrl.slice(this.videoUrl.search('vimeo.com/') + 10, this.videoUrl.length)
            }

            return "https://player.vimeo.com/video/" + vimeoId + "?title=0&byline=0&portrait=0\""
        }
    },
    methods: {},
    template: `
        <div style="transform: scale(0.5);opacity: 0.4;">
        <iframe style="z-index: 9;position: relative;"
            :src="embedUrl"
            width="239.72px" height="134px" frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
        </div>
    `
});
</script>
