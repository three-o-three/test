<?php
/**
 * Ondango Team
 * 
 * www.ondango.com 
 * apidocs.ondango.com
 */

require_once "../libs/Ondango.php";

$api_key = "f877615136d70e0ffc2fb224d5872d6a8fd2xbxx";
$api_secret = null;	// optional
$ondango = new Ondango ($api_key, $api_secret);
$limit = 10;

$bestSellers = $ondango->GET( 'hello/ondango', array(
                                                     'shop_id' => 87,
                                                     ));

echo '<pre>';
print_r ($bestSellers);
    
$bestSellers = $ondango->GET( 'products/best_sellers_ids', array(
                                                                 'shop_id' => 87,
                                                                 'limit' => $limit,
                                                                 'fields' => array( 'product_id', 'title','category_id' ),
                                                                 'fetch_category_name' => 'true',                                                     
                                                                 ));

print_r ($bestSellers);
echo '</pre>';

?>