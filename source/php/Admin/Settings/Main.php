<?php

namespace MunicipioElasticsearch\Admin\Settings;

class Main {
  public static $MENU_SLUG = 'municipio-elasticsearch';
  public function __construct() {
    add_action('init', [$this, 'init'], 100);

    add_filter('acf/load_value/name=use_algolia_search', '__return_false', 10);

    new Query();
    new Indexing();
    new Synonyms();
  }

  public function init() {
    // Disable Algolia search options field group
    acf_remove_local_field_group('group_5a61b852f3f8c'); // Algolia sök
  }
}
