<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/islamBelabbes
 * @since             1.0.0
 * @package           Woocommerce_Zid_Spreadsheet
 *
 * @wordpress-plugin
 * Plugin Name:       woocommerce - zid spreadsheet
 * Plugin URI:        https://github.com/islamBelabbes
 * Description:       a plugin to help migrate woocommerce data to zid using spreadsheet api
 * Version:           1.0.0
 * Author:            islam belabbes
 * Author URI:        https://github.com/islamBelabbes/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-zid-spreadsheet
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


include_once 'lib/sheetdb.php';
include_once 'lib/woocommerce.php';



function woocommerceZidUsersMigrate () {
 $page = 1;
 $limit = 1000;
 function doMigrate($page,$limit) {
	$data = customGetUsers($page,$limit);
     pushUsersTosheetdb($data["data"]);
	if ($data["hasMore"] === true || $data["hasMore"] === "true" ) {
        doMigrate($page + 1 , $limit);
    }
	return true;
 }

 return doMigrate($page, $limit);
}

function woocommerceZidProductsMigrate ($page = 1) {
 return pushProductsTosheetdb(customGetProducts(100,$page));
}


