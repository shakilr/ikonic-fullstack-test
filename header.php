<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <header class="site-header" role="banner">
    <nav id="site-navigation" class="main-navigation" role="navigation">
      <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e('Primary Menu', 'ikonic-test-project'); ?></button>
      <?php
      wp_nav_menu(array(
        'theme_location' => 'header-menu',
        'menu_class' => 'nav-menu',
        'container' => false,
      ));
      ?>
    </nav><!-- #site-navigation -->
  </header><!-- .site-header -->
</body>