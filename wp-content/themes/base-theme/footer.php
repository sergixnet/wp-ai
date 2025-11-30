<?php
/**
 * The footer template
 *
 * @package Base_Theme
 * @since 1.0.0
 */
?>
        </div><!-- .container -->
    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="site-info">
                <p>
                    <?php
                    printf(
                        /* translators: 1: Theme name, 2: WordPress link */
                        esc_html__('Proudly powered by %2$s', 'base-theme'),
                        '<span class="theme-name">Base Theme</span>',
                        '<a href="' . esc_url(__('https://wordpress.org/', 'base-theme')) . '">WordPress</a>'
                    );
                    ?>
                </p>
            </div><!-- .site-info -->
            
            <?php
            if (has_nav_menu('footer')) :
                ?>
                <nav class="footer-navigation" aria-label="<?php esc_attr_e('Footer Navigation', 'base-theme'); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_id'        => 'footer-menu',
                        'depth'          => 1,
                        'container'      => false,
                    ));
                    ?>
                </nav>
                <?php
            endif;
            ?>
        </div><!-- .container -->
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
