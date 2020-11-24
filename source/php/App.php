<?php
namespace MunicipioElasticsearch;

class App {
  public function __construct() {
    new Admin\Settings\Main();
    new Query\Query();
    // new Indexing\Indexing();
  }
}
