<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ukuli_chinavasion_product_tabs( $tabs) {
	 global $woocommerce, $post;
         $tabs['chinavasion'] = array(
                        'label'         => __( 'Chinavasion Dropshipping', 'ukuli' ),
                        'target'        => 'chinavasion_options',
                        'class'         => array( 'show_if_simple'),
                        );
        return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'ukuli_chinavasion_product_tabs' );

function ukuli_chinavasion_options_product_tab_content() {
	global $woocommerce, $post;
	 ?><div id='chinavasion_options' class='panel woocommerce_options_panel'
	 ><div class='options_group'><?php
        woocommerce_wp_select( array(
        'id'            => 'ukuli_chinavasion_dropshipping',
        'label'         => __( 'Chinavasion Dropshipping', 'ukuli'),
        'desc_tip'      => 'true',
        'description'   => __( 'Is this a Chinavasion dropshipping product?', 'ukuli' ),
        'value'       => get_post_meta($post->ID, 'ukuli_chinavasion_dropshipping', true ),
        'options' => array(
					'no'  => __( 'No', 'ukuli' ),
					'yes'  => __( 'Yes', 'ukuli' ),
					)
        ) );
        woocommerce_wp_text_input( array(
        'id'            => 'chinavasion_product_url',
        'label'         => __( 'Chinavasion product url', 'ukuli'),
        'desc_tip'      => 'true',
        'description'   => __( 'Chinavasion product url', 'ukuli' ),
        'type'          => 'text',
        ) );
        woocommerce_wp_text_input( array(
        'id'            => 'chinavasion_model_code',
        'label'         => __( 'Chinavasion model code', 'ukuli'),
        'desc_tip'      => 'true',
        'description'   => __( 'Chinavasion model code', 'ukuli' ),
        'type'          => 'text',
        ) );
        woocommerce_wp_text_input( array(
        'id'            => 'chinavasion_id',
        'label'         => __( 'Chinavasion ID', 'ukuli'),
        'desc_tip'      => 'true',
        'description'   => __( 'Chinavasion ID', 'ukuli' ),
        'type'          => 'text',
        ) );
        woocommerce_wp_text_input( array(
        'id'            => 'chinavasion_ean',
        'label'         => __( 'Chinavasion EAN', 'ukuli'),
        'desc_tip'      => 'true',
        'description'   => __( 'Chinavasion EAN', 'ukuli' ),
        'type'          => 'text',
        ) );
	?></div></div>
	<?php }
add_action( 'woocommerce_product_data_panels', 'ukuli_chinavasion_options_product_tab_content' );

function ukuli_save_chinavasion_option_fields( $post_id ) {
         update_post_meta( $post_id, 'chinavasion_product_url', sanitize_text_field( $_POST['chinavasion_product_url'] ) );
         update_post_meta( $post_id, 'chinavasion_model_code', sanitize_text_field( $_POST['chinavasion_model_code'] ) );
         update_post_meta( $post_id, 'chinavasion_id', sanitize_text_field( $_POST['chinavasion_id'] ) );
         update_post_meta( $post_id, 'chinavasion_ean', sanitize_text_field( $_POST['chinavasion_ean'] ) );
         update_post_meta( $post_id, 'ukuli_chinavasion_dropshipping', sanitize_text_field( $_POST['ukuli_chinavasion_dropshipping'] ) );
}
add_action( 'woocommerce_process_product_meta_ukuli_chinavasion', 'ukuli_save_chinavasion_option_fields'  );

add_action( "woocommerce_before_shop_loop_item", "ukuli_check_chinavasion_availability", 10);
add_action( "woocommerce_before_single_product", "ukuli_check_chinavasion_availability", 10 );
function ukuli_check_chinavasion_availability() {
	 global $product;
	 $product_id = $product->get_id();
     $ukuli_chinavasion_dropshipping = get_post_meta($product_id, 'ukuli_chinavasion_dropshipping', true );
	 if($ukuli_chinavasion_dropshipping == "yes") {
	    $lasttimestamp = get_post_meta( $product_id, "ukuli_chinavasion_availability_timestamp", true);
	    $currenttimestamp = time();
	    $timepassed = intval($currenttimestamp) - intval($lasttimestamp);
	    if($timepassed > 3600) {
	   		update_post_meta( $product_id, "ukuli_chinavasion_availability_timestamp", $currenttimestamp);
            $ukuliChinavasionAPI = new ukuliChinavasionAPI();
            $result = $ukuliChinavasionAPI->chinavasionCheckProductAvailability($product_id);
            echo "<script>";
            echo "jQuery( document ).ready(function() {";
            echo "location.reload();";
            echo "console.log(" . $result . ");";
            echo "});";
            echo "</script>";
		}
	}
}
