<template>
    <div class="modal-mask-responsive">
        <div class="modal-mask-wrapper"></div>
        <div class="modal-wrapper modal-mask-wrapper__wrapper" v-on:click="emitCloseModal" >
            <div class="explorer-portfolio-add-modal">
                <div class="explorer-form-modal e-f-m">
                    <div class="modal-header text-center">
                        <h1 class="modal-title">Ajouter un projet</h1>
                        <p>Soignez leur présentation, les projets publiés peuvent être mis en avant par nos certificateurs.</p>
                    </div>

                    <form method="post" @submit.prevent="postPortfolio" class="explorer-form portfolio-form e-p-f" enctype="multipart/form-data">
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>Titre de votre projet*</h2>
                            </div>
                            <div class="form-section-content">
                                <input @keydown="formErrorMessage=''" class="portfolio-title" v-model="portfolio.name" type="text" placeholder="Titre de votre projet (Ex : Spot TV Saint Laurent)" name="portfolio_name" style="margin-bottom: 0px;"/>
                                <p v-if="formErrorMessage !== null" style="color: red;padding-left: 10px;text-align: left;margin-top: 6px;">{{ formErrorMessage }}</p>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-mission-detail-white"></span>
                                </div>
                                <h2>Ajouter du contenu*</h2><span style="margin-left: 10px;color: #585266;">Ajouter au moins une vidéo ou/et une image</span>
                            </div>
                            <div class="form-section-content">
                                <div class="form-media-type-selectors">
                                    <div class="container-p">
                                        <button  @click="toggleVideoLink" type="button" class="form-media-type-selector portfolio-media" style="margin: 0 5px 0 0;">
                                            <span class="picto-explorer-video"
                                                  style="height: 28px; margin-bottom: 10px"></span>

                                            <span>
                                                Ajouter<br>
                                                une vidéo
                                            </span>
                                        </button>
                                    </div>
                                    <div class="container-p">
                                        <button  @click="$refs.file.click()" type="button" class="form-media-type-selector portfolio-img" :class="[medias.length > 5 || img_media !== null ? 'explorer-is_i': '']" style="margin: 0 0 0 5px;">
                                            <span class="picto-explorer-img"
                                                  style="height: 25px; margin-bottom: 10px"></span>
                                            <span>
                                            Ajouter une image<br>
                                            {{ addImg && medias.length < 5 ? 5 - medias.length : 0 }} images restantes
                                            <input id="p-i" class="portfolio-img" type="file" ref="file" name="portfolio_medias[]" multiple="multiple" @change="handleFileUploads( $event )" style="display: none">
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <p class="error-portfolio" v-if="img_media" style="color: red;text-align: center;margin-bottom: 1rem;">{{ img_media }}</p>

                                <input class="portfolio-media-i" v-if="addVideo" v-model="portfolio.video_url" type="text" placeholder="Lien de votre video" name="portfolio_video_url" style="margin: 0;"/>
                                <p v-if="videoUrl !== null && addVideo" style="color: red;text-align: center;margin-top: 6px;">{{ videoUrl }}</p>
                                <div v-if="medias.length > 0" class="form-sended-medias-container" :class="[medias.length > 5 ? 'field-error' : '']">
                                    <div v-for="(media, index) in medias" :key="index" class="form-sended-media">
                                        <p>{{media.name }}<span>({{ niceBytes(media.size) }})</span></p>
                                        <button v-on:click="handleRemoveFile(index)" type="button">X</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>Description du projet</h2>
                            </div>
                            <div class="form-section-content">
                                <textarea class="p-a" v-model="portfolio.description" placeholder="Décrivez en quelques lignes votre projet ..." name="portfolio_description"/>
                            </div>
                        </div>
                        <button class="form-validation-btn" type="submit" v-if="!isSended">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            portfolio: {
                name: '',
                video_url: '',
                description: '',
                video_url_image:''
            },
            errors:null,
            portfolios:[],
            isSended: false,
            medias: [],
            addVideo: false,
            formErrorMessage: null,
            img_media:null,
            videoUrl:null,
            addImg:true
        }
    },
    created() {

    },
    mounted() {

    },
    methods: {
        emitCloseModal() {
            if (event.target.className === "modal-wrapper modal-mask-wrapper__wrapper") {
                this.$emit('close-modal');
            }
        },
        postPortfolio() {

            let fd = new FormData();

            fd.append('portfolio_name', this.portfolio.name);
            fd.append('portfolio_video_url', this.portfolio.video_url);
            fd.append('portfolio_video_image',this.portfolio.video_url_image)
            fd.append('portfolio_description', this.portfolio.description);
            for (const i of Object.keys(this.medias)) {
                fd.append('portfolio_medias[]', this.medias[i]);
            }
            axios.post('/api/explorer/portfolios', fd, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            }).then(res => {

                if (res.success === false) {
                    // Error
                } else {
                    this.isSended = true;
                    this.$emit('close-modal');
                    window.Echo.private('explorer').listen('.portfolio-added', (e) => {
                        this.getPortfolios()
                    });
                    //this.$bus.$emit('updateProjects',()=>this.getPortfolios())
                }
            }).catch(error => this.errors = this.handleError(error));
        },
        getPortfolios()
        {
            axios.get("/api/explorer/portfolios",{
                headers: {
                    'Content-Type': 'multipart/form-data',
                }}).then(res=>{
                this.portfolios = res.data
                //this.$bus.$emit('updateProjects', this.portfolios);
            }).catch(error=>console.log(error))
        },
        handleError(error) {
            let response = error.response;
            let media = document.querySelector(".portfolio-media")
            let image = document.querySelector(".portfolio-img")
            let title = document.querySelector(".portfolio-title")
            let mediaInput = document.querySelector(".portfolio-media-i")
            let container = document.getElementsByClassName("form-sended-medias-container")
            if(error) {
                if (response.status === 400) {
                    if (typeof response.data.portfolio_name !== 'undefined') {
                        if (response.data.portfolio_name.length > 0) {
                            this.formErrorMessage = response.data.portfolio_name[0];
                            title.classList.add("is-invalid")
                            title.classList.add("field-error")
                        }
                    }else{
                        title.classList.remove("is-invalid")
                        title.classList.remove("field-error")
                    }
                    if (typeof response.data.portfolio_video_url !== 'undefined') {
                        if (response.data.portfolio_video_url.length > 0) {
                            this.videoUrl = response.data.portfolio_video_url[0];
                            media.classList.add("explorer-is_i")
                            media.classList.add("is-invalid")
                            mediaInput.classList.add("explorer-is_i")
                            mediaInput.classList.add("field-error")
                        } else {
                            this.videoUrl = ""
                            media.classList.remove("explorer-is_i")
                            mediaInput.classList.remove("is-invalid")
                            mediaInput.classList.remove("is-invalid")
                            media.classList.remove("field-error")
                        }
                    }
                    if (typeof response.data.portfolio_medias !== 'undefined') {
                        if (response.data.portfolio_medias.length < 2) {
                            this.img_media = response.data.portfolio_medias[0];
                            image.classList.add("explorer-is_i")
                            image.classList.add("explorer-is_i")
                        } else {
                            image.classList.add("explorer-is_i")
                            image.classList.add("explorer-is_i")
                            this.img_media = response.data.portfolio_medias[1];
                        }
                    }else{
                        image.classList.remove("explorer-is_i")
                        image.classList.remove("explorer-is_i")
                    }
                }
                if (response.status === 500) {
                    this.errorMessage = "Une erreur est survenue, veuillez nous contacter";
                }
            }
        },
        handleFileUploads(response) {
            let image = document.querySelector(".portfolio-img")
            const arr = event.target.files
            if(this.medias.length <= 5 || arr.length > 6)
            {
                Object.keys(arr).forEach(el =>{
                    if(this.medias.length < 6)
                    {
                        this.medias.push(arr[el])
                    }
                });
                let fd = new FormData();
                fd.append('portfolio_name', this.portfolio.name);
                fd.append('portfolio_video_image',this.portfolio.video_url_image)
                for (const i of Object.keys(this.medias)) {
                    fd.append('portfolio_medias[]', this.medias[i]);
                }
                axios.post('/api/explorer/portfolios', fd, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }).then(res => {
                }).catch(error => this.handleError(error));
            }else{
                this.addImg = false
            }
        },
        handleRemoveFile(index) {
            if (index > -1) {
                this.medias.splice(index, 1);
                if(this.medias.length === 5){
                    document.querySelector('.error-portfolio').style.display = 'none'
                }
                let fd = new FormData();
                fd.append('portfolio_name', this.portfolio.name);
                fd.append('portfolio_video_image',this.portfolio.video_url_image)
                for (const i of Object.keys(this.medias)) {
                    fd.append('portfolio_medias[]', this.medias[i]);
                }
                axios.post('/api/explorer/portfolios', fd, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }).then(res => {

                    if (res.success === false) {
                        // Error
                    } else {

                    }
                }).catch(error => this.handleError(error));
            }
        },
        toggleVideoLink() {
            this.addVideo = !this.addVideo;
        },
        niceBytes(x) {
            const units = ['octets', 'Ko', 'Mo', 'Go', 'To'];
            let l = 0, n = parseInt(x, 10) || 0;
            while (n >= 1024 && ++l) {
                n = n / 1024;
            }
            return (n.toFixed(n < 10 && l > 0 ? 1 : 0) + ' ' + units[l]);
        },
        checkSendFormError() {
            if (this.medias.length >= 5) {
                this.formErrorMessage = "Veuillez ajouter au moins une vidéo ou une image";
                return false;
            }
            this.formErrorMessage = null
            return true;
        },
    },
}
</script>
