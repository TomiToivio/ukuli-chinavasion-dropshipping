<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
  * Class for requests to the Chinavasion API.
  */

class ukuliChinavasionAPI {
    private $chinavasionApiKey;
    private $wpdb;
    public $chinavasionApiUrl = "https://secure.chinavasion.com/api/";
    public $apiError = false;
    public $apiErrorMessage;
    public $apiJson;
    public $httpCode;
    public $productExists = false;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->chinavasionApiKey = get_option( 'ukuli_chinavasion_api_key' );
    }

    /**
     * Curl request to API.
     */

    public function chinavasionRequest($apiMethod, $requestData) {
	    $dataString = json_encode($requestData);
	    $ch = curl_init($this->chinavasionApiUrl . $apiMethod);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
      curl_setopt($ch, CURLOPT_NOBODY, FALSE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    	 		  'Content-Type: application/json',
        		  'Content-Length: ' . strlen($dataString))
	    );
      $this->apiJson = curl_exec($ch);
      $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      $this->apiJson = json_decode($this->apiJson);
	    if(isset($this->apiJson->error)) {
		  $this->apiError = true;
		  $this->apiErrorMessage = $this->apiJson->error_message;
          $logMessage = __("API Error:","ukuli") . ' ' . $this->apiErrorMessage;
          $this->chinavasionLogEvent($logMessage);
	    }
        return $this->apiJson;
    }

    /**
     * Get Chinavasion categories.
     */

    public function chinavasionCategories() {
       $requestData = array("key" => $this->chinavasionApiKey);
       $apiMethod = "getCategory.php";
       $categories = $this->chinavasionRequest($apiMethod,$requestData);
       if($this->apiError == false) {
           $categories = $categories->categories;
           foreach($categories as $category) {
               $categoryName = $category->name;
               $categoryUrl = $category->url;
               $categoryImage = $category->image;
               echo '<h3 class="chinavasion_category_title">' . $categoryName . '</h3>';
               echo '<p>';
               echo '<button class="chinavasion_category_button chinavasion_category" ';
               echo 'value="' . $categoryName . '">';
               echo $categoryName;
               echo '</button>';
               if(isset($category->subcategories)) {
                $categorySubcategories = $category->subcategories;
                foreach($categorySubcategories as $categorySubcategory) {
                   $subCategoryName = $categorySubcategory->name;
                   echo '<button class="chinavasion_category_button chinavasion_subcategory" ';
                   echo 'value="' . $subCategoryName . '">';
                   echo $subCategoryName;
                   echo '</button>';
                }
               }
               echo '</p>';
           }
       } else {
	       echo "<p>" . __("API Error message: ", "ukuli") . " " . $this->apiErrorMessage . "</p>";
       }
    }

    /**
     * Get Chinavasion products.
     */

    public function chinavasionProducts($category) {
        $paginationStart = 0;
        $paginationCount = 50;
        $paginationTotal = 100;
        $result = "";
        $result .= '<h3 class="ukuli_admin_title">' . __( 'Select product', 'ukuli' ) . '</h3>';
        while($paginationStart <= $paginationTotal) {
            $paginationArray = array(
                'start'=> $paginationStart,
                'count'=> $paginationCount,
            );
            $paginationObject = (object) $paginationArray;
            $requestData = array("key" => $this->chinavasionApiKey,
                                 "categories" => array($category),
                                 "pagination" => $paginationObject);
            $apiMethod = "getProductList.php";
            $products = $this->chinavasionRequest($apiMethod,$requestData);
            if($this->apiError == false) {
                $paginationTotal = $products->pagination->total;
                $products = $products->products;
                foreach($products as $product) {
                    $productName = $product->short_product_name;
                    $productId = $product->product_id;
                    $result .= '<button class="chinavasion_product_button" ';
                    $result .= 'value="' . $productId . '">';
                    $result .= $productName;
                    $result .= '</button>';
                }
                $paginationStart = $paginationStart + 50;
            } else {
                $result = "<p>" . __("API Error message: ", "ukuli") . " " . $this->apiErrorMessage . "</p>";
                return $result;
            }
        }
        return $result;
    }

    /**
     * Get Chinavasion balance.
     */

    public function chinavasionBalance() {
       $requestData = array("key" => $this->chinavasionApiKey);
       $apiMethod = "getCreditBalance.php";
       $creditBalance = $this->chinavasionRequest($apiMethod,$requestData);
       if($this->apiError == false) {
           $creditBalance = $creditBalance->credit_balance;
           foreach($creditBalance as $balance) {
               echo '<p>';
               echo $balance->amount;
               echo " ";
               echo $balance->currency;
               echo "</p>";
           }
       } else {
	       echo "<p>" . __("API Error message: ", "ukuli") . " " . $this->apiErrorMessage . "</p>";
       }
    }

    /**
     * Import product images from Chinavasion API.
     */

    public function chinavasionImportImage($postId, $imageUrl) {
    	$event = __("Importing image","ukuli") . " " . $imageUrl . " ". __("from Chinavasion","ukuli") . ".";
    	$this->chinavasionLogEvent($event);
			$uploadDir = wp_upload_dir();
			$uploadPath = $uploadDir['path'];
			$fileName = explode("/", $imageUrl);
			$fileName = end($fileName);
			$filePath = $uploadPath . "/" . $fileName;

    	$ch = curl_init($imageUrl);
    	$fp = fopen($filePath, 'w');
    	curl_setopt($ch, CURLOPT_FILE, $fp);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
  		curl_exec($ch);
    	curl_close($ch);
    	fclose($fp);

        $fileType = wp_check_filetype( basename( $filePath ), null );
        $wpUploadDir = wp_upload_dir();
        $attachment = array(
			'guid'           => $wpUploadDir['url'] . '/' . basename( $filePath ),
			'post_mime_type' => $fileType['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filePath ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
        );

        $attachId = wp_insert_attachment( $attachment, $filePath, $postId );

        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        $attachData = wp_generate_attachment_metadata( $attachId, $filePath );
        wp_update_attachment_metadata( $attachId, $attachData );
				return $attachId;
    }

    /**
     * Check if this product is new or just to be updated. Return post_id.
     */

    public function getPostID($apiId, $productTitle, $productContent) {
        $table_name = $this->wpdb->prefix . 'ukuli_chinavasion_api';
        $results = $this->wpdb->get_row( "SELECT * FROM $table_name WHERE api_id = '$apiId'" );
        if(is_null($results)) {
            $this->productExists = false;
            $post = array(
							'post_author' => get_current_user_id(),
			        'post_content' => $productContent,
				    	'post_status' => 'draft',
				  		'post_title' => $productTitle,
				  		'post_parent' => '',
				  		'post_type' => 'product',
						);
            $postId = wp_insert_post($post);
            $this->wpdb->insert($table_name,
            array(
                'post_id' => $postId,
                'api_id' => $apiId,
                 ));
        } else {
            $this->productExists = true;
            $postId = $results->post_id;
            /*
            $post = array(
                'ID'            => $postId,
                'post_title'    => $productTitle,
                'post_content'  => $productContent,
                'post_status'   => '',
            );
            wp_update_post($post);
            */
        }
        return $postId;
    }

    /**
     * Update WooCommerce product meta.
     */

    public function updateProductAttachments($postId, $chinavasionProduct) {
        $mainPicture = $chinavasionProduct->main_picture;
        $imageUrl = $mainPicture;
        $attachList = "";
        $attachId = $this->chinavasionImportImage($postId, $imageUrl);
        $attachList .= $attachId . ",";
        set_post_thumbnail( $postId, $attachId );
        $additionalImages = $chinavasionProduct->additional_images;
        foreach($additionalImages as $imageUrl) {
            $attachId = $this->chinavasionImportImage($postId, $imageUrl);
            $attachList .= $attachId . ",";
        }
        update_post_meta( $postId, '_product_image_gallery', $attachList);
    }

    /**
     * Update WooCommerce product meta.
     */

    public function updateProductMeta($postId, $chinavasionProduct) {
        update_post_meta( $postId, 'total_sales', '0');
        update_post_meta( $postId, '_downloadable', 'no');
        update_post_meta( $postId, '_virtual', 'no');
        update_post_meta( $postId, '_regular_price', $chinavasionProduct->price );
        update_post_meta( $postId, '_sale_price', "" );
        update_post_meta( $postId, '_purchase_note', "" );
        update_post_meta( $postId, '_featured', "no" );
        update_post_meta( $postId, '_weight', $chinavasionProduct->package->weight_kg );
        update_post_meta( $postId, '_length', $chinavasionProduct->package->depth_cm );
        update_post_meta( $postId, '_width', $chinavasionProduct->package->width_cm );
        update_post_meta( $postId, '_height', $chinavasionProduct->package->height_cm );
        update_post_meta( $postId, '_sku', $chinavasionProduct->product_id);
        update_post_meta( $postId, '_product_attributes', array());
        update_post_meta( $postId, '_sale_price_dates_from', "" );
        update_post_meta( $postId, '_sale_price_dates_to', "" );
        update_post_meta( $postId, '_price', $chinavasionProduct->retail_price );
        update_post_meta( $postId, '_sold_individually', "yes" );
        update_post_meta( $postId, '_manage_stock', "no" );
        update_post_meta( $postId, '_backorders', "no" );
        update_post_meta( $postId, '_stock', "" );
        update_post_meta( $postId, 'ukuli_chinavasion_dropshipping', "yes");
        update_post_meta( $postId, 'chinavasion_ean', $chinavasionProduct->ean);
        update_post_meta( $postId, 'chinavasion_id', $chinavasionProduct->product_id);
        update_post_meta( $postId, 'chinavasion_model_code', $chinavasionProduct->model_code);
        update_post_meta( $postId, 'chinavasion_product_url', $chinavasionProduct->product_url);
    }

    /**
     * Import product from Chinavasion API.
     */

    public function chinavasionImport($chinavasionId) {
	   $requestData = array("key" => $this->chinavasionApiKey,
	       	 	            "product_id" => $chinavasionId,
	       	 	            "currency" => get_option("woocommerce_currency")
	       	 	   );
        $apiMethod = "getProductDetails.php";
        $chinavasionProduct = $this->chinavasionRequest($apiMethod, $requestData);
        if($this->apiError == false) {
            $chinavasionProduct = $chinavasionProduct->products;
            $chinavasionProduct = $chinavasionProduct[0];
            $productTitle = $chinavasionProduct->full_product_name;
            $productContent = $chinavasionProduct->overview . " " . $chinavasionProduct->specification;
            $postId = $this->getPostID($chinavasionId,$productTitle,$productContent);
            if($this->productExists) {
                $result = '<p>' . __("Product already exists:","ukuli") . ' ' . $productTitle . '.</p>';
                return $result;
            }
            wp_set_object_terms($postId,'simple_chinavasion','product_type');
            wp_set_object_terms($postId, $chinavasionProduct->subcategory_name, 'product_cat' );
            update_post_meta($postId, '_visibility', 'visible' );
            $chinavasionStock = $chinavasionProduct->status;
            if($chinavasionStock == "In Stock") {
                update_post_meta( $postId, '_stock_status', 'instock');
            } else {
                update_post_meta( $postId, '_stock_status', 'outofstock');
            }
            $this->updateProductMeta($postId, $chinavasionProduct);
            $productTags = $chinavasionProduct->meta_keyword;
            $productTags = explode(",", $productTags);
            wp_set_object_terms($postId, $productTags, 'product_tag');
            $this->updateProductAttachments($postId, $chinavasionProduct);
            $event = __("Product","ukuli") . " " . $postId . " ". __("imported from Chinavasion","ukuli") . ".";
            $this->chinavasionLogEvent($event);
            $result = '<p>' . __("Check imported product","ukuli") . ' <a href="' . get_permalink($postId) . '">' . $chinavasionProduct->full_product_name . '</a> ' . __("and publish it!","ukuli") . '</p>';
            return $result;
	   } else {
	       $result = '<p>' . __("API Error:","ukuli") . ' ' . $this->apiErrorMessage . '</p>';
	       return $result;
	   }
    }

    /**
     * Add Chinavasion order.
     */

    public function chinavasionAddOrder($orderId, $shipping, $payment) {
            global $woocommerce;
            $order = new WC_Order($orderId);
            $data = $order->get_data();
            $address = array(
                "first_name" => $data["shipping"]["first_name"],
                "last_name" => $data["shipping"]["last_name"],
                "street" => $data["shipping"]["address_1"],
                "zip" => $data["shipping"]["postcode"],
                "city" => $data["shipping"]["city"],
                "state" => $data["shipping"]["state"],
                "telephone" => $data["billing"]["phone"],
                "country_iso2" => $data["shipping"]["country"],
                "socket" => "EU");
            $products = array();
            $items = $data["line_items"];
            foreach($items as $item) {
                $ukuliChinavasionDropshipping = get_post_meta($item["product_id"], 'ukuli_chinavasion_dropshipping', true );
                if($ukuliChinavasionDropshipping == "yes") {
                    array_push($products,array("product_id" => get_post_meta( $item["product_id"], 'chinavasion_id', true ), "quantity" => $item["qty"]));
                }
            }
            $requestData = array(
                "key" => $this->chinavasionApiKey,
                "products" => $products,
                "currency" => get_option("woocommerce_currency"),
                "payment_method" => $payment,
                "shipping" => $shipping,
                "address" => $address);
            $apiMethod = "createOrder.php";
            $orderResult = $this->chinavasionRequest($apiMethod, $requestData);
            if($this->apiError == false) {
                add_post_meta($orderId, "ukuli_chinavasion_order_sent", 1, true);
                $event = __("Order","ukuli") . " " . $orderId . " ". __("sent to Chinavasion","ukuli") . ".";
                $this->chinavasionLogEvent($event);
                $orderNote = "<p>" . ( __('Chinavasion order sent', 'ukuli')) . "</p>";
                $orderResult = $orderResult->order;
                $orderNote .= "<p>" . __("Order ID:","ukuli") . ' ' . $orderResult->order_id . "</p>";
                $orderNote .= "<p>" . __("Status:","ukuli") . ' ' . $orderResult->status . "</p>";
                $orderNote .= "<p>" . __("Shipping:","ukuli") . ' ' . $orderResult->shipping . "</p>";
                $orderNote .= "<p>" . __("Shipping cost:","ukuli") . ' ' . $orderResult->shipping_cost . "</p>";
                $orderNote .= "<p>" . __("Insurance:","ukuli") . ' ' . $orderResult->insurance . "</p>";
                $orderNote .= "<p>" . __("Subtotal:","ukuli") . ' ' . $orderResult->subtotal . "</p>";
                $orderNote .= "<p>" . __("Total:","ukuli") . ' ' . $orderResult->total . "</p>";
                $order->add_order_note($orderNote);
                return "OK";
	       } else {
                $result = '<p>' . __("API Error:","ukuli") . ' ' . $this->apiErrorMessage . '</p>';
	            return $result;
	       }
    }

    /**
     * Get price from Chinavasion.
     */
    public function chinavasionGetPrice($products, $country) {
	       $requestData = array("key" => $this->chinavasionApiKey,
	       	 	   "products" => $products,
	       	 	   "socket" => "EU",
	       	 	   "currency" => get_option("woocommerce_currency"),
	       	 	   "shipping_country_iso2" => $country
	       );
				 $apiMethod = "getPrice.php";
	       $chinavasionPrice = $this->chinavasionRequest($apiMethod, $requestData);
	       return $chinavasionPrice;
    }

    public function chinavasionCheckProductAvailability($productId) {
	 					$requestData = array(
                "key" => $this->chinavasionApiKey,
	       	 	    "product_id" =>  get_post_meta($productId, 'chinavasion_id', true),
	       	 	    "currency" => get_option('woocommerce_currency'));
            $apiMethod = "getProductDetails.php";
            $chinavasionAvailability = $this->chinavasionRequest($apiMethod, $requestData);
            if($this->apiError == false) {
                $chinavasionProduct = $chinavasionAvailability->products;
                $chinavasionProduct = $chinavasionProduct[0];
                $chinavasionStock = $chinavasionProduct->status;
                if($chinavasionStock == "In Stock") {
				    				update_post_meta( $productId, '_stock_status', 'instock');
                    $event = __("Product","ukuli") . " " . $productId . " ". __("is in stock","ukuli") . ".";
                    $this->chinavasionLogEvent($event);
                    return true;
			     			} else {
				    				update_post_meta( $productId, '_stock_status', 'outofstock');
                    $event = __("Product","ukuli") . " " . $productId . " ". __("is out of stock","ukuli") . ".";
                    $this->chinavasionLogEvent($event);
                    return false;
			     			}
            } else {
		      return false;
		    }
    }

    /**
     * Add and view logs.
     */

    public function chinavasionLogEvent($event) {
	   	$table_name = $this->wpdb->prefix . 'ukuli_chinavasion_log';
	   	$this->wpdb->insert($table_name, array('event' => $event));
    }
    
    public function chinavasionLogViewer() {
        $table_name = $this->wpdb->prefix . 'ukuli_chinavasion_log';
        $logs = $this->wpdb->get_results( "SELECT * FROM " . $table_name . " ORDER BY date DESC LIMIT 50");
        echo '<ul>';
        foreach($logs as $log) {
            echo '<li>';
            echo $log->date;
            echo " - ";
            echo $log->event;
            echo "</li>";
        }
        echo "</ul>";
    }
}
