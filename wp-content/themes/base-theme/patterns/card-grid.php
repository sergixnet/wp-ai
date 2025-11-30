<?php
/**
 * Card Grid Block Pattern
 *
 * @package Base_Theme
 */

return array(
    'title'       => __('Card Grid', 'base-theme'),
    'description' => __('A grid of three cards with icons, headings, and descriptions', 'base-theme'),
    'categories'  => array('columns'),
    'keywords'    => array('cards', 'grid', 'features'),
    'content'     => '<!-- wp:group {"align":"full","backgroundColor":"gray-100","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-gray-100-background-color has-background"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","right":"2rem","bottom":"2rem","left":"2rem"}}},"backgroundColor":"white","className":"card"} -->
<div class="wp-block-group card has-white-background-color has-background" style="padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem"><!-- wp:heading {"level":3} -->
<h3>' . __('Feature One', 'base-theme') . '</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>' . __('Describe your first amazing feature here with compelling details.', 'base-theme') . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","right":"2rem","bottom":"2rem","left":"2rem"}}},"backgroundColor":"white","className":"card"} -->
<div class="wp-block-group card has-white-background-color has-background" style="padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem"><!-- wp:heading {"level":3} -->
<h3>' . __('Feature Two', 'base-theme') . '</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>' . __('Highlight your second feature with engaging information.', 'base-theme') . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","right":"2rem","bottom":"2rem","left":"2rem"}}},"backgroundColor":"white","className":"card"} -->
<div class="wp-block-group card has-white-background-color has-background" style="padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem"><!-- wp:heading {"level":3} -->
<h3>' . __('Feature Three', 'base-theme') . '</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>' . __('Showcase your third feature with descriptive content.', 'base-theme') . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
);
