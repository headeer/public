# 🔒 Security: Anti-Bot Protection Guide

## ✅ What Has Been Implemented

I've added comprehensive security measures to prevent bots from publishing posts:

### 1. **REST API Protection**
- ✅ Blocks unauthenticated users from creating posts via REST API
- ✅ Requires `publish_posts` capability for all post creation/updates
- ✅ Only authenticated users with proper permissions can publish

### 2. **XML-RPC Disabled**
- ✅ Completely disabled XML-RPC (common attack vector)
- ✅ Removed XML-RPC headers from responses

### 3. **Post Publishing Restrictions**
- ✅ Blocks unauthorized post publishing via `wp_insert_post`
- ✅ Requires authentication for all post publishing
- ✅ Checks user capabilities before allowing publication

### 4. **Spam Detection**
- ✅ Automatically detects spam keywords (casino, gambling, betting, etc.)
- ✅ Changes post status to "draft" if spam detected (for admin review)
- ✅ Logs all suspicious activity

### 5. **Security Headers**
- ✅ Adds security headers (X-Content-Type-Options, X-Frame-Options, etc.)
- ✅ Removes server signature

## 🚨 Immediate Actions Required

### Step 1: Delete Spam Posts

1. Go to **WordPress Admin → Posts → All Posts**
2. Look for posts with:
   - Casino/gambling links
   - Suspicious titles
   - Unknown authors
   - Recent publication dates you don't recognize
3. **Delete them immediately** (or move to trash)

### Step 2: Check User Accounts

1. Go to **WordPress Admin → Users → All Users**
2. Look for suspicious accounts:
   - Unknown usernames
   - Users with "Author" or "Editor" role you didn't create
   - Users with suspicious email addresses
3. **Delete suspicious accounts** (or change their role to "Subscriber")

### Step 3: Review WordPress Settings

1. Go to **WordPress Admin → Settings → General**
2. Check:
   - ✅ **Anyone can register** should be **UNCHECKED**
   - ✅ **New User Default Role** should be **Subscriber** (not Author/Editor)

3. Go to **WordPress Admin → Settings → Discussion**
4. Check:
   - ✅ **Users must be registered and logged in to comment** - CHECK THIS
   - ✅ **Comment must be manually approved** - CHECK THIS

### Step 4: Check for Compromised Plugins

1. Go to **WordPress Admin → Plugins → Installed Plugins**
2. Look for:
   - Unknown plugins you didn't install
   - Plugins with suspicious names
   - Plugins that allow "guest submissions" or "public posts"
3. **Deactivate and delete** any suspicious plugins

## 🔍 How to Find the Source

### Check Post Authors

1. Go to **WordPress Admin → Posts → All Posts**
2. Click on a spam post
3. Check the **Author** field - note the username
4. Go to **Users → All Users** and find that user
5. Check when the account was created and what role it has

### Check Activity Logs

The security system logs suspicious activity. Check your server error logs:

```bash
# Check WordPress debug log (if enabled)
tail -f wp-content/debug.log

# Or check server error logs
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx
```

Look for entries like:
```
KPG Security: Post "..." created by user "..."
KPG Security: Post blocked due to spam keyword "casino"
```

## 🛡️ Additional Security Recommendations

### 1. Change All Passwords

- Change your WordPress admin password
- Change your database password
- Change your hosting/FTP passwords

### 2. Install Security Plugin

Consider installing a security plugin like:
- **Wordfence Security** (free)
- **Sucuri Security** (free)
- **iThemes Security** (free)

### 3. Enable Two-Factor Authentication

- Install a 2FA plugin
- Require 2FA for all admin/editor accounts

### 4. Review File Permissions

Ensure proper file permissions:
```bash
# WordPress files should be 644
find . -type f -exec chmod 644 {} \;

# WordPress directories should be 755
find . -type d -exec chmod 755 {} \;

# wp-config.php should be 600 (very restrictive)
chmod 600 wp-config.php
```

### 5. Check .htaccess for Suspicious Code

1. Open `.htaccess` file in root directory
2. Look for suspicious redirects or code you didn't add
3. If unsure, restore from backup

## 📋 Checklist

- [ ] Delete all spam posts
- [ ] Delete suspicious user accounts
- [ ] Disable "Anyone can register"
- [ ] Set default user role to "Subscriber"
- [ ] Require login for comments
- [ ] Review and remove suspicious plugins
- [ ] Change all passwords
- [ ] Check server logs for attack patterns
- [ ] Review file permissions
- [ ] Check .htaccess for malicious code

## 🔄 What Happens Now

With the security measures in place:

1. **Bots cannot publish posts** - All post creation requires authentication
2. **REST API is protected** - Only authenticated users with proper permissions can use it
3. **XML-RPC is disabled** - Common attack vector is closed
4. **Spam is auto-detected** - Posts with casino/gambling keywords are set to draft
5. **Activity is logged** - All suspicious activity is logged for review

## ⚠️ Important Notes

- The security measures are **active immediately** after plugin activation
- Existing spam posts will **not be automatically deleted** - you must delete them manually
- The spam detection will **prevent new spam** but won't remove old spam
- If you find a compromised user account, **delete it immediately**

## 🆘 If Problems Persist

If bots are still publishing posts after implementing these measures:

1. **Check if there's a compromised plugin** that's bypassing security
2. **Review server access logs** to see where requests are coming from
3. **Consider temporarily disabling all plugins** except this one to isolate the issue
4. **Contact your hosting provider** - they may need to block IP addresses at server level
