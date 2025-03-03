<?php

/**
 * If the WP_List_Table class doesn't exist, require it.
 */
if (!class_exists('WP_List_Table')) {
  require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class for displaying unused media files in a table.
 */
class Ikonic_Media_List_Table extends WP_List_Table
{

  /**
   * Get the columns for the table.
   *
   * @return array The columns of the table.
   */
  function get_columns()
  {
    $columns = array(
      'cb'            => '<input type="checkbox" />',
      'media_id'      => __('Media ID', 'ikonic-test-project'),
      'media_name'    => __('File Name', 'ikonic-test-project'),
      'media_preview' => __('File Preview', 'ikonic-test-project'),
      'action'        => __('Delete', 'ikonic-test-project')
    );

    return $columns;
  }

  /**
   * Prepare the items for the table.
   */
  function prepare_items()
  {
    $this->process_bulk_action();
    $data = $this->get_unused_media();
    $per_page = 10;
    $current_page = $this->get_pagenum();
    $total_items = count($data);

    $this->set_pagination_args(array(
      'total_items' => $total_items,
      'per_page'    => $per_page
    ));

    $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

    $this->_column_headers = array($this->get_columns(), array(), array());
    $this->items = $data;
  }

  /**
   * Render the checkbox column.
   *
   * @param object $item The current item.
   * @return string The checkbox element.
   */
  function column_cb($item)
  {
    return sprintf('<input type="checkbox" name="media[]" value="%s" />', $item->ID);
  }

  /**
   * Render the media ID column.
   *
   * @param object $item The current item.
   * @return string The media ID.
   */
  function column_media_id($item)
  {
    return $item->ID;
  }

  /**
   * Render the media name column.
   *
   * @param object $item The current item.
   * @return string The media name.
   */
  function column_media_name($item)
  {
    return $item->post_title;
  }

  /**
   * Render the media preview column.
   *
   * @param object $item The current item.
   * @return string The media preview.
   */
  function column_media_preview($item)
  {
    return wp_get_attachment_image($item->ID, 'thumbnail');
  }

  /**
   * Render the action column.
   *
   * @param object $item The current item.
   * @return string The delete button.
   */
  function column_action($item)
  {
    return sprintf('<button class="ikonic-umc-delete" data-id="%s">' . __('Delete', 'ikonic-test-project') . '</button>', esc_attr($item->ID));
  }

  /**
   * Get unused media files.
   *
   * @return array The list of unused media files.
   */
  function get_unused_media()
  {
    global $wpdb;
    $media_files = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'attachment'");
    $unused_media = [];
    foreach ($media_files as $file) {
      $post_content_refs = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_content LIKE %s", '%' . $file->guid . '%'));
      $custom_field_refs = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE meta_value LIKE %s", '%' . $file->guid . '%'));
      if ($post_content_refs == 0 && $custom_field_refs == 0) {
        $unused_media[] = $file;
      }
    }
    return $unused_media;
  }

  /**
   * Get bulk actions.
   *
   * @return array The list of bulk actions.
   */
  function get_bulk_actions()
  {
    $actions = array(
      'delete' => __('Delete', 'ikonic-test-project')
    );
    return $actions;
  }

  /**
   * Process bulk actions.
   */
  function process_bulk_action()
  {
    if ('delete' === $this->current_action()) {
      $media_ids = isset($_POST['media']) ? $_POST['media'] : array();
      foreach ($media_ids as $media_id) {
        wp_delete_attachment($media_id, true);
      }
    }
  }
}

/**
 * Add an admin menu item for the unused media page.
 */
function ikonic_add_admin_menu()
{
  add_menu_page(__('Unused Media', 'ikonic-test-project'), __('Unused Media', 'ikonic-test-project'), 'manage_options', 'unused-media', 'ikonic_admin_page');
}
add_action('admin_menu', 'ikonic_add_admin_menu');

/**
 * Render the unused media admin page.
 */
function ikonic_admin_page()
{
  $umcTable = new Ikonic_Media_List_Table();
  $umcTable->prepare_items();
?>
  <div class="wrap">
    <h1><?php _e('Unused Media', 'ikonic-test-project'); ?></h1>
    <form method="post">
      <?php $umcTable->display(); ?>
    </form>
  </div>
<?php
}

/**
 * Enqueue Admin Scripts and Styles.
 */
function enqueue_admin_scripts()
{
  wp_enqueue_script('custom-admin-js', get_template_directory_uri() . '/js/unsed-media-js.js', array('jquery'), null, true);

  // Localize script with AJAX parameters
  wp_localize_script('custom-admin-js', 'ajax_params', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('project_filter_nonce'),
  ));
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

/**
 * Handle AJAX Request to Delete Media.
 */
function ikonic_delete_media()
{
  // Verify nonce for security
  check_ajax_referer('project_filter_nonce', 'nonce');

  // Get media ID from the request
  $media_id = intval($_POST['media_id']);

  // Delete the media attachment
  $deleted = wp_delete_attachment($media_id, true);

  if ($deleted) {
    wp_send_json_success();
  } else {
    wp_send_json_error();
  }
}
add_action('wp_ajax_ikonic_delete_media', 'ikonic_delete_media');

?>