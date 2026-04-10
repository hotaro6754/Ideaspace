# IdeaSync Models - Quick Reference Guide

## Model Initialization

All models follow the same initialization pattern:

```php
// Get database connection
$database = new Database();
$conn = $database->connect();

// Initialize model
$model = new ModelName($conn);
```

---

## Notification Model Usage

```php
$notification = new Notification($conn);

// Create notification for collaboration application
$notification->create($idea_creator_id, 'application', $applicant_id, $idea_id);

// Get all notifications for user
$notifications = $notification->getForUser($user_id, $limit = 20, $offset = 0);

// Get unread count
$unread = $notification->getUnreadCount($user_id);

// Mark as read
$notification->markAsRead($notification_id);

// Mark all as read
$notification->markAllAsRead($user_id);

// Get notification statistics
$stats = $notification->getStatistics($user_id);
```

---

## Upvote Model Usage

```php
$upvote = new Upvote($conn);

// Add upvote
$result = $upvote->addUpvote($idea_id, $user_id);

// Remove upvote
$upvote->removeUpvote($idea_id, $user_id);

// Check if user upvoted
if ($upvote->hasUpvoted($idea_id, $user_id)) {
    echo "User already upvoted";
}

// Get upvote count
$count = $upvote->getCount($idea_id);

// Get trending ideas
$trending = $upvote->getTrendingIdeas($limit = 10);
```

---

## Collaboration Model Usage

```php
$collaboration = new Collaboration($conn);

// Create collaboration
$collab = $collaboration->create($idea_id, $leader_id, $collaborator_id, $role = 'Developer');

// Get collaborators for idea
$team = $collaboration->getForIdea($idea_id);

// Get user's collaborations
$collabs = $collaboration->getForUser($user_id);

// Update role
$collaboration->updateRole($collaboration_id, 'Tech Lead');

// User leaves project
$collaboration->markInactive($collaboration_id);

// Get team stats
$stats = $collaboration->getTeamStats($idea_id);

// Get top collaborators
$top = $collaboration->getTopCollaborators($limit = 10);
```

---

## AdminAction Model Usage

```php
$admin = new AdminAction($conn);

// Create admin action
$admin->create($admin_user_id, 'feature_idea', $idea_id, null, 'Great innovative project');

// Get all actions
$actions = $admin->getAll($limit = 50, $offset = 0);

// Get actions by admin
$admin_actions = $admin->getByAdmin($admin_user_id);

// Get actions on idea
$idea_actions = $admin->getByIdea($idea_id);

// Get actions on user
$user_actions = $admin->getByTargetUser($target_user_id);

// Get statistics
$stats = $admin->getStatistics();
```

---

## SearchQuery Model Usage

```php
$search = new SearchQuery($conn);

// Simple idea search
$results = $search->setQuery('AI Machine Learning')->searchIdeas();

// Search with filters
$results = $search->setQuery('AI', [
    'domain' => 'AI/ML',
    'sort_by' => 'trending'
])->searchIdeas();

// Search by skills
$results = $search->searchBySkills(['Python', 'TensorFlow']);

// Search users
$users = $search->searchUsers();

// Combined search
$all = $search->searchAll();

// Get search suggestions (autocomplete)
$suggestions = $search->getSuggestions('AI', $limit = 5);

// Get available filters
$filters = $search->getAvailableFilters();

// Advanced search
$results = $search->advancedSearch([
    'search_term' => 'AI',
    'domain' => 'AI/ML',
    'status' => 'open',
    'branch' => 'CSE'
]);
```

---

## Message Model Usage

```php
$message = new Message($conn);

// Send message
$msg = $message->send($sender_id, $recipient_id, 'Hello, interested in collaborating?');

// Get conversation between users
$conversation = $message->getConversation($user1_id, $user2_id, $limit = 50);

// Get all conversations (inbox)
$inbox = $message->getConversations($user_id, $limit = 20);

// Get single message
$msg = $message->getById($message_id);

// Mark message as read
$message->markAsRead($message_id);

// Mark entire conversation as read
$message->markConversationAsRead($user_id, $contact_id);

// Get unread count
$unread = $message->getUnreadCount($user_id);

// Search messages
$results = $message->search($user_id, 'collaboration');

// Delete message
$message->delete($message_id);

// Get message statistics
$stats = $message->getStatistics($user_id);
```

---

## FileUpload Model Usage

```php
$fileUpload = new FileUpload($conn, '/var/www/html/uploads');

// Upload file for idea
$result = $fileUpload->uploadForIdea($idea_id, $user_id, $_FILES['document']);

// Upload file for collaboration
$result = $fileUpload->uploadForCollaboration($collaboration_id, $user_id, $_FILES['file']);

// Get files for idea
$files = $fileUpload->getFilesForIdea($idea_id);

// Get files for collaboration
$files = $fileUpload->getFilesForCollaboration($collaboration_id);

// Get user's files
$user_files = $fileUpload->getFilesByUser($user_id, $limit = 50);

// Get single file
$file = $fileUpload->getById($file_id);

// Delete file
$result = $fileUpload->delete($file_id);

// Get file count
$count = $fileUpload->getCountForIdea($idea_id);

// Get statistics
$stats = $fileUpload->getStatistics($idea_id);

// Configure file upload settings
$fileUpload->setMaxFileSize(52428800); // 50MB
$fileUpload->setAllowedTypes(['image/jpeg', 'image/png', 'application/pdf']);
```

---

## Response Format

All models follow consistent response format:

### Success Response
```php
[
    'success' => true,
    'id' => 123,  // or 'notification_id', 'message_id', etc.
    'data' => []  // optional additional data
]
```

### Error Response
```php
[
    'success' => false,
    'error' => 'Descriptive error message',
    'code' => 'ERROR_CODE'  // optional
]
```

### Query Response (no action)
```php
[
    'id' => 123,
    'title' => 'Example',
    'created_at' => '2026-04-10 12:00:00',
    // ... other fields
]
```

---

## Error Handling Pattern

```php
$result = $model->create(...);

if ($result['success']) {
    echo "Operation successful: " . $result['id'];
} else {
    echo "Error: " . $result['error'];
    // Handle error appropriately
}
```

---

## Common Queries

### Get user's pending applications
```php
$application = new Application($conn);
$pending = $application->getByUser($user_id); // Returns all applications
// Filter for 'pending' status in application layer
```

### Get user's active collaborations
```php
$collaboration = new Collaboration($conn);
$collabs = $collaboration->getForUser($user_id); // Automatically returns active
```

### Get user's unread messages
```php
$message = new Message($conn);
$unread_count = $message->getUnreadCount($user_id);
```

### Get trending ideas
```php
$upvote = new Upvote($conn);
$trending = $upvote->getTrendingIdeas($limit = 10);
```

### Search ideas by skill
```php
$search = new SearchQuery($conn);
$results = $search->searchBySkills(['Python', 'React']);
```

---

## Performance Considerations

### Indexes Implemented
- User branch filtering: `idx_user_branch`
- Idea domain filtering: `idx_idea_domain`
- Notification user lookup: `idx_notification_user`
- Message conversation: `idx_conversation`
- File uploads by idea: `idx_idea_uploads`

### Pagination Best Practices
```php
// Always use pagination for large result sets
$ideas = $idea->getAll($limit = 20, $offset = ($page - 1) * 20);

// For large searches
$results = $search->searchIdeas($limit = 20, $offset = 0);
```

### Query Optimization
- Use filters to reduce result sets
- Limit initial queries to necessary fields
- Use pagination to avoid memory issues
- Leverage indexes for frequent queries

---

## Security Notes

✅ All queries use prepared statements
✅ Input validation in all methods
✅ FOREIGN KEY constraints prevent orphaned records
✅ Prepared statements prevent SQL injection
✅ File type validation for uploads
✅ File size limits enforced

---

## Constants and Enums

### Notification Types
- `application` - New collaboration application
- `acceptance` - Application accepted
- `rejection` - Application rejected
- `upvote` - Idea upvoted
- `message` - New direct message

### Admin Actions
- `feature_idea` - Feature on homepage
- `remove_idea` - Remove inappropriate content
- `flag_user` - Flag for review
- `verify_skills` - Verify credentials

### Application Statuses
- `pending` - Awaiting response
- `accepted` - Accepted by creator
- `rejected` - Rejected by creator
- `withdrawn` - Withdrawn by applicant

### Collaboration Status
- `active` - Active collaboration
- `inactive` - User left or project ended

---

## Database Relationships

```
Users
├── Ideas (1:Many)
├── Applications (1:Many)
├── Collaborations (1:Many as leader)
├── Upvotes (1:Many)
├── Notifications (1:Many)
├── Messages (1:Many as sender/recipient)
└── FileUploads (1:Many)

Ideas
├── Applications (1:Many)
├── Collaborations (1:Many)
├── Upvotes (1:Many)
├── Notifications (1:Many)
└── FileUploads (1:Many)

Collaborations
├── Messages (potential)
└── FileUploads (1:Many)
```

---

Generated: 2026-04-10
Ready for integration with controllers and views.
