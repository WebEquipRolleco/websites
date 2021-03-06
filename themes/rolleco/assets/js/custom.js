$(document).ready(function() {

	loadQtyTouchSpin();

	/*$.fn.right = function() {
  		return $(document).width() - (this.offset().left + this.outerWidth());
	}*/

	$(document).on('click', '.discount-code', function(e) {
		e.preventDefault();

		var code = $(this).data('code');
		var input = $('input[name=discount_name]');
		
		input.val(code);
		input.closest('form').submit();
	});

	$('.add-to-cart').on('click', function() {
		$(document).find('#modal_product_added').remove();
	});

	$('#previous_menu').on('click', function(e) {

		var element = window.menu.shift();
		window.menu.push(element);

		$('.show-menu').remove();
		for(let i=0; i<window.menu_elements; i++)
			$('#megamenu').append(window.menu[i]);

		$(".show-menu").last().find('li').css("border-right", "0px");
	});

	$('#next_menu').on('click', function(e) {
		
		var element = window.menu.pop();
		window.menu.unshift(element);
		
		$('.show-menu').remove();
		for(let i=0; i<window.menu_elements; i++)
			$('#megamenu').append(window.menu[i]);

		$(".show-menu").last().find('li').css("border-right", "0px");
	});

	var menu_timeout;
	$(document).on('mouseover', '.show-menu', function() {
		clearTimeout(menu_timeout);

		var id_menu = $(this).data('id');
		menu_timeout = setTimeout(function() {
			$('.megamenu_level_2').hide();
			$('#submenu_'+id_menu).slideDown('fast');
		}, 100);
	});

	$(document).on('mouseout', '.show-menu', function() {
		clearTimeout(menu_timeout);
	});

	$('#brand_nav').on('mouseover', function() {
		$('.megamenu_level_2').slideUp('fast');
	}); 
	
	$('#wrapper').on('mouseover', function() {
		$('.megamenu_level_2').slideUp('fast');
	});

	$('#footer').on('mouseover', function() {
		$('.megamenu_level_2').slideUp('fast');
	});

	$('.display-step').on('click', function(e) {
		e.preventDefault();

		$('.-current').removeClass('-current');
		var target = "#"+$(this).data('target');
		$(target).addClass('-current');
	});

	$('#display_mobile_menu').on('click', function() {
		$('#iziMenu').iziModal({
			headerColor: "#1e4688",
			icon: 'fas fa-list',
            title: "<b>Rolléco</b>",
            transitionIn: "bounceInDown",
            transitionOut: "bounceOutUp",
            closeButton: true,
            width: "100%",
            autoOpen: 1,
            top: '0px'
		});
	});

	/*$('.show-cart-summary').on('mouseenter', function() {
		
		var right = $('#_desktop_cart').right();
		

		$('#shopping_cart_summary').iziModal({
			headerColor: "#1e4688",
            icon: 'fa fa-shopping-cart',
            title: "Mon panier",
            subtitle: "1 produit",
            top: "50px",
            width: "400px",
            transitionIn: "bounceInDown",
            transitionOut: "bounceOutUp",
            closeButton: true,
            overlay: false,
            autoOpen: 1,
            timeout: 5000,
            pauseOnHover: true,
            afterRender: function() {
            	$('#shopping_cart_summary').css('margin-right', right+'px');
            	console.log($('#_desktop_cart').right());
            }
         });
		
	});*/

	$(document).on('change', '.combination-quantity', function() {
		
		var element = $(this);
		var id_combination = $(this).data('id-combination');
		var value = $(this).val();

		$('.specific_prices_'+id_combination).removeClass('active');

		var found = false;
		$('.specific_prices_'+id_combination).each(function() {

			var min = $(this).data('min');
			var max = $(this).data('max');

			if((value >= min || !min) && (value <= max || !max)) {
				element.data('price', $(this).data('price'));
				$(this).addClass('active');
			}
		});

		updateSelectionPrice();
	});

	$(document).on('click', '#add_all_to_cart', function(e) {

		var id_product = $('#product_page_product_id').val();
		var token = $('input[name=token]').val();

		$('.combination-quantity').each(function() {
			if($(this).val() > 0) {

				var id_selected_product = $(this).data('id-product');
				var id_combination = $(this).data('id-combination');
				var qty = $(this).val();

				if(id_selected_product) {
					$(document).queue("cart", function() {
						$.ajax({
							url: prestashop.urls.pages.cart,
							data: {
								'ajax':true, 
								'add': true, 
								'id_product' : id_selected_product,
								'id_product_attribute' : 0,
								'qty' : qty,
								'token' : token
							},
						}).always(function() {
							$(document).dequeue("cart");
						});
					});
				}
				else {
					$(document).queue("cart", function() {
						$.ajax({
							url: prestashop.urls.pages.cart,
							data: {
								'ajax':true, 
								'add': true, 
								'id_product' : id_product,
								'id_product_attribute' : id_combination,
								'qty' : qty,
								'token' : token
							}
						}).always(function() {
							$(document).dequeue("cart");
						});
					});
				}
			}

			$(document).queue("cart", function() {
				reloadCartPreview();
				$(document).dequeue("cart");
			});
			
			$(document).dequeue("cart");
		});

	});

	$(document).on('click', '.display-image', function(e) {
		e.preventDefault();
		$('#product_image_'+$(this).data('image-id')).click();
	});

	$('#quick_navigation').on('click', function(e) {

      $('#modal_navigation').iziModal({
        headerColor: "#1e4688",
        icon: 'fas fa-list',
        title: "Mon Compte",
        subtitle: "Navigation rapide",
        padding: "15px",
        closeButton: true,
        autoOpen: 1
      });

    });

	$('.js-qv-mask').addClass('scroll');
});

function updateSelectionPrice() {

	var price = 0;
	$('.combination-quantity').each(function() {
		
		var nb = $(this).val();
		var current = parseFloat($(this).data('price'));
		if(current) price = price + (nb * current);
	});

	if(price) {
		price_wt = price * 1.2;
		$('#total_price_selection').html(price.toFixed(2)+" € HT");
		$('#total_price_selection_wt').html(price_wt.toFixed(2)+" € TTC");
		$('#total_price_selection').fadeIn('fast');
		$('#total_price_selection_wt').fadeIn('fast');
		$('#add_all_to_cart').removeClass('disabled');
		$('#add_all_to_cart').prop('disabled', false);
	}
	else {
		$('#total_price_selection').fadeOut('fast');
		$('#total_price_selection_wt').fadeOut('fast');
		$('#add_all_to_cart').addClass('disabled');
		$('#add_all_to_cart').prop('disabled', true);
	}

}

function loadQtyTouchSpin() {
	$('.combination-quantity').TouchSpin({
		verticalbuttons:!0,
		verticalupclass:"material-icons touchspin-up",
		verticaldownclass:"material-icons touchspin-down",
		buttondown_class:"btn btn-touchspin js-touchspin js-increase-product-quantity",
		buttonup_class:"btn btn-touchspin js-touchspin js-decrease-product-quantity",
		min: $(this).prop('min'),
		max: 1e6,
	});
}

var preview_timeout;
function reloadCartPreview() {
	clearTimeout(preview_timeout);
	preview_timeout = setTimeout(function() {
		$.ajax({
			url: $('.cart-preview').data('refresh-url'),
			dataType: 'json',
			data : {
				'action': 'add-to-cart'
			},
			success : function(response) {

				$('#_desktop_cart').replaceWith(response.preview);
				if(response.modal) $('#ajax_content').html(response.modal);
			}	
		});
	}, 250);
}