<?php

/**
 * Register Custom Post Type: Projects.
 */
function projects_post_type()
{
  $labels = array(
    'name'               => _x('Projects', 'Post Type General Name', 'ikonic-test-project'),
    'singular_name'      => _x('Project', 'Post Type Singular Name', 'ikonic-test-project'),
    'menu_name'          => __('Projects', 'ikonic-test-project'),
    'name_admin_bar'     => __('Project', 'ikonic-test-project'),
    'parent_item_colon'  => __('Parent Content Block:', 'ikonic-test-project'),
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array('slug' => 'project'),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array('title', 'editor', 'thumbnail'),
  );

  register_post_type('projects', $args);
}
add_action('init', 'projects_post_type', 0);


/**
 * Add Meta Boxes to Project Post Type.
 */
function add_project_meta_boxes()
{
  add_meta_box(
    'project_meta', // ID of the meta box
    __('Project Details', 'ikonic-test-project'), // Title of the meta box
    'project_meta_callback', // Callback function to display the meta box content
    'projects' // Post type where the meta box should appear
  );
}
add_action('add_meta_boxes', 'add_project_meta_boxes');

/**
 * Meta Box Callback: Display Custom Meta Fields.
 *
 * @param WP_Post $post The post object.
 */
function project_meta_callback($post)
{
  wp_nonce_field('save_project_meta', 'project_meta_nonce');

  // Define the fields
  $fields = array(
    'project_name'       => array('label' => __('Project Name', 'ikonic-test-project'), 'type' => 'text'),
    'project_description' => array('label' => __('Project Description', 'ikonic-test-project'), 'type' => 'textarea'),
    'project_start'      => array('label' => __('Project Start Date', 'ikonic-test-project'), 'type' => 'date'),
    'project_end'        => array('label' => __('Project End Date', 'ikonic-test-project'), 'type' => 'date'),
    'project_url'        => array('label' => __('Project URL', 'ikonic-test-project'), 'type' => 'url')
  );

  echo '<div class="metabox-holder">';

  // Loop through fields and render them
  foreach ($fields as $field_key => $field) {
    $value = get_post_meta($post->ID, '_' . $field_key, true);
    echo '<div class="form-field">';
    echo '<label for="' . $field_key . '">' . $field['label'] . '</label>';
    if ($field['type'] === 'textarea') {
      echo '<textarea id="' . $field_key . '" name="' . $field_key . '">' . esc_textarea($value) . '</textarea>';
    } else {
      echo '<input type="' . $field['type'] . '" id="' . $field_key . '" name="' . $field_key . '" value="' . esc_attr($value) . '">';
    }
    echo '</div>';
  }

  echo '</div>';
}


/**
 * Save Meta Fields for Project Post Type.
 *
 * @param int $post_id The ID of the post being saved.
 */
function save_project_meta($post_id)
{
  // Verify nonce
  if (!isset($_POST['project_meta_nonce']) || !wp_verify_nonce($_POST['project_meta_nonce'], 'save_project_meta')) {
    return;
  }

  // Check for autosave
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Define the fields
  $fields = array(
    'project_name'       => 'sanitize_text_field',
    'project_description' => 'sanitize_textarea_field',
    'project_start'      => 'sanitize_text_field',
    'project_end'        => 'sanitize_text_field',
    'project_url'        => 'esc_url_raw'
  );

  // Loop through fields and save them
  foreach ($fields as $field_key => $sanitize_function) {
    if (isset($_POST[$field_key])) {
      $value = call_user_func($sanitize_function, $_POST[$field_key]);
      update_post_meta($post_id, '_' . $field_key, $value);
    }
  }
}
add_action('save_post', 'save_project_meta');

function enqueue_admin_styles()
{
  wp_enqueue_style('custom-admin-css', get_template_directory_uri() . '/css/project-meta-box-css.css', false, '1.0.0');
}
add_action('admin_enqueue_scripts', 'enqueue_admin_styles');
