<?php
/**
 * Include External Files
 */
include 'inc/project-post-type.php';
include 'inc/unsed-media.php';

/**
 * Register Custom Navigation Menus.
 */
function register_my_menus()
{
  register_nav_menus(
    array(
      'header-menu' => __('Header Menu', 'ikonic-test-project')
    )
  );
}
add_action('init', 'register_my_menus');

/**
 * Enqueue Theme Styles and Scripts.
 */
function custom_theme_enqueue_styles()
{
  wp_enqueue_style('custom-style', get_stylesheet_uri());
  wp_enqueue_script('jquery');
  wp_enqueue_script('custom-menu-js', get_template_directory_uri() . '/js/main.js', array('jquery'), null, true);

  // Localize script with AJAX parameters
  wp_localize_script('custom-menu-js', 'ajax_params', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('project_filter_nonce'),
  ));
}
add_action('wp_enqueue_scripts', 'custom_theme_enqueue_styles');

/**
 * Register Custom REST API Endpoint.
 */
function register_project_api()
{
  register_rest_route('custom/v1', '/projects', array(
    'methods'  => 'GET',
    'callback' => 'get_projects'
  ));
}
add_action('rest_api_init', 'register_project_api');

/**
 * Get Projects Data for REST API Endpoint.
 *
 * @return array The project data.
 */
function get_projects()
{
  $args = array(
    'post_type'      => 'projects',
    'posts_per_page' => -1
  );

  $projects = new WP_Query($args);
  $data = array();

  if ($projects->have_posts()) {
    while ($projects->have_posts()) {
      $projects->the_post();
      $data[] = array(
        'title'       => get_the_title(),
        'url'         => get_post_meta(get_the_ID(), '_project_url', true),
        'start_date'  => get_post_meta(get_the_ID(), '_project_start', true),
        'end_date'    => get_post_meta(get_the_ID(), '_project_end', true)
      );
    }
    wp_reset_postdata();
  }
  return $data;
}

/**
 * Filter Projects by Start and End Date.
 *
 * @param WP_Query $query The WP_Query instance (passed by reference).
 */
function filter_projects_by_date($query)
{
  if (is_admin() || !$query->is_main_query() || !is_post_type_archive('projects')) {
    return;
  }

  if (isset($_GET['start-date']) && isset($_GET['end-date']) && !empty($_GET['start-date']) && !empty($_GET['end-date'])) {
    $start_date = sanitize_text_field($_GET['start-date']);
    $end_date = sanitize_text_field($_GET['end-date']);

    $meta_query = array(
      'relation' => 'AND',
      array(
        'key'     => '_project_start',
        'value'   => $start_date,
        'compare' => '>=',
        'type'    => 'DATE'
      ),
      array(
        'key'     => '_project_end',
        'value'   => $end_date,
        'compare' => '<=',
        'type'    => 'DATE'
      )
    );

    $query->set('meta_query', $meta_query);
  }
}
add_action('pre_get_posts', 'filter_projects_by_date');

/**
 * Handle AJAX Request to Filter Projects.
 */
function filter_projects_ajax()
{
  // Verify nonce for security
  check_ajax_referer('project_filter_nonce', 'nonce');

  // Initialize meta query with relation 'AND'
  $meta_query = array('relation' => 'AND');

  // Add start date filter if present
  if (!empty($_POST['start_date'])) {
    $meta_query[] = array(
      'key'     => '_project_start',
      'value'   => sanitize_text_field($_POST['start_date']),
      'compare' => '>=',
      'type'    => 'DATE'
    );
  }

  // Add end date filter if present
  if (!empty($_POST['end_date'])) {
    $meta_query[] = array(
      'key'     => '_project_end',
      'value'   => sanitize_text_field($_POST['end_date']),
      'compare' => '<=',
      'type'    => 'DATE'
    );
  }

  // Query arguments
  $args = array(
    'post_type'      => 'project',
    'posts_per_page' => -1,
    'meta_query'     => $meta_query,
  );

  // Execute the query
  $query = new WP_Query($args);

  // Generate the response
  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
?>
      <div class="project-card">
        <h2 class="project-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="project-meta">
          <p><?php _e('Start Date:', 'your-text-domain'); ?> <?php echo esc_html(get_post_meta(get_the_ID(), '_project_start', true)); ?></p>
          <p><?php _e('End Date:', 'your-text-domain'); ?> <?php echo esc_html(get_post_meta(get_the_ID(), '_project_end', true)); ?></p>
        </div>
        <div class="project-excerpt">
          <?php the_excerpt(); ?>
        </div>
        <a class="project-link" href="<?php the_permalink(); ?>"><?php _e('Read More', 'your-text-domain'); ?></a>
      </div>
<?php
    }
  } else {
    echo '<p>' . __('No projects found.', 'your-text-domain') . '</p>';
  }

  // Reset post data
  wp_reset_postdata();
  wp_die();
}
add_action('wp_ajax_filter_projects', 'filter_projects_ajax');
add_action('wp_ajax_nopriv_filter_projects', 'filter_projects_ajax');
