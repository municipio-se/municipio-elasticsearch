<?php
namespace MUNICIPIO_ELASTICSEARCH;

class App {
  public function __construct() {
    new \MUNICIPIO_ELASTICSEARCH\Admin\Settings\Main();
    new \MUNICIPIO_ELASTICSEARCH\Query\Query();
    // new \MUNICIPIO_ELASTICSEARCH\Indexing\Indexing();
  }
}
