<?php
// Load WordPress Core
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

// Only allow logged-in admin users
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('Unauthorized access');
}

// Delete cached sitemap
delete_transient('cached_news_sitemap');

echo 'Sitemap cache cleared successfully!';
?>

<!--
direct URL-based cache clearing method:

Access it in your browser: https://yourwebsite.com/clear-sitemap-cache.php

If you're an admin, it will clear the cache and show a confirmation message.
🚀 Why This Works
Ensures only logged-in admins can clear the cache.
No need to edit functions.php.

-->