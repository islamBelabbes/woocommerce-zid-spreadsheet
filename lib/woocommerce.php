<?php

function customGetUsers($page , $limit = 1000) {
    $user_query_args = array(
        'paged' => $page,
        'number' => $limit,
        'offset' => ($page - 1) * $limit,
        'meta_query' => array(
            'relation' => 'AND', // Make sure both conditions are met
            array(
                'key' => 'billing_phone', // Replace 'billing_phone' with the actual meta key for phone number
                'compare' => 'EXISTS', // Check if the billing_phone meta key exists
            ),
            array(
                'key' => 'billing_phone', // Same meta key
                'value' => '', // Ensuring it's not an empty string
                'compare' => '!=', // Exclude empty strings
                'type' => 'NUMERIC', // Make sure the value is numeric
            ),
        ),
    );
// Create a new WP_User_Query
$user_query = new WP_User_Query($user_query_args);

// Get the results
$customers = $user_query->get_results();

  
$result = [
    "data" => [],
    "hasMore" => $user_query->get_total() > $page * $limit,
 ];

foreach ($customers as $customer) {
    $telephone = get_user_meta($customer->ID, 'billing_phone', true);

// Check if $telephone starts with '+966' and contains only numeric characters
if (substr($telephone, 0, 4) !== '+966' || !ctype_digit(substr($telephone, 4))) {
    // If not, modify $telephone to include '+966' and remove non-numeric characters
    $telephone = '+966' . preg_replace('/[^0-9]/', '', $telephone);
}

  // Check if the phone number starts with '+966'
  if (strpos($telephone, '+966') === 0) {
    // Remove the first '0' after '+966'
    $telephone = '+966' . ltrim(substr($telephone, 4), '0');
}

    $firstName = get_user_meta($customer->ID, 'billing_first_name', true);
    $lastName = get_user_meta($customer->ID, 'billing_first_name', true);
    $fullName = $firstName === $lastName ? $firstName : $firstName . ' ' . $lastName;
    $nickName = $customer->user_login;
    $result["data"][] = [
        'name' => trim($fullName) ? $fullName : $nickName,
        "telephone" => $telephone,
        "email" => $customer->user_email,
        "type" => "individual",
        "business_name" => "",
        "tax_number" => "",
        "commercial_registration" => "",
        "city" =>  get_user_meta($customer->ID, 'billing_city', true),
        "points" => 0,
    ];
}

return $result;
}



function customGetProducts($limit = 100 , $page = 1) {
    $query = new WC_Product_Query(array(
        'limit' => $limit,
        "page" => $page,
        'orderby' => 'date',
        'order' => 'DESC',
        'return' => 'ids',
    ));
    
    // Get the products
    $products = $query->get_products();
    $productsData = [];
    
    foreach ($products as $product_id) {
        $product = wc_get_product($product_id);
        $gallery_image_ids = [$product->get_image_id() , ...$product->get_gallery_image_ids()];

        // Get image URLs and split them by comma
        $image_urls = array_map(function ($image_id) {
            return wp_get_attachment_url($image_id);
        }, $gallery_image_ids);
          
        //Seo
        $seoTitle = "";
        $seoDesc = "";
        if (function_exists('YoastSEO')) {
            $meta_helper = YoastSEO()->classes->get(Yoast\WP\SEO\Surfaces\Meta_Surface::class);
            $meta = $meta_helper->for_post($product_id);
            $seoTitle = $meta->get_head()->json["title"];
            $seoDesc = $meta->get_head()->json["description"];
        }

        $productData = array(
            'sku' => $product->get_sku(),
            'name_ar' => $product->get_name(),
            'name_en' => "",
            'weight_unit' => "",
            'weight' => $product->get_weight(),
            'price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price(),
            'cost' => "",
            'quantity' => $product->get_stock_quantity(),
            'categories' => implode(' , ', wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'))),
            'published' => "Yes",
            'images' => implode(', ', $image_urls),
            'images_alt_text' => '', // You may need to fetch alt text from somewhere
            'vat_free' => '', // You may need to fetch this information from somewhere
            'minimum_quantity_per_order' => "",
            'maximum_quantity_per_order' => "",
            'shipping_required' => true,
            'keywords' => '', // You may need to fetch keywords from somewhere
            'description_ar' => $product->get_description(),
            'description_en' => "",
            'short_description_ar' => $product->get_short_description(),
            'short_description_en' => "",
            "product_page_title_ar" => $seoTitle,
            "product_page_description_ar" => $seoDesc
        );
    
        $productsData[] = $productData;
    }

    return $productsData;
}
