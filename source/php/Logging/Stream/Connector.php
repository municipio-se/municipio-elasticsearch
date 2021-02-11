<?php

namespace MunicipioElasticsearch\Logging\Stream;

use WP_Stream\Connector as BaseConnector;

class Connector extends BaseConnector {
  /**
   * Connector slug
   *
   * @var string
   */
  public $name = "municipio_elasticsearch";

  /**
   * Actions registered for this connector
   *
   * @var array
   */
  public $actions = ["ep_after_bulk_index", "ep_before_retry_bulk_index"];

  /**
   * Display an admin notice if plugin dependencies are not satisfied
   *
   * @return bool
   */
  public function is_dependency_satisfied() {
    return true;
  }

  /**
   * Return translated connector label
   *
   * @return string
   */
  public function get_label() {
    return __("Municipio Elasticsearch", "municipio-elasticsearch");
  }

  /**
   * Return translated context labels
   *
   * @return array
   */
  public function get_context_labels() {
    return [
      "post" => __("Post", "municipio-elasticsearch"),
    ];
  }

  /**
   * Return translated action labels
   *
   * @return array
   */
  public function get_action_labels() {
    return [
      "bulk_index" => __("Bulk-indexed", "municipio-elasticsearch"),
      "retry_bulk_index" => __(
        "Retried bulk-indexing",
        "municipio-elasticsearch"
      ),
    ];
  }

  /**
   * Gets the singular post type label
   *
   * @param string $post_type_slug  Post type slug.
   *
   * @return string Post type label
   */
  public function get_post_type_name($post_type_slug) {
    $name = esc_html__("Post", "stream"); // Default.

    if (post_type_exists($post_type_slug)) {
      $post_type = get_post_type_object($post_type_slug);
      $name = $post_type->labels->singular_name;
    }

    return $name;
  }

  /**
   * Add action links to Stream drop row in admin list screen
   *
   * This method is optional.
   *
   * @param array  $links  Previous links registered
   * @param Record $record Stream record
   *
   * @return array Action links
   */
  public function action_links($links, $record) {
    switch ($record->context) {
      case "post":
        // Check if the Foo or Bar exists
        if ($record->object_id) {
          $post_type_name = $this->get_post_type_name(
            get_post_type($record->object_id)
          );
          $action_link_text = sprintf(
            esc_html_x("Edit %s", "Post type singular name", "stream"),
            $post_type_name
          );
          $links[$action_link_text] = get_edit_post_link($record->object_id);
        }
        break;
    }
    return $links;
  }

  /**
   * Track bulk indexing
   *
   * @param array $foo
   * @param bool  $is_new
   *
   * @return void
   */
  public function callback_ep_after_bulk_index($object_ids, $slug, $result) {
    if ($slug != "post") {
      return;
    }
    if (is_wp_error($result)) {
      $action = "bulk_index";
      foreach ($object_ids as $object_id) {
        $post = get_post($object_id);
        $this->log(
          // Summary message
          sprintf(
            __("Could not index %s \"%s\".", "municipio-elasticsearch"),
            $this->get_post_type_name($post->post_type),
            $post->post_title
          ),
          // This array is compacted and saved as Stream meta
          [
            "error" => true,
            "object_id" => $object_id,
            "slug" => $slug,
          ],
          $object_id, // Object ID
          "post", // Context
          $action
        );
      }
    } else {
      $action = "bulk_index";
      $this->log(
        // Summary message
        sprintf(
          __("Indexed %d %s", "municipio-elasticsearch"),
          count($object_ids),
          "post(s)"
        ),
        // This array is compacted and saved as Stream meta
        [
          "success" => true,
          "object_ids" => $object_ids,
          "slug" => $slug,
        ],
        null, // Object ID
        null, // Context
        $action
      );
    }
  }

  /**
   * Track retried bulk-indexing
   *
   * @param array $bar
   * @param bool  $is_new
   *
   * @return void
   */
  public function callback_ep_before_retry_bulk_index($object_ids, $slug) {
    $action = "retry_bulk_index";
    $this->log(
      // Summary message
      sprintf(
        __(
          "Could not index chunk of %d %s. Retrying in two passes…",
          "municipio-elasticsearch"
        ),
        count($object_ids),
        "post(s)"
      ),
      // This array is compacted and saved as Stream meta
      [
        "object_ids" => $object_ids,
        "slug" => $slug,
      ],
      null, // Object ID
      null, // Context
      $action
    );
  }
}
