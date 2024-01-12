<template>
	<input type="text"
		v-model="$parent.$parent.$parent.filters.talent_name"
		:placeholder="$t('search-user-planning')"
		class="filter-field js-search-planning"
	/>
</template>


<script>
export default {
    props: ['_workspace'],

    data(){
    	return {
			workspace: this._workspace
      	}
    },

    beforeMount(){
    },

    mounted(){
    	$(() => {
	    	$(".js-search-planning").autocomplete({
				  source: (request, response) => {
				    $.get('/api/talent', {
							term: request.term,
							mode: 'search',
							workspace: this.workspace
						})
				    .done((data) => {
				    	response(data.datas);
				    });
				  },
				  create: function() {
            $(this).data('ui-autocomplete')._renderItem = (ul, item) => {
            	return $('<li>')
								.data('item.autocomplete', item)
								.append('<a>' + item.firstname + ' ' + item.lastname +'</a>')
								.appendTo(ul);
            };
        	},
				  minLength: 2,
				  select: (event, ui) => {
					let talents = [ui.item.id, ui.item.firstname, ui.item.lastname];
					this.$bus.$emit('UPDATE_PLANNING_TYPE', 'MONTH');
					this.$bus.$emit("PLANNING_CALENDAR_UPDATE", {
                        talents: [talents],
                        talentData: [{id: talents[0] ? talents[0] : false, firstname: talents[1] ? talents[1] : false, lastname: talents[2] ? talents[2] : false}]
                    });
					this.$parent.filters.talents_id = [];
				  	this.$parent.filters.talents_id.push(ui.item.id);
				  	this.$parent.myPlanning = false;
					this.$bus.$emit('USER_PLANNING_TAB', {id: ui.item.id, firstname: ui.item.firstname, lastname: ui.item.lastname});
					this.$bus.$emit("PLANNING_CALENDAR_SEARCH_USER", {
						'talent' : ui.item
                    });
					this.$bus.$emit("PLANNING_FILTERS_SEARCH", {
						'talent' : ui.item,
                    });					
				  },
				  open: (event, ui) => {
				  	$('.ui-autocomplete').css({
				  		width: $('#planning-calendar').width(),
				  	});
				  },
				  close: (event, ui) => {
				  }
				});
	    });
    },

    methods: {
    }
}
</script>