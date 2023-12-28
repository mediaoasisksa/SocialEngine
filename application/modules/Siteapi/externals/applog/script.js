en4.core.runonce.add(function () {
	scriptJquery('.filter-div').on('click', function ($this) {
		container = scriptJquery(this).parent().parent();
		container.find('.filter-div.active').removeClass('active');
		scriptJquery(this).addClass('active');
		showClass = '.' + scriptJquery(this).get('data-class');
		baseClass = '.row';
		if (baseClass != showClass) {
			container.find(baseClass).each( function(el) { el.hide(); });
		}
		container.find(showClass).each( function(el) { el.show(); });
	})

	scriptJquery('.clear-logs').on('click', function ($this) {
		url = en4.core.baseUrl + scriptJquery(this).attr('data-url');
		scriptJquery(this).parent().parent().find('.row').remove();
		request = scriptJquery.ajax({
			url: url,
			data: {},
			success: function (responseJSON) {
				if (!responseJSON.status) {
					alert('Something went wrong.');
				}
			}
		});
		//request.send();
	})
})

function copyText(target) {
	var range = window.getSelection().getRangeAt(0);
	range.selectNode(target);
	window.getSelection().addRange(range);
	document.execCommand("copy");
}

function processJsonData(responseArray, container) {
	responseArray.each(function(response, index) {

		url = new Element('a', {'html' : response.request_url, 'href' : response.request_url, target: '_blank'});
		description = 'Error Code: ' + response.error_code
		if (response.message) {
			description += '%0A%0A%0A' +  'Error Message: ' + response.message ;
		}
		paramString = '';
		params = (typeof response.request_params == 'object' && response.request_params) ? response.request_params : {} ;
		Object.keys(params).each(function(item) {
			paramString += item + ': ' + params[item] + '\n';
		});

		additional_info = '';
		additional_info = response.request_url ? 'Request URI: ' + response.request_url.split('?')[0] + '%0A%0A' : '';
		additional_info =  response.request_method ? additional_info + 'Request Method: ' + response.request_method + '%0A%0A' : additional_info;
		additional_info = (paramString) ? additional_info + 'Request Param: ' + '%0A%0A'+ paramString.replace(/(?:\r\n|\r|\n)/g, '%0A') + '%0A%0A' : additional_info;
	
		summary = response.error_type + ' In ' + window.location.hostname;
		mantisUrl =  'http://bugtracker.bigsteptech.in/bug_report_page.php?description='+ description +'&category=Android App&reproducibility=100&summary='+ summary +'&additional_info='+ additional_info;
		assignMantis = response.error && seaoDeveloper ? '<a class="mantis-button tdnone" href="' + mantisUrl +'" target="_blank">ASSIGN MANTIS</a>' : '';

		showDropdown = response.error_type == 'APP Issue';
		type = response.error_type == 'API Issue' ? 'api-issue' : 'app-issue';
		rowClasses = response.error ? 'row-issue ' + type : 'row-okay'; 

		row = new Element('div', {class: 'row ' + rowClasses}).injectSeaoCustom(container);
		row .adopt(new Element('div')
			.adopt(new Element('div', {class: 'inline-block width80 truncate'}).adopt(url))
			.adopt(new Element('div', {class: 'inline-block width20 center padlr15', }).addClass(response.error ? 'fa cross red' : 'fa check green'))
			);

		if (response.error) {
			row.adopt(new Element('div')
				.adopt(new Element('div', {class: 'posa fa error-message red'}))
				.adopt(new Element('div', {class: 'inline-block width80 truncate padl40', html: response.error_code}))
				.adopt(new Element('div', {class: 'inline-block width20 center', html: response.error_type}))
				)
			.adopt(new Element('div')
				.adopt(new Element('div', {class: 'posa fa message'}))
				.adopt(new Element('div', {class: 'inline-block width80 break-word padl40', html: response.message}))
				.adopt(new Element('div', {class: 'inline-block width20 center'}))
				)
		}
		row.adopt(new Element('div', {class: 'mtop30 pointer'})
				.adopt(new Element('div', {class: 'inline-block width80' , html: assignMantis}))
				.adopt(new Element('div', {class: 'inline-block width20 center row-toggle fa-large circle-down' + (showDropdown ? ' hide' : '')})
				.addEvent('click', function($this) {
					el = scriptJquery(this).parent('.row').find('.row-bottom')
					el.hasClass('hide') ? el.removeClass('hide') :  el.addClass('hide');
				}))
				)
		rowBottom = new Element('div', { class: 'row-bottom hide'}).injectSeaoCustom(row);
		rowBottom
		.adopt(new Element('div')
			.adopt(new Element('div', {class: 'inline-block width20', html: 'Time'}))
			.adopt(new Element('div', {class: 'inline-block copy-target width80', html: response.time }))
			)
		.adopt(new Element('div')
			.adopt(new Element('div', {class: 'inline-block width20', html: 'Request Method'}))
			.adopt(new Element('div', {class: 'inline-block copy-target width70', html: response.request_method }))
			.adopt(new Element('div', {class: 'inline-block fa copy width10 center'})
				.addEvent('click', function($this) {
					copyText(scriptJquery(this).parent().find('.copy-target'));
				})
				)
			)
		.adopt(new Element('div')
			.adopt(new Element('div', {class: 'inline-block width20', html: 'Request Uri'}))
			.adopt(new Element('div', {class: 'inline-block copy-target width70 break-word', html: response.request_url }))
			.adopt(new Element('div', {class: ' inline-block fa copy width10 center'})
				.addEvent('click', function($this) {
					copyText(scriptJquery(this).parent().find('.copy-target'));
				})
				)
			)
		.adopt(new Element('div')
			.adopt(new Element('div', {class: 'inline-block width20', html: 'Request Params'}))
			.adopt(new Element('div', {class: 'inline-block copy-target width70', html: paramString.replace(/(?:\r\n|\r|\n)/g, '<br />')
		}))
			.adopt(new Element('div', {class: 'inline-block fa copy width10 center'})
				.addEvent('click', function($this) {
					copyText(scriptJquery(this).parent().find('.copy-target'));
				})
				)
			)
	})
}
