"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

// Global Helper
var Global = {
  install: function install(Vue, options) {
    Vue.prototype.Global = {};
    Vue.prototype.moment = moment;

    Vue.prototype.Global._setAction = function (action, datas) {
      //Vue.prototype.$bus.$emit('ACTION_CHANGED', {action: null });
      Vue.prototype.$bus.$emit('ACTION_CHANGED', _objectSpread({
        action: action
      }, datas));
    };

    Vue.prototype.Global._slugify = function (str) {
      str = str.replace(/^\s+|\s+$/g, ''); // trim

      str = str.toLowerCase(); // remove accents, swap ñ for n, etc

      var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
      var to = "aaaaaeeeeeiiiiooooouuuunc------";

      for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
      }

      str = str.replace(/[^a-z0-9 .-]/g, '') // remove invalid chars
      .replace(/\s+/g, '-') // collapse whitespace and replace by -
      .replace(/-+/g, '-'); // collapse dashes

      return str;
    };

    Vue.prototype.Global._removeTime = function (value) {
      var split = value.split('_');
      split.splice(0, 1);
      return split.join('_');
    };

    Vue.prototype.Global._convertDate = function (date) {
      return new Date(date).toLocaleDateString('fr-FR');
    };

    Vue.prototype.Global._formatPhone = function (phone) {
      if (phone) {
        return phone.replace(/[^\dA-Z]/g, '').replace(/(.{2})/g, '$1 ').trim();
      }
    };

    Vue.prototype.Global._showAccount = function (user) {
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {
        action: 'ACCOUNT',
        user: user
      });
    };

    Vue.prototype.Global._saveNotification = function (message, user_id, element_type, element_id) {
      axios.post("/api/notification", {
        'message': message,
        'user_id': user_id,
        'element_type': element_type,
        'element_id': element_id
      }).then(function (res) {
        if (res.success === false) {// Todo : Error
        } else {
          console.log('-- SAVE NOTIFICATION -- ');
          console.log(message);
        }
      })["catch"](function (error) {
        return console.log(error);
      });
    };

    Vue.prototype.Global._validateEmail = function (email) {
      var re = /\S+@\S+\.\S+/;
      return re.test(email);
    };

    var email_has_error = false;

    Vue.prototype.Global._checkForFields = function (form, step) {
      var errors = 0;
      var currentForm = form;

      if (step !== undefined) {
        currentForm = form + ' .step-' + step;
      }

      var requiredFields = $(currentForm + ' input.js-required');
      requiredFields.each(function (index, item) {
        $(item).removeClass('field-error');

        if ($(item).val() == '') {
          $(item).addClass('field-error');
          errors++;
        }
      }); // Select

      var requiredSelects = $(currentForm + ' select.js-required');
      requiredSelects.each(function (index, item) {
        var multiple = $(item).attr('multiple');
        $(item).next('.select2').removeClass('field-error');

        if (_typeof(multiple) !== (typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) && multiple !== false) {
          if ($(item).find('option:selected').length == 0) {
            $(item).next('.select2').addClass('field-error');
            errors++;
          }
        } else {
          if ($(item).select2('val') == null) {
            $(item).next('.select2').addClass('field-error');
            errors++;
          }
        }
      }); // Radio

      var requiredRadio = $(currentForm + ' .js-checkbox-list.js-required');
      requiredRadio.each(function (index, item) {
        $(item).removeClass('field-error');

        if ($(item).find('input:checked').length < 1) {
          $(item).addClass('field-error');
          errors++;
        }
      }); // Email

      var email = $(currentForm + ' input[type="email"].js-required');
      email.each(function (index, item) {
        var emailVal = $(item).val();

        if (emailVal !== '' && !Vue.prototype.Global._validateEmail(emailVal)) {
          $(item).addClass('field-error');

          if (email_has_error == false) {
            $('<p class="error-msg js-email-error">Veuillez entrer une adresse e-mail valide</p>').insertAfter($(item));
            email_has_error = true;
          }
        } else {
          $(item).next('.js-email-error').remove();
          email_has_error = false;
        }
      });
      return errors;
    };
  }
};
Vue.use(Global);