//--------------
// Imports
//--------------
require('./bootstrap');
require('select2');
import { gsap } from "gsap";
      import { ScrollTrigger } from "gsap/ScrollTrigger";
      import { ScrollToPlugin } from "gsap/ScrollToPlugin";
import Vue from 'vue';
import VueI18n from 'vue-i18n'
import messages from './i18n/messages'
window.Vue = Vue;
Vue.use(VueI18n);
Vue.config.devtools = false
import typical from './components/explorer/ExplorerHome';

//--------------
// Variables
//--------------
window.Main = {};
var Main = window.Main;
Main.mode = document.querySelector('body').getAttribute('data-mode');

//--------------
// Vue JS Setup
//--------------

//-- Load components
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Utils code
require('./utils');

// Helpers
require('./helpers/global');
require('./helpers/alert');
require('./helpers/project');
require('./helpers/task');
require('./helpers/appointment');
require('./helpers/talent');
require('./helpers/exxport');
require('./helpers/file');
require('./helpers/drive');
require('./helpers/portfolios');


  // Create VueI18n instance with options
  const i18n = new VueI18n({
    locale: 'fr', // set locale
    messages, // set locale messages
  })

//-- Create a fresh Vue application
window.app = new Vue({
    i18n
}).$mount('#app');

window.changeWhenPopupIsOpen = false;

window.eventWhenPopupIsOpen = () => {
    $('body').on('select2:select', '.select2-hidden-accessible', (e) => {
        window.changeWhenPopupIsOpen = true;
    });
    $('body').on('select2:unselect', '.select2-hidden-accessible', (e) => {
        window.changeWhenPopupIsOpen = true;
    });
    $('body').on('keyup', 'input, textarea', (e) => {
        window.changeWhenPopupIsOpen = true;
    });
    /*$('body').on('keyup', 'input[type="text"], textarea, input[type="search"]', function(e) {
    if(!$(this).hasClass('no-caps')) {
        var parts = this.value.split(" ");
      for(var i = 0; i < parts.length; i++){
        var j = parts[i].charAt(0).toUpperCase();
        parts[i] = j + parts[i].substr(1);
      }
      this.value = parts.join(" ");
    }
    });*/
}


//--------------
// Classic methods
//--------------

/**
 * Init all scripts
 * @return {void}
 */
Main.init = function() {

    //----------------------
    // Common
    //----------------------
    var Common = require('./modules/common.js');
    window.eventWhenPopupIsOpen();

    //----------------------
    // Login
    //----------------------
    var $login = $('[data-module="login"]');
    if ($login.length > 0) {
        var Login = require('./modules/login.js');
    }

    //----------------------
    // User ID - Display "Hello" Screen
    //----------------------
    if (window.userId) {
        if (!localStorage.getItem('first_login')) {
            localStorage.setItem('first_login', window.userId);
            $('.js-welcome-screen').fadeIn();
            setTimeout(function() {
                $('.js-welcome-screen').fadeOut();
                $('body').addClass('is-ready');
            }, 2000);
        } else {
            $('body').addClass('is-ready');
        }
    } else {
        localStorage.removeItem('first_login');
        $('body').addClass('is-ready');
    }
    /*if(window.userId) {
        if(!localStorage.getItem('first_login')) {
            $('.js-first-login').fadeIn();
            localStorage.setItem('first_login', window.userId);
            //$('.js-welcome-screen').fadeIn();
            $('.js-first-login').fadeIn();
            console.log($('.js-welcome-screen').fadeIn())
            setTimeout(function(){
              //$('.js-welcome-screen').fadeOut();
              $('body').addClass('is-ready');
            }, 2000);
        } else {
            $('body').addClass('is-ready');
        }
    } else {
        localStorage.removeItem('first_login');
        $('body').addClass('is-ready');
    }*/

    //----------------------
    // Dark mode
    //----------------------
    // var Darkmode = require('./modules/darkmode.js');


    //----------------------
    // Notifications
    //----------------------
    $('body').on('click', '.js-open-notif', function() {
        $('.js-notifications').addClass('is-open');
    });
    $('body').on('click', '.js-close-notif', function() {
        $('.js-notifications').removeClass('is-open');
    });
    $(document).on('click', (e) => {
        if ($('.js-notifications').hasClass('is-open') && !e.target.matches('.js-notifications, .js-notifications *, .js-open-notif')) {
            $('.js-notifications').removeClass('is-open');
        }
    });


    //-- Notification nb
    setTimeout(function() {
        var nbNotif = $('.js-notifications .js-notifications-item').length;

        if (nbNotif > 0) {
            $('.js-notifications').remove();
        } else {
            $('.js-open-notif').html(nbNotif);
            $('.js-notifications').fadeIn();
            $('.js-open-notif').fadeIn();
        }
    }, 1000);

}


//--------------
// Classic execution
//--------------

$(function() {
    Main.init();
});

/*Vue.prototype.$shortcutActive = true;

document.body.addEventListener('click', function( event ){
    if(event && event.target && event.target.tagName && (event.target.tagName.toLowerCase() == 'input' || event.target.tagName.toLowerCase() == 'textarea')){
        Vue.prototype.$shortcutActive = false;
    } else {
        Vue.prototype.$shortcutActive = true;
	}
});


let createActive = false;
const commandKey = 91;
const controlKey = 17;
const deleteKey = 8;

window.addEventListener("keydown", checkKeyPressed, false);
window.addEventListener("keyup", preventCmdKeyUp, false);

function preventCmdKeyUp(evt) {
    if (evt.keyCode == commandKey || evt.keyCode == controlKey) {
        Vue.prototype.$shortcutActive = true;
    }
}

function checkKeyPressed(evt) {
    if (evt.keyCode == commandKey || evt.keyCode == controlKey || evt.keyCode == deleteKey) {
        Vue.prototype.$shortcutActive = false;
    }
    if (Vue.prototype.$shortcutActive) {
        switch (evt.keyCode) {
            case 67: //C
                if (!createActive) {
                    Vue.prototype.Global._setAction('CREATE');
                } else {
                    Vue.prototype.Global._setAction(null);
                }
                createActive = !createActive;
                break;
            case 68: //D
                let dashboardLink = document.getElementById("item-dashboard");
                if (dashboardLink) {
                    dashboardLink.classList.add('is-active');
                    dashboardLink.click();
                }
                break;
            case 76: //L
                let planningLink = document.getElementById("item-planning");
                if (planningLink) {
                    planningLink.classList.add('is-active');
                    planningLink.click();
                }
                break;
            case 80: //P
                let projectsLink = document.getElementById("item-projects");
                if (projectsLink) {
                    projectsLink.classList.add('is-active');
                    projectsLink.click();
                }
                break;
            case 82: //R
                let contactsLink = document.getElementById("item-contacts");
                if (contactsLink) {
                    contactsLink.classList.add('is-active');
                    contactsLink.click();
                }
                break;
            case 69: //E
                let explorerLink = document.getElementById("item-explorer");
                if (explorerLink) {
                    explorerLink.classList.add('is-active');
                    explorerLink.click();
                }
                break;
        }
    }
} */
