// common js

// plugins: overly & parallax
(function($) {
	$.extend({
		ovrly: function(wrapper) {
			var container = wrapper ? wrapper : "body";
			var i = "Please Wait";
			var methods = {
				init: function() {
					$(container).css("position", "relative");

					$(container)
						.find(".overlay-block")
						.remove();
					$(
						'<div class="overlay-block"><h2><i class="fa fa-spinner fa-pulse fa-2x fa-fw d-block mx-auto" aria-hidden="true"></i></h2></div>'
					).appendTo(container);
					// return this;
					$(
						"<style>.overlay-block { position: fixed; height: 100%; width: 100%; top: 0; left: 0; background-color: rgba(30, 30, 30, 0.75); z-index: 999999; color: #fff; }.overlay-block>* { position: absolute; transform: translate3d(-50%, -50%, 0); top: 50%; left: 50%; }</style>"
					).appendTo("body");
				},
				kill: function() {
					$(container)
						.css("position", "")
						.find(".overlay-block")
						.fadeOut(250, function() {
							$(this).remove();
						});
				}
			};

			// init from inside
			// methods.init();
			return methods;
		}
	});

	$.fn.parallax = function() {
		return this.each(function() {
			var $elm = $(this),
				speed = $elm.attr("data-speed") ? $elm.attr("data-speed") : 1.5,
				scale = $elm.attr("data-scale") ? $elm.attr("data-scale") : 1;

			function updateParallax(initial) {
				var $img = $elm.children("img").first(),
					ch = $elm.height(),
					dt = $elm.offset().top,
					db = dt + ch,
					st = $(window).scrollTop(),
					wh = $(window).innerHeight(),
					sp = speed,
					scl = scale,
					wb = st + wh,
					parallax,
					trans;
				if (initial) {
					$img.css("display", "block");
				}
				if (dt < wb) {
					parallax = Math.round((st - dt) / sp + 28);
					trans =
						"translate3d(-50%," +
						parallax +
						"px, 0) scale(" +
						scl +
						")";
				}
				$img.css({ transform: trans });
			}
			$elm.children("img")
				.one("load", function() {
					updateParallax(true);
				})
				.each(function() {
					if (this.complete) {
						$(this).trigger("load");
					}
				});
			$(window).scroll(function() {
				updateParallax(false);
			});
		});
	};

	$.fn.elasticMenu = function() {
		if ($(window).outerWidth() < 992) {
			console.log("small device not supported");
			return;
		} else {
			var $elm = $(this);
			$elm.each(function() {
				var $nav = $(this);
				var activeItem = $($nav).find(".active");
				var navItems = $($nav).attr("data-targets");
				var shadow = $("<div>", { class: "nav-shadow" }).css({
					width: 0,
					transform: "translate3d(-50%,-100%, 0)",
					opacity: 0
				});

				// activeItem.addClass("is-active");
				shadow.insertAfter($nav);

				// i_ = initial;
				var i_top = 0;
				var i_left = 0;
				var i_height = 0;
				var i_width = 0;
				var i_opacity = 0;

				function UpdateActiveCoords() {
					if (activeItem.length == 1) {
						i_top = activeItem.offset().top;
						i_left = activeItem.offset().left;
						i_height = activeItem.outerHeight();
						i_width = activeItem.outerWidth();
						i_opacity = 1;
					} else {
						i_top = $nav.offset().top;
						i_left = $nav.offset().left;
						i_height = $nav.outerHeight();
						i_width = 0; //$nav.outerWidth();
						i_opacity = 0;
					}
				}

				function moveShadow(t, l, h, w, o) {
					shadow.css({
						"background-color": "#f90",
						transition: "0.35s all",
						opacity: o,
						position: "fixed",
						"z-index": -1,
						height: h,
						width: w,
						// "top": t,
						left: l + w / 2
					});
				}

				UpdateActiveCoords();
				moveShadow(i_top, i_left, i_height, i_width, i_opacity);

				// c_ = current
				var c_height;
				var c_width;
				var c_top;
				var c_left;
				var c_opacity;

				$(navItems).each(function() {
					$(this).hover(
						function() {
							c_height = $(this).outerHeight();
							c_width = $(this).outerWidth();
							c_top = $(this).offset().top;
							c_left = $(this).offset().left;
							c_opacity = 1;
							// console.log(top, left, height, width);
							moveShadow(
								c_top,
								c_left,
								c_height,
								c_width,
								c_opacity
							);
						},
						function() {
							moveShadow(
								i_top,
								i_left,
								i_height,
								i_width,
								i_opacity
							);
						}
					);
					$(window).on("resize scroll", function() {
						setTimeout(function() {
							if (activeItem.length == 1) {
								UpdateActiveCoords();
								moveShadow(
									i_top,
									i_left,
									i_height,
									i_width,
									i_opacity
								);
							}
						}, 300);
						// console.log(left, top, height, width);
					});
				});
			});
		}
	};
})(jQuery);

function showLoader() {
	$.ovrly().init();
}

function hideLoader() {
	$.ovrly().kill();
}

$(document).on("click", ".reset-wild-tigers", function() {
	showLoader();
	window.location.reload();
});

function alertifyMessage(type, message) {
	switch (type) {
		case "error":
			alertify.notify(message, "error", 5);
			break;
		case "success":
			alertify.notify(message, "success", 5);
			break;
		case "warning":
			alertify.notify(message, "warning", 5);
			break;
		case "info":
			alertify.notify(message);
			break;
		default:
			alertify.notify(message);
	}
}

//override defaults
if (typeof alertify !== "undefined") {
	alertify.defaults.transition = "slide";
	alertify.defaults.theme.ok = "btn btn-primary";
	alertify.defaults.theme.cancel = "btn btn-danger";
	alertify.defaults.theme.input = "form-control";
}

if (typeof jQuery.validator !== "undefined") {
	jQuery.validator.setDefaults({
		errorPlacement: function(error, element) {
			if (
				element.hasClass("select2") &&
				element.next(".select2-container").length
			) {
				error.insertAfter(element.next(".select2-container"));
			} else if (element.parent(".input-group").length) {
				error.insertAfter(element.parent());
			} else if (
				element.prop("type") === "radio" &&
				element.parent(".radio-inline").length
			) {
				error.insertAfter(element.parent().parent());
			} else if (
				element.prop("type") === "checkbox" ||
				element.prop("type") === "radio"
			) {
				error.appendTo(element.parent().parent());
			} else if (element.prop("type") === "file") {
				error.appendTo(
					element
						.parent()
						.parent()
						.parent()
				);
			} else {
				error.insertAfter(element);
			}
		}
	});

	$.validator.addMethod("email_regex",function(value, element, regexp) {
		var re = new RegExp(/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,10}\b$/i);
		return this.optional(element) || re.test($.trim(value));
	},function(params, element) {
		return "Please Enter Valid " + ( ( $(element).attr("placeholder") != "" && $(element).attr("placeholder") != null ) ? $(element).attr("placeholder").replace("*", "") : "Email" );
	});

	$.validator.addMethod('comma_separated_email_regex',function(value, element, regexp) {
		var validate_email = true;
		var value_array = $.trim(value).split(',');
		$.each(value_array, function(key,value){
			var re = new RegExp(/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,10}\b$/i);
			if(re.test($.trim(value)) != true){
				validate_email = false;
			}
		});
		return this.optional(element) || validate_email;
	},function(params, element) {
		if($.trim($(element).val()).split(',').length > 1) {
			return 'Please Enter All '+( ( $(element).attr("placeholder") != "" && $(element).attr("placeholder") != null ) ? $(element).attr("placeholder").replace("*", "") : "Email" )+'s In Valid Format';	
		}
		return "Please Enter Valid " + ( ( $(element).attr("placeholder") != "" && $(element).attr("placeholder") != null ) ? $(element).attr("placeholder").replace("*", "") : "Email" );
	});
	
	$.validator.addMethod( "noSpace", function(value, element) {
		return this.optional(element) || $.trim(value) != "";
	}, function(params, element) {
		var element_msg = $(element).siblings('label').html().replace('*' , '');
		return "Please enter " + element_msg + '.';
	});

	$.validator.addMethod("mobile_regex",function(value, element, regexp) {
		var re = new RegExp(/^[6789]\d{9}$/);
		return this.optional(element) || re.test(value);
	},function(params, element) {
		var getMessage = ( ( $(element).attr("placeholder") != "" && $(element).attr("placeholder") != null ) ? $(element).attr("placeholder").replace("*", "") : "Mobile No" ) + "."
		var message = getMessage.toLowerCase().replace('enter', '').replace('your', '');
		return "Please Enter valid " + ucword( message );
	});
	
	$.validator.addMethod("all_zero_regex",function(value, element, regexp) {
		var re = new RegExp(/^(?=.*\d)(?=.*[1-9]).{1,15}$/);
		return this.optional(element) || re.test($.trim(value));
	},function(params, element) {
		return "Please Enter Valid " + $(element).attr("placeholder") + ".";
	});
	
	$.validator.addMethod("inetrnation_mobile_regex",function(value, element, regexp) {
		var re = new RegExp(/^\+?\d+$/);
		return this.optional(element) || re.test($.trim(value));
	},function(params, element) {
		return "Please Enter Valid " + $(element).attr("placeholder") + ".";
	});
	
	$.validator.addMethod( "youtube_regex", function(value, element, regexp) {
		var re = new RegExp(regexp);
		return this.optional(element) || re.test(value);
	},"Please enter valid youtube video link.");
}

function onlyDecimal(thisitem) {
	var val = $(thisitem)
		.val()
		.trim();

	if (parseInt(val) == 0) {
		var newValue = val.replace(/^0+/, "");
		return $(thisitem).val(newValue);
	}

	if (isNaN(val)) {
		val = val.replace(/[^0-9\.]/g, "");
		if (val.split(".").length > 2) val = val.replace(/\.+$/, "");
	}
	return $(thisitem).val(val);
}
function onlyDecimalWithZero(thisitem) {
	var val = $(thisitem)
		.val()
		.trim();
	
	if (isNaN(val)) {
		val = val.replace(/[^0-9\.]/g, "");
		if (val.split(".").length > 2) val = val.replace(/\.+$/, "");
	}
	return $(thisitem).val(val);
}

function onlyNumber(thisitem) {
	var $val = $(thisitem)
		.val()
		.trim()
		.replace(/[^\d]/g, "");
	$(thisitem).val($val);
}

function mobileIneternationFormat(thisitem) {
	var $val = $(thisitem)
		.val()
		.trim()
		.replace(/[^+\d]/g, "");
	$(thisitem).val($val);
}


function onlyNumberWithSpaceAndPlusSign(thisitem) {
	var $val = $(thisitem)
		.val()
		.replace(/[^ +\d]/g, "");
	$(thisitem).val($val);
}

function naturalNumber(thisitem) {
	var $val = $(thisitem)
		.val()
		.trim()
		.replace(/[^\d]/g, "")
		.replace(/^0+/g, "");
	$(thisitem).val($val);
}

function deleteRecord(thisitem, moduleName) {
    alertify.confirm(messages["delete_record"],messages["confirm_delete_record"],function() {
           //user id
   	var record_id = $(thisitem).data("record-id");
   	
   	
   	var token = $("meta[name='csrf-token']").attr("content");
   	   
       $.ajax(
       {
           url: moduleName + "/" + record_id,
           type: 'DELETE',
           dataType:'json',
           data: {
               "id": record_id,
               "_token": token,
           },
           success: function (response){
           	if( response.status_code == 1 ){
           		$(thisitem).parents('tr').remove();
           		alertifyMessage('success' , response.message );
           	} else if ( response.status_code == 101 ){
           		alertifyMessage('error' , response.message );
           	}
               console.log("it Works");
           }
       });

    },function() {});
}


function imagePreview(thisitem) {
	   var filedId = $(thisitem).attr("id");
	   
	   var validImageTypes = ["image/jpg", "image/jpeg", "image/png"];
	   
	   if ($("#" + filedId).valid() == true) {
	       //$(this).next('label').text($(this).val().replace(/C:\\fakepath\\/i, ''));

	       var input = this;

	       if (thisitem.files && thisitem.files[0]) {
	       		var fileType = thisitem.files[0]["type"];
	            if ($.inArray(fileType, validImageTypes) < 0) {
	            	$("." + filedId + "-preview-div").hide();
			       	$("." + filedId + "-preview").attr("src", "");
			       	$(thisitem).siblings(".custom-file-label").html("");
			       	alertifyMessage("error",'Please select jpg, jpeg, png image.');
			       	return false;
		       	}
	       	
	       		var reader = new FileReader();

	           reader.onload = function (e) {
	        	   
	        	   $("." + filedId + "-preview-div").show();
	               $("." + filedId + "-preview").show();
	               $("." + filedId + "-preview").attr("src", "");
	               $("." + filedId + "-preview").attr("src", e.target.result);
	               $(thisitem).siblings(".custom-file-label").html(thisitem.files[0]["name"]);
	           };

	           reader.readAsDataURL(thisitem.files[0]);
	       }
	   } else {
		   $(thisitem).siblings(".custom-file-label").html("");
	       $("." + filedId + "-preview-div").hide();
	       $("." + filedId + "-preview").attr("src", "");
	   }
	}



function searchAjax(ajaxUrl, ajaxData, pagination = false) {
	var result;
	$.ajax({
		type: "POST",
		url: ajaxUrl,
		async: false,
		data: ajaxData,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			if (pagination != false) {
				$(".ajax-view").append(response);
			} else {
				$(".ajax-view").html("");
				$(".ajax-view").html(response);
			}
			result = response;
		},
		error: function() {
			hideLoader();
		}
	});
	return result;
}
var multipleImageName = [];
var single_image_field_name = ["gallery_img"];
function multipleImagePreview(thisitem, placeToInsertImagePreview) {
	var invalidImage = false;
	var field_id = $(thisitem).attr("id");
	var field_name = $(thisitem).attr("data-field-name");

	$("." + field_id + "-preview-div").html("");
	if (thisitem.files) {
		var filesAmount = thisitem.files.length;

		for (i = 0; i < filesAmount; i++) {
			var f = thisitem.files[i];
			var reader = new FileReader();

			if (
				thisitem.files[i].type == "image/jpg" ||
				thisitem.files[i].type == "image/png" ||
				thisitem.files[i].type == "image/jpeg"
			) {
				reader.onload = (function(theFile) {
					return function(e) {
						var imageName = "";
						var imageName = theFile.name;
						var imageHtml = "";

						if (imageName != "") {
							multipleImageName.push(imageName);
							$("#final_selected_image").val(
								multipleImageName.toString()
							);
						}
						imageHtml =
							'<div class="col-lg-4 mb-5 "><div class="img-gallery"><img src="' +
							event.target.result +
							'" alt="" srcset="" class="img-fluid gallery_img-preview"><button type="button" class="button-gallery btn btn-danger rounded" onclick="removeImage(this)" data-field-name="' +
							field_name +
							'" data-preview-name="' +
							imageName +
							'"><i class="fas fa-times cancel-icon"></i></button></div></div>';
						$("." + field_id + "-preview-div").append(
							$.parseHTML(imageHtml)
						);
					};
				})(f);
			} else {
				invalidImage = true;
			}

			reader.readAsDataURL(thisitem.files[i]);
		}

		$("#final_selected_image").val(multipleImageName.toString());

		if (invalidImage != false) {
			$("#" + field_id).val("");
			$("." + field_id + "-preview-div").hide();
			$("." + field_id + "-preview-div").html("");
			alertifyMessage("error", messages["invalid-image"]);
		} else {
			$("." + field_id + "-preview-div").show();
		}
	}
}

function removeImage(thisitem) {
	alertify.confirm(
		messages["remove_image"],
		messages["delete-image-msg"],
		function() {
			//console.log($(thisitem).attr('data-preview-name'));
			var image_name = $(thisitem).attr("data-image-name");
			var module_name = $(thisitem).attr("data-module-name");
			var record_id = $(thisitem).attr("data-record-id");
			var field_name = $(thisitem).attr("data-field-name");

			if (image_name != "" && image_name != null) {
				$.ajax({
					type: "POST",
					dataType: "json",
					url: site_url + "product/removeUploadedFile",
					async: false,
					data: {
						image_name: image_name,
						module_name: module_name,
						record_id: record_id,
						field_name: field_name
					},
					beforeSend: function() {
						//block ui
						//showLoader();
					},
					success: function(response) {
						//hideLoader();
						//window.location.reload()
						console.log(response);
					},
					error: function() {
						//hideLoader();
					}
				});
			}

			//console.log(field_name);
			//console.log(single_image_field_name);
			//console.log($.inArray(field_name, single_image_field_name));
			if ($.inArray(field_name, single_image_field_name) !== -1) {
				console.log("check");
				$(thisitem)
					.parents(".img-gallery")
					.parent()
					.remove();
				$("input[name='" + field_name + "']").val("");
			} else {
				console.log("remopve unuploaded image");
				//console.log("input[name='" + field_name + "']");
				var preview_image_name = $(thisitem).attr("data-preview-name");

				$(thisitem)
					.parents(".img-gallery")
					.parent()
					.remove();
				if (preview_image_name != "") {
					multipleImageName.splice(
						$.inArray(preview_image_name, multipleImageName),
						1
					);
				}
				console.log($(thisitem).attr("data-preview-name"));
				//console.log(multipleImageName);
				$("#final_selected_image").val(multipleImageName.toString());
			}
		},
		function() {}
	);
}

// boundry -------------------------------------------------------------------------------------------------------

// designers
function menuDrop() {
	if ($(window).innerWidth() > 991) {
		$(
			".twt-navbar .nav-item.dropdown, .twt-navbar .dropdown-submenu"
		).hover(
			function() {
				$(this)
					.find(".dropdown-menu")
					.first()
					.stop(true, true)
					.delay(250)
					.slideDown(150);
			},
			function() {
				$(this)
					.find(".dropdown-menu")
					.first()
					.stop(true, true)
					.delay(100)
					.slideUp(100);
			}
		);
		$(".twt-navbar .dropdown > a").click(function() {
			try {
				var hrefAttr = $(this).attr('href');
				// avoid navigating to javascript:void(0) or empty/hash anchors
				if (hrefAttr && hrefAttr.trim() !== '' && hrefAttr.indexOf('javascript:') !== 0 && hrefAttr !== '#') {
					location.href = this.href;
				}
			} catch (e) {
				console.error('navigation guard error', e);
			}
		});
	}
}

$(document).ready(function() {
	menuDrop();
	$("#slide-toggle").on("click", function() {
		$("body").toggleClass("nav-slide-open");
	});

	$(function() {
		// var current = window.location.href.substring(window.location.href.lastIndexOf("/") + 1);
		var current = window.location.href;

		if (current != "") {
			$(".nav-link").each(function() {
				var href = $(this).attr("href");
				if (href == current) {
					$(this)
						.parent()
						.addClass("active");
				}
			});
		}
	});

	$(document).on("click", function(e) {
		// console.log(!$(e.target).is('#slide-toggle, #slide-toggle .fas'), $(window).innerWidth() < 992);
		if (
			$(window).innerWidth() < 992 &&
			!$(e.target).is("#slide-toggle, #slide-toggle .fas")
		) {
			$("body").removeClass("nav-slide-open");
		}
	});

	// sidebar
	$(document).on("click", ".navbar-toggler", function() {
		$("#wrapper").toggleClass("toggled");
	});
	// sidebar sub menu
	$('.sidebar [data-toggle="collapse"]').on("click", function() {
		var current = $(this);
		current
			.parent()
			.siblings()
			.find(".collapse.show")
			.collapse("hide");
	});

	// sidebar close on outside click
	$(document).on("click", function(e) {
		if (
			$(window).innerWidth() < 1200 &&
			!$(e.target).closest("#sidebar").length > 0 &&
			!$(e.target).is(".navbar-toggler")
		) {
			$("#wrapper").removeClass("toggled");
		}
	});

	if (window.location.hash) {
		console.log(window.location.hash);

		setTimeout(function() {
			window.scrollTo(0, 0);
		}, 1);
		setTimeout(function() {
			$("html, body").animate(
				{
					scrollTop: $(window.location.hash).offset().top - 96
				},
				1000
			);
		}, 300);
	}

	$('a[href*="#"]').on("click", function(event) {
		if (this.hash !== "") {
			event.preventDefault();
			var hash = this.hash;

			if (!$(this).attr("data-toggle")) {
				$("html, body").animate(
					{
						scrollTop:
							$(hash).offset().top -
							$(".navbar").outerHeight() -
							70
					},
					800
				);
			}
		}
	});

	var topOffset = $("#navMain").attr("data-offset")
		? parseInt($("#navMain").attr("data-offset"))
		: 0;
	if ($(".main-navbar-wrapper").hasClass("fallen-nav")) {
		$(".main-navbar-wrapper").css(
			"min-height",
			$("#navMain").outerHeight() + topOffset
		);
	} else if ($(".main-navbar-wrapper").hasClass("notch-nav")) {
		$(".main-navbar-wrapper").css(
			"min-height",
			$("#navMain").outerHeight() + topOffset
		);
	} else {
		$(".main-navbar-wrapper").css(
			"min-height",
			$("#navMain").outerHeight()
		);
	}

	$(".parallax").parallax();

	setTimeout(function()  {
		$("#elastic_parent").elasticMenu();
	}, 300);

	if ($(document).find(".select2").length > 0) {
		$(".select2").select2();
	}

	// slick slider
	$("#header-slider").on("init", function(e, slick) {
		var $firstAnimatingElements = $("div.header-slide:first-child").find(
			"[data-animation]"
		);
		doAnimations($firstAnimatingElements);
	});
	$("#header-slider").on("beforeChange", function(
		e,
		slick,
		currentSlide,
		nextSlide
	) {
		var $animatingElements = $(
			'div.header-slide[data-slick-index="' + nextSlide + '"]'
		).find("[data-animation]");
		doAnimations($animatingElements);
	});
	if ($("#header-slider").length > 0) {
		$("#header-slider").slick({
			autoplay: true,
			autoplaySpeed: 4000,
			dots: false,
			arrows: true,
			// fade: true,
			pauseOnHover: false
		});
	}

	function doAnimations(elm) {
		var animationEndEvents =
			"webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend";
		elm.each(function() {
			var $this = $(this);
			var $animationDelay = $this.data("delay");
			var $animationType = "animated " + $this.data("animation");
			$this.css({
				"animation-delay": $animationDelay,
				"-webkit-animation-delay": $animationDelay
			});
			$this.addClass($animationType).one(animationEndEvents, function() {
				$this.removeClass($animationType);
			});
		});
	}

	/*
	$('input[type="file"]').each(function() {
		var finput = $(this);
		finput.on("change", function(e) {
			let filenames = [];

			let files = e.target.files;

			if (files.length > 1) {
				// filenames.push(files.length + " images added");
				filenames.push("Multiple images added");
			} else {
				for (let i in files) {
					if (files.hasOwnProperty(i)) {
						filenames.push(files[i].name);
					}
				}
			}
			$(this)
				.siblings(".custom-file-label")
				.html(filenames.join(","));
		});
	});
	*/

	$(".dropdown-submenu > a").on("click", function(e) {
		console.log("submenu clicked");
		if ($(window).innerWidth() < 992) {
			e.preventDefault();
		}

		var submenu = $(this);
		$(this)
			.parent()
			.siblings()
			.find(".dropdown-menu")
			.removeClass("show");
		submenu.next(".dropdown-menu").addClass("show");
		e.stopPropagation();
	});

	$(".dropdown").on("hidden.bs.dropdown", function() {
		// hide any open menus when parent closes
		$(".dropdown-menu.show").removeClass("show");
	});
});
$(window).resize(function() {
	setTimeout(function() {
		//console.log("resized to =>", $(window).innerWidth());
		menuDrop();
	}, 500);
});

$(window).scroll(function() {
	// console.log($(this).scrollTop());
	if ($(this).scrollTop() > 72) {
		$(".twt-navbar").addClass("fixed");
	} else {
		$(".twt-navbar").removeClass("fixed");
	}
});

// $(function() {
// 	$('[data-toggle="tooltip"]').tooltip();
// });

$(document).on("keypress", function(e) {
	if (e.keyCode == 13 && $("input:focus").val() != "") {
		$("input:focus")
			.parents("#searchFilter")
			.find('[onclick*="filterData()"]')
			.click();
	}
});


// test

function pushNotify(msg, title, i) {
	var notify = new Notification(title, {
		body: msg,
		icon:  i ? i : '',
	});
	if (!window.Notification) {
		console.log('Browser does not support notifications.');
	} else {
		// check if permission is already granted
		if (Notification.permission === 'granted') {

			// show notification here
			notify
			
		} else {
			// request permission from user
			Notification.requestPermission().then(function (p) {
				if (p === 'granted') {

					// show notification here
					notify

				} else {
					console.log('User blocked notifications.');
				}
			}).catch(function (err) {
				console.error(err);
			});
		}
	}
}

$( document ).ready(function() {
	$('.form-input').focus(function(){
		$(this).parents('.form-group').addClass('focused');
	  });
	  
	  $('.form-input').blur(function(){
		var inputValue = $(this).val();
		if ( inputValue == "" ) {
		  $(this).removeClass('filled');
		  $(this).parents('.form-group').removeClass('focused');  
		} else {
		  $(this).addClass('filled');
		}
	  })  
});

$.validator.addMethod('noemail', function (value) {
    return /^([\w-.]+@(?!gmail\.com)(?!yahoo\.com)(?!hotmail\.com)(?!mail\.ru)(?!yandex\.ru)(?!mail\.com)([\w-]+.)+[\w-]{2,4})?$/.test(value);
}, 'Free email addresses are not allowed.');




$( document ).ready(function() {
$('.text-areabox input, .text-areabox textarea').on('focus blur', function (e) {
	$(this).parents('.text-areabox').toggleClass('is-focused', (e.type === 'focus' || this.value.length > 0));
  }).trigger('blur');
});

function openBootstrapModal(modal_id){
	
	$('#' + modal_id).modal({
	    backdrop: 'static',
	    keyboard: false
	})
	
}

/*
function refreshToken(){
    $.get('refresh-csrf').done(function(data){
        csrfToken = data; // the new token
    });
    
    setInterval(refreshToken, 60000);
}
*/
/*
$.validator.addMethod("validateUniqueEmailAddress", function (value, element) {
	var result = true;
	var working_email_check_request = null;
	var currentRequest  = null;
	working_email_check_request = $.ajax({
		type: "POST",
		async: false,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		url: site_url + 'partner/checkUniqueEmail',
		dataType: "json",
		data: { 'business_email': $.trim($("[name='business_email']").val()), 'user_master_id': ( $.trim($("[name='hidden_partner_id']").val()) != '' ? $.trim($("[name='hidden_partner_id']").val()) : null) },
		beforeSend: function() {
			//block ui
			//showLoader();
			//working_email_check_request = true;
			if(working_email_check_request != null) {
				working_email_check_request.abort();
            }
		},
		success: function (response) {
			if (response.status_code == 1) {
				working_email_check_request = false;
				return false;
			} else {
				result = false;
				return true;
			}
		}
	});
	return result;
}, 'This Email is already in use. Please try another email.');
*/





function updateRecordStatus( thisitem , update_url = null){

	var record_id = $.trim($(thisitem).attr('data-record-id'))
	var current_status = $.trim($(thisitem).attr('data-current-status'))
	
	var updateRecordUrl =  ( ( update_url != "" && update_url != null ) ? update_url : window.location.href );
	
	$.ajax({
		type: "POST",
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		url: updateRecordUrl + '/updateStatus',
		dataType: "json",
		data: { 'current_status': current_status ,  'record_id' : record_id },
		beforeSend: function() {
			//block ui
			showLoader();
			
		},
		success: function (response) {
			hideLoader();
			//refreshToken();
			if (response.status_code == 1) {
				var update_status = response.data.update_status;	
				$(thisitem).attr('data-current-status' , update_status);
				$(thisitem).parents('.custom-switch').find('.switch-title').html(update_status);
				alertifyMessage('success' , response.message );
			} else {
				alertifyMessage('error' , response.message );
			}
		}
	})
}

$(document).keypress(function(event){
	
	var element_class_list = ( ( $(event.target).attr('class') != "" && $(event.target).attr('class') != null )  ? $(event.target).attr('class') : "" )
	
	var elementClassArray = element_class_list.length > 0 ? element_class_list.split(' ') : [];
	
	if(jQuery.inArray("twt-enter-search", elementClassArray) !== -1){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			filterData();
		}
	}
});



function getFileBaseName(str)
{
	var base = '';
	if( str != "" && str != null ){
	  var base = new String(str).substring(str.lastIndexOf('/') + 1); 
	    if(base.lastIndexOf(".") != -1)       
	        base = base.substring(0, base.lastIndexOf("."));
		
	}
 
   return base;
}

function checkAll(thisitem){
	
	$(thisitem).closest('table').find('.all-checkbox').prop('checked' , $(thisitem).prop('checked'));
	
}

$(document).ajaxStart(function(){
   //showLoader();
});

$(document).ajaxStop(function(){
   //hideLoader();
});


function thousands_separators(num)
{
  var num_parts = num.toString().split(".");
  console.log(num_parts);
  num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  if(num_parts[1] != "" && num_parts[1] != null ){
	  num_parts[1] = parseInt(num_parts[1]);
	  num_parts[1] = num_parts[1].toFixed(2);
  }
  return num_parts.join(".");
}

function formatMoney(amount, decimalCount = 0, decimal = ".", thousands = ",") {
  try {
    decimalCount = Math.abs(decimalCount);
    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

    const negativeSign = amount < 0 ? "-" : "";

    let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
    let j = (i.length > 3) ? i.length % 3 : 0;

    return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
  } catch (e) {
    console.log(e)
  }
};

function ucword( message ){
	var str = '';
	str = message.toLowerCase().replace(/\b[a-z]/g, function(letter) {
	    return letter.toUpperCase();
	});
	return str;
}

function enumText(str){
	var str = str.replace(/_/g, ' ');
	str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
	    return letter.toUpperCase();
	});
	return str;
}

$(document).on('click', '.remove-all-btn', function() {
	$("#filter").find("[name='search_status']").val('').trigger('change');
});

$(function(){
	$(".modal").find("form").on("submit",function(e){
		e.preventDefault();
	    e.stopPropagation();
	});
});