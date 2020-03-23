<?php
    /*
        Plugin Name: Discount
        Description: Discount Settings
        Version: 2.0.0
        Author: Vladimir
    */

	register_activation_hook( __FILE__, 'discount_vl_activate' );
    
	require_once 'inc/DiscountAdmin.php';
	
	function discount_vl_activate () {

    }

	add_action('wp_enqueue_scripts', 'discount_vl_styles');
    function discount_vl_styles() {
        wp_register_style( 'discount-vl-style', plugins_url('/assets/css/style.css', __FILE__) );
		wp_enqueue_style( 'discount-vl-style' );
		wp_enqueue_script( 'discount-vl-cookie', plugins_url('/assets/js/jquery.cookie.js', __FILE__), array('jquery') );
		wp_enqueue_script( 'discount-vl-front', plugins_url('/assets/js/script.js', __FILE__), array('jquery') );
		wp_localize_script('discount-vl-front', 'MyAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'my-special-string' ),
			'redirecturl' => home_url(),
			'loadingmessage' => __('Sending info, please wait...'),
		));
    }
?>
<?php

// add_filter('woocommerce_product_get_regular_price', 'discount_price', 99, 2);
// add_filter('woocommerce_product_get_sale_price', 'discount_price', 99, 2);
add_filter('woocommerce_product_get_price', 'discount_price', 99, 2);

 function discount_price ($price, $product) {
	global $post, $woocommerce, $options;
	$post_id = $product->id;
	
	if( is_user_logged_in() && !empty($options['vl_register'])) {
		$discount = $price / 100 * $options['vl_register'];
		$discount_price = $price - $discount;
	
	} else if( !is_user_logged_in() && $_COOKIE['vl_period'] == 'discount') {
		$discount = $price / 100 * $options['vl_period'];
		$discount_price = $price - $discount;
	} else if( !is_user_logged_in() && $_COOKIE['vl_exit'] == 'exit') {
		$discount = $price / 100 * $options['vl_exit'];
		$discount_price = $price - $discount;	
	} else {
		$discount_price = $price;
	}
	
	 
	return $discount_price;
 }
 
global  $options;

$expire = 86400 - 3600*date("H") - 60*date("i") - date("s");
if ($_COOKIE['vl_period'] != 'step1' && $_COOKIE['vl_period'] != 'discount') {
	setcookie("vl_period", 'step1', time()+$expire, "/");  
}

if ($_COOKIE['vl_period'] == 'step1') {
	setcookie("vl_period", 'step2', time()+$expire, "/"); 
}  
 
 
add_action( 'wp_footer', 'vl_discount_exit_from');
function vl_discount_exit_from () {
	global $post, $woocommerce, $options;
	$exit_discount = $options['vl_exit'];
	if (!empty($exit_discount)) { 
?>
	<div class="outss"><div class="outssback"></div>
		<div class="outsstext"><div class="outsscl">x</div>
			Press Continue shopping and get a discount <?php echo $exit_discount . '%';?>
			<form>
				<input class="exit_discount" name="exit" type="text" value="<?php echo $exit_discount;?>">
				<button type="submit" class="exit_button">Continue shopping</button>
			</form>
		</div>
	</div>
	<script>
		jQuery( function ($) {
			if (typeof $.cookie('vl_exit') === 'undefined' && $.cookie('vl_period') != 'step2') {
				if ($.cookie('vl_period') == 'discount') { return false; }
				$(document).mouseleave(function(e){ 
					if (e.clientY < 0) { 
						$(".outss").addClass("outssye"); 
						//$.cookie('vl_period', 'step0');
					} 
				});
				$('.exit_button').click( function (e) { 
					e.preventDefault();
					var discount_exit = $('.exit_discount').val();
					
					$.cookie('vl_exit', 'exit', { expires: 1, path: '/' });
					//console.log($.cookie('vl_period'));
					
					if($(".outss").hasClass('outssye')) { 
						$(".outss").css('display', 'none'); 
						location.reload()
					} 
				
				});	
			}
			
			//alert($.cookie('vl_period'));
			
		});
	</script>
<?php
 }
}
 
 



 
add_action( 'wp_footer', 'vl_return_from');
	
function vl_return_from () {
	global $post, $woocommerce, $options;
	$return_discount = $options['vl_period'];
	if (!empty($return_discount)) { 
	
	
?>

	<div class="outss_new"><div class="outssback_new"></div>
		<div class="outsstext_new"><div class="outsscl_new">x</div>
			Thank you for coming back. Your discount <?php echo $return_discount . '%';?>
			<form>
				<input class="return_discount_discount" name="period" type="text" value="<?php echo $return_discount;?>">
				<button type="submit" class="return_discount_button">Continue shopping</button>
			</form>
		</div>
	</div>
	<script>
		jQuery( function ($) {

			<?php if ($_COOKIE['vl_period'] == 'step2' && $_COOKIE['vl_exit'] != 'exit') { ?>
				$(".outss_new").addClass("outssye_new"); 

				$('.return_discount_button').click( function (e) { 
					e.preventDefault();
					
					$.cookie('vl_period', 'discount', { expires: 1, path: '/' });
					
					console.log($.cookie('vl_period'));
					if($(".outss_new").hasClass('outssye_new')) { 
						$(".outss_new").css('display', 'none'); 

						location.reload()
					
					} 
				});	
			<?php } ?>	
		});
	</script>
		
	
<?php
 }
}


// echo '<pre>';
// print_r($_COOKIE);
// echo '</pre>';

