<?php
if (function_exists('acf_add_local_field_group')):
  acf_add_local_field_group(array(
    'key' => 'group_5d08f2f81c66d',
    'title' => 'Indexing settings',
    'fields' => array(
      array(
        'key' => 'field_5d08f56d7691c',
        'label' => 'Indexerade posttyper',
        'name' => 'indexed_post_types',
        'type' => 'posttype_select',
        'instructions' =>
          'OBS! Uppdatering kräver en sync av elasticpress indexet. <a href="/wp/wp-admin/admin.php?page=elasticpress">Sida för synkronisering</a>',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '80',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'allow_null' => 0,
        'multiple' => 1,
        'placeholder' => '',
        'disabled' => 0,
        'readonly' => 0,
      ),
      array(
        'key' => 'field_5d08f56d4690a',
        'label' => 'Indexera posttypernas arkiv',
        'name' => 'indexed_post_type_archives',
        'type' => 'true_false',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '20',
          'class' => '',
          'id' => '',
        ),
        'default_value' => 1,
        'message' => '',
        'ui' => 1,
        'ui_on_text' => 'Ja',
        'ui_off_text' => 'Nej',
      ),
      array(
        'key' => 'field_5d08f4ef7691b',
        'label' => 'Exkludera post från sök',
        'name' => 'exclude_post_from_index',
        'type' => 'post_object',
        'instructions' =>
          'OBS! Uppdatering kräver en sync av elasticpress indexet. <a href="/wp/wp-admin/admin.php?page=elasticpress">Sida för synkronisering</a>',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'post_type' => array(),
        'taxonomy' => array(),
        'allow_null' => 1,
        'multiple' => 1,
        'return_format' => 'id',
        'ui' => 1,
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'municipio-elasticsearch-indexing',
        ),
      ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
  ));
endif;
