<?php

namespace MunicipioElasticsearch\Admin\Settings;

use ElasticPress\Indexables;

class Indexing {
  private $_includedPostTypes = [];
  private $_excludedPostIds = [];
  private $_boostedPostTypes = [];

  public function __construct() {
    add_action("plugins_loaded", [$this, "init"]);

    $this->_includedPostTypes = get_field("indexed_post_types", "options");
    $this->_excludedPostIds = get_field("exclude_post_from_index", "options");
    $this->_boostedPostTypes = get_field("boost_post_type", "options");

    add_filter("ep_index_posts_args", [$this, "filterPosts"], 999);

    add_filter("ep_indexable_post_types", [$this, "indexablePostTypes"], 999);

    add_filter(
      "ep_post_sync_kill",
      [$this, "skipIndexOnSaveIgnoredPost"],
      10,
      3
    );

    add_filter("ep_post_query_db_args", [$this, "mediaIndexMimeTypes"], 10, 1);

    add_filter(
      "ep_post_sync_args_post_prepare_meta",
      [$this, "indexAdditionalData"],
      20,
      2
    );
  }

  public function init() {
  }

  public function indexablePostTypes($post_types) {
    if (is_array($this->_includedPostTypes)) {
      $post_types = [];
      foreach ($this->_includedPostTypes as $key => $postType) {
        $post_types[] = $postType;
      }
    }
    return $post_types;
  }

  public function filterPosts($args) {
    if (
      is_array($this->_excludedPostIds) &&
      count($this->_excludedPostIds) > 0
    ) {
      $args["post__not_in"] = $this->_excludedPostIds;
    }
    return $args;
  }

  public function skipIndexOnSaveIgnoredPost(
    $return_val,
    $post_args,
    $post_id
  ) {
    if (
      is_array($this->_excludedPostIds) &&
      in_array($post_id, $this->_excludedPostIds)
    ) {
      return true;
    }
    return $return_val;
  }

  public function mediaIndexMimeTypes($args) {
    if (array_search("attachment", $args["post_type"]) !== false) {
      add_filter("posts_where", [$this, "indexOnlyPDF"], 99, 1);
      $args["post_mime_type"] = ["CHANGEME"]; // Fulhax, if you do array('application/pdf', '') it will still find e.g. images
    }
    return $args;
  }

  public function indexOnlyPDF($where) {
    if (strpos($where, "LIKE 'CHANGEME/%'") !== false) {
      $where = str_replace(
        "LIKE 'CHANGEME/%'",
        'IN("application/pdf","")',
        $where
      );
      remove_filter("posts_where", [$this, "indexOnlyPDF"]);
    }
    return $where;
  }

  public function indexAdditionalData($post_args, $post_id) {
    $post_indexable = Indexables::factory()->get("post");

    if ($post_args["post_type"] == "attachment") {
      // File permalink, without uploads folder
      $meta = get_post_meta($post_id);
      if (!empty($meta["_wp_attached_file"][0])) {
        $post_args["municipio_permalink"] = "/" . $meta["_wp_attached_file"][0];
      }

      // File size
      $filesize = @filesize(get_attached_file($post_id));
      if (!empty($filesize)) {
        $post_args["municipio_filesize"] = $filesize;
        $post_args["municipio_filesize_format"] = size_format($filesize, 2);
      }
    } else {
      // Content without html tags
      $content = $post_args["post_content"];
      $content = do_shortcode($content);
      $content = strip_tags($content);
      $post_args["municipio_content"] = $content;

      // Title lowercase
      $post_args["municipio_title"] = mb_strtolower($post_args["post_title"], 'UTF-8');

      // Permalink without home url
      $post_args["municipio_permalink"] = wp_make_link_relative(
        $post_args["permalink"]
      );
    }
    return $post_args;
  }
}
