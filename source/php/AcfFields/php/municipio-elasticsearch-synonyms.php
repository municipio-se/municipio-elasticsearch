<?php
if (function_exists('acf_add_local_field_group')):
  acf_add_local_field_group(array(
    'key' => 'group_5d08e58806d4c',
    'title' => 'Synonyms',
    'fields' => array(
      array(
        'key' => 'field_5d08e65a46bf0',
        'label' => 'Alla Synonymer',
        'name' => 'municipio_elasticpress_synonyms',
        'type' => 'repeater',
        'instructions' =>
          'OBS! Uppdatering kräver en sync av elasticpress indexet. <a href="/wp/wp-admin/admin.php?page=elasticpress">Sida för synkronisering</a>',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'collapsed' => '',
        'min' => 0,
        'max' => 0,
        'layout' => 'block',
        'button_label' => 'Map a new synonym',
        'sub_fields' => array(
          array(
            'key' => 'field_5d08e67046bf1',
            'label' => 'En Synonym',
            'name' => 'one_synonym',
            'type' => 'repeater',
            'instructions' => 'Skapa en lista av synonymer för gemensamma ord.
    Ordningen spelar ingen roll. Alla ord kommer att motsvara varandra och bli sökbara',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'collapsed' => '',
            'min' => 2,
            'max' => 0,
            'layout' => 'table',
            'button_label' => 'Add a new synonym',
            'sub_fields' => array(
              array(
                'key' => 'field_5d08e6ae46bf2',
                'label' => 'Synonym',
                'name' => 'synonym',
                'type' => 'text',
                'instructions' =>
                  'Endast 1 ord. Undvik specialtecken och mellanslag',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                  'width' => '',
                  'class' => '',
                  'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
              ),
            ),
          ),
        ),
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'municipio-elasticsearch-synonyms',
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
