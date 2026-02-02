# Rank Math SEO Migration Guide

## Overview
This tool migrates Rank Math SEO meta tags from the old WordPress site to the new site. It matches posts, terms, and users by their slugs/nicenames and transfers all Rank Math SEO data.

## What Gets Migrated

### Posts
- Title (rank_math_title)
- Description (rank_math_description)
- Focus Keyword (rank_math_focus_keyword)
- Robots settings (rank_math_robots)
- Canonical URL (rank_math_canonical_url)
- Facebook meta (title, description, image)
- Twitter meta (title, description, image, card type)
- Schema data
- SEO scores
- Breadcrumb titles
- And all other Rank Math meta fields

### Categories & Tags
- Title
- Description
- All other Rank Math term meta

### Authors
- Title
- Description
- All other Rank Math user meta

## Method 1: WordPress Admin Interface

### Step 1: Get Database Credentials
You need the following information from the old WordPress site:
- Database host (usually `localhost`)
- Database name
- Database username
- Database password
- Database table prefix (usually `wp_`)
- Old site URL (e.g., `https://old-site.com`)

### Step 2: Access Migration Tool
1. Log in to WordPress admin on the **new site**
2. Go to: **Tools → Rank Math Migration**
3. You'll see a form with database connection fields

### Step 3: Enter Database Information
Fill in all the required fields:
- **Old Database Host**: Usually `localhost` or the database server address
- **Old Database Name**: The name of the old WordPress database
- **Old Database User**: Database username
- **Old Database Password**: Database password
- **Old Database Prefix**: Usually `wp_` (check old site's wp-config.php if unsure)
- **Old Site URL**: Full URL of the old site (e.g., `https://www.kpgio.pl`)

### Step 4: Run Migration
1. Click **"Start Migration"** button
2. Wait for the process to complete (may take a few minutes for large sites)
3. Review the results:
   - Number of posts migrated
   - Number of terms migrated
   - Number of users migrated
   - Number of items skipped (posts/terms/users not found in new site)

### Step 5: Verify Results
1. Check a few posts in the new site to ensure Rank Math meta is present
2. Go to a post editor and check the Rank Math SEO box
3. Verify that titles, descriptions, and other meta are populated

## Method 2: WP-CLI (Command Line)

If you have SSH access to the server, you can use WP-CLI for faster migration.

### Step 1: Connect via SSH
SSH into your server where the new WordPress site is hosted.

### Step 2: Navigate to WordPress Directory
```bash
cd /path/to/wordpress
```

### Step 3: Run Migration Command
```bash
wp kpg-migrate-rankmath \
  --old-db-host=localhost \
  --old-db-name=old_database_name \
  --old-db-user=old_database_user \
  --old-db-pass=old_database_password \
  --old-db-prefix=wp_ \
  --old-site-url=https://old-site.com
```

Replace the values with your actual old database credentials.

### Step 4: Review Output
The command will show:
- Progress messages
- Number of items migrated
- Any errors or skipped items

## Method 3: Using Constants in wp-config.php

You can also define the old database credentials in `wp-config.php` to avoid entering them each time:

```php
// Rank Math Migration Settings
define( 'KPG_OLD_DB_HOST', 'localhost' );
define( 'KPG_OLD_DB_NAME', 'old_database_name' );
define( 'KPG_OLD_DB_USER', 'old_database_user' );
define( 'KPG_OLD_DB_PASS', 'old_database_password' );
define( 'KPG_OLD_DB_PREFIX', 'wp_' );
define( 'KPG_OLD_SITE_URL', 'https://old-site.com' );
```

After adding these, the migration form will be pre-filled with these values.

## Important Notes

### URL Replacement
- The tool automatically replaces old site URLs with new site URLs in all meta values
- This ensures that canonical URLs, images, and other links point to the correct domain

### Matching Logic
- **Posts**: Matched by post slug (`post_name`)
- **Terms**: Matched by term slug and taxonomy
- **Users**: Matched by user nicename (`user_nicename`)

### What Happens if Items Don't Match?
- If a post/term/user from the old site doesn't exist in the new site, it will be skipped
- The migration tool will report how many items were skipped
- You can manually add missing items or re-run migration after creating them

### Safety
- The migration only **adds** or **updates** Rank Math meta - it doesn't delete existing data
- If a post already has Rank Math meta, it will be updated with values from the old site
- You can run the migration multiple times safely

## Troubleshooting

### "Failed to connect to old database"
- Check database credentials
- Ensure the old database server is accessible from the new server
- Check firewall rules if databases are on different servers

### "Post not found (skipped)"
- The post slug might be different between old and new site
- Check if the post exists in the new site
- You may need to manually match posts if slugs changed

### "No Rank Math meta found"
- Ensure Rank Math SEO plugin was active on the old site
- Check if posts actually had Rank Math meta set
- Some posts might not have SEO data configured

### Migration Takes Too Long
- For sites with 100+ posts, migration may take 2-5 minutes
- Use WP-CLI method for faster execution
- Consider running during off-peak hours

## After Migration

1. **Clear Caches**: Clear any caching plugins (WP Rocket, W3 Total Cache, etc.)
2. **Check Rank Math Settings**: Verify Rank Math SEO plugin settings are configured correctly
3. **Test a Few Posts**: Check frontend to ensure meta tags are appearing correctly
4. **Verify Schema**: Use Google's Rich Results Test to verify schema markup
5. **Check Search Console**: Monitor Google Search Console for any issues

## Support

If you encounter issues:
1. Check WordPress debug log: `wp-content/debug.log`
2. Enable WordPress debugging in `wp-config.php`:
   ```php
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_LOG', true );
   ```
3. Review the migration details/output for specific error messages
