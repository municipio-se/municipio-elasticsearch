<?php

namespace MunicipioElasticsearch\Indexable\Post;

use ElasticPress\Indexable\Post\Post as ElasticPressPost;
use ElasticPress\Elasticsearch;
use WP_Query;
use WP_User;
use WP_Error;

if (!defined("ABSPATH")) {
  // @codeCoverageIgnoreStart
  exit(); // Exit if accessed directly.
  // @codeCoverageIgnoreEnd
}

/**
 * Post indexable class
 */
class Post extends ElasticPressPost {
  protected $prepared_documents = [];

  public function prepare_document($post_id) {
    if (!isset($this->prepared_documents[$post_id])) {
      $this->prepared_documents[$post_id] = parent::prepare_document($post_id);
    }
    return $this->prepared_documents[$post_id];
  }

  public function clear_prepared_documents() {
    $this->prepared_documents = [];
  }
  // public function clear_prepared_document($object_id) {
  //   unset($this->prepared_documents[$object_id]);
  // }

  /**
   * Bulk index objects. This calls prepare_document on each object
   *
   * @param  array $object_ids Array of object IDs.
   * @since  3.0
   * @return WP_Error|array
   */
  public function bulk_index($object_ids, $skip_attachments = false) {
    $body = "";

    foreach ($object_ids as $object_id) {
      $action_args = [
        "index" => [
          "_id" => absint($object_id),
        ],
      ];

      $document = $this->prepare_document($object_id);

      if ($skip_attachments) {
        unset($document["attachments"]);
      }

      /**
       * Conditionally kill indexing on a specific object
       *
       * @hook ep_bulk_index_action_args
       * @param  {array} $action_args Bulk action arguments
       * @param {array} $document Document to index
       * @since  3.0
       * @return {array}  New action args
       */
      $body .=
        wp_json_encode(
          apply_filters("ep_bulk_index_action_args", $action_args, $document)
        ) . "\n";
      $body .= addcslashes(wp_json_encode($document), "\n");

      $body .= "\n\n";
    }

    $result = Elasticsearch::factory()->bulk_index(
      $this->get_index_name(),
      $this->slug,
      $body
    );

    if (is_wp_error($result)) {
      if ($result->get_error_code() == "413") {
        if (count($object_ids) > 1) {
          $result = $this->retry_bulk_index($object_ids);
        } elseif (!$skip_attachments) {
          $result = $this->bulk_index($object_ids, true);
        }
      }
    }

    /**
     * Perform actions after a bulk indexing is completed
     *
     * @hook ep_after_bulk_index
     * @param {array} $object_ids List of object ids attempted to be indexed
     * @param {string} $slug Current indexable slug
     * @param {array|bool} $result Result of the Elasticsearch query. False on error.
     */
    do_action("ep_after_bulk_index", $object_ids, $this->slug, $result);

    return $result;
  }
  public function retry_bulk_index($object_ids) {
    $chunks = array_chunk($object_ids, ceil(count($object_ids) / 2));
    foreach ($chunks as $object_ids) {
      $result = $this->bulk_index($object_ids);
    }
    return $result;
  }
}
