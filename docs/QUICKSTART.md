# Quick Start Guide

Welcome to Infinity CMS! This guide will help you get up and running quickly.

## 5-Minute Setup

### Step 1: Upload Files
Upload all Infinity CMS files to your web server's document root (usually `public_html` or `www`).

### Step 2: Create Database
In your hosting control panel (cPanel, Plesk, etc.):
1. Go to MySQL Databases
2. Create a new database
3. Create a database user
4. Add the user to the database with ALL privileges

### Step 3: Run Setup
Visit your website URL. The setup wizard will automatically start:
1. Enter your database details
2. Create your admin account
3. Set your site name and description
4. Click "Install"

That's it! Your CMS is ready to use.

## First Steps After Installation

### 1. Log into Admin Panel
Visit `yoursite.com/admin` and log in with your admin credentials.

### 2. Configure Basic Settings
Go to **Settings > General** to configure:
- Site title and tagline
- Timezone
- Date format
- Homepage settings

### 3. Create Your First Post
1. Go to **Posts > Add New**
2. Enter a title and content (supports Markdown!)
3. Add categories and tags
4. Click "Publish" or "Save as Draft"

### 4. Create Pages
1. Go to **Pages > Add New**
2. Create essential pages like About, Contact, Privacy Policy
3. Set page templates if needed

### 5. Customize Your Theme
1. Go to **Appearance > Themes**
2. Preview available themes
3. Activate your preferred theme
4. Go to **Appearance > Customize** for theme options

### 6. Install Plugins
1. Go to **Plugins**
2. Browse available plugins
3. Click "Activate" to enable plugins
4. Configure plugin settings

## Essential Tasks

### Setting Up Navigation Menus
1. Go to **Appearance > Menus**
2. Create a new menu
3. Add pages, posts, categories, or custom links
4. Assign menu to a location (Primary, Footer, etc.)
5. Save menu

### Managing Media
1. Go to **Media > Library** to view uploaded files
2. Click "Add New" to upload images, videos, or documents
3. Media can be inserted into posts/pages using the editor

### User Management
1. Go to **Users** to manage user accounts
2. Add new users with roles:
   - **Administrator**: Full access
   - **Editor**: Can manage and publish all content
   - **Author**: Can write and publish own posts
   - **Contributor**: Can write but not publish
   - **Subscriber**: Can only manage profile

### SEO Setup
1. Install an SEO plugin
2. Configure meta titles and descriptions
3. Set up XML sitemap
4. Configure robots.txt

## Content Creation Tips

### Using the Editor

#### Markdown Support
```markdown
# Heading 1
## Heading 2
### Heading 3

**Bold text**
*Italic text*
[Link text](https://example.com)

- Bullet list
- Item 2

1. Numbered list
2. Item 2

> Blockquote

`inline code`

​```
code block
​```
```

#### Keyboard Shortcuts
- **Ctrl/Cmd + B**: Bold
- **Ctrl/Cmd + I**: Italic
- **Ctrl/Cmd + K**: Insert link
- **Ctrl/Cmd + S**: Save draft
- **Ctrl/Cmd + Enter**: Publish

### Adding Dynamic Content with HTMX

In your posts or pages, you can add dynamic content:

```html
<!-- Load latest posts -->
<div hx-get="/api/posts/latest"
     hx-trigger="load">
    Loading latest posts...
</div>

<!-- Contact form -->
<form hx-post="/api/contact"
      hx-target="#result">
    <input type="email" name="email" required>
    <textarea name="message" required></textarea>
    <button type="submit">Send</button>
</form>
<div id="result"></div>
```

### Using Alpine.js Components

Add interactivity to your content:

```html
<!-- Accordion -->
<div x-data="{ open: false }">
    <button @click="open = !open">
        Click to expand
    </button>
    <div x-show="open" x-transition>
        Hidden content here
    </div>
</div>

<!-- Counter -->
<div x-data="{ count: 0 }">
    <button @click="count++">Click me</button>
    <span x-text="count"></span>
</div>
```

## Common Tasks

### Backing Up Your Site

#### Manual Backup
1. **Database**: Export via phpMyAdmin or hosting control panel
2. **Files**: Download via FTP or File Manager
3. Store backups in a safe location

#### Automated Backup
1. Install a backup plugin
2. Configure backup schedule
3. Set remote storage (Google Drive, Dropbox, etc.)

### Updating Infinity CMS

1. **Backup your site** (database and files)
2. Download the latest version
3. Upload new files (except config.php)
4. Visit `/admin/update` to run database updates
5. Clear cache

### Troubleshooting

#### White Screen of Death
1. Enable debug mode in config.php
2. Check error logs
3. Disable recently activated plugins
4. Switch to default theme

#### Can't Login to Admin
1. Clear browser cookies
2. Try incognito/private mode
3. Reset password via database
4. Check file permissions

#### Slow Performance
1. Enable caching
2. Optimize images
3. Use a CDN
4. Minimize plugins
5. Upgrade hosting if needed

## Keyboard Shortcuts

### Admin Panel
- **Alt + N**: New post
- **Alt + M**: Media library
- **Alt + S**: Settings
- **Alt + H**: Help

### Editor
- **Ctrl/Cmd + S**: Save
- **Ctrl/Cmd + P**: Preview
- **Ctrl/Cmd + Z**: Undo
- **Ctrl/Cmd + Shift + Z**: Redo

## Getting Help

### Documentation
- [Full Documentation](README.md)
- [API Reference](API.md)
- [Development Guide](DEVELOPMENT.md)

### Community
- GitHub Issues for bug reports
- Community Forum for discussions
- Stack Overflow tag: `infinity-cms`

### Professional Support
Contact professional Infinity CMS developers for:
- Custom development
- Theme design
- Plugin development
- Performance optimization
- Security audits

## Next Steps

Now that you're up and running:

1. **Explore the Admin Panel**: Familiarize yourself with all sections
2. **Customize Your Site**: Make it unique with themes and plugins
3. **Create Content**: Start building your content library
4. **Optimize for SEO**: Set up meta tags, sitemaps, and analytics
5. **Join the Community**: Get support and share your experience

Welcome to Infinity CMS! We're excited to see what you'll build.