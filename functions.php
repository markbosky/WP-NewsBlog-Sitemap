<?
function news_sitemap_admin_page() {
    add_menu_page(
        'News Sitemap Cache',
        'News Sitemap Cache',
        'manage_options',
        'news-sitemap-cache',
        'news_sitemap_cache_page'
    );
}
add_action('admin_menu', 'news_sitemap_admin_page');

function news_sitemap_cache_page() {
    if (isset($_POST['clear_cache'])) {
        delete_transient('cached_news_sitemap');
        echo '<div class="updated"><p><strong>Sitemap cache cleared successfully!</strong></p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Clear News Sitemap Cache</h1>
        <form method="post">
            <input type="submit" name="clear_cache" class="button button-primary" value="Clear Cache Now">
        </form>
    </div>
    <?php
}
?>