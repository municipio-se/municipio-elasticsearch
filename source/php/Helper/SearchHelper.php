<?php

namespace MunicipioElasticsearch\Helper;

use MunicipioElasticsearch\Query\Query;

class SearchHelper {
  // Query
  public $s = null;
  public $type = null;
  public $from = 0;
  public $page = 1;
  public $sort = null;
  public $sortOrder = "asc";
  public $site = null;
  public $allowEmptySearch = true;

  // Data
  public $resultCount = null;
  public $results = null;
  public $facets = [];
  public $pagination = false;

  // Config
  public $resultsPerPage = 10;

  public function __construct($queryVariables) {
    foreach ($queryVariables as $key => $value) {
      $this->$key = $value;
    }

    if ($this->page > 1) {
      $this->from = ($this->page - 1) * $this->resultsPerPage;
    }

    $this->search();
  }

  public function query($query = []) {
    $queryVariables = [];
    if ($this->s !== null) {
      $queryVariables["s"] = $this->s;
    }

    if ($query["page"] !== 1) {
      if (!empty($query["page"])) {
        $queryVariables["page"] = $query["page"];
      } elseif ($this->page !== 1) {
        $queryVariables["page"] = $this->page;
      }
    }

    if ($this->sort !== null) {
      $queryVariables["sort"] = $this->sort;
    }

    if (isset($query["type"])) {
      if ($query["type"] !== false) {
        $queryVariables["type"] = $query["type"];
      }
    } else {
      $queryVariables["type"] = $this->type;
    }

    if (!empty($query["site"])) {
      $queryVariables["site"] = $query["site"];
    } elseif ($this->site !== null) {
      $queryVariables["site"] = $this->site;
    }

    return "/?" . http_build_query($queryVariables);
  }

  public function get_excerpt($str, $startPos = 0, $maxLength = 100) {
    if (strlen($str) > $maxLength) {
      $excerpt = substr($str, $startPos, $maxLength - 3);
      $lastSpace = strrpos($excerpt, " ");
      $excerpt = substr($excerpt, 0, $lastSpace);
      $excerpt .= "...";
    } else {
      $excerpt = $str;
    }

    return $excerpt;
  }

  public function search() {
    $query = new Query();

    $query->search(
      $this->s,
      $this->type,
      $this->from,
      $this->resultsPerPage,
      $this->sort,
      $this->sortOrder,
      $this->site,
      $this->allowEmptySearch
    );

    if (!empty($query->results)) {
      foreach ($query->results as $nr => $resultItem) {
        $item = new \stdClass();
        $item->id = $resultItem["ID"];
        $item->type = $resultItem["post_type"];

        // Highlight
        $item->title =
          $query->highlight[$nr]["post_title"][0] ?? "" ?:
          $resultItem["post_title"] ?? "";
        $item->lead =
          $query->highlight[$nr]["municipio_content"][0] ?? "" ?:
          $resultItem["municipio_content"] ?? "";

        $item->permalink = $resultItem["permalink"];

        $this->results[] = $item;
      }
    }

    $this->resultCount = $query->resultCount;

    if (!empty($query->aggregations["post_type"])) {
      $items = [];

      // Add all facet
      $item = new \stdClass();
      $item->active = empty($this->type);
      $item->permalink = $this->query([
        "type" => false,
        "page" => 1,
      ]);
      $item->name = "Alla träffar";
      $items[] = $item;

      // Count all facets
      $countAll = 0;

      foreach ($query->aggregations["post_type"]["buckets"] as $bucket) {
        $item = new \stdClass();

        $item->active = $this->type == $bucket["key"];
        $item->key = $bucket["key"];
        $item->permalink = $this->query([
          "type" => $bucket["key"],
          "page" => 1,
        ]);
        $item->count = $bucket["doc_count"];

        if ($bucket["key"] === "attachment") {
          $item->name = "Filer";
        } else {
          $postType = get_post_type_object($bucket["key"]);
          $item->name = $postType->labels->name ?? "";
        }

        if ($item->name !== "Text") {
          $items[] = $item;
          $countAll += $item->count;
        }
      }

      // Add count to "Alla träffar"
      $items[0]->count = $countAll;

      $this->facets["post_type"] = $items;
    }

    $this->pagination = $this->pagination();
  }

  public function pagination() {
    // $return = new \stdClass();

    // if ($this->page > 1) {
    //   $return->isFirstPage = $this->query(['page' => $this->page - 1]);
    // }

    $totalPages = ceil($this->resultCount / $this->resultsPerPage);

    // if ($totalPages == 1) {
    //   return false;
    // }

    // $from = max([$this->page - 2, 1]);
    // $to = min([$this->page + 2, $totalPages]);

    // $return->pages = [];
    // for ($i = $from; $i <= $to; $i++) {
    //   $item = new \stdClass();
    //   $item->page = $i;
    //   $item->permalink = $this->query(['page' => $i]);
    //   $item->active = ($this->page ?: 1) == $i;

    //   $return->pages[] = $item;
    // }

    // if ($this->page < $totalPages) {
    //   $return->isLastPage = $this->query(['page' => $this->page + 1]);
    // }

    return [
      "current" => $this->page ?: 1,
      "total" => $totalPages,
    ];
  }

  /**
   * Gets the modified date for an item
   * @param  object $item The item
   * @return string       The modified date
   */
  public function getModifiedDate($item) {
    if (!isset($item->pagemap)) {
      return null;
    }

    $meta = $item->pagemap->metatags[0];
    $dateMod = null;

    if (isset($meta->moddate)) {
      $dateMod = $meta->moddate;
    } elseif (isset($meta->pubdate)) {
      $dateMod = $meta->pubdate;
    } elseif (isset($meta->{'last-modified'})) {
      $dateMod = $meta->{'last-modified'};
    }

    $dateMod = $this->convertDate($dateMod);

    return $dateMod;
  }

  public function convertDate($date) {
    if (substr($date, 0, 1) == "D") {
      $date = date("d M Y", strtotime(substr($date, 2, -7)));
    } elseif (strlen($date) > 20) {
      $date = date("d M Y", strtotime($date));
    }

    return $date;
  }
}
