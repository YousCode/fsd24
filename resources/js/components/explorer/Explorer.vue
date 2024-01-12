<template>
    <div class="page_explorer" id="vue__explorer">
        <div class="main-container-explorer" style="background: none;">
            <div
                v-if="isFreelance"
                class="explorer-highlight-project"
                :style="{
                    backgroundImage:
                        'linear-gradient( to left,rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1) ),url(' +
                        highlightProject.mainMedia.path +
                        ')',backgroundSize:'cover',backgroundRepeat:'no-repeat',backgroundPosition:'center'
                }"
            >
                <h1>
                    {{$t('explorer-home-title')}} <br />
                    {{$t('explorer-home-title-1')}} <br />
                   <span>{{$t('explorer-home-title-2')}}</span>
                </h1>
                <div class="highlight-infos">
                    <span class="highlight-talent">{{
                        highlightProject.userName
                    }}</span>
                    <span class="highlight-project">{{
                        highlightProject.name
                    }}</span>
                </div>
            </div>
            <div
                v-else
                class="explorer-highlight-project"
                :style="{
                    backgroundImage:
                        'linear-gradient( to left,rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1) ),url(' +
                        highlightProject.mainMedia.path +
                        ')',backgroundSize:'cover',backgroundRepeat:'no-repeat',backgroundPosition:'center'
                }"
            >
                <h1>
                    {{$t('explorer-home-client-title-second')}} <br />
                    <span>{{$t('explorer-home-client-title-second-1')}}</span>
                    {{$t('explorer-home-client-title-second-2')}}
                </h1>

                <div class="highlight-infos">
                    <span class="highlight-talent">{{
                        highlightProject.userName
                    }}</span>
                    <span class="highlight-project">{{
                        highlightProject.name
                    }}</span>
                </div>
            </div>
            <div class="explorer-how-to">
                <div></div>
                <h1>
                    {{$t('explorer-steps-title')}}<br />
                    {{$t('explorer-steps-title-1')}}
                </h1>

                <div class="step-container">
                    <div class="explorer-how-to-step" v-if="isFreelance">
                        <div class="step-icon-container">
                            <span class="picto-propose" />
                        </div>
                        <p>{{$t('explorer-freelance-steps-step-1')}}</p>
                    </div>

                    <div class="explorer-how-to-step" v-else>
                        <div class="step-icon-container">
                            <span class="picto-propose" />
                        </div>
                        <p>{{$t('explorer-client-steps-step-1')}}</p>
                    </div>

                    <span class="line dotted" />

                    <div
                        class="explorer-how-to-step"
                        style="width: 196px; "
                        v-if="isFreelance"
                    >
                        <div class="step-icon-container">
                            <span class="picto-paid" />
                        </div>
                        <p>
                            {{$t('explorer-freelance-steps-step-2')}}<br />
                        </p>
                    </div>
                    <div
                        class="explorer-how-to-step"
                        style="width: 155px; "
                        v-else
                    >
                        <div class="step-icon-container">
                            <span class="picto-paid" />
                        </div>
                        <p>{{$t('explorer-client-steps-step-2')}}</p>
                    </div>

                    <span class="line dotted" />

                    <div
                        class="explorer-how-to-step"
                        style="width: 244px;"
                        v-if="isFreelance"
                    >
                        <div class="step-icon-container">
                            <span class="picto-sent" />
                        </div>
                        <p>
                            {{$t('explorer-freelance-steps-step-3')}}
                        </p>
                    </div>

                    <div
                        class="explorer-how-to-step"
                        style="width: 244px; "
                        v-else
                    >
                        <div class="step-icon-container">
                            <span class="picto-explorer-home-shield" />
                        </div>
                        <p>
                            {{$t('explorer-client-steps-step-3')}}
                        </p>
                    </div>

                    <span class="arrow dotted" />

                    <div
                        class="explorer-how-to-step"
                        style="width: 155px;"
                        v-if="isFreelance"
                    >
                        <div class="step-icon-container">
                            <span class="picto-validated" />
                        </div>
                        <p v-if="isFreelance">
                            {{ $t('explorer-freelance-steps-step-4') }}
                        </p>
                    </div>

                    <div
                        class="explorer-how-to-step"
                        style="width: 196px; "
                        v-else
                    >
                        <div class="step-icon-container">
                            <span class="picto-validated" />
                        </div>
                        <p>
                            {{$t('explorer-client-steps-step-4')}}
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="isFreelance" class="explorer-talents">
                <explorer-projects-list :user="user"/>
            </div>

            <div v-else class="explorer-talents">
                <explorer-talents-list />
            </div>
        </div>
    </div>
</template>
<script>
import talents from "../talents/Talents";

export default {
    props: ["user", "user_role"],

    data() {
        return {
            highlightProject: {
                mainMedia: { path: "images/temp-img/explorer-white_home.webp" },
                userName: "LACOSTE",
                name: "Sneakers L003"
            }
        };
    },

    mounted() {
        $("body").toggleClass("theme-explorer");
    },

    methods: {},
    computed: {
        isFreelance: function() {
            return this.user.role[0]["name"] === "talent";
        }
    }
};

/**
 * Dashboard components
 */

Vue.component("explorer-projects-list", {
    template: `
        <div class="explorer-talents l-p_e">
        <div class="projects-list-title">
            <span class="picto-explorer-fire" style="height: 22px; width: 16px; margin-right: 18px;"/>
            <h1>{{$t('project-this-moment')}}</h1>
        </div>
        <span class="separator"/>

        <div class="explorer-talents-list l-e_p">
            <div v-for="project in portfolios" class="explorer-talent-card" :style="project.video_url_image === null || project === null ? { background:'linear-gradient(rgba(0, 0, 0, 0.35), rgb(0, 0, 0)), url(' + project.mainMedia.path  + ')',backgroundRepeat: 'no-repeat',backgroundSize: 'cover',backgroundPosition: 'center' } : { background:'linear-gradient(rgba(0, 0, 0, 0.35), rgb(0, 0, 0)), url(' + project.video_url_image + ')',backgroundRepeat: 'no-repeat',backgroundSize: 'cover', backgroundPosition:'center' } " style="background-size: cover;" @click="showPortfolio(project)">
                <div class="card-infos">
                    <div class="t-i">
                        <img :src="'/upload/avatars/'+ project.avatar"/>
                    </div>
                    <div class="c-i-f">
                        <span class="talent-name" style="display: flex;align-items: center;">{{ project.userName }}<img v-if="project.certified" style="max-width: 1.5rem;width: 100%;height: auto;margin-left: 7px;" src="images/explorer/checkmark.png"/></span>
                        <span class="talent-job">{{ project.name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <explorer-profile-project-detail v-if="showPortfolioDetail" :project="portfolioToShow" v-on:close-modal="closePortfolioDetailModal"/>
        </div>`,
    props:["user"],
    data: function() {
        return {
            all_talents:[],
            talents:[],
            portfolios: [],
            portfolioToShow: null,
            showPortfolioDetail: false,
            page:1
        };
    },
    mounted(){
        this.getProjects()
        this.getTalents(true)
        let search = document.querySelector(".explorer-talents-searchbar")
        window.addEventListener('mousewheel',()=>{
            if(search.getBoundingClientRect() <= 0){
                search.classList.add('sticky')
            }else{
                search.classList.remove('sticky')
            }
        })
        window.Echo.private('explorer').listen('.new-pp',(e)=> {
            this.getProjects()
        })
        window.Echo.private('explorer').listen('.portfolio-added',(e)=> {
            this.getProjects()
        })
    },
    methods: {
        handleLoadMore() {
            //adding the other explorer user
            if(this.portfolios)
            {
                this.page++
                let params = {isHome: true};
                if(this.nextPage !== null )
                {
                    axios
                        .get("/api/explorer/portfolios", {
                            params: params
                        })
                        .then(res => {
                            if (res.success === false) {
                                // Todo : Error
                            } else {
                                this.portfolios = res.data;
                                setTimeout(()=>{
                                    if(this.portfolios)
                                    {
                                        const elements = document.querySelectorAll(".explorer-talent-card")
                                        const observer = new IntersectionObserver(
                                            entries => {
                                                entries.forEach(entry => {
                                                    entry.target.classList.toggle('show',entry.isIntersecting)
                                                    if(entry.isIntersecting) observer.unobserve(entry.target)
                                                })
                                            },
                                            {
                                                threshold:0.1,
                                            }
                                        )
                                        elements.forEach(element=>{
                                            observer.observe(element)
                                        })
                                    }
                                },500)
                            }
                        })
                        .catch(error => console.log(error));
                }else{
                    axios.get(`${this.prevPage}`,{
                        params: params
                    }).then(res => {
                        if(res){
                            if (res.data.datas !== null){
                                res.data.datas.data.forEach(element=>this.talents.push(element))
                            }
                        }
                    });
                }
            }
        },
        /**
         * getTalents
         * @return void
         */
        /*getTalents(all) {
            let params = {};

            setTimeout(async () => {
                await axios.get("/api/talent", {})
                    .then(res => {
                        if (res.data.success === false) {
                            // Todo : Error
                        } else {
                            this.talents = res.data.datas; // Update talents list
                            if (all){
                                this.all_talents = res.data.datas;
                            }
                            this.all_talents = res.data.datas // Set all talents for first time
                        }
                    })
                    .catch(error => console.log(error));
            }, 10);
        },*/
        getProjects() {
            let params = {isHome: true};

            setTimeout(() => {
                axios
                    .get("/api/explorer/portfolios", {
                        params: params
                    })
                    .then(res => {
                        if (res.success === false) {
                            // Todo : Error
                        } else {
                            this.portfolios = res.data;
                            setTimeout(()=>{
                                if(this.portfolios)
                                {
                                    const elements = document.querySelectorAll(".explorer-talent-card")
                                    const observer = new IntersectionObserver(
                                        entries => {
                                            entries.forEach(entry => {
                                                entry.target.classList.toggle('show',entry.isIntersecting)
                                                if(entry.isIntersecting) observer.unobserve(entry.target)
                                            })
                                        },
                                        {
                                            threshold:0.1,
                                        }
                                    )
                                    elements.forEach(element=>{
                                        observer.observe(element)
                                    })
                                }
                            },500)
                        }
                    })
                    .catch(error => console.log(error));
            }, 10);

        },
        showPortfolio(portfolio) {
            $('body').toggleClass('body-modal');
            this.portfolioToShow = portfolio;
            this.showPortfolioDetail = true;
        },
        closePortfolioDetailModal() {
            $('body').toggleClass('body-modal');
            this.showPortfolioDetail = false;
        }
    }
});

Vue.component("explorer-talents-list", {
    template: `
        <div class="explorer-talents l-e_h">
        <div id="loader" class ="l-e" v-show="!isLoaded">
            <div class="l_e">
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
        <div class="explorer-talents-searchbar">
            <div class="talents-search">
                <span class="picto picto-explorer-search"/>
                <input v-on:input="getTalents" v-model="filters.name" type="text" :placeholder="$t('search-explorer')"/>
            </div>
            <div class="talents-search talents-filters">
                <span class="filter-text">{{$t('filter-explorer')}}</span>

                <div class="filters-container">
                    <div class="filter-container">
                        <div class="filter-item filter-select has-picto">
                            <span class="filter-picto picto-explorer-filter-work"></span>
                            <select @click="getTalents" v-select2 v-model="filters.jobs" class="filter-field js-jobs-data"
                                    name="jobs[]" multiple="multiple"></select>
                        </div>
                    </div>
                    <div class="filter-container">
                        <div class="filter-item filter-select has-picto">
                            <span class="filter-picto picto-explorer-filter-competence"></span>
                            <select @click="getTalents" v-select2 v-model="filters.skills" class="filter-field js-skills-datas"
                                    name="skills[]" multiple="multiple"></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="explorer-talents-searchbar_phant">
            <div class="talents-search">
                <span class="picto picto-explorer-search"/>
                <input v-on:input="getTalents" v-model="filters.name" type="text" :placeholder="$t('search-explorer')"/>
            </div>
            <div class="talents-search talents-filters">
                <span class="filter-text">{{$t('filter-explorer')}}</span>

                <div class="filters-container">
                    <div class="filter-container">
                        <div class="filter-item filter-select has-picto">
                            <span class="filter-picto picto-explorer-filter-work"></span>
                            <select @click="getTalents" v-select2 v-model="filters.jobs" class="filter-field js-jobs-data"
                                    name="jobs[]"></select>
                        </div>
                    </div>
                    <div class="filter-container">
                        <div class="filter-item filter-select has-picto">
                            <span class="filter-picto picto-explorer-filter-competence"></span>
                            <select @click="getTalents" v-select2 v-model="filters.skills" class="filter-field js-skills-datas"
                                    name="skills[]" multiple="multiple"></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="talents.length === 0 " class="explorer-talents-list" style="display: flex; align-items: center; justify-content: center">
            <h1>Aucun résultat</h1>
        </div>
        <div v-else-if="talents.lastPortfolioMainMedia !== null" class="explorer-talents-list">
            <a v-for="talent in talents" class="explorer-talent-card" :href="'/explorer/profile/'+talent.id" :style="(talent && talent.lastPortfolioMainMedia && (talent.lastPortfolioMainMedia.video_url_image && talent.lastPortfolioMainMedia.video_url_image !== null)) ? { background:'linear-gradient(rgba(0, 0, 0, 0.35), rgb(0, 0, 0)), url(' + talent.lastPortfolioMainMedia.video_url_image + ')',backgroundRepeat: 'no-repeat',backgroundSize: 'cover', backgroundPosition:'center' } : (talent && talent.lastPortfolioMainMedia && (talent.lastPortfolioMainMedia.path && talent.lastPortfolioMainMedia.path !== '/images/explorer/kolabdefault')) ? { background:'linear-gradient(180deg, rgba(0, 0, 0, 0) 21.59%, rgba(0, 0, 0, 0.6) 94%), url(' + talent.lastPortfolioMainMedia.path  + ')',backgroundRepeat: 'no-repeat',backgroundSize: 'cover', backgroundPosition:'center' } : (talent && talent.lastPortfolioMainMedia === null && talent.portfolios.length > 0) ? { background:'linear-gradient(180deg, rgba(0, 0, 0, 0) 21.59%, rgba(0, 0, 0, 0.6) 94%), url('+ talent.portfolios[0].video_url_image + '.webp)',backgroundRepeat: 'no-repeat',backgroundSize: 'cover', backgroundPosition:'center' } : { background:'linear-gradient(180deg, rgba(0, 0, 0, 0) 21.59%, rgba(0, 0, 0, 0.6) 94%), url('+ defaultMedia + talent.randomNumber + '.webp)',backgroundRepeat: 'no-repeat',backgroundSize: 'cover', backgroundPosition:'center' } ">
                <div v-if="(talent && talent.lastPortfolioMainMedia == null) || (talent.lastPortfolioMainMedia.path === null && talent.lastPortfolioMainMedia.video_url_image === null)" class="inner-t_i ">
                    <div v-if="(talent && talent.lastPortfolioMainMedia == null) || (talent.lastPortfolioMainMedia.path === null && talent.lastPortfolioMainMedia.video_url_image === null)" class="talent-initials-container">
                        <span class="talent-initials" >{{ getTalentInitials(talent) }}</span>
                    </div>
                </div>
                    <div >
                        <div class="card-infos">
                            <div class="t-i">
                                <img :src="'/upload/avatars/'+talent.avatar"/>
                            </div>
                            <div class="c-i-f">
                                <span class="talent-name" style="display: flex;align-items: center;">{{ talent.firstname +' '+talent.lastname }}<img v-show="talent.certified" style="max-width: 1.5rem;width: 100%;height: auto;margin-left: 7px;" src="images/explorer/checkmark.png"/></span>
                                <span class="talent-job">{{ $t(talent.job.name) }}</span>
                            </div>
                         </div>
                    </div>
            </a>
        </div>
        </div>`,
    data() {
        return {
            all_talents: [],
            portfolios: [],
            talents: [],
            filters: {
                alpha: null,
                name: null,
                date: null,
                jobs: [],
                skills: []
            },
            page:1,
            test:[],
            number:[],
            defaultMedia: "/images/explorer/kolabdefault",
            isLoaded:false,
            isNew:false,
            nextPage:null,
            prevPage:null,
            lastPage:null,
            i:0
        };
    },
    mounted() {
        this.getTalents(true);
        this.setSkillsSelect();
        this.setJobsSelect();
        window.Echo.private('explorer').listen('.portfolio-added',(e)=> {
            this.getTalents(true)
        })
        window.Echo.private('explorer').listen('.new-pp',(e)=> {
            this.getProjects()
        })
        window.addEventListener('scroll', ()=>{
            let list = document.querySelector(".explorer-talents-list").getBoundingClientRect()
            let search = document.querySelector(".explorer-talents-searchbar")
            if(list.y - 25 < 0){
                document.querySelector('.explorer-talents-searchbar_phant').style.visibility = 'visible'
                document.querySelector('.explorer-talents-searchbar_phant').style.opacity = 1
                search.classList.add('sticky')
            }else{
                document.querySelector('.explorer-talents-searchbar_phant').style.opacity = 0
                document.querySelector('.explorer-talents-searchbar_phant').style.visibility = 'hidden'
                search.classList.remove('sticky')
            }
            if((window.innerHeight + Math.round(window.scrollY)) >= document.body.offsetHeight - 150 && this.page < this.lastPage){
                this.handleLoadMore()
            }
        });

    },
    methods: {
        getTalentInitials(talent) {
            return talent.firstname.charAt(0) + talent.lastname.charAt(0);
        },
        lazyLoading(elements){
            if(this.talents)
            {
                const observer = new IntersectionObserver(
                    entries => {
                        entries.forEach(entry => {
                            entry.target.classList.toggle('show',entry.isIntersecting)
                            if(entry.isIntersecting) observer.unobserve(entry.target)
                        })
                    },
                    {
                        threshold:0.1,
                    }
                )
                elements.forEach(element=>{
                    observer.observe(element)
                })
            }
        },
        handleLoadMore() {

            //adding the other explorer user
            if(this.talents)
            {
                this.page++
                let params = {};
                params.page = this.page
                params.filter_is_explorer = 1;
                params.only_talent = 1
                if(this.nextPage !== null )
                {
                    //this.isLoaded = false
                    axios.get("api/talent",{
                        params: params
                    }).then(res => {
                        if(res){
                            if (res.data.datas !== null){
                                this.isNew = true
                                //this.isLoaded = true
                                res.data.datas.data.forEach(element=>this.talents.push(element))
                            }
                            if (this.isNew){
                                setTimeout(()=>{
                                    const elements = document.querySelectorAll(".explorer-talent-card")
                                    this.lazyLoading(elements)
                                },500)
                            }
                        }
                    });
                }else{
                    axios.get(`${this.prevPage}`,{
                        params: params
                    }).then(res => {
                        if(res){
                            if (res.data.datas !== null){
                                res.data.datas.data.forEach(element=>this.talents.push(element))
                            }
                        }
                    });
                }
            }
        },
        /**
         * getTalents
         * @return void
         */
         getTalents(all) {
            let params = {};

            setTimeout(async () => {
                this.isLoaded = false
                //document.getElementById('loader').style.display='grid'
                if (this.filters.alpha)
                    params.filter_alpha = this.filters.alpha;
                if (this.filters.name){
                    params.filter_name = this.filters.name;
                    this.isLoaded = true
                    //document.getElementById('loader').style.display='grid'
                }else {
                    this.isLoaded = false
                   // document.getElementById('loader').style.display='none'
                }
                if (this.filters.jobs.length > 0)
                {
                    this.isLoaded = true
                    //document.getElementById('loader').style.display='grid'
                    params.filter_jobs=(this.filters.jobs);
                }else{
                    this.isLoaded = false
                    //document.getElementById('loader').style.display='none'
                }
                if (this.filters.skills.length > 0)
                {
                    this.isLoaded = true
                    //document.getElementById('loader').style.display='grid'
                    params.filter_skills=(this.filters.skills);
                }else{
                    this.isLoaded = false
                    //document.getElementById('loader').style.display='none'
                }

                params.filter_is_explorer = 1;
                params.only_talent = 1

                await axios
                    .get("/api/talent", {
                        params: params
                    })
                    .then(res => {
                        if (res.data.success === false) {
                            // Todo : Error
                        } else {
                            this.nextPage = res.data.datas.next_page_url
                            this.prevPage = res.data.datas.prev_page_url
                            this.lastPage = res.data.datas.last_page
                            this.page = res.data.datas.current_page
                            this.isLoaded = true
                            //document.getElementById('loader').style.display='none'
                            this.talents = res.data.datas.data; // Update talents list
                            if (all){
                                this.all_talents = res.data.datas.data;
                            }
                           this.all_talents = res.data.datas.data // Set all talents for first time
                            setTimeout(()=>{
                                const explorerCard = document.querySelectorAll(".explorer-talent-card")
                                this.lazyLoading(explorerCard)
                            },200)
                        }
                    })
                    .catch(error => console.log(error));
            }, 100);
        },

        /**
         * Set skills select2
         */
        setSkillsSelect() {
            $(() => {
                $(".js-skills-datas").select2({
                    dropdownCssClass: "multiple-choices filters-dropdown",
                    placeholder: this.$t('skills'),
                    closeOnSelect: false,
                    allowClear: true,
                    debug: false,
                    language: {
                        searching: function() {
                            return "Chargement...";
                        }
                    },
                    ajax: {
                        url: "/api/skill",
                        processResults: function(data) {
                            var data = $.map(data.datas, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: data
                            };
                        }
                    }
                });

                $(".js-skills-datas").on("select2:select", (e) => {
                    $(".js-skills-datas").trigger('change');
                    this.getTalents();
                });
                $(".js-skills-datas").on("select2:unselecting", (e) => {
                    let skillId = e.params.args.data.id;
                    let strSkillId = e.params.args.data.id.toString();
                    if (skillId && this.filters.skills.includes(strSkillId)) {
                        let indexOf = this.filters.skills.indexOf(strSkillId);
                        this.filters.skills.splice(indexOf, 1);
                        $(".js-skills-datas").val(this.filters.skills).trigger('change');
                        this.getTalents();
                    }
                });
            });
        },

        /**
         * Set jobs select2
         */
        setJobsSelect() {
            $(() => {
                $(".js-jobs-data").select2({
                    dropdownCssClass: "multiple-choices filters-dropdown",
                    placeholder: this.$t('jobs'),
                    closeOnSelect: false,
                    allowClear: true,
                    //tags: "true",
                    language: {
                        searching: function() {
                            return "Chargement...";
                        }
                    },
                    ajax: {
                        url: "/api/job",
                        processResults: function(data) {
                            var data = $.map(data.datas, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: data
                            };
                        }
                    }
                });

                $(".js-jobs-data").on("select2:select", (e) => {
                    this.getTalents();
                });
                $(".js-jobs-data").on("select2:unselecting", (e) => {
                    let jobId = e.params.args.data.id;
                    let strJobId = e.params.args.data.id.toString();
                    if (jobId && this.filters.jobs.includes(strJobId)) {
                        let indexOf = this.filters.jobs.indexOf(strJobId);
                        this.filters.jobs.splice(indexOf, 1);
                        $(".js-jobs-data").val(this.filters.jobs).trigger('change');
                        this.getTalents();
                    }
                });
            });
        },

        /**
         * Clear field
         */
        clear(type) {
            if (typeof this.filters[type] == "object") {
                this.filters[type] = [];
            } else {
                this.filters[type] = null;
            }

            if ($(".js-" + type + "-data")) {
                $(".js-" + type + "-data")
                    .val("")
                    .trigger("change");
            }

            this.getTalents();
        }
    }
});
</script>
