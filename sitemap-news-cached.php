<?php
// Load WordPress Core
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

// Set the correct content type for XML output
header("Content-Type: application/xml; charset=UTF-8");

// ✅ Step 1: Check for Cached Version (Avoids Unnecessary Database Queries)
$cached_sitemap = get_transient('cached_news_sitemap');
if ($cached_sitemap) {
    echo $cached_sitemap;
    exit;
}

// ✅ Step 2: Start Output Buffering to Capture XML Output
ob_start();

// Output XML header
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

<?php
// ✅ Step 3: Fetch All Published Blog Posts Efficiently
$args = array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1, // Fetch all posts
    'orderby'        => 'date',
    'order'          => 'DESC'
);

$posts = new WP_Query($args);

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

<?php
// ✅ Step 4: Store Generated Sitemap in Cache (Lasts 1 Hour)
$sitemap_output = ob_get_clean(); // Get output and clear buffer
set_transient('cached_news_sitemap', $sitemap_output, HOUR_IN_SECONDS);

echo $sitemap_output;
?>
