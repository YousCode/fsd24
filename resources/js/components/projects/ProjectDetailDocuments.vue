<template>
	<div class="project-detail-modal block project-messenger-conversation explorer-messenger-card explorer-messenger-drive-card">
		<button type="button" v-on:click="toggleDocument()" class="card-header">
			<div class="picto-container"><span class="picto-medias"></span></div>
			<h1 class="card-title"><span v-if="files.length < 2">Média partagé</span> <span v-else>Médias partagés</span> ({{ files.length }})</h1><span v-show="newFile" class="notification"></span>
			<div class="card-dropdown-button">
				<span v-if="isShow" class="picto-explorer-dropdown-up"/>
				<span v-else class="picto-explorer-dropdown-down"/>
			</div>
		</button>

		<div v-show="isShow" class="card-body" style="padding: 10px 0 0px 0;">
			<div v-if="state == 'DELETE'" class="mb-30 js-toggle-wrapper">
				<div class="delete-actions">
					<button type="button" class="action-link" v-on:click="state = null">Annuler</button>
					<button :disabled="deletedItems.length == 0" v-on:click="File._delete(deletedItems)" class="button icon-button">
						<span class="icon icon-delete"></span>
					</button>
				</div>
			</div>
			<input type="file" id="fileupload" ref="files" class="fileupload" multiple hidden />
			<div v-if="files.length == 0" class="no-items" v-on:click="onClickUpload()">
				<!-- <div id="dropzone" class="fade well">
					<div class="text-center">
						<span class="icon icon-download-cloud"></span>
							<p>Chargez vos fichiers ici</p>
					</div>
      			</div> -->
				<div class="text-center">
					<span class="picto-medias-empty"></span>
					<div>
						<p>Déposez vos fichiers</p>
						<span>Chargez vos fichiers en les déposant</span>
						<br/>
						<span>dans cette fenêtre (maximum 4mo)</span>
					</div>
				</div>
        	</div>
			<div v-else class="row">
				<div class="col-md-12">
					<div class="media-container">
						<div class="simple-table__header">
							<div class="simple-table__row media-row">
								<div class="media-col name">Nom</div>
								<div class="media-col date">Date</div>
								<div class="media-col extension">Type de fichier</div>
							</div>
						</div>
						<div class="actions-boxx">
							<button class="upload-button" v-on:click="onClickUpload()">
								<span class="icon icon-plus" style="margin-right:10px;"></span><span>Uploader un ou des fichier(s)</span>
							</button>
						</div>
						<div class="simple-table__body media-content">
							<div class="media-progress-bar">
								<div class="close" v-on:click="closeMediaUpload()">
									<span class="picto-delete-export-line"></span>
								</div>	
								<div class="media-infos">
									<span id="media-filename"></span><span id="media-percentage"><span id="media-prctg">0</span>%</span>
								</div>
								<div class="progress-bar">
									<span class="progress-bar__inner"></span>
									<span class="progress-bar__background"></span>
								</div>
							</div>
							<div v-for="file in files" v-bind:id="'media-item-' + file.id" class="media-item" v-on:mouseover="mediaHover(file.id, true, _isShared, _isClient, _user, file.created_by)" v-on:mouseleave="mediaHover(file.id, false, _isShared, _isClient, _user, file.created_by)" >
								<span class="notification row-notification" v-show="showRowNotification(file.id)" :style="{right:0}"></span>
								<div class="wrapper-checkbox" v-if="state == 'DELETE'">
									<input type="checkbox" v-model="deletedItems" :value="file.id" :id="'document-' + file.id" />
									<span class="checkbox"></span>
								</div>
								<div class="name">
									<label :for="'document-' + file.id" v-if="state == 'DELETE'">{{ Global._removeTime(file.name) }}</label>
									<span v-else>{{ file.name }}</span>
								</div>
								<div class="date">
									<span>{{ moment(file.created_at).format("DD/MM/YYYY") }}</span>
								</div>
								<div class="extension">
									<span>{{ file.extension }}</span>
								</div>
								<a :href="file.url_download" class="actions-container download">
									<span class="icon icon-download"></span>
								</a>
								<div class="actions-container delete" v-show="!_isShared || (_isShared && contactId == file.created_by)" v-on:click="File._delete([file.id])">
									<span class="icon icon-delete"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
  </div>
</template>

<script>
export default {
    props: ['_project', '_conversation', '_isShared', '_isClient', 'user'],

    data() {
      return {
      	project: this._project,
      	files: [],
      	state: null,
      	deletedItems: [],
		isShow: false,
		filesToUploadFromMessenger: [],
		conversation: this._conversation,
		newFile: false,
		currentFilesIds: [],
		newFilesIds: [],
		_user: false,
		contactId: false,
      }
    },
	 mounted() {
    	this.getFiles();
    	this.$bus.$on('FILE_ADD_OR_UPDATE', () => {
			this.getFiles(true);
			this.state = null;
      	});

		this.$bus.$on('UPDATE_CONTACT_ID', (data) => {
			if (data && data.contactId) {
				this.contactId = data.contactId;
			}
      	});

		  window.Echo.private('kolab').listen('.file-added', (e) => {
            this.getFiles(true);
            this.newFile = true;
        });

		window.Echo.private('kolab').listen('.file-deleted', (e) => {
            this.getFiles(true);
            this.newFile = true;
        });
		$(() => {
			this.uploadFiles();
		});
		if (this.user) {
			this._user = this.user;
		}
		localStorage.removeItem('shared_project_contact_id');
    },
	created() {
    },
    methods: {
    	async getFiles(isUpdated = false) {
    		await axios.get('/api/project/'+ this.project.id +'/file')
    			.then(res => {
		      	if (res.success === false) {
		      		// Error
		      	} else {
		      		this.files = res.data.datas;
					if (!isUpdated) {
						this.currentFilesIds = this.files.map(a => a.id);
					} else {
						this.newFilesIds = this.files.map(a => a.id);
						this.newFilesIds = this.newFilesIds.filter(x => !this.currentFilesIds.includes(x));
					}
		      	}
		      }).catch(error => console.log(error));
    	},
    	setState(value){
    		this.state = value;
    		$('.js-toggle-content').fadeOut();
    	},
		toggleDocument() {
			this.$parent.$children[4].isShow = false;
            this.$parent.$children[5].isShow = false;
            this.$parent.$children[7].isShow = false;
            this.isShow = !this.isShow;
			if (this.newFile) {
				this.newFile = !this.newFile;
			}
			//this.newFile = false;
		},
		onClickUpload() {
			if(document.getElementById("fromMessenger")) {
                document.getElementById("fromMessenger").value = 0;
            }
			$('#fileupload').click();
		},
		uploadFiles() {
			$("#project-right-component").bind('dragover', function (e) {
    		var dropZone = $('#dropzone'),
        	timeout = window.dropZoneTimeout;
				if (timeout) {
					clearTimeout(timeout);
				} else {
					dropZone.addClass('in');
				}
				var hoveredDropZone = $(e.target).closest(dropZone);
				dropZone.toggleClass('hover', hoveredDropZone.length);
				window.dropZoneTimeout = setTimeout(function () {
				window.dropZoneTimeout = null;
				dropZone.removeClass('in hover');
				}, 100);
			});
			$('#fileupload').fileupload({
		      url: '/transfert/index.php?folder=project',
		      dataType: 'json',
		      autoUpload: true,
		      acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf|doc)$/i,
			  dropZone: $('#dropzone'),
			  singleFileUploads: true,
		      submit: (e, data) => {
		      	data.files[0].uploadName = Vue.prototype.Global._slugify(data.files[0].name);
		      }
			  }).on('fileuploadadd', (e, data) => {
				var mediaFilename = '';
				if (data.files && typeof data.files[0] !== "undefined" && data.files[0].name) {
					mediaFilename = Vue.prototype.Global._slugify(data.files[0].name);
					$('#media-filename').html(mediaFilename);
				} 
				
			  }).on('fileuploadprocessalways', (e, data) => {
			  }).on('fileuploadprogressall', (e, data) => {
				
				const updateCount = () => {
					if (document.getElementById("media-prctg")) {
						const counter = document.getElementById("media-prctg");
						const count = +counter.innerHTML;
						const target = parseInt(100);
						const increment = 1;
						if (count < target) {
							counter.innerHTML = count + increment;
							setTimeout(updateCount, 10);
						} else {
							counter.innerHTML = target;
						}
					}
				};

				updateCount();

		      var progress = parseInt(data.loaded / data.total * 100, 10);
      		  $('.progress-bar__inner').css('width', progress + '%');
			  }).on('fileuploaddone', (e, data) => {
		      var files = data.result.files;
			  var uploadError = [];
			  files.forEach(file => {
				if (file.error && file.error.length > 0 && file.error == "File is too big") {
					uploadError.push(file);
					this.$bus.$emit('ACTION_CHANGED', {
                        action: 'CONGRATS',
                        type: 'FILE_TOO_BIG'
                    });
				} else {
					$('.media-progress-bar').fadeIn();
				}
			  });
			  let contactId = localStorage.getItem('shared_project_contact_id');
			  if (uploadError.length == 0) {
				data.result.files[0].previewUrl = URL.createObjectURL(data.files[0]);
			  	this.filesToUploadFromMessenger.push(data.result.files[0]);
				var fromMessenger = document.getElementById("fromMessenger").value;

				if (document.getElementById("preview-container") && data.files[0] && fromMessenger == 1) {
					var uploadPreviewContainer = document.getElementById("preview-container");
					uploadPreviewContainer.style.display = "flex";
				} else {
					axios.post("/api/explorer/missions/conversations/" + this.conversation.id + "/messages", {
                    params: {
                        message: '§',
						contactId: contactId ? contactId : false,
                    }
                    }).then(res => {
                        if(files) {
                            var messageId = res.data;
                            axios.post('/project/'+ this.project.id +'/file', {
	    			            project: this.project.id,
	    			            files: files,
                                messageId: messageId,
								isShared: this._isShared,
								contactId: contactId ? contactId : false
                            }).then(res => {
                                if (res.success === false) {
                                    // Error
                                } else {
                                    this.files = res.data.datas;
									if (this.files && this.files[this.files.length - 1] && this.files[this.files.length - 1].id) {
										let fileId = this.files[this.files.length - 1].id;
										if (!this.newFilesIds.includes(fileId)) {
											this.newFile = true;
											this.newFilesIds.push(fileId);
										}
									}
                                    var mediaAction = "ADD_MEDIA";
                                    this.$bus.$emit('ACTION_CHANGED', {
                                        action: 'CONGRATS',
										type: mediaAction
                                    });
									this.filesToUploadFromMessenger = [];
									this.$bus.$emit('FILES_FROM_MESSENGER');
                                }
                            }).catch(error => console.log(error));
                        }

                        this.messageToPost = '';
                        //this.getConversationMessage();
                        //this.scrollDown();
                    }).catch(error => console.log(error));
				}
			  }

		      setTimeout(() => {
				//$('.progress-bar__inner').css('width', '0%');
			    }, 800);
		      setTimeout(() => {
			      $('.media-progress-bar').fadeOut();
			    }, 1000);
				setTimeout(() => {
					if (document.getElementById("media-prctg")) {
						var mediaPercentage = document.getElementById("media-prctg");
				  		mediaPercentage.innerHTML = 0;
					}
			    }, 2000);
			  }).on('fileuploadfail', function (e, data) {
			    console.log('-- UPLOAD FAIL');
			    console.error(e);
			    setTimeout(() => {
				    $('.progress-bar__inner').css('width', '0%');
				    $('.media-progress-bar').fadeOut();
			    }, 1000);
			  });
		},
		mediaHover: function(mediaId, active, isShared, isClient, user, createdById) {
			let contactId = localStorage.getItem('shared_project_contact_id') ? localStorage.getItem('shared_project_contact_id')  : false;
            var mediaElement = document.getElementById('media-item-' + mediaId);
                if ((active && isShared && contactId == createdById) || (!isShared && active)) {
                    mediaElement.classList.add('media-hover');
                } else {
                    mediaElement.classList.remove('media-hover');
                }
        },
		closeMediaUpload: function() {
			var mediaProgressBar = document.getElementsByClassName('media-progress-bar');
			if (typeof mediaProgressBar[0] !== "undefined") {
				mediaProgressBar[0].style.display = "none";
			}
		},
		showRowNotification(fileId) {
			if (this.newFilesIds.length == 0) {
                return false;
            } else if (this.newFilesIds.includes(fileId)) {
                return true;
            }

            return false;
        }
     }
}
</script>