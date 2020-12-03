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
  }
}
