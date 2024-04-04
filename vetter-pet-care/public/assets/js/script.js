(function ($) {
	/* doc ready */

	$(function(){
		$(".numpets").hide();
//        $("#charge-form").hide();
		$(".savecontinuepet:enabled").click(function(e){
        e.preventDefault();
      if($(this).data('flow') == "schedule"){
        var selected = $(this).data('num');
        var numChecked = $("input:checkbox:checked").length;

        if((numChecked < selected) || (numChecked > selected) || !$(".confirm_pet").hasClass('confirmed')){
                alert('The number of pets scheduled for the appointment must match the number checked in the "Select Pets" section. Please adjust and press Confirm before pressing the Continue button.');
                  return false;
              } else {
  			//$.post("api/setTab",{tab:"2"},function(data){
  			//	window.location.href = "/billing";
         // });
          $.ajax({
                async: false,
                type: 'POST',
                url: "api/setTab",
                data: { tab : 2},
                success: function(data) {
                  window.location.href = "/billing";
                }
            });
        }
      } else {
      window.location.href = "/billing";
      }
			});

    $("#tos-modal").hide();
    $("#tos").click(function(e){
      e.preventDefault();
      $("#tos-modal").show();
      $("body").css('overflow','hidden');
    });
    $(".tos-Modal-close").click(function(){
        $("#tos-modal").hide();
        $("body").css('overflow','auto');
    });

		$("#modnum").click(function(e){
			e.preventDefault();
			if($(".numpets").is(":visible")){
				$(".numpets").hide();
			} else {
				$(".numpets").show();
			}
		});
		$(".petSelected").change(function(e){
			var selected = $(this).data('num');
      $(".confirm_pet").removeClass("confirmed");
      $(".pet-error").html('');
			var numChecked = $("input:checkbox:checked").length;
			if(numChecked == selected){
				$("input:checkbox:not(:checked)").prop('disabled', true);
			}
			if(numChecked < selected){
					$("input:checkbox:not(:checked)").prop('disabled', false);
			}

		});
		$(".confirm_pet").click(function(e){
			var selected = $(this).data('num');
			var numChecked = $("input:checkbox:checked").length;
      var pets = '';
      if(selected < 2){
        pets = 'pet.';
      }else{
        pets = 'pets.';
      }
			if(numChecked < selected){
					$(".pet-error").html('<p>You must select ' + selected + ' '+pets);
					return false;
			} else if(numChecked > selected){
							$('input:checkbox').prop('checked', false);
								$(".pet-error").html('<p>You can only select ' + selected + ' '+pets);
								return false;
			}else{}
      localStorage['confirmedpet'] = "yes";
		});
    if($(".confirm_pet").length && ($(".confirm_pet").hasClass('confirmed')) && localStorage['confirmedpet'] == "yes"){
      $('html, body').animate({
        scrollTop: $("#jumptoemr").offset().top
      }, 1300);
      localStorage.removeItem('confirmedpet');
    }

		$(".numpets").change(function(e){
			e.preventDefault();
			var curr = $("#numSelected").val();
			var num = $(this).val();
			console.log(curr + " " + num);
			if(curr == 1 && num > 1){
				alert('Hi! We want to make sure our vets can meet the needs of your pets. We are redirecting you back to the Schedule so that we can ensure a more appropriate appointment window.');

				 var id = localStorage.getItem('bookingid');
         var data = {
         entryId: id,
             };
         $.ajax({
                 context: this,
                 type: "post",
                 dataType: "json",
                 data: data,
                 url: "/actions/discounts/deleteHold",
                 success: function(data, response){
                localStorage.clear();
                $.get('api/killsess', function(data){
                     window.location.href = "/schedule";
                 });

                 }
         });
					 localStorage.clear();
					 $.get('api/killsess', function(data){
							window.location.href = "/schedule";
					 });
			} else {
			//$.post("api/modpets", {modnum:num}, function(data){
			//window.location.href = "/petinfo";
			//});
      $.ajax({
                async: false,
                type: 'POST',
                url: "api/modpets",
                data: { modnum:num},
                success: function(data) {
                  window.location.href = "/petinfo";
                }
            });

		}
		})
		$(".back").click(function(e){
			e.preventDefault();
			var uri = window.location;
			var stringify = String(uri);
			var parts = stringify.split("/");
			var lastSegment = parts.pop() || parts.pop();
			switch(lastSegment) {
			    case "petinfo":
			        destination = "/basicinfo";
			        break;
			    case "billing":
			        destination = "/petinfo";
			        break;
						}
						window.location.href = destination;
		});
      $("#cellphone, #alt_phone, #billcellphone").inputmask({
        mask: "###-###-####",
        greedy: false,
        definitions: { '#': { validator: "[0-9]", cardinality: 1}},
        placeholder: "",
    });
/*   $("#cellphone, #alt_phone").on("keyup paste", function() {
    // Remove invalid chars from the input
    var input = this.value.replace(/[^0-9\(\)\s\-]/g, "");
    var inputlen = input.length;
    // Get just the numbers in the input
    var numbers = this.value.replace(/\D/g,'');
    var numberslen = numbers.length;
    // Value to store the masked input
    var newval = "";

    // Loop through the existing numbers and apply the mask
    for(var i=0;i<numberslen;i++){
        if(i==0) newval="("+numbers[i];
        else if(i==3) newval+=") "+numbers[i];
        else if(i==6) newval+="-"+numbers[i];
        else newval+=numbers[i];
    }

    // Re-add the non-digit characters to the end of the input that the user entered and that match the mask.
    if(inputlen>=1&&numberslen==0&&input[0]=="(") newval="(";
    else if(inputlen>=6&&numberslen==3&&input[4]==")"&&input[5]==" ") newval+=") ";
    else if(inputlen>=5&&numberslen==3&&input[4]==")") newval+=")";
    else if(inputlen>=6&&numberslen==3&&input[5]==" ") newval+=" ";
    else if(inputlen>=10&&numberslen==6&&input[9]=="-") newval+="-";

    $(this).val(newval.substring(0,14));
}); */
		$(".basic").submit(function(e){
					var error_flag = 0;
          var flow = $(".basicsubmit").data('flow');
          if(flow == "schedule"){
                     var zip = $.trim($("#selectedZip").val());
			               if(zip.length > 0){
                        var selected = $.trim($('#customerZip option:selected').text());
                        if(zip != selected){
                            if(confirm('The Zip Code in your address does not match the Zip Code you selected during scheduling. Click Cancel to modify your address Zip Code on this page, or OK to return to the Schedule and select a new Zip Code.')){
                               error_flag = 1;
                              var id = localStorage.getItem('bookingid');
                                 var data = {
                                 entryId: id,
                                     };
            //  data[window.csrfTokenName] = window.csrfTokenValue; // Append CSRF Token
                                 $.ajax({
                                         context: this,
                                         type: "post",
                                         dataType: "json",
                                         data: data,
                                         url: "/actions/discounts/deleteHold",
                                         success: function(data, response){
                                        localStorage.clear();
                                        $.get('api/killsess', function(data){
                                             window.location.href = "/schedule";
                                         });

                                         }
                                 });
                               localStorage.clear();
                               $.get('api/killsess', function(data){});
                             } else {
                              // $("#customerZip option[data-id='"+zip+"']").prop('selected', true);
                              return false;
                             }
				    }
					}
        }
                                            //error

					if($("#streetaddress").val().trim().length < 1){
      $("#streetaddress").parent().addClass('field-error');
						$("#error_streetaddress").html("Street Address is required.");
						error_flag = 1;
					}
					if($("#cellphone").val().trim().length < 1){
      $("#cellphone").parent().addClass('field-error');
						$("#error_cellphone").html("Cell Phone is required.");
						error_flag = 1;

					} /* if($("#cellphone").val().trim().length < 14){
                        $("#error_cellphone").html("invalid phone.");
                        error_flag = 1;

                    } */
					if($("#city").val().trim().length < 1){
      $("#city").parent().addClass('field-error');
						$("#error_city").html("City is required.");
						error_flag = 1;
					}
					if($("#states").val().trim().length < 1){
      $("#states").parent().addClass('field-error');
						$("#error_states").html("State is required.");
						error_flag = 1;
					}
					if($("#customerZip").val().trim().length < 1){
      $("#customerZip").parent().addClass('field-error');
						$("#error_customerZip").html("Zipcode is required.");
						error_flag = 1;
					}
					if(error_flag < 1){
            $.ajax({
                async: false,
                type: 'POST',
                url: "api/setTab",
                data: { tab : 1},
                success: function(data) {
                  $(this).submit(0);
                }
            });
            /*
						$.post("api/setTab",{tab:"1"},function(data){
							$(this).submit(0);
						});
            */
					} else {
						return false;
					}
				});

    $(".basic_user").submit(function(e){
          var error_flag = 0;

          if($("#firstName").val().trim().length < 1){
      $("#firstName").parent().addClass('field-error');
            $("#error_firstName").html("First Name is required.");
            error_flag = 1;
          }
            if($("#lastName").val().trim().length < 1){
      $("#lastName").parent().addClass('field-error');
            $("#error_lastName").html("Last Name is required.");
            error_flag = 1;
          }
             if($("#email").val().trim().length < 1){
      $("#email").parent().addClass('field-error');
            $("#error_email").html("Email is required.");
            error_flag = 1;
          }
          if($("#streetaddress").val().trim().length < 1){
      $("#streetaddress").parent().addClass('field-error');
            $("#error_streetaddress").html("Street Address is required.");
            error_flag = 1;
          }
          if($("#cellphone").val().trim().length < 1){
      $("#cellphone").parent().addClass('field-error');
            $("#error_cellphone").html("Cell Phone is required.");
            error_flag = 1;
          }

          if($("#city").val().trim().length < 1){
      $("#city").parent().addClass('field-error');
            $("#error_city").html("City is required.");
            error_flag = 1;
          }
          if($("#c_states").val().trim().length < 1){
      $("#states").parent().addClass('field-error');
            $("#error_states").html("State is required.");
            error_flag = 1;
          }
          if($("#customerZip").val().trim().length < 1){
      $("#customerZip").parent().addClass('field-error');
            $("#error_customerZip").html("Zipcode is required.");
            error_flag = 1;
          }
          if(error_flag < 1){

              $(this).submit(0);

          } else {
            return false;
          }
        });

		$('.remove-pet').click(function(e){
            e.preventDefault();
		    /* confirmation for delete message */


          if (confirm('Are you sure you want to delete this pet? ')) {

              /* call AJAX to delete */
              var field_id = $(this).data("field");
              var asset_id = $(this).data("asset");
              var petid = $(this).data("petid");
              var el = $(this);
              var csrf = $('input[name="csrf_token_name"]').val();

              // el.closest('.existpet').remove();

              var data = {
                  fieldId: field_id,
                  assetId: asset_id
              }

              //$.post('/actions/updateMatrix/delete_pet', data, function(response) {
              //     $("div.pet[data-petid="+petid+"]").remove();
              //    $("div.petinfo[data-petid="+petid+"]").remove();
              //     location.reload();
              //});
              $.ajax({
                  async: false,
                  type: 'POST',
                  url: "/actions/updateMatrix/delete_pet",
                  data: data,
                  success: function (data) {
                      $("div.pet[data-petid=" + petid + "]").remove();
                      $("div.petinfo[data-petid=" + petid + "]").remove();
                      location.reload();
                  }
              });

              /* add the inactive pet back to pets  or reload window */
          }else{
              return false;
          }
        });

        $('.remove-emr').click(function(e){

            e.preventDefault();
            if (confirm('Are you sure you want to delete this file? ')) {

            /* call AJAX to delete */
            var field_id = $(this).data("field");
            var asset_id = $(this).data("asset");
            var recordid = $(this).data("recordid");
            var el = $(this);
            var csrf = $('input[name="csrf_token_name"]').val();

            // remove EMR
            $("div.exist_petrecord[data-recordid="+recordid+"]").remove();


            var data = {
                fieldId : field_id,
                assetId: asset_id
            }

            //$.post('/actions/updateMatrix/delete_pet', data, function(response) {
            //});
            $.ajax({
                async: false,
                type: 'POST',
                url: "/actions/updateMatrix/delete_pet",
                data: data,
                success: function(data) {
                }
            });

            }else{
              return false;
            }

        });


        $('.eachPet').click(function(e){

            e.preventDefault();
            var petid = $(this).data("petid");
            //console.log(petid);
            check_pet_required_fields(petid);
            $('.newpet[data-petid='+petid+']').removeClass('hidden');
            dragndrop(petid);
            $('.eachPet').addClass('hidden-important');
            $('.pet').addClass('hidden');
            $('.inactivePet').addClass('hidden-important');
        });



        $('.cancelCreatePet').click(function(e){
           e.preventDefault();
            var petid = $(this).data("petid");
            $('.newpet[data-petid='+petid+']').addClass('hidden');
            $('.eachPet').removeClass('hidden-important');
            $('.pet').removeClass('hidden');
            $('.eachPet').removeClass('hidden');
            $('.inactivePet').removeClass('hidden-important');
            $('.editpetpicture').remove();
        });


        /**
         * form input fields
         */
        // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
        if (!String.prototype.trim) {
            (function() {
                // Make sure we trim BOM and NBSP
                var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
                String.prototype.trim = function() {
                    return this.replace(rtrim, '');
                };
            })();
        }

        [].slice.call( document.querySelectorAll( '.input__field--yoshiko' ) ).forEach( function( inputEl ) {
            // in case the input is already filled..
            if( inputEl.value.trim() !== '' ) {
                classie.add( inputEl.parentNode, 'input--filled' );
            }

            // events:
            inputEl.addEventListener( 'focus', onInputFocus );
            inputEl.addEventListener( 'blur', onInputBlur );
        } );

        function onInputFocus( ev ) {
            classie.add( ev.target.parentNode, 'input--filled' );
        }

        function onInputBlur( ev ) {
            if( ev.target.value.trim() === '' ) {
                classie.remove( ev.target.parentNode, 'input--filled' );
            }
        }

        /**
         * end of form input fields
         */



	}) /* end of doc ready */


     /**
      *  Drag & Drop for upload file for pets
      */
     dragndrop();

    /**
     *  Slide up and down on add a description
     */
$(".addDescription").toggle(function(){
    $('.addDescriptionText').slideup;
});


$("#useremail").change(function(e){
   e.preventDefault();
   $(".password").removeClass('hidden').addClass('showme');
});

    $("#useremail").click(function(){
        console.log("click on user email");
    });


$('.birth_month').change(function(){
    petid = $(this).data('petid');
    $('select.birth_day[data-petid='+petid+']').empty();
    var month = $.trim($('select.birth_month[data-petid='+petid+']').val());

    switch(month){
        case '01':
        case '03':
        case '05':
        case '07':
        case '08':
        case '10':
        case '12':
            for(i=1; i<=31; i++){
                if (i<10){
                    tstr = "0";
                    i=tstr+i;
                }
                $('select.birth_day[data-petid='+petid+']').append('<option value='+i+'>'+i+'</option>');
            }
            break;
        case '04':
        case '06':
        case '09':
        case '11':
            for(i=1; i<=30; i++){
                if (i<10){
                    tstr = "0";
                    i=tstr+i;
                }
                $('select.birth_day[data-petid='+petid+']').append('<option value='+i+'>'+i+'</option>');
            }
            break;
        case '02':
            for(i=1; i<=28; i++){
                if (i<10){
                    tstr = "0";
                    i=tstr+i;
                }
                $('select.birth_day[data-petid='+petid+']').append('<option value='+i+'>'+i+'</option>');
            }
            break;
        default:
            for(i=1; i<=30; i++){
                if (i<10){
                    tstr = "0";
                    i=tstr+i;
                }
                $('select.birth_day[data-petid='+petid+']').append('<option value='+i+'>'+i+'</option>');
            }
    }
//update birthdate value
   $('input.birthdate[data-petid='+petid+']').val($.trim($('input.birth_year[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_month[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_day[data-petid='+petid+']').val()));

});

    $('.birth_day').change(function() {
        petid = $(this).data('petid');
    //update birthdate value
        $('input.birthdate[data-petid='+petid+']').val($.trim($('input.birth_year[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_month[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_day[data-petid='+petid+']').val()));
    });

    $('.birth_year').keyup(function(){
    //update birthdate value
        petid = $(this).data('petid');
        $('input.birthdate[data-petid='+petid+']').val($.trim($('input.birth_year[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_month[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_day[data-petid='+petid+']').val()));
    });


    $('.birthdate').change(function(){
        console.log($(this).val());
    });

    $('.1year').change(function(){
        petid = $(this).parent().siblings('div.inputbirthdate').data('petid');
        if ($(this).val() == 'yes'){
            $(this).next().removeAttr('checked');
            $('.inputbirthdate[data-petid='+petid+']').removeClass('hidden');
            if (!$('.calculatebirthdate[data-petid='+petid+']').hasClass('hidden')){
                $('.calculatebirthdate[data-petid='+petid+']').addClass('hidden');
            }
        }else{
            $(this).prev().removeAttr('checked');
            $('.calculatebirthdate[data-petid='+petid+']').removeClass('hidden');
            if (!$('.inputbirthdate[data-petid='+petid+']').hasClass('hidden')){
                $('.inputbirthdate[data-petid='+petid+']').addClass('hidden');
            }
            if( $('.age[data-petid='+petid+']').val() != ""){
              $('.inputbirthdate[data-petid='+petid+']').removeClass('hidden');
            }
        $('input.birthdate[data-petid='+petid+']').val($.trim($('input.birth_year[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_month[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_day[data-petid='+petid+']').val()));
        }
    });

    $('.calculatebirthdate').keyup(function(){
        petid = $(this).data('petid');
        currentyear = new Date().getFullYear();
        $('.inputbirthdate[data-petid='+petid+']').removeClass('hidden');
        if(!isNaN(parseInt($('input.age[data-petid='+petid+']').val()))){
          $('input.birth_year[data-petid='+petid+']').val(currentyear - parseInt($('input.age[data-petid='+petid+']').val()));
        }
        $('input.birthdate[data-petid='+petid+']').val($.trim($('input.birth_year[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_month[data-petid='+petid+']').val())+'-'+$.trim($('select.birth_day[data-petid='+petid+']').val()));

    });


$('.aggressiveBehavior').click(function(e){
    var thisName = $(this).attr('name');
    petid = $(this).data('petid');
    var result = $("input[name='" + thisName + "']:checked").val();
    if  (result == 'yes'){
       $('.detailAboutAggressiveBehavior[data-petid='+petid+']').removeClass('hidden');
    }else{
    //if (!$('.detailAboutAggressiveBehavior').hasClass('hidden')){
       $('.detailAboutAggressiveBehavior[data-petid='+petid+']').addClass('hidden');
       $('.detailAboutAggressiveBehaviortext[data-petid='+petid+']').val('');
    //}
    }
});

/* $(".vet_cancel_init").click(function(e){
   e.preventDefault();
    var entryid = $(this).data('id');
    var start = $(this).data('start');
    var zip = $(this).data('zip');
    var vetname = $(this).data('vetname');
     var data = {
                zip : zip,
                entryid : entryid,
                start : start,
                vetname : vetname
            };
     $.ajax({
          type: 'POST',
          url: "/actions/displacedAppointments/findAvail",
          data: data,
           success: function(data) {
            console.log(data);
          //  $(".vet_cancel_init[data-id="+entryid+"]").text("processing");
          //  $(".vet_cancel_init[data-id="+entryid+"]").removeAttr('href');
           }
         });
  });

$(".testcalendarservice").click(function(e){
  e.preventDefault();
    var entryid = $(this).data('id');
    var start = $(this).data('start');
    var zip = $(this).data('zip');
    var vetname = $(this).data('vetname');
      $.ajax({
          type: 'POST',
          url: "/actions/displacedAppointments/findAvail",
          data: { entryid : entryid, start : start, zip : zip, vetname : vetname},
           success: function(data) {
           console.log(data);
           }
         });
});
*/
  $(".vet_cancel_init").click(function(e){
      console.log("in vet_cancel_init");
    var entryid = $(this).data('id');
    var start = $(this).data('start');
    var displaystart = $(this).data('displaystart');
    var zip = $(this).data('zip');
    var vetname = $(this).data('vetname');
    var vetid = $(this).data('vid');

    var date1 = new Date(start);
    var date2 = Date.now();
    var hours = Math.floor(Math.abs(date1 - date2) / 36e5);
    console.log(hours);
      console.log(date1);
    console.log(date2);

//     if(confirm(isNaN(hours)? "Are you sure you want to cancel this appointment? we will be charged $25 cancellation fee if canceling less than 48 hours in advance. There is no fee if canceling more than 48 hours." :(hours < 48) ? "Are you sure you want to cancel this appointment? Because you are cancelling less than 48 hours in advance, you will be charged a $25 cancellation fee for this action." : "Are you sure you want to cancel this appointment? There is no fee for this action.")){
      if(confirm((hours < 48) ? "Are you sure you want to cancel? If cancelling within 48 hours of appointment start time, you'll be charged a $25 fee." : "Are you sure you want to cancel? If cancelling within 48 hours of appointment start time, you'll be charged a $25 fee.")){
      if(hours < 12){
          console.log(start);
          $.ajax({
          type: 'POST',
          async:false,
          url: "/actions/displacedAppointments/vetCancelLessThan",
          data: { entryid : entryid, start : start, vetid: vetid, displaystart: displaystart,},
           success: function(result) {
            $(".vet_cancel_init[data-id="+entryid+"]").text("Cancelled");
            $(".vet_cancel_init[data-id="+entryid+"]").removeAttr('href');
               location.reload();
           }
         });
      } else {
    $.ajax({
      type: 'POST',
      async:false,
      url: "/api/checkforavail.html",
      data: { entryid : entryid, start : start, zip : zip, vetname : vetname, vetid: vetid,displaystart: displaystart,},
      success: function(result) {
     // console.log(data[0].status);
     // console.log(data);
      if(result[0].status == "unavailable"){
        var entryid = result[0].entryid;
        var status = result[0].status;
        var vetid = $(this).data('vid');
        var data = {
                entryid : entryid,
                status: status,
                vetid: vetid,
            };
      $.ajax({
          type: 'POST',
          async:false,
          url: "/actions/displacedAppointments/entrySubmit",
          data: data,
           success: function(result) {
            location.reload();
            $(".vet_cancel_init[data-id="+entryid+"]").text("Cancelled");
            $(".vet_cancel_init[data-id="+entryid+"]").removeAttr('href');
           }
         });
      } else if(result[0].status == "available"){
        var entryid = result[0].entryid;
        var status = result[0].status;
        var vetname = result[0].vetname;
        var vetid = result[0].userid;
        var data = {
                entryid : entryid,
                status: status,
                vetname : vetname,
                userid : vetid,
                displaystart: displaystart,
                mode: "auto"
            };
       $.ajax({
          type: 'POST',
           async:false,
          url: "/actions/displacedAppointments/rebook",
          data: data,
           success: function(result) {
            location.reload();
            $(".vet_cancel_init[data-id="+entryid+"]").text("Cancelled");
            $(".vet_cancel_init[data-id="+entryid+"]").removeAttr('href');
           }
         });
         } else {

        }

     }

    });
  }

  } else {
    return false;
  }

    e.preventDefault();
  });

  $(".customer_cancel_appt").click(function(e){
    var date1 = new Date($(this).data('start'));
     var date2 = Date.now();

    var hours = Math.floor(Math.abs(date1 - date2) / 36e5);

     //if(confirm(isNaN(hours)? "Are you sure you want to cancel this appointment? we will be charged $95 cancellation fee if canceling less than 24 hours in advance. There is no fee if canceling more than 24 hours." :(hours < 24) ? "Are you sure you want to cancel this appointment? Because you are canceling less than 24 hours in advance, you will be charged a $95 cancellation fee for this action." : "Are you sure you want to cancel this appointment? There is no fee for this action.")){
      if(confirm((hours < 24) ? "Are you sure you want to cancel this appointment? If cancelling within 24 hours of appointment start time, you'll be charged a $95 fee." : "Are you sure you want to cancel this appointment? If cancelling within 24 hours of appointment start time, you'll be charged a $95 fee.")){

    //Customer initiating cancel
    var entryid = $(this).data('id');
    var start = $(this).data('start');
    var vetname = $(this).data('vetname');
    var data = {
                entryid : entryid,
                start   : start,
                vetname : vetname,
            };
      $.ajax({
          type: 'POST',
          url: "/actions/displacedAppointments/customerCancel",
          data: data,
           success: function(data) {
            console.log(data);
            $("a[data-id="+entryid+"]").remove();
            $(".cancel-appt[data-id="+entryid+"]").html("Cancelled");
            location.reload();
           }
         });

          } else {
          return false;
         }
  });

    $(".edit-pet").click(function(e){
        e.preventDefault();
        $('.pet').addClass('hidden');
        $('.eachPet').addClass('hidden');
        var petid = $(this).data('petid');
        $(".petnum[data-petid="+petid+"]").removeClass('hidden');
        var petImageUrl = $(this).data('image');

        check_pet_required_fields(petid);
        
        var petinfo = $('div.petinfo[data-petid='+petid+']');
        var newUploadPicture = '<div class ="vet editpetpicture">'+
                                  '<div class="vetProfile">'+
                                   '<div class="" id="pets" data-name="pets" data-petid="'+petid+'">'+
                                         '<div class="picture">'+
                                           '<img class="petImage addpetpreview" src="'+petImageUrl+'">'+
                                          '</div>'+
                                          '<div class="overlay">'+
                                             '<div class="button" id="vet" data-name="vet"><!-- <i class="icon-photo"></i> -->&nbsp;<label for="changePhoto">Change Photo</label>'+
                                             '<input class="inputfile" type="file" id="changePhoto" name="fields[pets]['+petid+'][fields][petPhoto]" data-petid="'+petid+'"/>'+
                                          '</div>'+
                                        '</div>'+
                                      '</div>'+
                                    '</div>'+
                                  '</div>';
        var uploadPicture = 'div class="box pet_dragandrophandler editpetpicture" id="pets" data-name="pets" data-petid="'+petid+'"><i class="icon-photo"></i><label'+
                                    'for="createPetPhoto">Upload a photo of your pet</label>'+
                                    '<input class="inputfile" type="file" id="createPetPhoto"'+
                                       'name="fields[pets]['+petid+'][fields][petPhoto]" data-petid="'+petid+'"/>'+
                                    '</div>';
        //petinfo.prepend(newUploadPicture);
        dragndrop(petid);
        petinfo.slideDown();
        return false;
    });

    $('.petinfo').on("change keyup blur input",function(){
      var petid = $(this).data('petid');
      check_pet_required_fields(petid);
    });

    $('.newpet').on("change keyup blur input",function(){
      var petid = $(this).data('petid');
      check_pet_required_fields(petid);
    });

    

    function check_pet_required_fields(petid){

      if($('.petFirstName[data-petid='+petid+']').val() == ""){
          $('.petSave[data-petid='+petid+']').prop('disabled', true);
          return false;
      }

      if($('.birth_year[data-petid='+petid+']').val().length != 4){
        $('.petSave[data-petid='+petid+']').prop('disabled', true);
        return false;
      }

      if($('.species[data-petid='+petid+']:checked').length < 1 ){
          $('.petSave[data-petid='+petid+']').prop('disabled', true);
          return false;
      }

      if($('.breed[data-petid='+petid+']').val() == ""){
          $('.petSave[data-petid='+petid+']').prop('disabled', true);
          return false;
      }

      if($('.gender[data-petid='+petid+']:checked').length < 1 ){
          $('.petSave[data-petid='+petid+']').prop('disabled', true);
          return false;
      }

      if($('.spayedOrNeutered[data-petid='+petid+']:checked').length < 1 ){
          $('.petSave[data-petid='+petid+']').prop('disabled', true);
          return false;
      }
      
      if(($('.aggressiveBehavior[data-petid='+petid+']:checked').length < 1) 
        || ($('.aggressiveBehavior[data-petid='+petid+']:checked').val() == "yes" && $('.detailAboutAggressiveBehaviortext[data-petid='+petid+']').val() == "")){
        $('.petSave[data-petid='+petid+']').prop('disabled', true);
        return false;
      }

      $('.petSave[data-petid='+petid+']').prop('disabled', false);
      
    }

    $(".addpet").click(function(e){
        $(".create_pet_clone").clone(true,true).removeClass('hidden').appendTo(".createPet");
        dragndrop();
        $(".createPet").slideDown();
        $(".addpet").addClass("hidden");
        $(".canceladdpet").removeClass("hidden");
        return false;
    });

    $(".canceladdpet").click(function(e){
        $(".addpet").removeClass("hidden");
        $(".createPet").empty();
        $(".canceladdpet").addClass("hidden");
        return false;
    });
    $(".cancelpet").click(function(e){
	/*$('.pet_dragandrophandler').find('input[type="file"]').val('');
	 $("#pets").find('label').text("Upload a photo of your pet");
	*/
       return false;
	});

    $(".addadescriptionlink").click(function(e){
        recordid = $(this).data('recordid');
        $("div.emrDescription[data-recordid="+recordid+"]").slideToggle();
        return false;
    });

    /**
     * To make sure first name of pet is in record before save
     */
/*	$('#petinfo input').change(function(e){
	  e.preventDefault();
	  var petid = $(this).data('petid');
//	console.log(petid);
	  if($('.petSave').prop("disabled") && $('input[data-petid='+petid+']').val() != ""){
		$('.petSave').prop('disabled', false);
		}
	});
*/
    /*
    $('.petFirstName').on("change keyup blur input", function(e){
        e.preventDefault();
        var petid = $(this).data('petid');
        if ($(this).val().length == 0){
            $('.petSave').prop('disabled', true);
        }else if($('.aggressiveBehavior[data-petid='+petid+']:checked').val() == "yes" && $('.detailAboutAggressiveBehaviortext[data-petid='+petid+']').val() == ""){
            $('.petSave').prop('disabled', true);
        }else{
            $('.petSave').prop('disabled', false);
        }
    });


    $('.aggressiveBehavior').change(function(e){
      var petid = $(this).data('petid');

      if($(this).val() == "yes" && $('.detailAboutAggressiveBehaviortext[data-petid='+petid+']').val() == ""){
        $('.petSave').prop('disabled', true);
      }else if($('.petFirstName[data-petid='+petid+']').val() == ""){
        $('.petSave').prop('disabled', true);
      }else{
        $('.petSave').prop('disabled', false);
      }
    })
  

    $('.detailAboutAggressiveBehaviortext').on("change keyup blur input", function(e){
        //e.preventDefault();
        var petid = $(this).data('petid');
        if ($(this).val() == ""){
            $('.petSave').prop('disabled', true);
        }else if($('.petFirstName[data-petid='+petid+']').val() == ""){
            $('.petSave').prop('disabled', true);
        }else{
            $('.petSave').prop('disabled', false);
        }
    });
*/
    $('.inputfile').change(function(e){
        e.preventDefault();
        $('.petEMRupload').prop('disabled', false);
    });

    $('.emrDescription').keyup(function(e){
        e.preventDefault();
        $('.emrSave').prop('disabled', false);
    });

    function dragndrop2() {
        /**
         *  Drag & Drop for upload file for pets
         */

            // var box = $('.box');
            //var obj = $("#dragandrophandler");
            //var obj = $('.pet_dragandrophandler');
        var obj = $('.pet_dragandrophandler');
        var droppedFiles = false;

        obj.on('dragenter', function (e) {
            console.log('drag')
            e.stopPropagation();
            e.preventDefault();
        });
        obj.on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
        obj.on('drop', function (e) {
            if ($(this).data('name') == 'pets') {
                var input = $('.pet_dragandrophandler').find('input[type="file"]');
                var label = $('.pet_dragandrophandler').find('label');

            } else if ($(this).data('name') == 'vet'){
                console.log('in vet');
                var input = $('.vet_dragandrophandler').find('input[type="file"]');
                var label = $('.vet_dragandrophandler').find('label');
            }
            else {
                var input = $('.petemr_dragandrophandler').find('input[type="file"]');
                var label = $('.petemr_dragandrophandler').find('label');

                // enable the upload button for EMR only
                $('.petEMRupload').prop('disabled', false);
            }
            var showFiles = function (files) {
                label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name);
            };
            console.log(input);
            e.preventDefault();
            droppedFiles = e.originalEvent.dataTransfer.files;
            input.prop("files", e.originalEvent.dataTransfer.files);
            //$("input[type='file']").prop("files", e.originalEvent.dataTransfer.files);



            showFiles(droppedFiles);

        });

        obj.on('change', function (e) {
            if ($(this).data('name') == 'pets') {
                var input = $('.pet_dragandrophandler').find('input[type="file"]');
                var label = $('.pet_dragandrophandler').find('label');

            } else {
                var input = $('.petemr_dragandrophandler').find('input[type="file"]');
                var label = $('.petemr_dragandrophandler').find('label');
            }

              if (e.target.files && e.target.files[0]) {
                  var reader = new FileReader();

                  reader.onload = function (e) {
                      $('.petImage').attr('src', e.target.result);
                    }

                  reader.readAsDataURL(e.target.files[0]);
                 }
            var showFiles = function (files) {
                label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name);
            };
            showFiles(e.target.files);
        });

        // To avoid that we can prevent ‘drop’ event on document.
        $(document).on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
        $(document).on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
        $(document).on('drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
    }

    function dragndrop(petid) {
        /**
         *  Drag & Drop for upload file for pets
         */

            // var box = $('.box');
            //var obj = $("#dragandrophandler");
            //var obj = $('.pet_dragandrophandler');
        if($("#vetaccount").length != 0){
          var obj = $('.vet');
        }else{
          var obj = $('.box');
        }

        var droppedFiles = false;

        obj.on('dragenter', function (e) {
            console.log('drag')
            e.stopPropagation();
            e.preventDefault();
        });
        obj.on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
        obj.on('drop', function (e) {
            if ($(this).data('name') == 'pets') {
                var input = $('.pet_dragandrophandler[data-petid='+petid+']').find('input[type="file"]');
                var label = $('.pet_dragandrophandler[data-petid='+petid+']').find('label');

            } else if ($(this).data('name') == 'vet'){
                console.log('in vet');
                var input = $('.vet_dragandrophandler').find('input[type="file"]');
                var label = $('.vet_dragandrophandler').find('label');
            }
            else {
                var input = $('.petemr_dragandrophandler').find('input[type="file"]');
                var label = $('.petemr_dragandrophandler').find('label');

                // enable the upload button for EMR only
                $('.petEMRupload').prop('disabled', false);
            }

            var showFiles = function (files) {
                if($("#vetaccount").length != 0){
                     label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : 'Change Photo');
                    }else{
                      if (typeof petid === "undefined") {
                        label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name);
                      }else{
                        label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : 'Change Photo');
                      }
                    }
            };
            console.log(input);
            e.preventDefault();
            droppedFiles = e.originalEvent.dataTransfer.files;
            input.prop("files", e.originalEvent.dataTransfer.files);
            //$("input[type='file']").prop("files", e.originalEvent.dataTransfer.files);

            showFiles(droppedFiles);

        });

        obj.on('change', function (e) {
            if ($(this).data('name') == 'pets') {
                var input = $('.pet_dragandrophandler[data-petid='+petid+']').find('input[type="file"]');
                var label = $('.pet_dragandrophandler[data-petid='+petid+']').find('label');
            }else if ($(this).data('name') == 'vet'){
                console.log('in vet');
                var input = $('.vet_dragandrophandler').find('input[type="file"]');
                var label = $('.vet_dragandrophandler').find('label');
            } else {
                var input = $('.petemr_dragandrophandler').find('input[type="file"]');
                var label = $('.petemr_dragandrophandler').find('label');
            }


              if (e.target.files && e.target.files[0]) {
                  var reader = new FileReader();

                  reader.onload = function (e) {
                    if($("#vetaccount").length != 0){
                      $('.addpetpreview').attr('src', e.target.result);
                    }else{
                     $('.addpetpreview[data-petid='+petid+']').attr('src', e.target.result);
                    }

                    }

                  reader.readAsDataURL(e.target.files[0]);
                 }
            var showFiles = function (files) {
                    if($("#vetaccount").length != 0){
                     label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : 'Change Photo');
                    }else{
                      if (typeof petid === "undefined") {
                        label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name);
                      }else{
                        label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : 'Change Photo');
                      }
                    }

            };
            showFiles(e.target.files);
        });

        // To avoid that we can prevent ‘drop’ event on document.
        $(document).on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
        $(document).on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
        $(document).on('drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
    }
        /**
         *  End of drag and drop
         */

			    function getTimeRemaining(endtime) {
			      var t = Date.parse(endtime) - Date.parse(new Date());
			      var seconds = Math.floor((t / 1000) % 60);
			      var minutes = Math.floor((t / 1000 / 60) % 15);
			      return {
			        'total': t,
			        'minutes': minutes,
			        'seconds': seconds
			      };
			    }

			    function initializeClock(id, endtime) {
			      if(document.getElementById(id)){
			      var clock = document.getElementById(id);
			      var minutesSpan = clock.querySelector('.minutes');
			      var secondsSpan = clock.querySelector('.seconds');
						var cycles = 0;
			      function updateClock() {
							var t = getTimeRemaining(endtime);
							if (t.minutes < 1 && t.seconds < 1 && cycles > 0) {
							alert('Your appointment booking time has expired, and the time slot you chose has been surrendered. By clicking OK, you will be redirected back to the Schedule to restart the booking process.');
		 				 var id = localStorage.getItem('bookingid');
             var data = {
             entryId: id,
                 };
  //  data[window.csrfTokenName] = window.csrfTokenValue; // Append CSRF Token
             $.ajax({
                     context: this,
                     type: "post",
                     dataType: "json",
                     data: data,
                     url: "/actions/discounts/deleteHold",
                     success: function(data, response){
                    localStorage.clear();
                    $.get('api/killsess', function(data){
                         window.location.href = "/schedule";
                          clearInterval(timeinterval);
                     });

                     }
             });
							 localStorage.clear();
							 $.get('api/killsess', function(data){
									window.location.href = "/schedule";
                   clearInterval(timeinterval);
							 });
					//		  clearInterval(timeinterval);
						 } else {
			        minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
			        secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
							localStorage.setItem('cur_time', endtime);
						  }
							cycles++;
			      }
			      updateClock();
			      var timeinterval = setInterval(updateClock, 1000);
			    }
			    }
					if(localStorage.getItem('cur_time')){
						deadline = localStorage.getItem('cur_time');
					} else {
			    var deadline = new Date(Date.parse(new Date()) + 15 * 24 * 60 * 60 * 1000);
					}
			    initializeClock('clockdiv', deadline);


  $('.ac-container .ac-body').hide();
  $('.ac-container .ac-title').on('click', function (e) {
   $(this).toggleClass('minus');
   $(this).next('.ac-body').slideToggle();
  });

}) (jQuery); /*  end */
