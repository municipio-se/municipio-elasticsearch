<?php
namespace MunicipioElasticsearch;

class App {
  public $settings;

  public function __construct($settings) {
    $this->settings = $settings;

    new Admin\Settings\Main();
    new Query\Query();
    // new Indexing\Indexing();
    new AutoSuggest\AutoSuggest($this->settings);

    add_action("plugins_loaded", [$this, "registerIndexables"], 10);
    add_filter("wp_stream_connectors", [$this, "register_stream_connector"]);
  }

  public function registerIndexables() {
    if (class_exists("\ElasticPress\Indexables")) {
      \ElasticPress\Indexables::factory()->register(new Indexable\Post\Post());
    }
  }

  function register_stream_connector($classes) {
    // $stream = wp_stream_get_instance();
    $class = new Logging\Stream\Connector();
    if ($class->is_dependency_satisfied()) {
      $classes[] = $class;
    }
    return $classes;
  }
}
