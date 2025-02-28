# WordPress Custom NewsBlog Sitemap
Generate custom 'News' XML sitemap for all /blog/ posts in WordPress

## Step 1: Create a Custom Sitemap PHP File
In your WordPress theme, child theme, or webserver root (public_html, www, etc) create a new PHP file called sitemap-news.php in the root directory.


## Step 2: Make It Accessible
To make your sitemap available at `https://yourwebsite.com/sitemap-news.xml`, follow these steps:

1. Upload the file sitemap-news.php to your WordPress root directory.

2. Modify .htaccess (if using Apache) by adding this rule to rewrite the URL:

`RewriteRule ^sitemap-news.xml$ sitemap-news.php [L]`

If you're using NGINX, add this rule to your server block:
```
location = /sitemap-news.xml {
    rewrite ^ /sitemap-news.php last;
}
```


3. For WordPress with Permalinks, you can add the following to functions.php to ensure the sitemap works properly:
```
function add_news_sitemap_rewrite_rule() {
    add_rewrite_rule('sitemap-news\.xml$', 'sitemap-news.php', 'top');
}
add_action('init', 'add_news_sitemap_rewrite_rule');`
```
Then flush permalinks by visiting Settings > Permalinks in WordPress and clicking "Save Changes."

## Step 3: Submit to Google Search Console
1. Go to Google Search Console.
2. Navigate to Sitemaps.
3. Submit `https://yourwebsite.com/sitemap-news.xml`.

## Steps to Add Rewrite Rule in WPEngine NGINX Web Rules
1. Log into WPEngine Admin Panel (my.wpengine.com).
2. Navigate to Your Environment (e.g., environmentname).
3. Go to "Web Rules" (found under "Site" → "Utilities").
4. Select "Rewrite Rules".
5. Add a New Rule:

Source: `^/sitemap-news.xml$`
Target/Destination: `/sitemap-news.php`

- Rewrite Type:
	- Choose "Rewrite to" (or "Internal Redirect" if available).
- Flag:
	- Select "Last" (L)
6. Save and Deploy the rule.

## How the Rewrite Rule Works
- When https://yourwebsite.com/sitemap-news.xml is requested, the server internally redirects the request to sitemap-news.php inside your theme.
- This ensures that search engines (Google, Bing) see the XML output without exposing the PHP file.

## ⚠️ Security Concerns
1. Direct Execution Risk
	- Problem: If the script is directly accessible (/sitemap-news.php), an attacker could potentially manipulate queries or exploit PHP vulnerabilities.
	- Mitigation:
		- Restrict direct access using .htaccess (for Apache) or NGINX rules.
		- Move sitemap-news.php outside the theme folder.

Apache (Add to .htaccess in Theme Folder)
`<Files "sitemap-news.php">
    Require all denied
</Files>`

NGINX (Security Block)
`location /sitemap-news.php {
    deny all;
}`

💡 Better Approach: Move sitemap-news.php to the WordPress root (/public_html/ or equivalent).

2. WordPress Updates & Theme Changes
	- Problem: If the theme is updated or changed, sitemap-news.php might be deleted, breaking the sitemap.
	- Mitigation:
		- Move the file to a custom plugin or the WordPress root directory (/public_html/).

## ⚡ Performance Concerns
1. Querying All Posts Can Be Slow
	- Problem: Fetching all blog posts (posts_per_page => -1) can lead to high memory usage and slow execution.
	- Mitigation:
		- Cache the Sitemap (Store the XML output and refresh it periodically).
		- Use WP Transients for caching.

Caching Example (Update Every Hour)
Add this inside sitemap-news.php:
```
$cached_sitemap = get_transient('cached_news_sitemap');
if ($cached_sitemap) {
    echo $cached_sitemap;
    exit;
}

// Generate sitemap (same code as before)
ob_start(); // Start output buffering

// (Your existing XML output code)

$sitemap_output = ob_get_clean(); // Get output and clear buffer
set_transient('cached_news_sitemap', $sitemap_output, HOUR_IN_SECONDS); // Cache for 1 hour

echo $sitemap_output;
```

🚀 Result: The sitemap only regenerates once per hour, reducing database load.
- How This Works
	- Retrieves a cached version if available → Reduces database load.
	- Uses ob_start() to capture XML output.
	- Stores generated XML in WordPress transients (set_transient()).
	- Refreshes every hour (HOUR_IN_SECONDS = 3600 seconds).


2. Sitemap Could Be Too Large
	- Problem: Google limits XML sitemaps to 50,000 URLs or 50MB uncompressed.
	- Mitigation:
		- Paginate Sitemap (Use multiple smaller sitemaps).
		- Modify the query to fetch only recent posts.
		- Example: Paginated Sitemap

		- Instead of one large sitemap:
			- Generate sitemap-news-1.xml, sitemap-news-2.xml dynamically.
			- Create sitemap-news-index.xml listing all smaller sitemaps.

## ✅ Best Practices for Security & Performance
- Move sitemap-news.php to the WordPress Root Directory (/public_html/).
- Use a Rewrite Rule to Map sitemap-news.xml → sitemap-news.php.
- Restrict Direct Access (.htaccess or NGINX rules).
- Implement Caching (set_transient() or a static file cache).
- Paginate If Needed to avoid performance issues.

![Screenshot of WPEngine NGINX Rewrite Rule.](/img/WPEngine-Rewrite-Rule.png)

![Screenshot of WPEngine NGINX Access Rule.](/img/WPEngine-Access-Rule.png)

🚀 Recommended Final Setup
- Move sitemap-news.php to /public_html/ (WordPress root).
- Rewrite /sitemap-news.xml → /sitemap-news.php (via NGINX or WordPress function).
- Enable Caching to prevent slow queries.
- Restrict Direct Access (prevent external execution).
This keeps your sitemap fast, secure, and future-proof! 🚀

## 🔧 Adjustments (Optional)
- Change Cache Duration:
	- HOUR_IN_SECONDS → 12 * HOUR_IN_SECONDS (12 hours)
	- DAY_IN_SECONDS → (Full day)
- Manually Clear Cache (via WordPress Admin):
`delete_transient('cached_news_sitemap');`
Run this if you manually update blog posts and need an immediate refresh.

## ✅ Benefits
- ✔️ Prevents high database load (only queries once per hour).
- ✔️ Makes sitemap super fast (loads from cache).
- ✔️ Ensures WordPress updates still reflect within 1 hour.

## Ways to Manually Clear the Sitemap Cache
You can use `delete_transient()` in multiple ways:

### 🔹 Option 1: Add a Button in WordPress Admin
You can create a simple admin page where you click a button to clear the cache.

#### 📌 Steps
1. Add this code to your theme’s functions.php or a custom plugin:
```
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
```

### 📌 How It Works
- This creates a "News Sitemap Cache" page under WordPress Admin > News Sitemap Cache.
- Clicking the "Clear Cache Now" button deletes the cached sitemap, forcing a fresh version next time it is accessed.

### 🔹 Option 2: Create a Separate PHP File to Delete Cache
If you want a direct URL-based cache clearing method:

1. Create a new file: clear-sitemap-cache.php inside your WordPress root directory (/public_html/).
2. Add this code:

```
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
```

3. Access it in your browser:
`https://yourwebsite.com/clear-sitemap-cache.php`

If you're an admin, it will clear the cache and show a confirmation message.

### 🚀 Why This Works
- Ensures only logged-in admins can clear the cache.
- No need to edit functions.php.

### 🔹 Option 3: Use WP-CLI to Clear Cache
If you have command-line access, run: `wp transient delete cached_news_sitemap`
This instantly clears the cache without needing a web interface.

## ✅ Best Option?
- If you prefer a UI, use Option 1 (Admin Button).
- If you want a quick URL-based solution, use Option 2 (clear-sitemap-cache.php).
- If you have WP-CLI access, use Option 3.

