<?php

namespace MunicipioElasticsearch\Controller;

abstract class Search extends \Municipio\Controller\Search {
  public $queryData = [];

  public function init() {
    //Translations
    $this->data["translation"] = [
      "filter_results" => __("Filter searchresults", "municipio"),
      "all_pages" => __("All pages", "municipio"),
    ];

    // Custom null result message
    $this->data["emptySearchResultMessage"] = get_field(
      "empty_search_result_message",
      "option"
    );

    $this->getQueryData();
    $this->data["activeSearchEngine"] = "elasticsearch";

    $this->elasticsearchSearch();
  }

  abstract protected function getElasticsearch();

  /**
   * Elasticsearch init
   * @return void
   */
  public function elasticsearchSearch() {
    $search = $this->getElasticsearch();

    $this->data["results"] = $search->results;
    $this->data["facets"] = $search->facets;
    $this->data["resultCount"] = $search->resultCount;
    $this->data["pagination"] = $search->pagination;
  }

  public function getQueryData() {
    $this->queryData["s"] = get_search_query();

    if (isset($_GET["page"]) && is_numeric($_GET["page"])) {
      $this->queryData["page"] = sanitize_text_field($_GET["page"]);
    }

    if (isset($_GET["sort"])) {
      $this->queryData["sort"] = sanitize_text_field($_GET["sort"]);
    }
    if (isset($_GET["type"])) {
      $this->queryData["type"] = sanitize_text_field($_GET["type"]);
    }
    if (isset($_GET["site"])) {
      $this->queryData["site"] = sanitize_text_field($_GET["site"]);
    }
  }
}
