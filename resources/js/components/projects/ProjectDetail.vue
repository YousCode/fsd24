<template>
    <div class="page__project_detail projects__view" id="vue__project_detail" v-on:mouseover="scrollTo()">
    <div id="toclose" v-on:click="toclose()"></div>
      <ContextMenu ref="ContextMenu"></ContextMenu>
      <div id="context_menu_wrapper" class="main-container">
        <div id="project_login" v-show="isShared">
          <div class="login">
            <div class="project-header">
                <div class="project">{{ $t('project:') }}</div>
                <div class="project-name">{{ project.name }}</div>
            </div>
            <div class="login-header">
              {{ $t('enter-your-mail') }}
            </div>
            <div class="login-container">
              <div class="input-container">
                <div class="mail">
                  <span class="picto-envelop"></span>
                </div>
                <input type="email" name="login" v-model="email" class="form-field" :class="!validMail ? 'login-input-error-mail' : ''" :placeholder="$t('mail')">
              </div>
              <p v-show="!validMail" class="login-error-mail">{{ $t('mail-not-valid') }}</p>
              <button v-on:click="accessToProject()">{{ $t('confirm') }}</button>
            </div>
          </div>
        </div>
       <!-- <a href="/projects" class="action-link mb-20">
        	<span class="icon icon-long-arrow"></span> Retour vers Projets
        </a>-->
      <div class="row">
        <ProjectDetailInfos :_project="project" :_talents="talents" :_isShared="isShared" :_isClient="isClient"/>
      </div>
        <div class="row">
          <div class="col-xl-8" v-show="type == 'MONTH'">
            <ProjectDetailPlanningMonth :_project="project" :_talents="talents" :contextMenu="$refs.ContextMenu" :_isShared="isShared" :_isClient="isClient"/>
          </div>
          <div class="col-xl-8" v-show="type == 'DAYS'" style="background: #191032;">
            <ProjectDetailPlanning :_project="project" :user="user" :_isShared="isShared" :_isClient="isClient" />
          </div>
          <div id="project-right-component" class="col-xl-4" style="position: relative;">
            <div id="dropzone" class="fade well">
							<div class="text-center">
								<span class="icon icon-download-cloud"></span>
								<p>{{ $t('load-your-files') }}</p>
							</div>
      			</div>
            <ProjectMessenger :conversation="conversation" :user="user" :_isShared="isShared" :_isClient="isClient"/>
            <ProjectDetailExports :_project="project" :_isShared="isShared" :_isClient="isClient" :_path="path"/>
            <ProjectDetailDocuments :_project="project" :_conversation="conversation" :_isShared="isShared" :_isClient="isClient" :user="user"/>
            <ProjectDrive :_drive="drive" :_conversation="conversation" :_isShared="isShared" :_isClient="isClient" :user="user"/>
          </div>
        </div>
      </div>
      <ContactModule></ContactModule>
    </div>
</template>

<script>
  export default {
    props: ['_project', '_talents', '_conversation', 'user', 'is_client', '_isShared', '_kanban', '_path'],

    data() {
      return {
        project: null,
        talents: null,
        conversation: null,
        drive: null,
        type: 'MONTH',
        isContextMenuOpen: false,
        changePlanningKey: 0,
        isShared: false,
        isClient: false,
        email: '',
        validMail: true,
        isClose: false,
        path: false,
      }
    },

    created() {
      if (this._project) {
        this.project = this._project;
        this.project.formated_created_at = Vue.prototype.Global._convertDate(this.project.created_at);
        this.project.reformat_updated_at = Vue.prototype.Global._reformatDate(this.project.updated_at);
        this.project.reformat_created_at = Vue.prototype.Global._reformatDate(this.project.created_at);
        this.project.formated_date_deadline = Vue.prototype.Global._convertDate(this.project.date_deadline);
      }
      if (this._conversation) {
          this.conversation = this._conversation;
      }
      if (this._talents) {
          this.talents = this._talents;
      }
      if (this.conversation.proposition.drive) {
          this.drive = this.conversation.proposition.drive;
      }
      if (typeof this.user.isAuth !== "undefined" && !this.user.isAuth && this.project) {
        this.isShared = true;
      }
      if (this._path) {
        this.path = this._path;
      }
    },
    mounted() {
    },

    methods: {
        scrollTo() {
          if (this.type == 'DAYS') {
            if (typeof this.$children[3] !== "undefined" && typeof this.$children[3].$children[1] !== "undefined" && typeof this.$children[3].$children[1].scrollToToday !== "undefined") {
              this.$children[3].$children[1].scrollToToday();
            } else if (typeof this.$children[7] !== "undefined" && typeof this.$children[7].$children[1] !== "undefined" && typeof this.$children[7].$children[1].scrollToToday !== "undefined") {
              this.$children[7].$children[1].scrollToToday();
            }
          }
        },
        accessToProject() {
          const re = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
          this.validMail = re.test(this.email);
          if (this.validMail) {
            axios.post("/project/createContact", {
	      			email: this.email
	      		}).then(res => {
	      			if (res.success === false) {
	  	      		// Todo : Error
	  	      	} else {
                localStorage.setItem("shared_project_email", this.email);
                localStorage.setItem("shared_project_contact_id", res.data.contactId);
                this.$bus.$emit("UPDATE_CONTACT_ID", ({contactId: res.data.contactId}));
                let loginModal = document.getElementById('project_login');
                const header = document.getElementById("kolab-header");
                if (header && header.style) {
                        header.style.width = screen.width + 'px';
                        header.style.position = 'relative';      
                }
                if (loginModal) {
                  loginModal.style.display = "none";
                  if (this.$children && this.$children[2] && this.$children[2].$children[1]) {
                    this.$children[2].$children[1].scrollToToday();
                  }
                }
	  	      	}
	  	      }).catch(error => console.log(error));
          }
        },
        toclose() {
          let toclose = document.getElementById('toclose');
          this.isClose = false;
          if (typeof this.$children[1] != undefined && this.$children[1].showKanban) {
            this.$children[1].openKanban();
            this.isClose = true;
          }
          if (typeof this.$children[2] != undefined && this.$children[2].typeTaskOpen) {
            this.$children[2].openTypeTasks();
            this.isClose = true;
          }
          if (toclose.style.display == "block" && this.isClose) {
            toclose.style.display = "none";
          }
        }
    }
}
</script>
