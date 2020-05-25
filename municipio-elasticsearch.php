<?php
/**
 * Plugin Name:       Municipio Elasticsearch
 * Description:       Improve search
 * Version:           0.0.1
 * Author:            Whitespace AB
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       municipio-elasticsearch
 * Domain Path:       /languages
 */

// Protect agains direct file access
if (!defined('WPINC')) {
  die();
}

define('MUNICIPIO_ELASTICSEARCH_PATH', plugin_dir_path(__FILE__));
// define('WS_EP_SEARCH_URL', plugins_url('', __FILE__));

/*load_plugin_textdomain(
  'municipio-elasticsearch',
  false,
  plugin_basename(dirname(__FILE__)) . '/languages'
);*/

require_once MUNICIPIO_ELASTICSEARCH_PATH .
  'source/php/Vendor/Psr4ClassLoader.php';
if (file_exists(MUNICIPIO_ELASTICSEARCH_PATH . 'vendor/autoload.php')) {
  require_once MUNICIPIO_ELASTICSEARCH_PATH . 'vendor/autoload.php';
}

// Instantiate and register the autoloader
$loader = new MUNICIPIO_ELASTICSEARCH\Vendor\Psr4ClassLoader();
$loader->addPrefix('MUNICIPIO_ELASTICSEARCH', MUNICIPIO_ELASTICSEARCH_PATH);
$loader->addPrefix(
  'MUNICIPIO_ELASTICSEARCH',
  MUNICIPIO_ELASTICSEARCH_PATH . 'source/php/'
);
$loader->register();

add_action('plugins_loaded', function () {
  $acfExportManager = new \AcfExportManager\AcfExportManager();
  $acfExportManager->setTextdomain('municipio-elasticsearch');
  $acfExportManager->setExportFolder(
    MUNICIPIO_ELASTICSEARCH_PATH . 'source/php/AcfFields/'
  );
  $acfExportManager->autoExport(array(
    'municipio-elasticsearch-query' => 'group_5d08f2f81c66d',
    'municipio-elasticsearch-synonyms' => 'group_5d08e58806d4c',
    'municipio-elasticsearch-indexing' => 'group_5d08f2f81c66d',
  ));
  $acfExportManager->import();
});

// Start application
new \MUNICIPIO_ELASTICSEARCH\App();
