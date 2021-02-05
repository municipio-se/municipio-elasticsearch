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
  }

  public function registerIndexables() {
    if (class_exists("\ElasticPress\Indexables")) {
      \ElasticPress\Indexables::factory()->register(new Indexable\Post\Post());
    }
  }
}
