<?php
namespace MUNICIPIO_ELASTICSEARCH\Admin;

class Tests {
  public function __construct() {
    if (!defined('ELASTICPRESS_TEST_ENV')) {
      return;
    }
    add_filter('wp', array($this, 'performTestSearch'));
  }

  public function includeScoreForTests($post, $elasticRes) {
    $post['post_excerpt'] = $elasticRes['_score'];
    return $post;
  }

  public function performTestSearch() {
    if (isset($_GET['testsearch'])) {
      add_filter(
        'ep_retrieve_the_post',
        array($this, 'includeScoreForTests'),
        10,
        2
      );
      $query = $_GET['testsearch'];
      $take = $_GET['testtake'] ?: 10;
      $testdate = $_GET['testdate'] ?: time();
      $args = array(
        's' => $_GET['testsearch'],
        'posts_per_page' => $take,
        'post_type' => 'any',
      );
      define('testdate', $testdate);
      $query = new \WP_Query($args);
      $res = array_map(function ($post) {
        return array(
          'ID' => $post->ID,
          'title' => $post->post_title,
          'post_type' => $post->post_type,
          'post_content' => $post->post_content,
          'score' => $post->post_excerpt,
        );
      }, $query->posts);
      remove_filter(
        'ep_retrieve_the_post',
        array($this, 'includeScoreForTests'),
        10
      );

      header('Content-Type: application/json');
      echo json_encode($res);
      die();
    }
  }
}
