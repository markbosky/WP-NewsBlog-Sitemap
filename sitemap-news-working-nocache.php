<?php
// Load WordPress core properly
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

// Set the correct content type for XML output
header("Content-Type: application/xml; charset=UTF-8");

// Get the site URL
$site_url = get_site_url();

// Fetch all published blog posts
$args = array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC'
);

$posts = new WP_Query($args);

// Output XML header
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

<?php
if ($posts->have_posts()) :
    while ($posts->have_posts()) : $posts->the_post();
        $post_url   = get_permalink();
        $post_title = get_the_title();
        $post_date  = get_the_date('Y-m-d');
        $post_time  = get_the_time('H:i:s');
        $site_name  = get_bloginfo('name');
        $language   = 'en';

        echo "<url>
            <loc>" . esc_url($post_url) . "</loc>
            <news:news>
                <news:publication>
                    <news:name>" . esc_html($site_name) . "</news:name>
                    <news:language>" . esc_html($language) . "</news:language>
                </news:publication>
                <news:publication_date>" . esc_html($post_date . 'T' . $post_time . '+00:00') . "</news:publication_date>
                <news:title>" . esc_html($post_title) . "</news:title>
            </news:news>
        </url>\n";
    endwhile;
    wp_reset_postdata();
endif;
?>
</urlset>
