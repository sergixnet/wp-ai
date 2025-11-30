<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Base_Theme
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'base-theme'); ?></h1>
            </header><!-- .page-header -->

            <div class="page-content">
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'base-theme'); ?></p>

                <div class="row">
                    <div class="col-md-6">
                        <?php
                        get_search_form();
                        ?>
                    </div>

                    <div class="col-md-6">
                        <?php
                        // Recent posts widget
                        the_widget('WP_Widget_Recent_Posts', array(
                            'number' => 5,
                        ));
                        ?>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="widget widget_categories">
                            <h2 class="widget-title"><?php esc_html_e('Most Used Categories', 'base-theme'); ?></h2>
                            <ul>
                                <?php
                                wp_list_categories(array(
                                    'orderby'    => 'count',
                                    'order'      => 'DESC',
                                    'show_count' => 1,
                                    'title_li'   => '',
                                    'number'     => 10,
                                ));
                                ?>
                            </ul>
                        </div><!-- .widget -->
                    </div>

                    <div class="col-md-6">
                        <?php
                        // Tag cloud widget
                        the_widget('WP_Widget_Tag_Cloud');
                        ?>
                    </div>
                </div>
            </div><!-- .page-content -->
        </section><!-- .error-404 -->
    </div>
</main><!-- #main -->

<?php
get_footer();
