<?php
namespace MunicipioElasticsearch\AutoSuggest;

class AutoSuggest {
  protected $settings;

  public function __construct($settings) {
    $this->settings = $settings;
    add_action(
      "wp_enqueue_scripts",
      [$this, "disableElasticPressAutosuggest"],
      99
    );
    add_action("wp_enqueue_scripts", [$this, "enableCustomAutoSuggest"], 99);
  }

  public function disableElasticPressAutosuggest() {
    wp_dequeue_style("elasticpress-autosuggest");
    wp_dequeue_script("elasticpress-autosuggest");
  }

  public function getSuggestions() {
    $input = get_field("municipio_elasticsearch_suggestions", "option") ?: "";
    preg_match_all('/^\s*(.+?)\s*$/m', $input, $matches);
    $suggestions = $matches[1] ?? [];
    $suggestions = apply_filters(
      "municipio_elasticsearch_autosuggest_options",
      $suggestions
    );
    return $suggestions;
  }

  public function enableCustomAutoSuggest() {
    wp_enqueue_script(
      "municipio-elasticsearch-awesomplete",
      $this->settings["plugin_url"] . "source/js/lib/awesomplete.js",
      ["jquery"],
      "0.1"
    );
    wp_enqueue_script(
      "municipio-elasticsearch-polyfill-closest",
      $this->settings["plugin_url"] . "source/js/lib/polyfill-closest.js",
      [],
      "0.1"
    );
    wp_enqueue_script(
      "municipio-elasticsearch-autosuggest",
      $this->settings["plugin_url"] . "source/js/autosuggest.js",
      [
        "municipio-elasticsearch-awesomplete",
        "municipio-elasticsearch-polyfill-closest",
      ],
      "0.1.1"
    );
    wp_localize_script(
      "municipio-elasticsearch-autosuggest",
      "MunicipioElasticsearchAutosuggestOptions",
      apply_filters("municipio_elasticsearch_autosuggest_options", [
        "suggestions" => $this->getSuggestions(),
      ])
    );
  }
}
