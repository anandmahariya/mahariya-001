$(function(){
    
    var true_img = base_url+'img/test-pass-icon.png';
    var false_img = base_url+'img/test-fail-icon.png';
    var loader = '<img class="loading" src="'+base_url+'img/spinner.gif" />';
    var loaderCenter = '<div class="loader">'+loader+'</div>';
    
    
/********************************************************************************
 * form validation
********************************************************************************/    
    
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
	$('#state').empty();
	$('#city').empty();
	$.ajax({
	    url: base_url + "sites/autocomplete/state/" + country,
	    dataType: "json",
	    success: function(response) {
		$('#state').append('<option value="*">All</option>');  
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
	$('#city').empty();
	$.ajax({
	    url: base_url + "sites/autocomplete/city/" + country + '/' + state,
	    dataType: "json",
	    success: function(response) {
		$('#city').append('<option value="*">All</option>');  
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
    $('body').on('DOMNodeInserted', '.alert', function(e) {
        $('.alert').delay(3000).fadeOut('slow');
    });
    $('.alert').delay(3000).fadeOut('slow');
    
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
    
});

    
    