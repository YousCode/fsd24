"use strict";

var _axios = _interopRequireDefault(require("axios"));

var _vCalendar = _interopRequireDefault(require("v-calendar"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

// V-Calendar
Vue.use(_vCalendar["default"], {
  componentPrefix: 'v'
}); // Use <vc-calendar /> instead of <v-calendar />
// Select2

Vue.directive('select2', {
  inserted: function inserted(el) {
    $(el).on('select2:select', function () {
      var event = new Event('change', {
        bubbles: true,
        cancelable: true
      });
      el.dispatchEvent(event);
    });
    $(el).on('select2:unselect', function () {
      var event = new Event('change', {
        bubbles: true,
        cancelable: true
      });
      el.dispatchEvent(event);
    });
  }
}); //-- Global datas and methods

Vue.mixin({
  data: function data() {
    return {
      get _letters() {
        return ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
      },

      get _months() {
        return ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
      }

    };
  },
  methods: {
    getComponent: function getComponent(componentName) {
      var component = null;
      var parent = this.$parent;

      while (parent && !component) {
        if (parent.$options.name === componentName) {
          component = parent;
        }

        parent = parent.$parent;
      }

      return component;
    },
    getProjectProgressBar: function getProjectProgressBar(project) {
      var passed = new Date() - new Date(project.created_at);
      var todo = new Date(project.date_deadline) - new Date();
      var progress = passed / (passed + todo);
      progress = progress * 100;
      progress = progress > 100 ? 100 : progress;
      return progress;
    },
    getTaskProgressBar: function getTaskProgressBar(task) {
      //    console.log(task.end_date)
      //    console.log(task)
      var passed = new Date() - new Date(task.start_date);
      var todo = new Date(task.end_date) - new Date();
      var progress = passed / (passed + todo);
      var segment = Math.floor((passed + todo) / (1000 * 60 * 60 * 24));
      var today = moment().format('YYYY-MM-DD 19:00:00'); //console.log("tache : " + task.id + " : " + progress + " : " + segment)

      progress = progress * 100;
      progress = progress > 100 ? 100 : progress;
      return {
        progress: progress,
        segment: segment
      };
    },
    getHours: function getHours(datetime) {
      return moment(datetime).format('H');
    },
    getMinutes: function getMinutes(datetime) {
      return moment(datetime).format('mm');
    },
    getParamsUrl: function getParamsUrl() {
      var queryString = window.location.search;
      var urlParams = new URLSearchParams(queryString);
      return urlParams;
    },
    checkTaskDelay: function checkTaskDelay(task) {
      var _this = this;

      var today = moment().format('YYYY-MM-DD 19:00:00');

      if (today > task.end_date) {
        _axios["default"].get("api/notification/" + task.id, {}).then(function (res) {
          if (res.success === false) {
            console.log("false");
          }

          var datas = res.data.datas;

          if (datas.length < 1) {
            var notif = {
              message: "Voulez vous clôturer cette tâche ? (Elle le sera automatiquement à la fin de la journée) ",
              user_Id: _this.user.id,
              element_type: "TÂCHE",
              element_id: task.id
            };

            _axios["default"].post("api/notification", {
              notification: notif
            }).then(function (res) {
              if (res.success === false) {
                console.log("error");
              }

              $('div[id=tasks][data-id=' + task.id + ']').addClass('marked');
              setTimeout(function () {
                $('div[id=tasks][data-id=' + task.id + ']').removeClass('marked');
              }, 5000);

              _this.$bus.$emit("ACTION_CHANGED", {
                action: "CONGRATS",
                type: "TASK_OVER",
                element: task
              });

              var datas = res.data.datas;
            });
          }
        });
      }
    }
  }
}); //-- Filters

Vue.filter('dateFormat', function (date) {
  return moment(date).format('DD/MM/YYYY');
});
Vue.filter('monthLabel', function (key) {
  var months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
  return months[key - 1];
});
Vue.filter('truncate', function (text, length, clamp) {
  return text.slice(0, length) + (length < text.length ? clamp || '...' : '');
});
Vue.filter('slugify', function (value) {
  value = value.replace(/^\s+|\s+$/g, ''); // trim

  value = value.toLowerCase(); // remove accents, swap ñ for n, etc

  var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
  var to = "aaaaaeeeeeiiiiooooouuuunc------";

  for (var i = 0, l = from.length; i < l; i++) {
    value = value.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
  }

  value = value.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
  .replace(/\s+/g, '-') // collapse whitespace and replace by -
  .replace(/-+/g, '-'); // collapse dashes

  return value;
}); //-- Global events, MAGIC !

Vue.prototype.$bus = new Vue();