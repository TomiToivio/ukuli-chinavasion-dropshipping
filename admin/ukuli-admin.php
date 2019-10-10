<?php
/**
 * Enqueue JavaScript and CSS files.
 */
function ukuli_chinavasion_enqueue_scripts() {
	 wp_enqueue_script( 'jquery' );
	 wp_enqueue_script( 'ukuli_chinavasion_script', plugin_dir_url( __FILE__ ) . 'js/ukuli_chinavasion.js');
	 wp_enqueue_style( 'ukuli_chinavasion_style', plugin_dir_url( __FILE__ ) . 'css/ukuli_chinavasion.css');
	 wp_localize_script( 'ukuli_chinavasion_script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
}
add_action('admin_enqueue_scripts', 'ukuli_chinavasion_enqueue_scripts');

/**
 * Options page for plugin.
 */
add_action( 'admin_menu', 'ukuli_chinavasion_add_admin_menu' );

function ukuli_chinavasion_add_admin_menu(  ) {
	 add_menu_page( 'Chinavasion Dropshipping', 'Chinavasion Dropshipping', 'manage_options', 'ukuli_chinavasion', 'ukuli_chinavasion_options_page', 'dashicons-cart' );
}

function ukuli_chinavasion_options_page(  ) {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
<script>
    jQuery(document).ready(function(){
	    jQuery('ul.tabs li').click(function(){
	       var tab_id = jQuery(this).attr('data-tab');
	       jQuery('ul.tabs li').removeClass('current');
	       jQuery('.tab-content').removeClass('current');
	       jQuery(this).addClass('current');
	       jQuery("#"+tab_id).addClass('current');
	   });
    });
</script>

<style>
#chinavasion_import {
    display:none;
}     
.ukuli-chinavasion-title {
    padding:20px;
}

.container{
    width: 90%;
    margin: 0 auto;
}

ul.tabs {
    margin: 0px;
    padding: 0px;
    list-style: none;
}

ul.tabs li  {
    background: none;
    color: #222;
    display: inline-block;
    padding: 10px 15px;
    cursor: pointer;
    border: #000000 1px solid;
}

ul.tabs li.current{
    box-shadow: 2px 2px 2px #A9A9A9;
}

.tab-content{
    display: none;
    border: #000000 1px solid;
    padding:5px;
}

.chinavasion_category_button, #chinavasion_api_button, .chinavasion_product_button {
    margin: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}   
    
.chinavasion_category_button:hover, #chinavasion_api_button:hover, .chinavasion_product_button:hover {
    box-shadow: 2px 2px 2px #A9A9A9;
} 

.chinavasion_category_button:active, #chinavasion_api_button:active, .chinavasion_product_button:active {
    box-shadow: 2px 2px 2px #A9A9A9;
} 
    
.tab-content.current{
    display: inherit;
}
</style>

<div class="container">
<h1 class="ukuli-chinavasion-title"><?php echo __( 'Chinavasion Dropshipping', 'ukuli' ); ?></h1>
<ul class="tabs">
      <li class="tab-link<?php if(!empty(get_option("ukuli_chinavasion_api_key"))) { echo " current"; } ?>" data-tab="tab-1"><?php echo __( 'Import', 'ukuli' ); ?></li>
      <li class="tab-link" data-tab="tab-2"><?php echo __( 'Status', 'ukuli' ); ?></li>
      <li class="tab-link<?php if(empty(get_option("ukuli_chinavasion_api_key"))) { echo " current"; } ?>" data-tab="tab-3"><?php echo __( 'Settings', 'ukuli' ); ?></li>
</ul>
		<?php if(!empty(get_option("ukuli_chinavasion_api_key"))) { ?>
        	<div id="tab-1" class="tab-content current">
		<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.js'></script>
                <h3 id="chinavasion_import_title"><?php echo __( 'Import Chinavasion product', 'ukuli' ); ?></h3>
                <div id="chinavasion_categories" class="chinavasion_scroll">
                <?php
                $ukuliChinavasionAPI = new ukuliChinavasionAPI();
                $ukuliChinavasionAPI->chinavasionCategories();
                ?>
                </div>
                <div id="chinavasion_products" class="chinavasion_scroll"></div>
                <div id="chinavasion_import"></div>
                <style>
                    #chinavasion_import {
                        position: fixed;
                        left: 25%;
                        width: 50%;
                        top: 40%;
                        border: #000000 1px solid;
                        -webkit-box-shadow: 5px 5px 5px 0px #A9A9A9;
                        -moz-box-shadow: 5px 5px 5px 0px #A9A9A9;
                        box-shadow: 5px 5px 5px 0px #A9A9A9;
                        background-color: #f1f1f1;
                    }
                    #chinavasion_import p {
                        padding:20px; 
                    }
                    #chinavasion_import a {
                        text-decoration:underline;
                    }
                    .chinavasion_scroll {
                        overflow-y: auto;
                    }
		    #chinavasion_import_close, #chinavasion_category_close {
			margin:10px;
			font-size:150%;
        		float:right;
        		height:20px;
        		width:20px;
        		background: transparent;
        		color: #000000;
		    }
                </style>
                <script>
                jQuery( document ).ready(function() {
                    jQuery("#chinavasion_products").hide();
                    jQuery("#chinavasion_import").hide();
                    jQuery("button.chinavasion_category_button").click(function(event) {
                        event.preventDefault();
                        jQuery.blockUI();
                        var ajaxurl = ajax_object.ajax_url;
                        var category = jQuery(this).val();
                        var security = "<?php echo wp_create_nonce( "chinavasion_category_action" ); ?>";
                        chinavasionCategoryAjax(category, security);
                    });    
                });
                function chinavasionCategoryAjax(category, security) {
                    jQuery.blockUI();
                    var ajaxurl = ajax_object.ajax_url;
                    var data = {
		          action: 'ukuli_chinavasion_category_action',
		          category: category,
                          security: security,
                    };
                    jQuery.post(ajaxurl, data, function(response) {
	               jQuery.unblockUI();
	               jQuery("#chinavasion_products").html('<span id="chinavasion_category_close" class="dashicons dashicons-dismiss"></span>' + response);
                       jQuery("#chinavasion_products").show();
		       jQuery("#chinavasion_categories").hide();
		       jQuery("#chinavasion_import_title").hide();
                       chinavasion_product_button_attach();
                    });
                }
                function chinavasion_product_button_attach() {
                       jQuery("#chinavasion_category_close").click(function(event) {
                                location.reload();
                       });
                    jQuery("button.chinavasion_product_button").click(function(event) {
                        event.preventDefault();
                        jQuery.blockUI();
                        var ajaxurl = ajax_object.ajax_url;
                        var product = jQuery(this).val();
                        var security = "<?php echo wp_create_nonce( "chinavasion_product_action" ); ?>";
                        chinavasionProductAjax(product, security);
                    });    
                }
                function chinavasionProductAjax(product, security) {
                    jQuery.blockUI();
                    var ajaxurl = ajax_object.ajax_url;
                    var data = {
			action: 'ukuli_chinavasion_product_action',
			product: product,
			security: security,
                    };
                    jQuery.post(ajaxurl, data, function(response) {
	               jQuery.unblockUI();
	               jQuery("#chinavasion_import").html('<span id="chinavasion_import_close" class="dashicons dashicons-dismiss"></span>' + response);
                       jQuery("#chinavasion_import").show();
		       jQuery(document.body).on("click", ":not(#chinavasion_import, #chinavasion_import *)", function(e){ 
    				location.reload();
		       });
                       jQuery("#chinavasion_import_close").click(function(event) { 
                       		location.reload();
                       });
                    });
                }
                </script>
        <?php } ?>
        </div>
    	<div id="tab-2" class="tab-content">
        <?php if(!empty(get_option("ukuli_chinavasion_api_key"))) { ?>
        <?php echo '<h3>' . __( 'Chinavasion balance', 'ukuli' ) . '</h3>';
            $ukuliChinavasionAPI = new ukuliChinavasionAPI();
            $ukuliChinavasionAPI->chinavasionBalance();                                                           
        }
	?><h3><?php echo __( 'Chinavasion Logs', 'ukuli' ); ?></h3><?php
    	$ukuliChinavasionAPI = new ukuliChinavasionAPI();
    	$ukuliChinavasionAPI->chinavasionLogViewer();                                                           
    	echo "</div>";
    ?>
    <div id="tab-3" class="tab-content <?php if(empty(get_option("ukuli_chinavasion_api_key"))) { echo "current"; } ?>">
        <h3><?php echo __( 'Chinavasion API key', 'ukuli' ); ?></h3>
        <form id="chinavasionapi" class="chinavasionapi">
  		<input type="text" id="chinavasionapi" class="chinavasionapi" name="chinavasionapi" placeholder="<?php echo __( 'API key', 'ukuli' ); ?>" required value = "<?php echo get_option( 'ukuli_chinavasion_api_key' ); ?>"><br>
        <?php wp_nonce_field( 'ukuli_chinavasion_api_action', 'security' ); ?>
  		<input type="submit" id="chinavasion_api_button" value="<?php echo __( 'Post', 'ukuli' ); ?>">
        </form> 
        <div id="chinavasion_api_result" class="chinavasion_api_result"></div>
    </div>
<?php } ?>
