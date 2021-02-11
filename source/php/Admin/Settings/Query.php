<?php

namespace MunicipioElasticsearch\Admin\Settings;

use ElasticPress\Elasticsearch;

class Query {
  public function __construct() {
    add_action("plugins_loaded", [$this, "init"]);

    add_filter("acf/load_field/name=query_indices", [$this, "indices"]);
  }

  public function init() {
  }

  public function indices($field) {
    $indices = $this->remote_request_helper("_cat/indices?format=json");

    $field["choices"] = [];

    if (is_array($indices)) {
      foreach ($indices as $index) {
        $field["choices"][$index["index"]] = $index["index"];
      }

      ksort($field["choices"]);
    }

    return $field;
  }

  protected function remote_request_helper($path) {
    $request = Elasticsearch::factory()->remote_request($path);

    if (is_wp_error($request) || empty($request)) {
      return false;
    }

    $body = wp_remote_retrieve_body($request);

    return json_decode($body, true);
  }
}
