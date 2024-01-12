<template>
    <div class="explorer-profile-projects-list">
        <div v-if="showAddProjectInList" class="project-empty" v-on:click="portfolioAddModalToggle">
            <span>Ajouter un projet</span>
        </div>
        <div v-for="(project,index) in portfolios" v-on:mouseover="isSelf ? show(project.id,true) : ''" v-on:mouseleave="isSelf ? show(project.id,false): ''" class="project"
             v-on:click="portfolioProjectDetailOpen(project)"
             v-bind:style="project.video_url_image == null ? { background:'linear-gradient(rgba(0, 0, 0, 0.35), rgb(0, 0, 0)), url(' + project.mainMedia.path  + ')',backgroundRepeat: 'no-repeat',backgroundSize: 'cover' } : { background:'linear-gradient(rgba(0, 0, 0, 0.35), rgb(0, 0, 0)), url(' + project.video_url_image + ')',backgroundRepeat: 'no-repeat',backgroundSize: 'cover', backgroundPosition:'center' } ">
            <span>{{ project.name }}</span>
            <div v-show="isSelf" v-bind:id="'edit-portfolio' + project.id" class="p-actions">
                <button v-bind:id="'portfolio-favicon_delete-' + project.id">
                    <span class="picto-explorer-delete-white" style="" v-on:click="Portfolios._delete([project.id])"></span>
                </button>
            </div>
        </div>

        <explorer-profile-portfolio-add v-if="showAddPortfolioModal" v-on:close-modal="portfolioAddModalToggle"/>
        <explorer-profile-project-detail v-if="showProjectDetailModal" :project="selectedProjectDetail"
                                         v-on:close-modal="portfolioProjectDetailClose"/>
    </div>
</template>
<script>
export default {
    props: ['user', 'isSelf'],

    data() {
        return {
            portfolios: [],
            currentPage: null,
            showAddPortfolioModal: false,
            showProjectDetailModal: false,
            selectedProjectDetail: null,
            newPortfolioIds:[],
            currentPortfolioIds:[]
        }
    },
    created() {
        if (this.currentPage == null) {
            this.currentPage = "projects";
        }
        if (this.portfolios) {
            this.currentPortfolioIds = this.receiveNbProjectsToParent.map(a => a.id);
        }
    },
    mounted() {
        this.emitNbProjectsToParent();
        this.$bus.$on('deleteProject',()=>{
            this.getPortfolios()
        })
        window.Echo.private('explorer').listen('.portfolio-added',(e)=> {
            this.getPortfolios()
        })
    },
    computed: {
        showAddProjectInList() {
            return this.isSelf //&& this.user.portfolios.length <= 0;
        },
        receiveNbProjectsToParent() {
            this.portfolios = this.user.portfolios
            return this.portfolios;
        },
    },
    methods: {
        show (portfolioId,active)
        {
            let edit = document.getElementById('portfolio-favicon_edit-' + portfolioId);
            let del = document.getElementById('edit-portfolio' + portfolioId);
            if (active) {
                del.classList.add('p-d-hover')
            } else {
                del.classList.remove('p-d-hover')
            }
        },
        setCurrentPage(current) {
            this.currentPage = current;
        },
        emitNbProjectsToParent() {
            this.$emit('updateProjects', this.portfolios.length)
        },
        portfolioAddModalToggle() {
            this.showAddPortfolioModal = !this.showAddPortfolioModal;
        },
        portfolioProjectDetailOpen(project) {
            if(event.target.className !== 'picto-explorer-delete-white')
            {
                this.selectedProjectDetail = project;
                this.showProjectDetailModal = true;
                $('body').toggleClass('body-modal');
            }
        },
        portfolioProjectDetailClose() {
            this.showProjectDetailModal = false;
            $('body').toggleClass('body-modal');
        },
        getPortfolios()
        {
            axios.get("/api/explorer/portfolios",{
                headers: {
                    'Content-Type': 'multipart/form-data',
                }}).then(res=>{
                if (typeof res.data !== "undefined") {
                    this.portfolios = res.data;
                    if (this.portfolios) {
                        this.newPortfolioIds = this.portfolios.map(a => a.id);
                    }
                    this.newPortfolioIds = this.newPortfolioIds.filter(x => !this.currentPortfolioIds.includes(x));
                } else {

                }
            }).catch(error=>console.log(error))
        },
        updateList(){
            axios.get("/api/explorer/portfolios",{
                headers: {
                    'Content-Type': 'multipart/form-data',
                }}).then(res=>{
                this.receiveNbProjectsToParent = res.data
                //this.$bus.$on('deleteProject',this.portfolios = res.data)
            }).catch(error=>console.log(error))
        }

    },
}
</script>
