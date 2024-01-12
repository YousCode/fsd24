import axios from 'axios';
import VCalendar from 'v-calendar';

// V-Calendar
Vue.use(VCalendar, { componentPrefix: 'v' }); // Use <vc-calendar /> instead of <v-calendar />

// Select2
Vue.directive('select2', {
    inserted(el) {
        $(el).on('select2:select', () => {
            const event = new Event('change', { bubbles: true, cancelable: true });
            el.dispatchEvent(event);
        });

        $(el).on('select2:unselect', () => {
            const event = new Event('change', { bubbles: true, cancelable: true })
            el.dispatchEvent(event)
        })
    },
});

//-- Global datas and methods
Vue.mixin({
    data: function() {
        return {
            get _letters() {
                return ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
            },
            get _months() {
                return ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            },
        }
    },
    methods: {
        getComponent(componentName) {
            let component = null
            let parent = this.$parent
            while (parent && !component) {
                if (parent.$options.name === componentName) {
                    component = parent
                }
                parent = parent.$parent
            }
            return component
        },
        getProjectProgressBar(project) {
            var passed = new Date() - new Date(project.created_at);
            var todo = new Date(project.date_deadline) - new Date();
            var progress = passed / (passed + todo);

            progress = progress * 100;
            progress = progress > 100 ? 100 : progress;

            return progress;
        },
        getTaskProgressBar(task) {
            //    console.log(task.end_date)
            //    console.log(task)
            var passed = new Date() - new Date(task.start_date);
            var todo = new Date(task.end_date) - new Date();
            var progress = passed / (passed + todo);
            var segment = Math.floor((passed + todo) / (1000 * 60 * 60 * 24))
            var today = moment().format('YYYY-MM-DD 19:00:00')
                //console.log("tache : " + task.id + " : " + progress + " : " + segment)
            progress = progress * 100;
            progress = progress > 100 ? 100 : progress;

            return { progress, segment };
        },
        getHours(datetime) {
            return moment(datetime).format('H');
        },
        getMinutes(datetime) {
            return moment(datetime).format('mm');
        },
        getParamsUrl() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);

            return urlParams;
        },
        /*checkTaskDelay(task) {
            let today = moment().format('YYYY-MM-DD 10:00:00');
            if (today > task.end_date) {
                axios.get("api/notification/" + task.id, {

                }).then(res => {
                    if (res.success === false) {
                        console.log("false")
                    }
                    let datas = res.data.datas
                    if (datas.length < 1) {
                        let notif = {
                            message: "Voulez vous clôturer cette tâche ? (Elle le sera automatiquement à la fin de la journée) ",
                            user_Id: this.user.id,
                            element_type: "TÂCHE",
                            element_id: task.id
                        };
                        axios.post("api/notification", {
                            notification: notif
                        }).then(res => {
                            if (res.success === false) {
                                console.log("error")
                            }
                            $('div[id=tasks][data-id=' + task.id + ']').addClass('marked');
                            setTimeout(function() {
                                $('div[id=tasks][data-id=' + task.id + ']').removeClass('marked');
                            }, 5000);
                            this.$bus.$emit("ACTION_CHANGED", {
                                action: "CONGRATS",
                                type: "TASK_OVER",
                                element: task
                            });
                            let datas = res.data.datas
                        })
                    }
                })

            }
        }*/
    }
});

//-- Filters
Vue.filter('dateFormat', function(date) {
    return moment(date).format('DD/MM/YYYY');
});

Vue.filter('monthLabel', function(key) {
    var months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ]

    return months[key - 1];
});

Vue.filter('truncate', function(text, length, clamp) {
    return text.slice(0, length) + (length < text.length ? clamp || '...' : '');
});

Vue.filter('slugify', function(value) {
    value = value.replace(/^\s+|\s+$/g, ''); // trim
    value = value.toLowerCase();

    // remove accents, swap ñ for n, etc
    var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
    var to = "aaaaaeeeeeiiiiooooouuuunc------";
    for (var i = 0, l = from.length; i < l; i++) {
        value = value.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    value = value.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

    return value;
});

//-- Global events, MAGIC !
Vue.prototype.$bus = new Vue();
