# API Documentation

## Overview

Infinity CMS provides a RESTful API for interacting with your content programmatically. All API endpoints are prefixed with `/api/` and return JSON responses.

## Authentication

API requests require authentication using either session cookies or API tokens.

### Session Authentication
When logged in through the web interface, your session cookie will automatically authenticate API requests.

### API Token Authentication
Include your API token in the Authorization header:
```
Authorization: Bearer YOUR_API_TOKEN
```

## Base URL
```
https://your-site.com/api
```

## Response Format

All API responses follow this format:

```json
{
  "success": true,
  "data": {},
  "message": "Success message",
  "errors": []
}
```

## Endpoints

### Posts

#### Get All Posts
```http
GET /api/posts
```

Query Parameters:
- `status` (string): Filter by status (published, draft, archived)
- `page` (integer): Page number for pagination
- `per_page` (integer): Items per page (default: 20)
- `sort` (string): Sort field (created_at, updated_at, title)
- `order` (string): Sort order (asc, desc)

Response:
```json
{
  "success": true,
  "data": {
    "posts": [
      {
        "id": 1,
        "title": "Post Title",
        "slug": "post-title",
        "content": "Post content...",
        "excerpt": "Post excerpt...",
        "status": "published",
        "author_id": 1,
        "created_at": "2024-01-01 00:00:00",
        "updated_at": "2024-01-01 00:00:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 100,
      "per_page": 20
    }
  }
}
```

#### Get Single Post
```http
GET /api/posts/{id}
```

Response:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Post Title",
    "slug": "post-title",
    "content": "Full post content...",
    "excerpt": "Post excerpt...",
    "status": "published",
    "author": {
      "id": 1,
      "name": "Author Name",
      "email": "author@example.com"
    },
    "categories": [
      {
        "id": 1,
        "name": "Category Name",
        "slug": "category-slug"
      }
    ],
    "tags": ["tag1", "tag2"],
    "meta": {
      "views": 150,
      "likes": 25,
      "comments_count": 10
    },
    "created_at": "2024-01-01 00:00:00",
    "updated_at": "2024-01-01 00:00:00"
  }
}
```

#### Create Post
```http
POST /api/posts
```

Request Body:
```json
{
  "title": "New Post Title",
  "content": "Post content in Markdown",
  "excerpt": "Optional excerpt",
  "status": "draft",
  "categories": [1, 2],
  "tags": ["tag1", "tag2"]
}
```

Response:
```json
{
  "success": true,
  "message": "Post created successfully",
  "data": {
    "id": 2,
    "title": "New Post Title",
    "slug": "new-post-title"
  }
}
```

#### Update Post
```http
PUT /api/posts/{id}
```

Request Body:
```json
{
  "title": "Updated Title",
  "content": "Updated content",
  "status": "published"
}
```

#### Delete Post
```http
DELETE /api/posts/{id}
```

Response:
```json
{
  "success": true,
  "message": "Post deleted successfully"
}
```

#### Preview Post (Markdown)
```http
POST /api/posts/preview
```

Request Body:
```json
{
  "content": "# Markdown content to preview"
}
```

Response:
```json
{
  "success": true,
  "data": {
    "html": "<h1>Markdown content to preview</h1>"
  }
}
```

### Pages

#### Get All Pages
```http
GET /api/pages
```

Query Parameters:
- `parent_id` (integer): Filter by parent page
- `status` (string): Filter by status

#### Get Single Page
```http
GET /api/pages/{id}
```

#### Create Page
```http
POST /api/pages
```

Request Body:
```json
{
  "title": "Page Title",
  "content": "Page content",
  "parent_id": null,
  "template": "default",
  "status": "published"
}
```

#### Update Page
```http
PUT /api/pages/{id}
```

#### Delete Page
```http
DELETE /api/pages/{id}
```

### Categories

#### Get All Categories
```http
GET /api/categories
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Technology",
      "slug": "technology",
      "description": "Tech related posts",
      "parent_id": null,
      "post_count": 25
    }
  ]
}
```

#### Get Category Posts
```http
GET /api/categories/{slug}/posts
```

#### Create Category
```http
POST /api/categories
```

Request Body:
```json
{
  "name": "Category Name",
  "description": "Category description",
  "parent_id": null
}
```

#### Update Category
```http
PUT /api/categories/{id}
```

#### Delete Category
```http
DELETE /api/categories/{id}
```

### Media

#### Upload Media
```http
POST /api/media/upload
```

Request:
- Method: POST (multipart/form-data)
- Field: `file` - The file to upload

Response:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "filename": "image.jpg",
    "url": "/uploads/2024/01/image.jpg",
    "mime_type": "image/jpeg",
    "size": 102400
  }
}
```

#### Get Media Library
```http
GET /api/media
```

Query Parameters:
- `type` (string): Filter by mime type (image, video, document)
- `page` (integer): Page number

#### Delete Media
```http
DELETE /api/media/{id}
```

### Users

#### Get Current User
```http
GET /api/users/me
```

Response:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "admin",
    "email": "admin@example.com",
    "name": "Admin User",
    "role": "administrator",
    "avatar": "/uploads/avatars/admin.jpg"
  }
}
```

#### Update Profile
```http
PUT /api/users/me
```

Request Body:
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "bio": "Updated bio"
}
```

#### Change Password
```http
POST /api/users/me/password
```

Request Body:
```json
{
  "current_password": "current_password",
  "new_password": "new_secure_password",
  "confirm_password": "new_secure_password"
}
```

### Search

#### Global Search
```http
GET /api/search
```

Query Parameters:
- `q` (string): Search query
- `type` (string): Content type (posts, pages, all)
- `limit` (integer): Max results (default: 10)

Response:
```json
{
  "success": true,
  "data": {
    "results": [
      {
        "type": "post",
        "id": 1,
        "title": "Matching Post",
        "excerpt": "...matching content...",
        "url": "/posts/matching-post"
      }
    ],
    "total": 5,
    "query": "search term"
  }
}
```

### Settings

#### Get Public Settings
```http
GET /api/settings/public
```

Response:
```json
{
  "success": true,
  "data": {
    "site_name": "My Site",
    "site_description": "Site description",
    "date_format": "Y-m-d",
    "timezone": "UTC"
  }
}
```

#### Get Settings (Admin Only)
```http
GET /api/settings
```

#### Update Settings (Admin Only)
```http
PUT /api/settings
```

Request Body:
```json
{
  "site_name": "Updated Site Name",
  "site_description": "Updated description"
}
```

### Comments

#### Get Post Comments
```http
GET /api/posts/{id}/comments
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "content": "Comment content",
      "author": {
        "name": "Commenter Name",
        "avatar": "/default-avatar.png"
      },
      "created_at": "2024-01-01 00:00:00",
      "replies": []
    }
  ]
}
```

#### Add Comment
```http
POST /api/posts/{id}/comments
```

Request Body:
```json
{
  "content": "Comment content",
  "parent_id": null
}
```

#### Delete Comment
```http
DELETE /api/comments/{id}
```

## Error Handling

### Error Response Format
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### HTTP Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request parameters
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Access denied
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed
- `500 Internal Server Error` - Server error

## Rate Limiting

API requests are rate-limited to prevent abuse:
- Authenticated users: 1000 requests per hour
- Unauthenticated users: 100 requests per hour

Rate limit information is included in response headers:
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1609459200
```

## Webhooks

Infinity CMS can send webhooks for various events. Configure webhooks in the admin panel.

### Webhook Events

- `post.created` - New post created
- `post.updated` - Post updated
- `post.deleted` - Post deleted
- `post.published` - Post status changed to published
- `comment.created` - New comment added
- `user.registered` - New user registered

### Webhook Payload Example
```json
{
  "event": "post.published",
  "timestamp": "2024-01-01T00:00:00Z",
  "data": {
    "id": 1,
    "title": "Post Title",
    "url": "https://your-site.com/posts/post-title"
  }
}
```

## Code Examples

### JavaScript (Fetch API)
```javascript
// Get all posts
fetch('/api/posts', {
  headers: {
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));

// Create a post
fetch('/api/posts', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    title: 'New Post',
    content: 'Post content',
    status: 'draft'
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

### PHP (cURL)
```php
// Get all posts
$ch = curl_init('https://your-site.com/api/posts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

// Create a post
$ch = curl_init('https://your-site.com/api/posts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'title' => 'New Post',
    'content' => 'Post content',
    'status' => 'draft'
]));
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);
```

### Python (requests)
```python
import requests

# Get all posts
response = requests.get('https://your-site.com/api/posts')
data = response.json()

# Create a post
response = requests.post('https://your-site.com/api/posts', json={
    'title': 'New Post',
    'content': 'Post content',
    'status': 'draft'
})
data = response.json()
```

## Support

For API support and questions, please refer to:
- GitHub Issues: [Report bugs or request features]
- Documentation: https://your-site.com/docs
- Community Forum: https://your-site.com/community