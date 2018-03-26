<?php
/**
 * Plugin name: Import Inspiration
 * Plugin URI: https://www.romainpetiot.com/
 * Description: Import expirations depuis CSV
 * Author : Romain Petiot
 * Text Domain:
 * Version: 1.0
 * Stable tag: 1.0
 *
 */

//Bloquer les accès directs
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require plugin_dir_path( __FILE__ ) . 'admin/import.php';

require plugin_dir_path( __FILE__ ) . 'admin/admin_tpl.php';
