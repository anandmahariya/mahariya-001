$(function(){
    
    var true_img = base_url+'img/test-pass-icon.png';
    var false_img = base_url+'img/test-fail-icon.png';
    var loader = '<img class="loading" src="'+base_url+'img/spinner.gif" />';
    var loaderCenter = '<div align="center">'+loader+'</div>';
    
    
/********************************************************************************
 * form validation
********************************************************************************/    
    $('#BlockasBlockasoprForm').validate({
        rules : {
            "data[Blockas][domains]" : {
                required: true
            }
        }
    });
    
    //Form Blocker IP
     $.validator.addMethod('IP4Checker', function(value) {
	var ip = /^(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])){3}$/;
	    return value.match(ip);
	}, 'Invalid IP address');
     
    $('#BlockipBlockipoprForm').validate({
        rules : {
            "data[Blockip][name]" : {
                required: true
            },
	    "data[Blockip][start]" : {
                required: true,
		IP4Checker: true
            },
	    "data[Blockip][end]" : {
                required: true,
		IP4Checker: true
            }
        }
    });
    
    //Form utility whois form
    $('#searchWhoisForm').validate({
        rules : {
            "data[search][ip]" : {
                required: true,
		IP4Checker: true
            }
        }
    });
    
    //Form blockip search
    $('#searchBlockipForm').validate({
        rules : {
            "data[search][ip]" : {
                IP4Checker: true
            }
        }
    });
    
    //Form siteopr form
    $("#SiteSiteoprForm").validate({
        rules : {
            "data[Site][name]" : {
                required: true,
		url: true
            }
        },
        messages: {
            "data[Site][name]" : {
                required: 'Please insert valid URL.',
		url: 'Please insert valid URL.'
            }
        }
    });
    
    //Form Replacer form
    $("#ReplacerReplaceroprForm").validate({
        rules : {
            "data[Replacer][type]" : {
                required: true
            },
	    "data[Replacer][name]" : {
                required: true
            },
	    "data[Replacer][content]" : {
                required: true
            }
        },
        messages: {
            "data[Replacer][type]" : {
                required: 'Required Field.'
            },
	    "data[Replacer][name]" : {
                required: 'Required Field.'
            },
	    "data[Replacer][content]" : {
                required: 'Required Field.'
            }
        }
    });
    
    $('#ReplacerReplaceroprForm #ReplacerType').on('change',function(){
	replacerfieldsarrange();
    });
    
    $('#ReplacerReplaceroprForm #ReplacerScriptType').on('change',function(){
	$('#ReplacerName').val($('#ReplacerScriptType').val());
    });
    
    function replacerfieldsarrange(){
	var tmp = $('#ReplacerReplaceroprForm #ReplacerType').val();
	switch (tmp){
	    case 'script' :
		$('.replacer_name').hide();
		$('.script_type').show();
		$('#ReplacerName').val($('#ReplacerScriptType').val());
		switch($('#ReplacerScriptType').val()){
		    case 'redirect' :
			    $('.ReplacerContent_label').html('Redirect Url');
			break;
		    
		}
		break;
	    default :
		$('.replacer_name').show();
		$('.script_type').hide();
		$('.ReplacerContent_label').html('Container Value');
		break;
	}
    }
    
    replacerfieldsarrange();
    
   
    //Form Restricted Zone form
    $("#RestrictedZoneRestrictedzoneoprForm").validate({
        rules : {
            "data[RestrictedZone][country]" : {
                required: true
            }
        },
        messages: {
            "data[RestrictedZone][country]" : {
                required: 'Required Field.'
            }
        }
    });
    
    //Fillup state select on change of country 
    $('#country').on('change',function(){
	var country = $(this).val();
	$('#state').html('<option value="*">All</option>'); 
	$('#city').html('<option value="*">All</option>'); 
	$.ajax({
	    url: base_url + "sites/autocomplete/state/" + country,
	    dataType: "json",
	    success: function(response) {
		$.each(response,function(key, value){
		    $('#state').append('<option value=' + value.code + '>' + value.name + '</option>');  
		});
	    }
	});
    });
    
    //Fillup city select on change of state 
    $('#state').on('change',function(){
	var country = $('#country').val();
	var state = $(this).val();
	$('#city').html('<option value="*">All</option>'); 
	$.ajax({
	    url: base_url + "sites/autocomplete/city/" + country + '/' + state,
	    dataType: "json",
	    success: function(response) {
		$.each(response,function(key, value){
		    $('#city').append('<option value=' + value.id + '>' + value.city + '</option>');  
		});
	    }
	});
    });
    
    
    //change status
    $('.changestatus').on('click',function(){
	ele = $(this);
	$(this).after(loader);
	var url = '';
	switch($(this).attr('type')){
	    case 'site' :
		url = base_url + "sites/setstatus/validsite/" + $(this).attr('id') + '/' +$(this).attr('value');
		break;
	    case 'zone' :
		url = base_url + "sites/setstatus/validzone/" + $(this).attr('id') + '/' +$(this).attr('value');
		break;
	    case 'restrictedzone' :
		url = base_url + "sites/setstatus/restrictedzone/" + $(this).attr('id') + '/' +$(this).attr('value');
		break;
	    case 'replacer' :
		url = base_url + "sites/setstatus/replacer/" + $(this).attr('id') + '/' +$(this).attr('value');
		break;
	}

	$.ajax({
	    url: url,
	    dataType: "json",
	    success: function(response) {
		if (response.status === true) {
		    if(response.value == 1){
			ele.children().attr('src',true_img);
		    }else{
			ele.children().attr('src',false_img);
		    }
		    ele.attr('value',response.value);
		}
		$('.loading').remove();
	    }
	});
	return false;
    });
    
    //Hide alert message after some time
    /*
    $('body').on('DOMNodeInserted', '.alert', function(e) {
        $('.alert').delay(3000).fadeOut('slow');
    });
    $('.alert').delay(3000).fadeOut('slow');
    */
    
    //Confirm box
    $('.confirm').on('click',function(){
        var url = $(this).attr('href');
        var message = $(this).attr('message') ? $(this).attr('message') : 'Are you sure?';
        bootbox.confirm(message,function(result) {
            if(result){
                location.href = url;
            }
        });
        return false;
    });
    
    //*************************************************************************************
    if ($("body").data("title") === "Dashboard-analytics") {
	renderAnalyticsCharts();
    }
    //Analytics JS
    //Form Dashboard Analytics
    $('#analyticsDate').datepicker({
        numberOfMonths: 1,
	changeMonth: true,
	changeYear: true,
        dateFormat: 'dd/mm/yy'
    });
    
    $('#analyticsAnalyticsForm .form-control').on('change',function(){
	renderAnalyticsCharts();
    });
    
    
    function renderAnalyticsCharts(){
	$('#request-analytics-chart').html(loaderCenter);
	$('#request-analytics-chart-vip').html(loaderCenter);
	$('#request-analytics-chart-city').html(loaderCenter);
	$('#analytics-unique-request-chart').html(loaderCenter);
	$('#request-analytics-chart-os').html(loaderCenter);
	renderAnalyticsReqChart();
	renderAnalyticsReqChart_Vip();
	renderAnalyticsCityChart();
	renderAnalyticsUniqueRequestChart();
	renderAnalyticsOSChart();
    }
    
    
    
    function renderAnalyticsReqChart_Vip(){
	var postData = $('#analyticsAnalyticsForm').serialize();
	$.ajax({
	    type: "POST",
	    url: base_url + 'dashboard/renderchart/analytic_request_vip',
	    data: postData, 
	    success: function(response){
		var data = $.parseJSON(response);
		$('#request-analytics-chart-vip').html('');
		var line = new Morris.Line({
			    element: 'request-analytics-chart-vip',
			    resize: true,
			    data:  data.data,
			    xkey: 'y',
			    ykeys: ['total','valid','in-valid','proxy'],
			    labels: data.labels,
			    lineColors: data.color,
			    xLabelAngle : 20,
			    parseTime : false,
			    hideHover: 'auto'
			});
	    }
        });
    }
    
    function renderAnalyticsCityChart() {
	var postData = $('#analyticsAnalyticsForm').serialize();
	$.ajax({
	    type: "POST",
	    url: base_url + 'dashboard/renderchart/analytic_city',
	    data: postData, 
	    success: function(response){
		$('#request-analytics-chart-city').html(response);
	    }
        });
    }
    
    function renderAnalyticsReqChart(){
	var postData = $('#analyticsAnalyticsForm').serialize();
	$.ajax({
	    type: "POST",
	    url: base_url + 'dashboard/renderchart/analytic_request',
	    data: postData, 
	    success: function(response){
		var data = $.parseJSON(response);
		$('#request-analytics-chart').html('');
		var line = new Morris.Line({
			    element: 'request-analytics-chart',
			    resize: true,
			    data:  data.data,
			    xkey: 'y',
			    ykeys: data.labels,
			    labels: data.labels,
			    lineColors: data.color,
			    xLabelAngle : 35,
			    parseTime : false,
			    hideHover: 'auto'
			});
	    }
        });
    }
    
    function renderAnalyticsUniqueRequestChart(){
	var postData = $('#analyticsAnalyticsForm').serialize();
	$.ajax({
	    type: "POST",
	    url: base_url + 'dashboard/renderchart/analytic_unique_request_chart',
	    data: postData, 
	    success: function(response){
		$('#analytics-unique-request-chart').html(response);
		$(".knob").knob();
	    }
        });
    }
    
    function renderAnalyticsOSChart(){
	var postData = $('#analyticsAnalyticsForm').serialize();
	$.ajax({
	    type: "POST",
	    url: base_url + 'dashboard/renderchart/analytic_os_chart',
	    data: postData, 
	    success: function(response){
		var data = $.parseJSON(response);
		$('#request-analytics-chart-os').html('');
		var donut = new Morris.Donut({
		    element: 'request-analytics-chart-os',
		    resize: true,
		    colors: data.color,
		    data: data.data,
		    hideHover: 'auto'
		});
	    }
        });
    }
    
    //*************************************************************************************
    //Restricted Zone script
    $('#restrictedzone').accordion({
	header: '.tmpheader',
	autoHeight: false,
	clearStyle: true,
	heightStyle: "content" ,
	beforeActivate: function( event, ui ) {
	    var country = ui.newHeader.attr('data-country');
	    var state = ui.newHeader.attr('data-state');
	    $('.containerRestrictedZone').html('');
	    $.ajax({
		type: "GET",
		url: base_url + 'sites/restrictedzoneopr/list/'+country+'/'+state,
		success: function(response){
		    var data = $.parseJSON(response);
		    var table = '<div class="row">';
		    $.each(data, function(key,val){
			//console.log(val);
			table += '<div class="form-group col-lg-3"><input type="checkbox"/>&nbsp;'+val.City.city+'</div>';
		    });
		    table += '</div>';
		    $('#collapse-'+state+' .containerRestrictedZone').append(table);
		}
	    });
	}
    });
    //*************************************************************************************
    //Dashboard js
    //call first time when page load
    if ($("body").data("title") === "Dashboard-index") {
	$('#request-chart').html(loaderCenter);
	$('#requestIndexForm select').on('change',function(){
	    renderReqChart();
	});
	renderReqChart();
	
	$('#topip-chart').html(loaderCenter);
	renderTopIp();
	
	renderClickData();
	renderStateChart();
	
	setInterval(function(){
	    renderReqChart();
	    renderTopIp();
	    renderClickData();
	    renderStateChart();
	}, 20000);
    }
    
    function renderStateChart(){
	$.ajax({
	    type: "GET",
	    url: base_url + 'dashboard/renderchart/statechart',
	    success: function(response) {
		var data = $.parseJSON(response);
		$('#state-chart').html('');
		var donut = new Morris.Donut({
		    element: 'state-chart',
		    resize: true,
		    colors: data.color,
		    data: data.data,
		    hideHover: 'auto'
		});
	    }
	});
    }
    
    function renderClickData(){
	$.ajax({
	    type: "GET",
	    url: base_url + 'dashboard/renderchart/clickdata',
	    success: function(response) {
		var data = $.parseJSON(response);
		$('.tot-reqest').html(data.total);
		$('.tot-valid').html(data.valid);
		$('.tot-invalid').html(data.invalid);
	    }
	});
    }
    
    function renderTopIp(){
	$.ajax({
	    type: "GET",
	    url: base_url + 'dashboard/renderchart/topip',
	    success: function(response) {
		var data = $.parseJSON(response);
		$('#topip-chart').html('');
		var donut = new Morris.Donut({
		    element: 'topip-chart',
		    resize: true,
		    colors: data.color,
		    data: data.data,
		    hideHover: 'auto'
		});
	    }
	});
    }
    
    function renderReqChart(){
	var postData = $('#requestIndexForm').serialize();
	$.ajax({
	    type: "POST",
	    url: base_url + 'dashboard/renderchart/request',
	    data: postData, 
	    success: function(response){
		var data = $.parseJSON(response);
		$('#request-chart').html('');
		var line = new Morris.Line({
			    element: 'request-chart',
			    resize: true,
			    data:  data.data,
			    xkey: 'y',
			    ykeys: ['valid', 'invalid','total'],
			    labels: ['Valid','In-valid','Total'],
			    lineColors: data.color,
			    xLabelAngle : 35,
			    parseTime : false,
			    hideHover: 'auto'
			});
	    }
        });
    }
    
    
    /************************** Dashboard search ***********************/
    //Datepicker script video index page
    $('#searchStartdate').datepicker({
        numberOfMonths: 1,
	changeMonth: true,
	changeYear: true,
        dateFormat: 'dd/mm/yy',
        onSelect: function(selected) {
            $("#searchEnddate").datepicker("option", "minDate", selected)
        }
    });
    
    $('#searchEnddate').datepicker({
        numberOfMonths: 1,
	changeMonth: true,
	changeYear: true,
        dateFormat: 'dd/mm/yy'
    });
    $("#searchEnddate").datepicker("option", "minDate", $('#searchStartdate').val());
    
    
    /************************ Utility whois form ****************************/
    $('#searchWhoisForm').submit(function(){
	$('.loading').html(loader);
	$('#searchWhoisForm .btn').addClass('disabled');
	var postData = $('#searchWhoisForm').serialize();
	$.ajax({
	    type: "POST",
	    url: base_url + 'utility/whoisapi/general',
	    data: postData, 
	    success: function(response){
		$('.loading').html('');
		$('#searchWhoisForm .btn').removeClass('disabled');
		$('#whoiscontainer').html(response);
	    }
        });
	return false;
    });
    
    /********************** Analytic panal JS ****************************/
    if ($("body").data("title") === "Analytics-request") {
		$('#request-analytics-chart-hour-wise').html(loaderCenter);
		$('#request-analytics-chart-vip').html(loaderCenter);
		$('#request-analytics-chart-os').html(loaderCenter);
		$('#request-analytics-unique-request-chart').html(loaderCenter);
		$('#request-analytics-chart-location').html(loaderCenter);
		
		renderAnalyticsAllCharts();

		
		/*
		$('#requestIndexForm select').on('change',function(){
		    renderReqChart();
		});
		renderReqChart();
		
		$('#topip-chart').html(loaderCenter);
		renderTopIp();
		
		renderClickData();
		renderStateChart();
		
		setInterval(function(){
		    renderReqChart();
		    renderTopIp();
		    renderClickData();
		    renderStateChart();
		}, 20000);
		*/
    }

    function renderAnalyticsAllCharts(){
		renderAnalyticRequestChart();
		renderAnalyticRequestChart();
	}

	//first chart request on hours wise
	function renderAnalyticRequestChart(){
		var postData = $('#analyticsRequestForm').serialize();
		$.ajax({
		    type: "POST",
		    url: base_url + 'analytics/renderchart/key_request_chart_hour_wise',
		    data: postData, 
		    success: function(response){
			var data = $.parseJSON(response);
			$('#request-analytics-chart-hour-wise').html('');
			var line = new Morris.Line({
				    element: 'request-analytics-chart-hour-wise',
				    resize: true,
				    data:  data.data,
				    xkey: 'y',
				    ykeys: ['Request'],
				    labels: ['Request'],
				    lineColors: data.color,
				    xLabelAngle : 35,
				    parseTime : false,
				    hideHover: 'auto'
				});
		    }
        });
	}
});

    
    