<template>
    <div class="modal-mask-responsive" >
        <div class="modal-mask-wrapper"></div>
        <div class="modal-wrapper modal-mask-wrapper__wrapper" v-on:click="emitCloseModal">
            <div class="modal-container">
                <div class="explorer-form-modal" style="height: fit-content">
                    <div class="modal-header text-center" style="padding-top:10px; padding-bottom: 30px;">
                        <h1 class="modal-title">{{$t('prepare-quote')}}</h1>
                    </div>

                    <form method="post" v-on:submit.prevent="postQuote" class="explorer-form">
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>{{$t('prepare-quote-describe')}} *</h2>
                            </div>
                            <div class="form-section-content">
                                <div style="position: relative;">
                                    <input class="q-l" v-model="quote.currentQuoteItem" type="text" :placeholder="$t('prepare-quote-describe-inp')" name="quoteItems" style="margin-bottom: 5px"/>
                                    <button class="form-add-quote-item-button" type="button" @click="handleAddQuoteItem">Add
                                    </button>
                                </div>
                                <span v-if="errorCurrentItem" style="color: red">{{$t('error-message')}}</span>
                                <div class="form-quote-items-container">
                                    <div v-for="quoteItem in quote.quoteItems" class="form-quote-item q-a_p">
                                        <span>{{ quoteItem }}</span>
                                        <button type="button" class="b-q_i" @click="handleRemoveQuoteItem(quoteItem)"><span class="filters-delete__picto icon-cross" style=""/>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-eur-white"></span></div>
                                <h2>{{$t('prepare-quote-price')}} *</h2>
                            </div>
                            <div class="form-section-content row prices">
                                <input class="q-p col-6" v-model="quote.price" type="number" name="price" :placeholder="$t('prepare-quote-price-inp')" onkeydown="if(event.key==='.'){event.preventDefault();}"  oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');"/>
                                <span v-if="errorPrice" style="color: red">{{ $t('error-message') }}</span>
                                <select class="js-currency-data js-required filters-dropdown col-6" v-model="quote.currency" v-select2 type="select" name="currency" :placeholder="$t('currency')">
                                    <option v-for="currency in currencies" :value="currency.id" :selected="(currency.id == 1) ? 'selected' : ''">{{ currency.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-mission-detail-white"></span></div>
                                <h2>{{$t('prepare-quote-deadline')}} *</h2>
                            </div>
                            <div id="d-q" class="form-section-content">
                                <select class="q-deadline js-deadline-data js-required filters-dropdown" v-model="quote.deadline" v-select2 style="width: 100%" name="deadline">
                                    <option value="" disabled selected>{{$t('prepare-quote-deadline-inp')}}</option>
                                    <option>{{ $t('form-choice-delivery-1') }}</option>
                                    <option>{{ $t('form-choice-delivery-2') }}</option>
                                    <option>{{ $t('form-choice-delivery-3') }}</option>
                                    <option>{{ $t('form-choice-delivery-4') }}</option>
                                </select>
                                <span v-if="errorDeadline" style="color: red">{{$t('error-message')}}</span>
                                <textarea class="d-q_m" v-model="quote.comment" :placeholder="$t('prepare-quote-deadline-inp-1')"/>
                            </div>
                        </div>

                        <button type="submit" class="form-validation-btn">{{$t('form-send-quote-cta')}}</button>
                    </form>
                </div>
            </div>
        </div>
        <explorer-messenger-quit-popup v-if="showModal" @close-modal="emitCloseModal" :conversation="conversation"/>
    </div>
</template>
<script>
import ExplorerMessengerQuitPopup from "./ExplorerMessengerQuitPopup";
export default {
    components: {ExplorerMessengerQuitPopup},
    props: ['user', 'conversation', 'isFreelance', '_currencies'],
    data() {
        return {
            quote: {
                quoteItems: [],
                currentQuoteItem: '',
                price: '',
                deadline: '',
                comment: '',
            },
            errorCurrentItem:null,
            errorPrice:null,
            errorDeadline:null,
            showModal:false,
            currencies: this._currencies,
        }
    },

    mounted() {
        this.sortDeadLine()
    },
    computed: {},
    methods: {
        postQuote() {
            axios.post('/api/explorer/missions/conversations/' + this.conversation.id + '/quotes', {
                params: {
                    quote: this.quote
                }
            }).then(res => {
                //this.emitCloseModal();
                this.$emit('close-modal');
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
                    placeholder: this.$t('prepare-quote-deadline-inp'),
                });
            });

            $(() => {
                $(".js-currency-data").select2({
                    allowClear: false,
                    closeOnSelect: true,
                    //dropdownCssClass: "filters-dropdown",
                    placeholder: this.$t('choose-your-currency'),
                }).val(1);
            });
        },
        //ajouter un livrable
        handleAddQuoteItem() {
            if (this.quote.currentQuoteItem.length > 0) {
                this.quote.quoteItems.push(this.quote.currentQuoteItem);
                this.quote.currentQuoteItem = '';
            }
        },
        //supprimer de la liste des livrables
        handleRemoveQuoteItem(item) {
            const index = this.quote.quoteItems.indexOf(item);
            if (index > -1) {
                this.quote.quoteItems.splice(index, 1); // 2nd parameter means remove one item only
            }
        },
        emitCloseModal() {
            //this.showModal = true
            if (event.target.className === "modal-wrapper modal-mask-wrapper__wrapper") {
                if (this.isPropositionSended && event.target.className !== "modal-wrapper modal-mask-wrapper__wrapper") {
                    this.redirectToExplorer();
                } else {
                    Vue.prototype.Explorer._confirm(()=>this.$emit('close-modal'))
                }
                Vue.prototype.Explorer._confirm()
                //this.$emit('close-modal');
            }
        },
        handleError(error)
        {
            let response = error.response
            let livrable = document.querySelector(".q-l")
            let price = document.querySelector(".q-p")
            let deadline = document.querySelector("#d-q .select2-selection")
            if(response){
                if (typeof response.data.quoteItems !== 'undefined') {
                    if(response.data.quoteItems.length > 0)
                    {
                        this.errorCurrentItem = response.data.quoteItems[0];
                        livrable.classList.add("is-invalid")
                        livrable.classList.add("field-error")
                    }
                }
                if (typeof response.data.price !== 'undefined') {
                    if(response.data.price.length > 0)
                    {
                        this.errorPrice = response.data.price[0];
                        price.classList.add("is-invalid")
                        price.classList.add("field-error")
                    }
                }
                if (typeof response.data.deadline !== 'undefined') {
                    if(response.data.deadline.length > 0)
                    {
                        this.errorDeadline = response.data.deadline[0];
                        deadline.classList.add("is-invalid")
                        deadline.classList.add("field-error")
                    }
                }
            }
        }
    },
}
</script>
