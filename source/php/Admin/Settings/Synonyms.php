<?php
namespace MunicipioElasticsearch\Admin\Settings;

class Synonyms {
  public static $synonyms = array();
  public function __construct() {
    add_action('plugins_loaded', array($this, 'pluginsInit'));
    add_action('init', array($this, 'initSynonyms'));
    add_filter('ep_config_mapping', array($this, 'epSynonymsMapping'), 10, 1);
  }

  public function pluginsInit() {
  }

  public function initSynonyms() {
    $municipio_elasticpress_synonyms = array('tomas, thomas');
    if (have_rows('municipio_elasticpress_synonyms', 'option')) {
      while (have_rows('municipio_elasticpress_synonyms', 'option')):
        the_row();
        $synonym = array();
        if (have_rows('one_synonym', 'option')) {
          while (have_rows('one_synonym', 'option')):
            the_row();
            array_push($synonym, strtolower(trim(get_sub_field('synonym'))));
          endwhile;
        }
        array_push($municipio_elasticpress_synonyms, implode(', ', $synonym));
      endwhile;
    }
    self::$synonyms = $municipio_elasticpress_synonyms;
  }

  public static function getSynonymsForTerm($term) {
    foreach (self::$synonyms as $synonymCollection) {
      if (stripos($synonymCollection, $term) !== false) {
        return explode(', ', $synonymCollection);
      }
    }
    return false;
  }

  public function epSynonymsMapping($mapping) {
    // bail early if $mapping is missing or not array
    if (!isset($mapping) || !is_array($mapping)) {
      return false;
    }

    // ensure we have filters and is array
    if (
      !isset($mapping['settings']['analysis']['filter']) ||
      !is_array($mapping['settings']['analysis']['filter'])
    ) {
      return false;
    }

    // ensure we have analyzers and is array
    if (
      !isset(
        $mapping['settings']['analysis']['analyzer']['default']['filter']
      ) ||
      !is_array(
        $mapping['settings']['analysis']['analyzer']['default']['filter']
      )
    ) {
      return false;
    }

    $mapping['settings']['analysis']['filter'][
      'ws_ep_acf_synonyms_filter'
    ] = array(
      'type' => 'synonym',
      'synonyms' => self::$synonyms,
    );

    // tell the analyzer to use our newly created filter
    $mapping['settings']['analysis']['analyzer']['default']['filter'][] =
      'ws_ep_acf_synonyms_filter';

    return $mapping;
  }
}
