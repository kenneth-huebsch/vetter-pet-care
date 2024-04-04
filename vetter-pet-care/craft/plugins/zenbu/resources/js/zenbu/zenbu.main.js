/**
 * Run the main query
 */
var queryRunning = null;
function runQuery(elem, theURL)
{
	if(queryRunning != null)
	{
		queryRunning.abort();
		queryRunning = null;
	}

	vm.queryRunning = true;

	var theAction = typeof theURL !== 'undefined' ? theURL : elem.closest("form").attr("action");

	var theData = {
					filter: vm.rows,
					sectionId: vm.sectionId,
					entryTypeId: vm.entryTypeId,
					limit: vm.limit,
					sort: vm.sort,
					orderby: vm.orderby,
					locale: vm.locale
				};
	theData[csrfTokenName] = csrfTokenValue;

	queryRunning = $.ajax({
					type:     "POST",
					url: theAction,
					data: theData,
					beforeSend: function(xhr, opts){
						vm.loading = true;
					},
					success: function(results){
							$("div.resultArea").html(results);
							$('.lightswitch').lightswitch();
							initSortableTable();
							vm.loading = false;
							vm.queryRunning = false;
							// $('div.lightswitch').unbind();
							return true;
						},
					error: function(results){
							// console.log(results.statusText);
							// console.log(results.responseText);
							$("div.resultArea").html(results.responseText);
							vm.loading = false;
							vm.queryRunning = false;
							return false;
					}
				});


} // END runQuery()

// --------------------------------------------------------------------


/**
 * Saving a Search
 */
function saveSearch()
{
	if(vm.saveSearchInputFieldValue === '')
	{
		vm.saveSearchButtonClicked = false;
		return;
	}

	var theAction = $('form#saveSearch').attr("action");
	var theData = {
					label: vm.saveSearchInputFieldValue,
					filter: vm.rows,
					sectionId: vm.sectionId,
					entryTypeId: vm.entryTypeId,
					limit: vm.limit,
					sort: vm.sort,
					orderby: vm.orderby,
					locale: vm.locale
				};
	theData[csrfTokenName] = csrfTokenValue;

	$.ajax({
			type:     "POST",
			url: theAction,
			data: theData,
			beforeSend: function(xhr, opts){
				vm.loading = true;
				vm.saveSearchButtonClicked = true;
			},
			success: function(results){
					// console.log(results);
					vm.savedSearches.push(results);
					vm.loading = false;
					vm.saveSearchButtonClicked = false;
					vm.saveSearchInputFieldValue = '';
				},
			error: function(results){
					console.log(results.statusText);
					console.log(results.responseText);
					vm.loading = false;
					vm.saveSearchButtonClicked = false;
			}
	});
} // END saveSearch()

// --------------------------------------------------------------------


/**
 * Fetch saved search filters
 */
function fetchSavedSearchFilters()
{
	var theAction = $('ul#savedSearchesList li a').eq(0).attr("href");

	$.ajax({
		type:       "POST",
		url:        theAction,
		data:       'searchId=' + vm.searchId + '&' + csrfTokenName + '=' + csrfTokenValue,
		dataType:   'json',
		success: function(results){
			$.each(results, function(i, result) {
				if(result.filterAttribute1 == 'sectionId')
				{
					indexSkip = 1;
					vm.sectionId = result.filterAttribute3;
					vm.sectionChanged();
				}

				if(result.filterAttribute1 == 'entryTypeId')
				{
					indexSkip = 2;
					vm.entryTypeId = result.filterAttribute3;
				}

				if((indexSkip == 1 && i == 2) || (indexSkip == 2 && i == 3))
				{
					// Clear the rows array since we're loading a new set.
					vm.rows = [vm.rows[0]];
				}

				if(! _.includes(['sectionId', 'entryTypeId', 'limit', 'orderby', 'locale'], result.filterAttribute1))
				{
					processRows(result.filterAttribute1, result.filterAttribute2, result.filterAttribute3, result.filterAttribute4, i - indexSkip);
				}

				if(result.filterAttribute1 == 'limit')
				{
					vm.limit = result.filterAttribute2;
					// $('select[name="limit"]').val(result.filterAttribute2);
				}

				if(result.filterAttribute1 == 'orderby')
				{
					vm.orderby = result.filterAttribute2;
					vm.sort    = result.filterAttribute3;
					// $('select[name="orderby"]').val(result.filterAttribute2);
					// $('select[name="sort"]').val(result.filterAttribute3);
				}

				if(result.filterAttribute1 == 'locale')
				{
					vm.locale = result.filterAttribute2;
				}
			}); // END $.each()
		},
		error: function(results){
				console.log(results.statusText);
				console.log(results.responseText);
				vm.loading = false;
		}
	});
} // END fetchSavedSearchFilters()

// --------------------------------------------------------------------


/**
 * Process the vm.rows based on various conditions
 * @param  {string} firstVal  First field value in row
 * @param  {string} secondVal Second field value in row
 * @param  {string} thirdVal  Third field value in row
 * @param  {string} fourthVal Fourth field value in row (if present)
 * @param  {int} i         Row index
 * @return {void}           vm.rows array is updated
 */
function processRows(firstVal, secondVal, thirdVal, fourthVal, i)
{
	// Unbinding datepicker to rebind it later
	// because v-if seems to bind on the wrong element
	// in some cases.
	$('input').datepicker('destroy');

	switch(firstVal)
	{
		case 'status':
			secondFilterType = 'is';
			thirdFilterType  = 'status';
		break;
		case 'id':
			secondFilterType = 'is_isnot';
			thirdFilterType  = 'text';
		break;
		case 'author':
			secondFilterType = 'is_isnot';
			thirdFilterType  = 'author';
		break;
		case 'postDate': case 'expiryDate': case 'dateCreated': case 'dateUpdated':
			secondFilterType = 'date';
			thirdFilterType  = _.includes(['inthelast', 'inthenext'], secondVal) ? 'dateDropdown' : 'datepicker';
			thirdFilterType  = _.includes(['between'], secondVal) ? 'dateRange' : thirdFilterType;
		break;
		case 'title': case 'uri':
			secondFilterType = 'contains';
			thirdFilterType  = 'text';
		break;
		default:
			secondFilterType = 'contains_plus_empty';
			thirdFilterType  = 'text';
			if(_.isNumber(Number(firstVal)))
			{
				// Date
				if(_.find(vm.customFields, function(f) {
					return f.id == firstVal && f.type == 'Date';
				}))
				{
					secondFilterType = 'date';
					thirdFilterType  = _.includes(['inthelast', 'inthenext'], secondVal) ? 'dateDropdown' : 'datepicker';
					thirdFilterType  = _.includes(['between'], secondVal) ? 'dateRange' : thirdFilterType;
				}

				// Lightswitch
				if(_.find(vm.customFields, function(f) {
					return f.id == firstVal && f.type == 'Lightswitch';
				}))
				{
					secondFilterType = 'is';
					thirdFilterType  = 'lightswitch';
				}

				// Categories
				if(_.find(vm.customFields, function(f) {
					return f.id == firstVal && f.type == 'Categories';
				}))
				{
					secondFilterType = 'is_isnot_w_contains_doesnotcontain_labels';
					thirdFilterType  = 'categories';
				}

				// Dropdown/Radio
				if(_.find(vm.customFields, function(f) {
					if(f.id == firstVal && _.includes(['Dropdown', 'RadioButtons'], f.type))
					{
						vm.customFieldDropdownOptions[f.id] = f.settings.options;
						return true;
					}
					return false;
				}))
				{
					secondFilterType = 'is_isnot';
					thirdFilterType  = 'dropdown';
				}

				// PositionSelect
				if(_.find(vm.customFields, function(f) {
					if(f.id == firstVal && _.includes(['PositionSelect'], f.type))
					{
						vm.customFieldDropdownOptions[f.id] = f.settings.options;
						return true;
					}
					return false;
				}))
				{
					secondFilterType = 'is_isnot';
					thirdFilterType  = 'positionSelect';
				}
			}
	}

	secondFilterList = vm.secondFilters[secondFilterType];

	// Set secondVal to first second filter option
	// if secondVal doesn't match anymore
	if(! _.includes(_.keys(secondFilterList), secondVal))
	{
		secondVal = _.first(_.keys(secondFilterList));
	}

	if(_.isUndefined(fourthVal))
	{
		fourthVal = '';
	}

	var theRow = {
		first: firstVal,
		second: secondVal,
		third: thirdVal,
		fourth: fourthVal,
		secondFilterList: secondFilterList,
		thirdFilterType: thirdFilterType
	};

	vm.rows[i] = theRow;

	// Make sure datepicker is bound when DOM is refreshed
	vm.$nextTick(function () {
		initDatepicker();
	});

	return;

	vm.$set(vm.rows, i, theRow);
} // END processRows()

// --------------------------------------------------------------------


/**
 * Update Craft main navigation (left)
 * Using jQuery since the nav Twig code mangles
 * Vue.js variables
 */
function updateMainCmsNavUrls()
{
	for(i = 1; i <= 2; i++)
	{
		params = '?sectionId=' + vm.sectionId + '&entryTypeId=' + vm.entryTypeId;
		newUrl = $('#nav-zenbu').find('a').eq(i).attr('href').replace(/\?.*/g, '') + params;
		$('#nav-zenbu').find('a').eq(i).attr('href', newUrl);
		window.history.pushState(null, document.title, params);
	}
} // END updateMainCmsNavUrls()

// --------------------------------------------------------------------


/**
 * Start sortable table
 */
function initSortableTable()
{
	/**
	*   ==============================
	*   Sortable
	*   ==============================
	*/

	/**
	*  Make table rows sortable!
	*  Return a helper with preserved width of cells
	*/
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	$("table.settingsTable tbody").sortable({
		cancel: 'table.settingsTable tr th, .not-sortable',
		start: function(e, ui) {
		},
		stop: function(event, ui) {
			if($(this).closest('table').hasClass('displaySettings'))
			{
				$(this).find('tr').each(function(i) {
					$(this).find('td').eq(2).html(i + 1);
					$(this).find('td').eq(3).find('input, select, textarea').each(function(i2) {
						newName = $(this).attr('name').replace(/\[([0-9].*?)\]\[(.*?)\]/g, '[' + i + '][$2]');
						$(this).attr('name', newName);
					});
					$(this).find('td').eq(5).find('input, select, textarea').each(function(i2) {
						newName = $(this).attr('name').replace(/\[([0-9].*?)\]\[(.*?)\]/g, '[' + i + '][$2]');
						$(this).attr('name', newName);
					});
				});
			}
		},
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
		helper: "clone",
		revert: 200,
		cursor: 'move',
		distance: 15,
		opacity: 0.9,
	});
} // END initSortableTable

// --------------------------------------------------------------------
/**
 * Adding a collapsible sidebar
 */

function handleSidebarDisplay()
{
	//    ----------------------------------------
	//    If this special <style> element is found,
	//    eg. if the sidebar state pulled from cache
	//    is closed and therefore load this element,
	//    remove the styles on first interaction
	//    with the sidebar toggle.
	//    ----------------------------------------
	$('style[data-hide-sidebar-on-load]').remove();

	if(vm.sidebarHidden == true)
	{
		// Hide the sidebar
		$("div.content").addClass('hiding-sidebar');
		$('a.show-sidebar-button').removeClass("showing").show();
		saveSidebarState('closed');
	}
	else
	{
		// Show the sidebar
		$("div.content").removeClass('hiding-sidebar');
		$('a.show-sidebar-button').addClass("showing").show();
		saveSidebarState('open');
	}

}

/**
 * Save the state of the sidebar
 * @param  {string} state The state of the sidebar
 * @return {void}       Console output on error
 */
function saveSidebarState(state)
{
	$.ajax({
		type:   "POST",
		url:    'saveSidebarState',
		data:   'state=' + state + '&' + csrfTokenName + '=' + csrfTokenValue,
		success: function(results){
			},
		error: function(results){
			console.log(results);
		}
	});
} // END saveSidebarState

/**
 * Datepicker
 */
function initDatepicker()
{
	$("body").delegate("input.datepicker", "focusin", function(e) {
		var datepickerElem = $(this);
		$(this).datepicker({
			dateFormat: 'yy-mm-dd',
			onSelect: function() {
				var rowIndex = $(this).closest('tr').index();
				if($(this).hasClass('from') || $(this).hasClass('single-date'))
				{
					vm.rows[rowIndex].third = $(this).val();
				}

				if($(this).hasClass('to'))
				{
					vm.rows[rowIndex].fourth = $(this).val();
				}
				vm.updateRows(rowIndex);
				vm.runQuery();
			}
		});
	});
}

// --------------------------------------------------------------------

// --------------------------------------------------------------------
$(document).ready(function ()
{

	initSortableTable();
	initDatepicker();
	/**
	 * Run a query on page load if
	 * filter data is available
	 */
	if( $("div.filters").hasClass('has-cached-filter-data') )
	{
		runQuery($("div.filters"));
	}

	/**
	 * Prevent the filter form from submitting on
	 * pressing Enter key
	 */
	$("form#zenbuSearchFilter").submit(function(e){
		e.preventDefault();
	});


	$("body").delegate("a.pagination", "click", function (e) {
		e.preventDefault();
		runQuery($("div.filters"), $(this).attr('href'));
	});


	/**
	 * Select All
	 */
	$("body").delegate("[name=selectAll]", "click", function(e){
		var checked  = $(this).is(':checked');
		var targetClass = $(this).attr('class');
		if(checked === true)
		{
			$("[name*=elementIds]").prop("checked", true);
			$("[class*='" + targetClass + "']").prop("checked", true);
		}
		else
		{
			$("[name*=elementIds]").prop("checked", false);
			$("[class*='" + targetClass + "']").prop("checked", false);
		}
	});


	/**
	 * Main - Show action button when entries are selected
	 */
	$("body").delegate("[name*=elementIds], [name=selectAll]", "click", function(e){
		var totalSelected = $("[name*=elementIds]:checked").length;
		if(totalSelected >= 1)
		{
			$(".actionDisplay").show();
		}
		else
		{
			$(".actionDisplay").hide();
		}
	});


	/**
	 * Modal window
	 */
	$("body").delegate(".showModal", "click", function(e){
		e.preventDefault();
		var $div = $(this).next("div.outerModal").html();
		var myModal = new Garnish.Modal($div, {
			resizable: true,
			onHide: function(){
				myModal.destroy();
				$(".modal-shade").remove();
			}
		});
	});


	/**
	 * Image Modal window
	 */
	$("body").delegate(".imageModal", "click", function(e){
		e.preventDefault();
		var imageUrl = $(this).attr("href");
		var image = $(this).next("div.outerModal").find("div.modal").html('<iframe src="'+imageUrl+'" class="imageiframe"></iframe>');
		var $div = $(this).next("div.outerModal").html();
		var originalWidth = $(this).attr("data-original-width");
		var originalHeight = $(this).attr("data-original-height");
		var modalWindow = Garnish.Modal.extend({
		   desiredWidth: originalWidth,
		   desiredHeight: originalHeight,
		});

		var myModal = new modalWindow($div, {
			resizable: true,
			onShow: function(){
				// Garnish's Modal doesn't go lower than ~600px in width.
				// This means even smaller images have wasted whitespace.
				// The following readjusts the modal to be smaller and centered
				$("div.modal").css({
						'width':        originalWidth,
						'min-width':    originalWidth + 'px',
						'height':       originalHeight,
						'min-height':   originalHeight + 'px',
						'left':         Math.round((Garnish.$win.width() - originalWidth) / 2)
					});
			},
			onHide: function(){
				myModal.destroy();
				$(".modal-shade").remove();
			}
		});
	});

	/**
	 * Display Settings: New/Edit label toggle if
	 * something is typed in customDisplayStringTemplate
	 */
	$('body').delegate('textarea[name^="customDisplayStringTemplate"]', 'keyup', function(e) {
		textLength = $(this).val().length;
		if(textLength > 0)
		{
			$(this).closest('.outerModal').prev('.showModal').find('.new').hide();
			$(this).closest('.outerModal').prev('.showModal').find('.edit').show();
		}
	});


	/**
	 * Fetch "New Entry" button
	 */
	function fetchNewEntryButton(sectionId)
	{
		newEntryButton = $("div.filterFields").find("div.new-entry-sectionId-" + sectionId).clone();
		$(".new-entry").empty().html(newEntryButton);

		// Activate the dropdown menu for "New Entry"
		// after selecting "All Sections"
		if(sectionId === 0)
		{
			newEntryButton.find("div.menu-dummy").removeClass("menu-dummy").addClass("menu");
			newEntryButton.find("div.savedSearchActions").menubtn();
		}
	} // END fetchNewEntryButton

	// --------------------------------------------------------------------


	/**
	 * Update Tab URLs
	 */
	function updateTabUrls()
	{
		var sectionId = $("div.filters select#section_select").val();
		var entryTypeId = $("div.filters div.entryType_select.sectionId_"+sectionId).find("select").val();

		// Tabs: Craft 2.4 and under
		$("nav").find("a.tab").each(function(){
			rawUrl           = $(this).attr('href');
			originalUrl      = rawUrl.replace(/\?(.*)/g, '');

			// Modify only Tab Urls that use GET variables (i.e. not "saved searches")
			if(rawUrl.indexOf("searches") == -1)
			{
				sectionIdParam   = typeof sectionId !== 'undefined' && sectionId !== '' ? '?sectionId=' + sectionId : '';
				entryTypeIdParam = typeof entryTypeId !== 'undefined' && entryTypeId !== '' ? '&entryTypeId=' + entryTypeId : '';
				$(this).attr('href', originalUrl  + sectionIdParam + entryTypeIdParam);
			}
		});

		// Subnavigation: Craft 2.5+
		$("li#nav-zenbu").find("a").each(function(){
			rawUrl           = $(this).attr('href');
			originalUrl      = rawUrl.replace(/\?(.*)/g, '');

			// Modify only Tab Urls that use GET variables (i.e. not "saved searches")
			if(rawUrl.indexOf("searches") == -1)
			{
				sectionIdParam   = typeof sectionId !== 'undefined' && sectionId !== '' ? '?sectionId=' + sectionId : '';
				entryTypeIdParam = typeof entryTypeId !== 'undefined' && entryTypeId !== '' ? '&entryTypeId=' + entryTypeId : '';
				$(this).attr('href', originalUrl  + sectionIdParam + entryTypeIdParam);
			}
		});
	} // END updateTabUrls

	// --------------------------------------------------------------------


	/**
	 * Show or don't show "-" remove
	 * filter rule button
	 */
	function toggleRemoveFilterRuleDisplay()
	{
		if($("tr.filter-params").length > 1)
		{
			$(".removeFilterRule").show();
		}
		else
		{
			$(".removeFilterRule").hide();
		}
	} // END toggleRemoveFilterRuleDisplay

	// --------------------------------------------------------------------





	/**
	 * Confirmation box
	 */
	$("body").delegate("a.action", "click", function(e){
		e.preventDefault();

		var param          = $(this).attr('data-param');
		var value          = $(this).attr('data-value');
		var confirmMessage = $(this).attr('data-confirm');
		var returnUrl      = $(this).attr('data-returnUrl');

		if( typeof confirmMessage !== 'undefined' )
		{
			var answer = confirm( confirmMessage );

			if(answer)
			{
				var theAction = $("form#resultList").attr('action');
				var theData   = $("form#resultList").serialize() + '&' + param + '=' + value;

				$.ajax({
					type:     "POST",
					url: theAction,
					data: theData,
					//dataType: 'json',
					success: function(results){
							 runQuery($("div.filters"));
							 if($("div.actionDisplay").hasClass('hideAfterAction'))
							 {
								$("div.actionDisplay").hide();
							 }

							 if( typeof returnUrl !== 'undefined' )
							 {
								window.location = returnUrl;
							 }
						},
					error: function(results){
							console.log(results.statusText);
							console.log(results.responseText);
					}
				});
			} else {
				return false;
			}
		}
	});
});