<?php

namespace MunicipioElasticsearch\Admin\Settings;

class Main {
  public static $MENU_SLUG = 'municipio-elasticsearch';
  public function __construct() {
    add_action('plugins_loaded', array($this, 'init'));
    new Query();
    new Indexing();
    new Synonyms();
  }

  public function init() {
    if (function_exists('acf_add_options_page')) {
      acf_add_options_page(array(
        'page_title' => __(
          'Municipio Elasticsearch',
          'municipio-elasticsearch'
        ),
        'menu_title' => __(
          'Municipio Elasticsearch',
          'municipio-elasticsearch'
        ),
        'menu_slug' => self::$MENU_SLUG,
        'capability' => 'edit_posts',
        'redirect' => true,
      ));
    }
  }
}
