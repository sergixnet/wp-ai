<?php
/**
 * The sidebar template
 *
 * @package Base_Theme
 * @since 1.0.0
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e('Sidebar', 'base-theme'); ?>">
    <?php dynamic_sidebar('sidebar-1'); ?>
</aside><!-- #secondary -->
