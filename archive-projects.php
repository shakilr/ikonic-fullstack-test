<?php get_header(); ?>

<div class="project-archive">
  <!-- Filter Form -->
  <form method="get" class="project-filters">
    <label for="start-date">
      <?php _e('Start Date:', 'ikonic-test-project'); ?>
    </label>
    <input type="date" name="start-date" id="start-date" value="<?php echo isset($_GET['start-date']) ? esc_attr($_GET['start-date']) : ''; ?>">

    <label for="end-date">
      <?php _e('End Date:', 'ikonic-test-project'); ?>
    </label>
    <input type="date" name="end-date" id="end-date" value="<?php echo isset($_GET['end-date']) ? esc_attr($_GET['end-date']) : ''; ?>">

    <button type="submit"><?php _e('Filter', 'ikonic-test-project'); ?></button>
  </form>

  <!-- Loading Indicator -->
  <div class="loading-indicator" style="display: none;">
    <?php _e('Loading...', 'ikonic-test-project'); ?>
  </div>

  <!-- Project List -->
  <div class="project-list">
    <?php if (have_posts()) : ?>
      <?php
      while (have_posts()) :
        the_post();
      ?>
        <div class="project-card">
          <h2 class="project-title">
            <a href="<?php the_permalink(); ?>">
              <?php the_title(); ?>
            </a>
          </h2>
          <div class="project-meta">
            <p>
              <?php _e('Start Date:', 'ikonic-test-project'); ?> <?php echo esc_html(get_post_meta(get_the_ID(), '_project_start', true)); ?>
            </p>
            <p>
              <?php _e('End Date:', 'ikonic-test-project'); ?> <?php echo esc_html(get_post_meta(get_the_ID(), '_project_end', true)); ?>
            </p>
          </div>
          <div class="project-excerpt">
            <?php the_excerpt(); ?>
          </div>
          <a class="project-link" href="<?php the_permalink(); ?>">
            <?php _e('Read More', 'ikonic-test-project'); ?>
          </a>
        </div>
      <?php endwhile; ?>
    <?php else : ?>
      <p>
        <?php _e('No projects found.', 'ikonic-test-project'); ?>
      </p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>