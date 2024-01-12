<template>
    <div style="position: relative; top: 50%;" v-on:click.stop>
        <input type="hidden" v-model="appointment.id" />
        <div class="rdv-popup popup-box">
            <div class="popup-intro text-center mb-30">
                <h2 class="popup-maintitle title-rdv">
                    <span v-if="!_appointment">Créer un RDV</span>
                    <span v-else>Modifier le RDV</span>
                </h2>
            </div>
            <div class="form-container">
                <div class="form-box">
                    <span class="icon picto-rdv-title"></span>
                    <div class="form-field-wrapper">
                        <InputField placeholder="Titre du rendez-vous" @triggerValidation="validateForm('title')" classes="w-100" :errors="errors.title" v-model="appointment.title" required /> 
                    </div>
                </div>
                <div class="form-box">
                    <span class="icon picto-rdv-date"></span>
                    <div class="form-field-wrapper">
                        <div class="w-100">
                            <div style="width:65%; display:inline-block;">
                                <v-date-picker
                                    v-model="date"
                                    :popover="{
                                        visibility: 'click',
                                        placement: $screens({
                                            default: 'bottom-start'
                                        })
                                    }"
                                    :masks="{ L: 'DD MMMM YYYY' }"
                                    color="kpurple"
                                    class="w-100"
                                    is-dark
                                >
                                    <template v-slot="{ inputValue, inputEvents }">
                                        <input :value="inputValue" v-on="inputEvents" class="form-field js-datepicker" @focusout="validateForm('date')" id="appointment_date" :style="errors.date ? 'border:1px solid #580619' : ''" ref="date">
                                    </template>
                                </v-date-picker>
                            </div>

                            <div style="width:33.5%; display:inline-block;">
                                <InputField type="time" v-model="time" required  @focusout="validateForm('time')" :style="errors.time.length ? 'border:1px solid #580619; border-radius:3px' : ''"/>
                            </div>
                            <span
                                v-if="errors.date.length"
                                class="errors_message"
                            >
                                Ce champ est obligatoire
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-box">
                    <span class="icon picto-rdv-addprofile"></span>
                    <div
                        class="form-field-wrapper"
                    >
                        <div :class="errors.talents.length ? 'hasError' : ''" style="min-width:100%">
                            <multiselect
                                v-model="selectedTalents"
                                :options="talentOptions"
                                :multiple="true"
                                :taggable="true"
                                tag-placeholder=""
                                @tag="addTalent"
                                label="name"
                                track-by="id"
                                :show-labels="false"
                                placeholder="Inviter un ou plusieurs talent(s) au RDV"
                                >
                                <template v-slot:tag="{ option, remove }">
                                    <div class="multiselect__tag">
                                        <span :class="`${option.id === null ? 'new_tag' : 'default_tag'}`">{{ option.name }}</span>
                                        <i class="multiselect__tag-icon" @click.prevent @mousedown.prevent.stop="remove(option, $event)" />
                                    </div>
                                </template>
                            </multiselect>
                            <div
                                v-if="errors.talents.length"
                                class="errors_message"
                            >
                                Ce champ est obligatoire
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-box">
                    <span class="icon picto-rdv-location"></span>
                    <div class="form-field-wrapper">
                        <div :class="errors.location.length ? 'hasError' : ''" style="min-width:100%">
                            <multiselect
                                v-model="appointment.location"
                                :options="locationOptions"
                                :multiple="false"
                                :taggable="true"
                                tag-placeholder=""
                                @tag="addLocation"
                                :show-labels="false"
                                placeholder="Lieu du RDV (ex. : 8 rue Dupont - 75000 Paris)"
                                >
                            </multiselect>
                            <div
                                v-if="errors.location.length"
                                class="errors_message"
                            >
                                Ce champ est obligatoire
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-box notes__form-box ">
                    <span class="icon picto-rdv-note"></span>
                    <InputField v-model="appointment.note" placeholder="Saisissez vos notes ici..." type="textarea" class="no-caps" maxlength="300"/>
                </div>
            </div>
            <div
                class="form-footer"
            >
                <a class="button popup-btn" @click="close()">Quitter</a>
                <button class="button popup-btn" @click="save()">
                    <span v-if="!_appointment">Créer</span>
                    <span v-else>Modifier</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import InputField from '../fields/InputField.vue';
import Multiselect from 'vue-multiselect'
import { throwStatement } from "@babel/types";

export default {
    components: {
        InputField,
        Multiselect
    },
    props: ["appointments", "_appointment", "users"], // why 2 appointmnts (list & filled one?)?

    data() {
        return {
            appointment: {
                id: null,
                title: '',
                datetime: moment(),
                location: '',
                talents: [],
                note: null,
            },
            selectedTalents: [],
            talentOptions: [],
            locationOptions: [],
            errors: {
                title: [],
                location: [],
                date: '',
                time: [],
                talents: [],
            },
            error_msg: false
        };
    },

    computed: {
        date: {
            get() {
                if (null !== this.appointment.datetime) {
                    return moment(this.appointment.datetime).format();
                }

                return moment();
            },

            set(newDate) {
                let previousDate = this.appointment.datetime; // needed as the hours:minutes may already be set to something new
                
                this.appointment.datetime = moment(newDate).set('hour', previousDate.hours()).set('minute', previousDate.minutes());
            }
        },
        time: {
            get() {
                if (null !== this.appointment.datetime) {

                    return ('0'+this.appointment.datetime.get('hours')).substr(-2)+':'+('0'+this.appointment.datetime.minutes()).substr(-2);
                }

                return moment().hours()+':'+moment.minutes();
            },

            set(newTime) {
                // Format is : HH:MM
                let hours = newTime.split(':')[0];
                let minutes = newTime.split(':')[1];

                this.appointment.datetime.set('hour', hours).set('minute', minutes);
            }
        }
    },
    created() {
        // set the data if any (_appointment means we're editing)
        if (this._appointment) {
            this.appointment = this._appointment

            this.appointment.datetime = moment(this._appointment.datetime)

            // Add contact to the list of talents selected
            this._appointment.contacts.forEach(contact => {
                this.addTalent(contact.email)
            });
        };
    },
    mounted() {
        this.selectedTalents = this.appointment.talents,
        this.getTalents();
        this.getLocations();
    },

    methods: {
        save() {
            this.validateForm('title');
            this.validateForm('location');
            this.validateForm('date');
            this.validateForm('time');

            this.selectedTalents.forEach(talent => {
                this.appointment.talents.push(talent);
            })

            this.validateForm('talents');

            if (!this.errors.title.length && !this.errors.location.length && !this.errors.date.length && !this.errors.time.length) {
                axios.post('/api/appointment', {
                    appointment: { ...this.appointment, datetime: this.appointment.datetime.format()}
                }).then(res => {
                    this.$bus.$emit(
                        "APPOINTMENT_ADD_OR_UPDATE",
                        res.data
                    ); // Emit add or update appointment event
                    this.$bus.$emit("ACTION_CHANGED", {
                        action: "CONGRATS",
                        type: "APPOINTMENT_ADD_OR_UPDATE",
                        element: this.appointment
                    }); // Congrats modal
                    // An emit to specify that a new appointment has been created so that we can show a red dot next to it
                    this.$bus.$emit('APPOINTMENT_ADDED', { appointment_id: res.data.datas.id })
                });
            }
        },
        getLocations() {
            axios.get("/api/appointment/location", {
                params: {
                    user_id: Vue.prototype.Global.user_id,
                }
            }).then(res => {
                let locationsList = [];

                res.data.datas.map((location) => {
                    locationsList.push(location.name);
                })

                this.locationOptions = locationsList;
            });
        },
        addLocation(location) {
            this.locationOptions.push(location);
            this.appointment.location = location; 
        },
        getTalents() {
            axios.get("/api/talent/", {
                params: {
                    applyWorkspace: true,
                    user_id: Vue.prototype.Global.user_id,
                    no_paginate:true
                }
            }).then(res => {
                let talentsList = [];

                res.data.datas.map((talent) => {
                    talentsList.push({
                        name: talent.firstname+' '+talent.lastname,
                        id: talent.id,
                        "data-attribute": 'new',
                    });
                })

                // Sort by name ASC
                talentsList.sort((a, b) => a.name - b.name);

                this.talentOptions = talentsList;
            });
        },
        addTalent(talentEmail) {
            // prevent adding a contact that is already there
            if (this.appointment.talents.filter(talent => talent.name === talentEmail).length) { return; }

            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,10})+$/.test(talentEmail)) {
                const tag = {
                    name: talentEmail,
                    id: null,
                }

                this.talentOptions.push(tag);
                this.appointment.talents.push(tag); 
            } else {
                console.error('bad email')
            }
        },
        validateForm(type) {
            if (type === 'title') {
                // @todo: check max length?
                this.appointment.title.length < 3 ? this.errors.title = ['Ce champ est obligatoire'] : this.errors.title = [];
            }

            if (type === 'location') {
                // @todo: check max length?
                this.appointment.location.length < 3 ? this.errors.location = ['Ce champ est obligatoire'] : this.errors.location = [];
            }

            if (type === 'date') {
                this.$refs.date.value.length === 0 ? this.errors.date = 'Ce champ est obligatoire' : this.errors.date = '';
            }

            if (type === 'time') {
                // NaN:NaN > 5
                this.time.length > 5 ? this.errors.time = ['Ce champ est obligatoire'] : this.errors.time = [];
            }

            if (type === 'talents') {
                this.appointment.talents.length === 0 ? this.errors.talents = ['Ce champ est obligatoire'] : this.errors.talents = [];
            }
        },
        close() {
            this.$bus.$emit("ACTION_CHANGED", {
                action: null
            }); // Close modal
        },
    },
};
</script>

<style lang="scss">
    .multiselect .multiselect__tags .multiselect__placeholder {
        padding-left: 6px;
        font-size: 1.6rem;
    }

    .multiselect__single {
        background-color: unset;
        color: white;
    }

    .multiselect__tag {
        padding-left: 14px;
        font-size:1.6rem;
        
        &:has(.new_tag) {
            background-color:#2A4F96;

            .multiselect__tag-icon {
                &:after  {
                    color: white;
                }

                &:hover  {
                    background-color:#2A4F96;
                }
            }
        }

        &:has(.default_tag) {
            background-color:#2B1C56;

            .multiselect__tag-icon {
                &:after  {
                    color: white;
                }

                &:hover  {
                    background-color:#2B1C56;
                }
            }
        }
    }

    .hasError {
        .multiselect {
            border: 1px solid #580619;
        }
    }

    .errors_message {
            color: #580619;
            font-size: 0.95rem;
            margin: 5px 0px -0.95rem 10px;
        }
</style>