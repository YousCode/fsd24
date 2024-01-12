<template>
    <div class="dashboard" id="vue__dashboard">
        <div class="js-dashboard-inner" id="context_menu_wrapper">
            <ContextMenu :contextMenuCallback="getDashboard" ref="ContextMenu"></ContextMenu>

            <div class="main-container" style="background: none;">
                <!--<AddButtons></AddButtons>-->

                <!-- <button type="button" class="notifications__btn js-open-notif">10</button>-->

                <div class="dashboard__container">
                    <div class="row-cols-1" style="background: #2B1C56; margin-bottom: 20px; max-width: 100%; border-radius: 20px; flex: 0 0 100%;">
                        <!-- <div class="col-w30 col-custom block__col">
                      <card-welcome
                      	:user="user"
                      ></card-welcome>
                  </div> -->

                        <div class="col-12 col-custom block__col" style="display: flex; width: 100%;flex: 0 0 100%; max-width: 100%;padding:0; margin:0;box-shadow: 0px 0px 15px #120B23;">
                            <!-- layout/CardTasks -->
                            <div class="col-3 col-custom " style="margin:0; padding:0;max-width: 300px;">
                                <card-welcome :user="user" :HelloCardImg="HelloCardImg"></card-welcome>
                            </div>

                            <card-tasks
                                :all_tasks="all_tasks"
                                :tasks="tasks"
                                :progress_tasks="progress_tasks"
                                :closed_tasks="closed_tasks"
                                :waiting_tasks="waiting_tasks"
                                :contextMenu="$refs.ContextMenu"
                                :user="user"
                            ></card-tasks>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-custom block__col">
                            <talk
                                :_workspace = 'workspace'
                                :all_tasks="all_tasks"
                                :tasks="tasks"
                                :user="user"
                                :contextMenu="$refs.ContextMenu"
                                :key="changePlanningKey"
                                :datetalk="datetalk"
                            ></talk>
                        </div>

                        <div class="col-md-6 col-custom block__col">
                            <!-- layout/CardRdv -->
                            <card-rdv
                                v-if="!isLoading"
                                :appointments="appointments"
                                :contextMenu="$refs.ContextMenu"
                                :waiting_appointments="waiting_appointments"
                                :contextMenuCallback="getDashboard"
                                :user="user"
                            ></card-rdv>
                        </div>
                    </div>
                </div>
            </div>

            <!-- layout/NotificationsPanel -->
            <!-- <NotificationsPanel></NotificationsPanel>-->
        </div>

        <WelcomeScreen :user="user"></WelcomeScreen>
        <ContactModule></ContactModule>
    </div>
</template>

<script>
export default {
    props: ["user", "project", "_workspace"],

    data() {
        return {
            isLoading: true,
            all_tasks: [],
            tasks: [],
            appointments: [],
            progress_tasks: 0,
            closed_tasks: 0,
            waiting_tasks: 0,
            waiting_appointments: 0,
            date: this.getDate(),
            time: this.getTime(),
            api_key: "31736d6054b9468d5ae0f7e1568fefa8",
            url_base: "https://api.openweathermap.org/data/2.5/",
            location: "Paris",
            weather: {},
            HelloCardImg: "background-image: url(/images/CARTE_",
            changePlanningKey: 0,
            datetalk: new Date(),
            edit: false,
            workspace: this._workspace
        };
    },

    mounted() {
        this.getDashboard(true);
        this.fetchWeather();
        // Catch event when element is added or updated
        this.$bus.$on("APPOINTMENT_ADD_OR_UPDATE", () => {
            this.getDashboard(); // Call talents with filters
        });

        this.$bus.$on("TASK_ADD_OR_UPDATE", () => {
            this.getDashboard(true); // Call talents with filters
        });
        this.$bus.$on("DASHBOARD_UPDATE_TASK", () => {
            this.edit = true;
            this.getDashboard();
        });

        window.Echo.private('kolab').listen('.task-event', (e) => {
            this.getDashboard();
        });

        window.Echo.private('appointment').listen('AppointmentDeletedEvent', (e) => {
            let talents = e.appointment.datas.talents;

            if (talents.find((talent) => talent.id === this.user.id)) {
                this.getDashboard();
            }
        })

        window.Echo.private('appointment').listen('AppointmentEvent', (e) => {
            let talents = e.appointment.datas.talents;

            if (talents.find((talent) => talent.id === this.user.id)) {
                this.$bus.$emit(
                    "APPOINTMENT_ADD_OR_UPDATE",
                    e.appointment.datas
                );

                setTimeout(() => {
                    this.$bus.$emit('APPOINTMENT_ADDED', { appointment_id: e.appointment.datas.id })
                }, 1500);
            }
        })
    },

    methods: {
        getDashboard(all, filters = {}) {
            var params = {};
            //console.log("break point")
            if (filters.status) params.filter_status = filters.status;
            params.edit = this.edit;

            axios
                .post("/api/dashboard/" + this.user.id, {
                    params: params
                })
                .then(res => {
                    this.isLoading = false;
                    this.tasks = res.data.datas.tasks.length
                        ? res.data.datas.tasks
                        : [];
                    this.appointments = res.data.datas.appointments.length
                        ? res.data.datas.appointments
                        : [];
                    this.closed_tasks = res.data.datas.closed_tasks;
                    this.waiting_tasks = res.data.datas.waiting_tasks;
                    if (all) {
                        this.all_tasks = res.data.datas.tasks.length
                            ? res.data.datas.tasks
                            : [];
                    }
                    let todayViewed = (this.tasks.length > 0) ? this.tasks.filter(task => typeof JSON.parse(task.viewed)[this.user.id] != "undefined") : [];
                    let tomorrowViewed = (this.waiting_tasks.length > 0) ? this.waiting_tasks.filter(task => typeof JSON.parse(task.viewed)[this.user.id] != "undefined") : [];
                    let closedViewed = (this.closed_tasks.length > 0) ? this.closed_tasks.filter(task => typeof JSON.parse(task.viewed)[this.user.id] != "undefined") : [];

                    this.$bus.$emit('UPDATE_VIEWED_FILTER', {todayViewed: todayViewed, tomorrowViewed: tomorrowViewed, closedViewed: closedViewed});

                    if (this.tasks.length > 0) {
                        this.$bus.$emit('UPDATE_TASKS_TO_SHOW', {tasks: this.tasks, status: 'progress'});
                    } else if (this.waiting_tasks.length > 0 && this.tasks.length == 0) {
                        this.$bus.$emit('UPDATE_TASKS_TO_SHOW', {tasks: this.waiting_tasks, status: 'waiting'});
                    } else if (this.waiting_tasks.length == 0 && this.tasks.length == 0 && this.closed_tasks.length > 0) {
                        this.$bus.$emit('UPDATE_TASKS_TO_SHOW', {tasks: this.closed_tasks, status: 'closed'});
                    }
                    if (filters.status == undefined){
                        let progress = document.getElementById("progress-btn");
                        let waiting = document.getElementById("waiting-btn");
                        let closed = document.getElementById("closed-btn");
                        this.hideProgressBar = false;
                        if(closed != null ){
                            closed.classList.remove("active-tasks");
                        }
                        if(waiting != null){
                            waiting.classList.remove("active-tasks");
                        }
                        if(progress!= null){
                            progress.classList.add("active-tasks");
                        }
                    }
                })
                .catch(error => console.log(error));
        },
        forceRender() {
            this.changePlanningKey += 1;
        },
        fetchWeather() {
            fetch(
                `${this.url_base}weather?q=${this.location}&units=metric&APPID=${this.api_key}`
            )
                .then(res => {
                    return res.json();
                })
                .then(this.setResults);
        },
        setResults(results) {
            this.weather = results;
            this.SetHelloCardImg();
        },
        SetHelloCardImg() {
            //set sunrise time
            let sunrise = moment
                .utc(this.weather.sys.sunrise, "X")
                .add(7200, "seconds")
                .format("HH:mm");
            let sunrise_hh = moment.duration(sunrise).hours();
            let sunrise_mm = moment.duration(sunrise).minutes();
            // set sunset time
            let sunset = moment
                .utc(this.weather.sys.sunset, "X")
                .add(7200, "seconds")
                .format("HH:mm");
            let sunset_hh = moment.duration(sunset).hours();
            let sunset_mm = moment.duration(sunset).minutes();
            //set time
            let time_hh = moment.duration(this.time).hours();
            let time_mm = moment.duration(this.time).minutes();

            if (
                time_hh <= sunrise_hh ||
                time_hh >= sunset_hh ||
                time_hh >= 23
            ) {
                this.HelloCardImg = this.HelloCardImg + "SOIR-";
            } else if (time_hh >= sunrise_hh && time_hh <= 10) {
                this.HelloCardImg = this.HelloCardImg + "MATIN-";
            } else if (time_hh >= sunrise_hh && time_hh <= sunset_hh) {
                this.HelloCardImg = this.HelloCardImg + "JOURNEE-";
            } else {
            }
            // FOR TEST PURPOSE : this.weather.weather[0].main = "sun"
            if (this.weather.weather[0].main == "Snow") {
                this.HelloCardImg = this.HelloCardImg + "neige.gif);";
            } else if (
                this.weather.weather[0].main == "Rain" ||
                this.weather.weather[0].main == "Thunderstorm"
            ) {
                this.HelloCardImg = this.HelloCardImg + "pluie.gif);";
            } else {
                this.HelloCardImg = this.HelloCardImg + "soleil.gif);";
            }
            return this.HelloCardImg;
        },
        getDate() {
            const options = {
                weekday: "long",
                month: "long",
                day: "numeric"
            };
            return new Date().toLocaleDateString("fr-FR", options);
        },
        getTime() {
            var hours = new Date()
                .getHours()
                .toString()
                .padStart(2, "0");
            var minutes = new Date()
                .getMinutes()
                .toString()
                .padStart(2, "0");

            return hours + ":" + minutes;
        }
    }
};
</script>
