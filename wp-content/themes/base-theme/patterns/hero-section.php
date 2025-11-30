<?php
/**
 * Hero Block Pattern
 *
 * @package Base_Theme
 */

return array(
    'title'       => __('Hero Section', 'base-theme'),
    'description' => __('A hero section with heading, description, and CTA button', 'base-theme'),
    'categories'  => array('featured'),
    'keywords'    => array('hero', 'banner', 'header'),
    'content'     => '<!-- wp:cover {"url":"","dimRatio":50,"overlayColor":"black","minHeight":500,"isDark":true,"align":"full"} -->
<div class="wp-block-cover alignfull is-light" style="min-height:500px"><span aria-hidden="true" class="wp-block-cover__background has-black-background-color has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center","level":1,"fontSize":"5-xl"} -->
<h1 class="has-text-align-center has-5-xl-font-size">' . __('Welcome to Our Website', 'base-theme') . '</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"lg"} -->
<p class="has-text-align-center has-lg-font-size">' . __('Discover amazing content and services tailored just for you', 'base-theme') . '</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"primary","textColor":"white"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-primary-background-color has-text-color has-background wp-element-button">' . __('Get Started', 'base-theme') . '</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->',
);
