<?php get_header(); ?>

<div class="project-single">
  <?php
    if (have_posts()) :
      while (have_posts()) :
        the_post();
  ?>
      <div class="project-details">
        <h1 class="project-title"><?php the_title(); ?></h1>
        <div class="project-meta">
          <?php
            // Define the fields
            $fields = array(
              'project_name'       => __('Project Name', 'ikonic-test-project'),
              'project_description' => __('Project Description', 'ikonic-test-project'),
              'project_start'      => __('Start Date', 'ikonic-test-project'),
              'project_end'        => __('End Date', 'ikonic-test-project'),
              'project_url'        => __('Project URL', 'ikonic-test-project')
            );

            // Loop through fields and display them
            foreach ($fields as $field_key => $field_label) {
              $value = get_post_meta(get_the_ID(), '_' . $field_key, true);
              if ($value) {
                echo '<p>';
                echo '<strong>' . $field_label . ':</strong> ';
                if ($field_key === 'project_url') {
                  echo '<a href="' . esc_url($value) . '" target="_blank">' . esc_html($value) . '</a>';
                } else {
                  echo esc_html($value);
                }
                echo '</p>';
              }
            }
          ?>
        </div>
        <div class="project-description">
          <?php the_content(); ?>
        </div>
      </div>
  <?php
      endwhile;
    endif;
  ?>
</div>

<?php get_footer(); ?>