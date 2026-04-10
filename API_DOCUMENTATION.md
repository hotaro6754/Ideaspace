# IdeaSync - Complete API Reference

## Base URL
`http://localhost:8000`

## Authentication
All protected endpoints require `session_id` cookie (set via login).

## Response Format
All API endpoints return JSON:
```json
{
  "success": true/false,
  "data": {},
  "message": "string",
  "error": "string (if success=false)"
}
```

---

## Authentication Endpoints

### Register
- **POST** `/src/controllers/auth.php?action=register`
- **Parameters:**
  - `roll_number` (string, required) - Format: LID### minimum 3 digits
  - `name` (string, required)
  - `email` (string, required)
  - `password` (string, required, min 8 chars)
  - `branch` (string, required) - CSE, ECE, MBA, etc.
  - `year` (integer, required) - 1, 2, 3, or 4
  - `user_type` (string) - 'visionary' or 'builder'
- **Response:** User object + session creation

### Login
- **POST** `/src/controllers/auth.php?action=login`
- **Parameters:**
  - `roll_number` (string, required)
  - `password` (string, required)
- **Response:** User object + session creation

### Logout
- **GET** `/src/controllers/auth.php?action=logout`
- **Response:** Success message

### GitHub Login
- **GET** `/?page=github-callback`
- **Parameters:** `code`, `state` (from GitHub OAuth)
- **Response:** User object + Github data linked

---

## Ideas Endpoints

### Create Idea
- **POST** `/src/controllers/ideas.php?action=create`
- **Protected:** Yes
- **Parameters:**
  - `title` (string, required)
  - `description` (string, required)
  - `domain` (string, required)
  - `skills_needed` (JSON array, required)
  - `github_repo_url` (string, optional)
- **Response:** Created idea object

### List Ideas
- **GET** `/?page=ideas`
- **Protected:** No
- **Parameters:**
  - `domain` (string, optional) - Filter by domain
  - `status` (string, optional) - open/in_progress/completed
  - `search` (string, optional) - Search title/description
  - `page` (integer) - Pagination
- **Response:** Array of ideas with pagination

### Get Idea Detail
- **GET** `/?page=idea-detail&id=X`
- **Protected:** No
- **Parameters:**
  - `id` (integer, required) - Idea ID
- **Response:** Full idea object with collaborators, upvotes

### Update Idea
- **POST** `/src/controllers/ideas.php?action=update`
- **Protected:** Yes (creator only)
- **Parameters:**
  - `id` (integer, required)
  - `title`, `description`, `domain`, `skills_needed`, `status`
- **Response:** Updated idea object

### Delete Idea
- **POST** `/src/controllers/ideas.php?action=delete`
- **Protected:** Yes (creator only)
- **Parameters:**
  - `id` (integer, required)
- **Response:** Success message

### Toggle Upvote
- **POST** `/src/controllers/ideas.php?action=toggle-upvote`
- **Protected:** Yes
- **Parameters:**
  - `idea_id` (integer, required)
- **Response:** Updated upvote count

---

## Collaboration Endpoints

### Apply for Collaboration
- **POST** `/src/controllers/collaboration.php?action=apply`
- **Protected:** Yes
- **Parameters:**
  - `idea_id` (integer, required)
  - `message` (string, optional)
- **Response:** Application object

### Get Applications
- **GET** `/src/controllers/collaboration.php?action=get-applications`
- **Protected:** Yes
- **Parameters:**
  - `idea_id` (integer, optional) - For creators
- **Response:** Array of applications

### Accept Application
- **POST** `/src/controllers/collaboration.php?action=accept`
- **Protected:** Yes (idea creator)
- **Parameters:**
  - `application_id` (integer, required)
  - `role` (string, optional) - Collaborator role
- **Response:** Updated application + collaboration created

### Reject Application
- **POST** `/src/controllers/collaboration.php?action=reject`
- **Protected:** Yes (idea creator)
- **Parameters:**
  - `application_id` (integer, required)
- **Response:** Success message

### Get Collaborations
- **GET** `/src/controllers/collaboration.php?action=get-collaborations`
- **Protected:** Yes
- **Parameters:**
  - `user_id` (integer, optional)
  - `idea_id` (integer, optional)
- **Response:** Array of collaboration objects

---

## User Endpoints

### Get Profile
- **GET** `/src/controllers/user.php?action=get-profile`
- **Protected:** Optional
- **Parameters:**
  - `user_id` (integer) - If not provided, returns current user
- **Response:** User profile object with stats

### Update Profile
- **POST** `/src/controllers/user.php?action=update`
- **Protected:** Yes
- **Parameters:**
  - `name`, `bio`, `branch`, `github_username` (optional)
  - `profile_pic` (file upload)
- **Response:** Updated user object

### Link GitHub
- **POST** `/src/controllers/user.php?action=link-github`
- **Protected:** Yes
- **Parameters:**
  - `github_token` (string) - From OAuth
- **Response:** Updated user with GitHub data

---

## Notifications Endpoints

### Get Notifications
- **GET** `/src/controllers/notifications.php?action=get`
- **Protected:** Yes
- **Parameters:**
  - `unread_only` (boolean, default true)
  - `limit` (integer, default 20)
  - `offset` (integer, default 0)
- **Response:** Array of notifications

### Mark as Read
- **POST** `/src/controllers/notifications.php?action=mark-read`
- **Protected:** Yes
- **Parameters:**
  - `notification_id` (integer) - If null, mark all as read
- **Response:** Success message

### Get Unread Count
- **GET** `/src/controllers/notifications.php?action=unread-count`
- **Protected:** Yes
- **Response:** Unread count

---

## Messaging Endpoints

### Send Message
- **POST** `/src/controllers/messages.php?action=send`
- **Protected:** Yes
- **Parameters:**
  - `recipient_id` (integer, required)
  - `message` (string, required)
- **Response:** Message object

### Get Conversation
- **GET** `/src/controllers/messages.php?action=get-conversation`
- **Protected:** Yes
- **Parameters:**
  - `user_id` (integer, required)
  - `limit` (integer, default 50)
  - `offset` (integer, default 0)
- **Response:** Array of messages

### Get Conversations
- **GET** `/src/controllers/messages.php?action=get-conversations`
- **Protected:** Yes
- **Response:** Array of recent conversations

---

## Search Endpoints

### Search Ideas & Users
- **GET** `/src/controllers/search.php?action=search`
- **Parameters:**
  - `q` (string, required) - Search query
  - `type` (string) - 'ideas', 'users', or 'all'
  - `domain` (string, optional) - Filter by domain
  - `skills` (array, optional) - Filter by skills
  - `limit` (integer, default 20)
- **Response:** Search results with pagination

### Search Suggestions
- **GET** `/src/controllers/search.php?action=suggestions`
- **Parameters:**
  - `q` (string, required) - Partial query
  - `limit` (integer, default 10)
- **Response:** Array of suggestions

---

## Gamification Endpoints

### Get Leaderboard
- **GET** `/src/controllers/gamification.php?action=leaderboard`
- **Parameters:**
  - `type` (string) - 'overall', 'builders', 'visionaries'
  - `limit` (integer, default 100)
  - `page` (integer, default 1)
- **Response:** Array of user rankings

### Get User Rank
- **GET** `/src/controllers/gamification.php?action=user-rank`
- **Parameters:**
  - `user_id` (integer) - If not provided, returns current user
- **Response:** User rank info + position on leaderboard

### Get User Stats
- **GET** `/src/controllers/gamification.php?action=user-stats`
- **Protected:** No
- **Parameters:**
  - `user_id` (integer, required)
- **Response:** Detailed user statistics

---

## File Upload Endpoints

### Upload File
- **POST** `/src/controllers/fileupload.php?action=upload`
- **Protected:** Yes
- **Parameters:**
  - `file` (file, required) - Max 10MB
  - `related_to_type` (string) - 'profile', 'idea', 'collaboration'
  - `related_to_id` (integer, required)
- **Response:** File object with URL

### Delete File
- **POST** `/src/controllers/fileupload.php?action=delete`
- **Protected:** Yes
- **Parameters:**
  - `file_id` (integer, required)
- **Response:** Success message

### Get Files
- **GET** `/src/controllers/fileupload.php?action=get`
- **Parameters:**
  - `related_to_type` (string, required)
  - `related_to_id` (integer, required)
- **Response:** Array of files

---

## Admin Endpoints

### Get Users
- **GET** `/src/controllers/admin.php?action=get-users`
- **Protected:** Yes (admin only)
- **Parameters:**
  - `limit` (integer, default 50)
  - `offset` (integer)
- **Response:** Array of users with stats

### Get Reports
- **GET** `/src/controllers/admin.php?action=get-reports`
- **Protected:** Yes (admin only)
- **Response:** Analytics data

### Moderate User
- **POST** `/src/controllers/admin.php?action=moderate-user`
- **Protected:** Yes (admin only)
- **Parameters:**
  - `user_id` (integer, required)
  - `action` (string) - 'warn', 'suspend', 'unfreeze'
  - `reason` (string, optional)
- **Response:** Success message

### Delete Idea
- **POST** `/src/controllers/admin.php?action=delete-idea`
- **Protected:** Yes (admin only)
- **Parameters:**
  - `idea_id` (integer, required)
  - `reason` (string, optional)
- **Response:** Success message

---

## Error Codes

| Code | Meaning |
|------|---------|
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Not logged in |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource doesn't exist |
| 409 | Conflict - Duplicate entry |
| 413 | Payload Too Large - File too big |
| 429 | Too Many Requests - Rate limited |
| 500 | Internal Server Error |

---

## Example Requests

### Register
```bash
curl -X POST http://localhost:8000/src/controllers/auth.php?action=register \
  -d "roll_number=LID001&name=John&email=john@example.com&password=Secure123&branch=CSE&year=3&user_type=builder"
```

### Create Idea
```bash
curl -X POST http://localhost:8000/src/controllers/ideas.php?action=create \
  -H "Content-Type: application/json" \
  -d '{
    "title": "AI Chatbot", 
    "description": "Build an AI chatbot",
    "domain": "AI/ML",
    "skills_needed": ["Python", "TensorFlow"]
  }'
```

### Search Ideas
```bash
curl "http://localhost:8000/src/controllers/search.php?action=search&q=chatbot&type=ideas&domain=AI/ML"
```

---

## Rate Limits

- Login: 5 attempts per hour
- API: 100 requests per minute
- File Upload: 5 uploads per hour
- Messaging: 50 messages per minute

Exceeding limits returns HTTP 429.

---

**Last Updated:** 2024-04-10
**Version:** 1.0.0
