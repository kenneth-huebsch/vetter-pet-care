var vm = new Vue({
	delimiters: ['${', '}'],
	el: 'main#main',
	data: data,
	methods: {
		addFilterRow: function(index) {
			this.updateRows(index);
			this.rows.splice(index + 1, 0, this.rows[index]); // adds row after current row
			this.updateRows(index + 1);
			this.runQuery(true);
		},
		removeFilterRow: function(index) {
			if(this.rows.length > 1)
			{
				this.rows.splice(index, 1);
			}
			this.runQuery(true);
		},
		sectionChanged: function(firstRunAfterMount) {

			if(this.sectionId != 0)
			{

				// Situation 1.
				// ------------
				// On first run (i.e. when mounted)
				// we know the locale (default value or
				// taken from stored filter data), so no need
				// to reset it here. Reset happens when
				// changing sections.
				//
				// Situation 2.
				// ------------
				// If the current locale is not in the list
				// of locales for the current section, set to first locale
				// of section.
				if(_.isUndefined(firstRunAfterMount) || _.isUndefined(_.find(this.sections[this.sectionId].locales, { 'name': this.locale })))
				{
					this.locale            = this.sections[this.sectionId].locales[0].name;
				}

				// Reset entryTypeId if it's not a valid sectionId/entryTypeId combination
				if(_.isUndefined(this.firstFilters[this.sectionId][this.entryTypeId]))
				{
					this.entryTypeId       = this.sections[this.sectionId].entryTypes[0].id;
				}

				this.firstFilterList   = this.firstFilters[this.sectionId][this.entryTypeId];
				this.orderbyFilterList = this.orderbyFilters[this.sectionId][this.entryTypeId];
			}
			else
			{
				this.entryTypeId       = 0;
				this.firstFilterList   = this.firstFilters[0][0];
				this.orderbyFilterList = this.orderbyFilters[0][0];
			}

			//  ----------------------------------------
			//  To reset or not reset orderby
			//  ----------------------------------------
			var doNotResetOrderby = false; // Will reset orderby unless found in list

			_.each(this.orderbyFilterList, function(list) {
				if(_.includes(_.keys(list), data.orderby))
				{
					doNotResetOrderby = true;
				}
			});

			if(doNotResetOrderby === false)
			{
				this.orderby = 'postDate';
			}

			this.updateMainCmsNavUrls();
			this.runQuery();

		},
		entryTypeChanged: function() {
				// Using v-if means this will get
				// triggered when element is on page
				this.firstFilterList   = this.firstFilters[this.sectionId][this.entryTypeId];
				this.orderbyFilterList = this.orderbyFilters[this.sectionId][this.entryTypeId];
				this.updateMainCmsNavUrls();
				this.runQuery();
		},
		updateRows: function(i, changedField, thing) {
				firstVal          = vm.rows[i].first;
				secondVal         = vm.rows[i].second;
				thirdVal          = vm.rows[i].third;
				fourthVal         = vm.rows[i].fourth; // Eg. date range
				// Reset third value if going from something => inthelast/next
				if(_.includes(['inthelast', 'inthenext'], secondVal) && changedField == 'second')
				{
					thirdVal = vm.rows[i].third = '1';
					fourthVal = vm.rows[i].fourth = '';
				}

				// Reset third value if going from inthelast/next => datepicker
				// ------------------------------------------------------------
				// If fetching a saved search you don't want to clear the third/fourth values
				// because of the uninevitable change in the changedField. In such as case, 
				// skip the resetting.
				if(! _.includes(['inthelast', 'inthenext'], secondVal) && changedField == 'second' && ! vm.fetchingSavedSearch)
				{
					thirdVal = vm.rows[i].third = '';
					fourthVal = vm.rows[i].fourth = '';
				}

				// Set third value to a default empty string or null
				// value if the update makes thirdVal undefined
				if(_.isUndefined(thirdVal))
				{
					if(_.includes(['author', 'status'], firstVal))
					{
						thirdVal = 'null';
					}
					else
					{
						thirdVal = '';
					}
				}

				if(! _.isUndefined(thirdVal) && thirdVal == '' && _.includes(['author', 'status'], firstVal))
				{
					thirdVal = 'null';
				}

				processRows(firstVal, secondVal, thirdVal, fourthVal, i);

				// If the field update was triggered by a saved search fetch,
				// fetchingSavedSearch is true. Let's reset this back to false now.
				vm.fetchingSavedSearch = false;
		},
		clearThirdFilter: function(index)
		{
			// if(_.includes(['datepicker', 'dateDropdown', 'dateRange'], vm.rows[index].thirdFilterType))
			// {
			// 	vm.rows[index].third = '';
			// }
			if(! vm.fetchingSavedSearch)
			{
				if(_.includes(['status', 'author'], vm.rows[index].thirdFilterType))
				{
					vm.rows[index].third = 'null';
				}
				else
				{
					vm.rows[index].third = '';
				}
			}

			// If the field update was triggered by a saved search fetch,
			// fetchingSavedSearch is true. Let's reset this back to false now.
			vm.fetchingSavedSearch = false;
		},
		runQuery: _.debounce(function(event)
		{
			//	----------------------------------------
			//	Current thinking about debounce vs throttle:
			//	We don't want to trigger every X ms (throttle)
			//	as this would trigger unwanted queries while Vue models
			//	(and their dropdowns) are changing, triggering this method
			//	again. The simpler debounce just waits until typing/input
			//	is over before running the query. Result: less queries fired.
			//
			//	Also, this.queryRunning is added as another way
			//	to help avoid running a query if one is
			//	still running.
			//	----------------------------------------
			if(this.queryRunning == false)
			{
				this.$nextTick(function () {
					runQuery($("div.filters"));
				});
			}
		}, 500),
		saveSearch: function()
		{
			saveSearch();
		},
		fetchSavedSearchFilters: function()
		{
			this.fetchingSavedSearch = true;
			this.$nextTick(function () {
				fetchSavedSearchFilters();
				
			});
		},
		updateMainCmsNavUrls: function()
		{
			for(i = 1; i <= 2; i++)
			{
				params = '?sectionId=' + this.sectionId + '&entryTypeId=' + this.entryTypeId;
				newUrl = $('#nav-zenbu').find('a').eq(i).attr('href').replace(/\?.*/g, '') + params;
				$('#nav-zenbu').find('a').eq(i).attr('href', newUrl);
				window.history.pushState(null, document.title, params);
			}
		},
		handleSidebarDisplay: function()
		{
			this.sidebarHidden = this.sidebarHidden === false ? true : false;
			this.$nextTick(function () {
				handleSidebarDisplay();
			});
		}
	},
	computed: {
		sectionIdUrlParam: function() {
			return this.sectionId != 0 ? '?sectionId=' + this.sectionId : '';
		},
		entryTypeIdUrlParam: function() {
			return this.entryTypeId != 0 ? '?entryTypeId=' + this.entryTypeId : '';
		}
	},
	mounted: function() {
		this.sectionChanged(true); // This runs this.runQuery() as well
	},
	watch: {
	}
});