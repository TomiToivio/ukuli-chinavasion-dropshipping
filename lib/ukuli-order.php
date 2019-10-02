<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Chinavasion order data on WooCommerce order admin page.
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'ukuli_chinavasion_order_list');

function ukuli_chinavasion_order_list($order) {
	global $woocommerce;
    $ukuliChinavasionAPI = new ukuliChinavasionAPI();
    $chinavasion_sent = get_post_meta( $order->get_id(), "ukuli_chinavasion_order_sent", true);
    if($chinavasion_sent != 1) {
        $data = $order->get_data();
        $country = $data["shipping"]["country"];
        $products = array();
        $items = $data["line_items"];
        $chinavasion_items = 0;
        foreach($items as $item) {
            $ukuli_chinavasion_dropshipping = get_post_meta($item["product_id"], 'ukuli_chinavasion_dropshipping', true );
            if($ukuli_chinavasion_dropshipping == "yes") {
                array_push($products,array("product_id" => get_post_meta( $item["product_id"], 'chinavasion_id', true ), "quantity" => $item["qty"]));
                $chinavasion_items++;
            }
        }
        if($chinavasion_items > 0) {
            $chinavasion_price = $ukuliChinavasionAPI->chinavasionGetPrice($products, $country);
		    echo '<div class="clear"></div>';
		    echo "<h3>" . __( 'Chinavasion order', 'ukuli' ) . "</h3>";
		    echo '<div>';
		    echo "<table class='chinavasion-table'><tr><th>" . __( 'Shipping', 'ukuli' ) . "</th><th>" . __( 'Price', 'ukuli' ) . "</th><th>" . __( 'Delivery', 'ukuli' ) . "</th></tr>";
		    foreach($chinavasion_price->shipping as $shipping) {
		        echo "<tr>";
		        echo "<td>" . $shipping->name . "</td>";
		        echo "<td>" . $shipping->price . "</td>";
		        echo "<td>" . $shipping->delivery . "</td>";
		        echo "</tr>";
		    }
		    echo "</table>";
		    echo "<table class='chinavasion-table'><tr><th>" . __( 'Product ID', 'ukuli' ) . "</th><th>" . __( 'Quantity', 'ukuli' ) . "</th><th>" . __( 'Price', 'ukuli' ) . "</th><th>" . __( 'Total', 'ukuli' ) . "</th></tr>";
		    foreach($chinavasion_price->products as $product) {
		        echo "<tr>";
		        echo "<td>" . $product->product_id . "</td>";
		        echo "<td>" . $product->quantity . "</td>";
		        echo "<td>" . $product->price . "</td>";
		        echo "<td>" . $product->total . "</td>";
		        echo "</tr>";
		    }
		    echo "</table>";
		    echo '<div id="chinavasion_order" class="chinavasion_order">';
		    echo '<strong>' . __( "Shipping", "ukuli" ) . '</strong>: <select name="chinavasion_shipping" id="chinavasion_shipping" class="chinavasion_shipping">';
		    foreach($chinavasion_price->shipping as $shipping) {
		        echo '<option value="' . $shipping->name . '">' . $shipping->name . '</option>';
		    }
		    echo '</select><br>';
		    echo '<strong>' . __( "Payment", "ukuli" ) . '</strong>: <select name="chinavasion_payment" id="chinavasion_payment" class="chinavasion_payment">';
		    echo '<option value="PayPal">PayPal</option>'; 
		    echo '<option value="Bank Transfer">Bank Transfer</option>';
		    echo '<option value="Credit Card">Credit Card</option>';
		    echo '</select><br>';
		    echo '<input type="hidden" name="chinavasion_order_id" id="chinavasion_order_id" class="chinavasion_order_id" value="' . $order->get_id() . '">';
            wp_nonce_field('ukuli_chinavasion_order_action', 'chinavasion_order_security');
		    echo '<button id="chinavasion_order_button" class="chinavasion_order_button">' . __( "Order", "ukuli" ) . '</button>';
		    echo '<div id="chinavasion-result"></div>';
		    echo '</div>';
		    echo "</div>";
		}
	}
}