<?php
/**
 * Plugin Name:       Municipio Elasticsearch
 * Plugin URI:        https://github.com/whitespace-se/municipio-elasticsearch
 * Description:       Manages search configuration and integration between modularity & elasticpress.
 * Version:           0.1.0
 * Author:            Whitespace AB
 * Author URI:        https://whitespace.se/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       municipio-elasticsearch
 * Domain Path:       /languages
 */

// Protect agains direct file access
if (!defined("WPINC")) {
  die();
}

$plugin_path = plugin_dir_path(__FILE__);
$plugin_url = plugin_dir_url(__FILE__);
// define('WS_EP_SEARCH_URL', plugins_url('', __FILE__));

/*load_plugin_textdomain(
  'municipio-elasticsearch',
  false,
  plugin_basename(dirname(__FILE__)) . '/languages'
);*/

require_once $plugin_path . "source/php/Vendor/Psr4ClassLoader.php";
if (file_exists($plugin_path . "vendor/autoload.php")) {
  require_once $plugin_path . "vendor/autoload.php";
}

// Instantiate and register the autoloader
$loader = new \MunicipioElasticsearch\Vendor\Psr4ClassLoader();
$loader->addPrefix("MunicipioElasticsearch", $plugin_path);
$loader->addPrefix("MunicipioElasticsearch", $plugin_path . "source/php/");
$loader->register();

add_action("plugins_loaded", function () use ($plugin_path) {
  $acfExportManager = new \AcfExportManager\AcfExportManager();
  $acfExportManager->setTextdomain("municipio-elasticsearch");
  $acfExportManager->setExportFolder($plugin_path . "source/php/AcfFields/");
  $acfExportManager->autoExport([
    "elasticsearch-settings" => "group_5d08f2f81c66d",
  ]);
  $acfExportManager->import();
});

// Start application
new \MunicipioElasticsearch\App([
  "plugin_path" => $plugin_path,
  "plugin_url" => $plugin_url,
]);
