<template>
    <div class="modal-mask-responsive">
        <div class="modal-mask-wrapper m-m-w"></div>
        <div class="modal-wrapper modal-mask-wrapper__wrapper" v-on:click="emitCloseModal">
            <div v-if="!isPropositionSended" class="modal-container">
                <div class="explorer-form-modal explorer-project-propose-modal">
                    <div class="modal-header text-center">
                        <h1 class="modal-title">{{$t('explorer-propose-new-form')}}</h1>
                        <p>{{$t('explorer-propose-new-form-sub')}}</p>
                    </div>

                    <form method="post" @submit.prevent="postMissionProposition"
                          class="explorer-form explorer-form-project-propose">
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>{{$t('explorer-propose-new-form-project-name')}}*</h2>
                            </div>
                            <div class="form-section-content">
                                <input class="m-title" @keydown="error=''" v-model="mission.name" type="text" :placeholder="$t('explorer-propose-new-form-project-name-inp')" name="name"/>
                                <span v-if="error" style="color: red">{{ $t('error-message') }}</span>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-people-white"></span></div>
                                <h2>Budget & deadline*</h2>
                            </div>
                            <div class="form-section-content">
                                <div style="display: flex;width: 100%;justify-content: space-between;">
                                    <div style="width: 100%;margin-right: 10px;" class="m-budget">
                                        <select class=" js-budget-data js-select2" v-select2  v-model="mission.budget" name="budget" style="width: 100%;">
                                            <option value="" disabled>{{ $t('explorer-propose-new-form-project-budget-inp') }}</option>
                                            <option value="Moins de 100€">{{ $t('less-100') }}</option>
                                            <option value="Entre 100€ et 500€">{{ $t('between-100') }}</option>
                                            <option value="Entre 500€ et 1000€">{{ $t('between-500') }}</option>
                                            <option value="Plus de 1000€">{{ $t('more-1000')}}</option>
                                        </select>
                                        <span v-if="budgetError" style="color: red">{{ $t('error-message') }}</span>
                                    </div>
                                    <div style="width: 100%" class="m-deadline">
                                        <select id="deadline-data" class="js-deadline-data js-select2" @click="deadlineError=''" name="deadline" v-select2  v-model="mission.deadline" style="width: 100%;">
                                            <option value="" disabled>{{$t('explorer-propose-new-form-project-budget-inp-1')}}</option>
                                            <option value="Dès maintenant">{{$t('starting-now')}}</option>
                                            <option value="Dans la semaine">{{$t('this-week')}}</option>
                                            <option value="Dans 2 semaines">{{$t('in-2-weeks')}}</option>
                                            <option value="Dans le mois">{{$t('this-month')}}</option>
                                        </select>
                                        <span v-if="deadlineError" style="color: red">{{ $t('error-message') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-shape-white"></span></div>
                                <h2>{{$t('explorer-propose-new-form-details')}}</h2>
                            </div>
                            <div class="form-section-content">
                                <div class="m-type">
                                    <select class="js-type-select2 js-required multiple-choices filters-dropdown js-select2" @click="typeError=''" name="type" v-select2 v-model="mission.type" style="width: 100%;">
                                        <option value="" disabled>{{$t('explorer-propose-new-form-details-inp')}}</option>
                                        <option :value="taskType.id" v-for="taskType in taskTypes">{{ $t(taskType.name) }}</option>
                                    </select>
                                </div>
                                <span v-if="typeError" style="color: red">{{ $t('error-message') }}</span>

                                <div>
                                    <textarea class="text-explorer" @keydown="descriptionError=''" v-model="mission.description" :placeholder="$t('explorer-propose-new-form-details-inp-1')" name="name" style="margin-left:0 !important;margin-right:0!important;"/>
                                </div>
                                <span v-if="descriptionError" style="color: red">{{ $t('error-message') }}</span>
                            </div>
                        </div>
                        <input type="submit" class="form-validation-btn" :disabled="isPropositionSended" :value="$t('send-cta')" style="padding-top:9px;"/>
                    </form>
                </div>
            </div>
            <div v-else class="explorer-mission-modal-validate">
                <img src="/images/explorer/mission-propose-validate.png" class="mission-modal-validate-icon"
                     alt="Validation icon"/>
                <h1 class="mission-modal-validate-text">{{$t('explorer-propose-new-form-details-sent')}}</h1>
                <button class="mission-modal-validate-button" type="button" @click="redirectToMessenger">{{$t('show-conversation')}}
                </button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['userToPropose'],

    data() {
        return {
            mission: {
                name: '',
                budget: '',
                deadline: '',
                type: '',
                description: '',
                status:''
            },
            error: null,
            budgetError: null,
            typeError: null,
            deadlineError: null,
            descriptionError: null,
            statusError: null,
            proposeToUserId: this.userToPropose.id ,
            isPropositionSended: false,
            taskTypes: false,
            modalOpen:false,
            rules:({
                email: 'email|min:5|required',
                password: 'same:confirm_password',
                confirm_password: 'min:6|required',
            }),
            messages:({
                 'name': 'required|string,',
                'budget' : 'required|string',
                'deadline' : 'required|string|unique:users,email',
                'type' : 'required|string|confirmed',
                'description' : 'required',
                'status' : 'required'
            }),
        }
    },
    created() {
    },
    mounted() {
        this.sortBudget()
        this.sortType()
        this.sortDeadLine()
        this.getTaskType()
    },
    methods: {
        getTaskType() {
            axios.get('/api/task-type?type=explorer').then(res => {
                if (res.status == 200) {
                    this.taskTypes = res.data.datas;
                }
            }).catch(error => {
                this.handleError(error)
            });
        },
        postMissionProposition() {
            axios.post('/api/explorer/missions/propositions', {
                    params: {
                        mission_proposition: {
                            mission: this.mission,
                            propose_to_user_id: this.proposeToUserId,
                        }
                    }
                }).then(res => {
                    if(res.status == 200){
                        this.isPropositionSended = true;
                        if(this.isPropositionSended && !this.modalOpen)
                        {
                            setTimeout(()=>{
                                this.redirectToMessenger()
                            },3000)
                        }
                    }

            }).catch(error => {
                this.handleError(error)
            });
        },
        sortDeadLine() {
            $(() => {
                $(".js-deadline-data").select2({
                    allowClear: false,
                    closeOnSelect: true,
                    //dropdownCssClass: "filters-dropdown",
                    placeholder: this.$t('explorer-propose-new-form-project-budget-inp-1'),
                });
            });
        },
        sortType() {
            $(() => {
                $(".js-type-select2").select2({
                    allowClear: false,
                    closeOnSelect: true,
                    //dropdownCssClass: "filters-dropdown",
                    placeholder: this.$t('explorer-propose-new-form-details-inp'),
                });
            });
        },
        sortBudget() {
            $(() => {
                $(".js-budget-data").select2({
                    allowClear: false,
                    closeOnSelect: true,
                    //dropdownCssClass: "filters-dropdown",
                    placeholder: this.$t('explorer-propose-new-form-project-budget-inp'),
                });
            });
        },
        emitCloseModal(event) {
            if (event.target.className === "modal-wrapper modal-mask-wrapper__wrapper") {
                this.$emit('close-modal');
                this.modalOff = true
            }else{
                if (this.isPropositionSended && event.target.className !== "modal-wrapper modal-mask-wrapper__wrapper") {
                    this.redirectToExplorer();
                }
            }
        },
        redirectToMessenger() {
            window.location.replace("/explorer/messenger");
        },
        redirectToExplorer() {
            window.location.replace("/explorer");
        },
        handleError(error)
        {
            let response = error.response
            let title = document.querySelector(".m-title")
            let selectError = document.querySelectorAll(".select2-selection--single")
            let budget = document.querySelector(".m-budget .select2-container .select2-selection--single")
            let deadline = document.querySelector(".m-deadline .select2-container .select2-selection--single")
            let type = document.querySelector(".m-type .select2-container  .select2-selection--single")
            let describe = document.querySelector(".text-explorer")
            if(error) {
                if (response) {
                    if (typeof response.data.name !== 'undefined') {
                        if (response.data.name.length > 0) {
                            this.error = response.data.budget[0];
                            title.classList.add("is-invalid")
                            title.classList.add("field-error")
                        }
                    }
                    if (typeof response.data.budget !== 'undefined') {
                        if (response.data.budget.length > 0) {
                            this.budgetError = response.data.budget[0];
                            budget.classList.add("is-invalid")
                            budget.classList.add("field-error")
                        }
                    }
                    if (typeof response.data.type !== 'undefined') {
                        if (response.data.type.length > 0) {
                            this.typeError = response.data.type[0];
                            deadline.classList.add("is-invalid")
                            deadline.classList.add("field-error")
                        }
                    }
                    if (typeof response.data.deadline !== 'undefined') {
                        if (response.data.deadline.length > 0) {
                            this.deadlineError = response.data.deadline[0];
                            type.classList.add("is-invalid")
                            type.classList.add("field-error")
                        }
                    }
                    if (typeof response.data.description !== 'undefined') {
                        if (response.data.description.length > 0) {
                            this.descriptionError = response.data.description[0];
                            describe.classList.add("is-invalid")
                            describe.classList.add("field-error")
                        }
                    }
                    if (typeof response.data.status !== 'undefined') {
                        if (response.data.status.length > 0) {
                            this.statusError = response.data.status[0];
                            title.classList.add("is-invalid")
                            title.classList.add("field-error")
                        }
                    }
                }
            }
        }
    }
}
</script>
