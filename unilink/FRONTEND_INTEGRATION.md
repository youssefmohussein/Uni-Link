# Frontend-Backend Integration Guide

## ✅ What's Been Created

### API Services (in `src/services/`)

1. **notificationService.js** - Notification API calls
   - `getNotifications()` - Get all notifications
   - `getUnreadNotifications()` - Get unread only
   - `getUnreadCount()` - Get unread count
   - `markAsRead(id)` - Mark as read
   - `markAllAsRead()` - Mark all as read
   - `deleteNotification(id)` - Delete notification

2. **chatService.js** - Chat API calls (Chain of Responsibility)
   - `sendMessage(roomId, content)` - Send message
   - `getRoomMessages(roomId)` - Get messages
   - `getMessageCount(roomId)` - Get count
   - `deleteMessage(id)` - Delete message

3. **profileService.js** - Profile API calls (Facade Pattern)
   - `getFullProfile()` - Get complete profile (uses ProfileFacade)
   - `getPublicProfile(userId)` - Get public profile
   - `updateProfile(data)` - Update profile

4. **projectService.js** - Project API calls (Command Pattern)
   - `getUserProjects()` - Get user projects
   - `uploadProject(data)` - Upload project
   - `approveProject(id, score, comment)` - Approve (Command)
   - `rejectProject(id, comment)` - Reject (Command)
   - `gradeProject(id, grade, comment)` - Grade (Command)

### React Components

1. **Notifications.jsx** - Notification panel component
   - Real-time notification updates (polls every 30s)
   - Mark as read functionality
   - Delete notifications
   - Unread count badge

---

## 🔧 How to Use in Your Frontend

### 1. Add Notification Bell to Navbar

Update your navbar component to include notifications:

```javascript
import React, { useState, useEffect } from 'react';
import Notifications from '../Components/Notifications/Notifications';
import { getUnreadCount } from '../services/notificationService';

function Navbar() {
    const [showNotifications, setShowNotifications] = useState(false);
    const [unreadCount, setUnreadCount] = useState(0);

    useEffect(() => {
        const fetchUnreadCount = async () => {
            const data = await getUnreadCount();
            if (data.status === 'success') {
                setUnreadCount(data.unread_count);
            }
        };

        fetchUnreadCount();
        const interval = setInterval(fetchUnreadCount, 30000); // Poll every 30s
        return () => clearInterval(interval);
    }, []);

    return (
        <nav>
            {/* Your existing navbar items */}
            
            <button 
                className="notification-bell"
                onClick={() => setShowNotifications(!showNotifications)}
            >
                🔔
                {unreadCount > 0 && (
                    <span className="badge">{unreadCount}</span>
                )}
            </button>

            <Notifications 
                isOpen={showNotifications}
                onClose={() => setShowNotifications(false)}
            />
        </nav>
    );
}
```

### 2. Update Profile Page to Use ProfileFacade

```javascript
import { getFullProfile } from '../services/profileService';

function ProfilePage() {
    const [profile, setProfile] = useState(null);

    useEffect(() => {
        const fetchProfile = async () => {
            const data = await getFullProfile();
            if (data.status === 'success') {
                setProfile(data);
                // data contains: user, skills, projects, recent_posts, cv, stats
            }
        };
        fetchProfile();
    }, []);

    return (
        <div>
            <h2>{profile?.user?.username}</h2>
            <p>Skills: {profile?.stats?.total_skills}</p>
            <p>Projects: {profile?.stats?.total_projects}</p>
            {/* Display skills, projects, posts, etc. */}
        </div>
    );
}
```

### 3. Update Chat Room to Use Chain of Responsibility

```javascript
import { sendMessage, getRoomMessages } from '../services/chatService';

function ChatRoom({ roomId }) {
    const [messages, setMessages] = useState([]);
    const [newMessage, setNewMessage] = useState('');

    useEffect(() => {
        const fetchMessages = async () => {
            const data = await getRoomMessages(roomId);
            if (data.status === 'success') {
                setMessages(data.messages);
            }
        };
        fetchMessages();
    }, [roomId]);

    const handleSend = async () => {
        const data = await sendMessage(roomId, newMessage);
        if (data.status === 'success') {
            setMessages([...messages, data.message]);
            setNewMessage('');
        }
    };

    return (
        <div>
            <div className="messages">
                {messages.map(msg => (
                    <div key={msg.message_id}>
                        <strong>{msg.username}:</strong> {msg.content}
                    </div>
                ))}
            </div>
            <input 
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                placeholder="Type @username to mention"
            />
            <button onClick={handleSend}>Send</button>
        </div>
    );
}
```

### 4. Professor Actions (Command Pattern)

```javascript
import { approveProject, rejectProject, gradeProject } from '../services/projectService';

function ProjectReview({ projectId }) {
    const handleApprove = async () => {
        const data = await approveProject(projectId, 95, "Excellent work!");
        if (data.status === 'success') {
            alert('Project approved! Student will be notified.');
        }
    };

    const handleReject = async () => {
        const data = await rejectProject(projectId, "Needs more work");
        if (data.status === 'success') {
            alert('Project rejected. Student will be notified.');
        }
    };

    const handleGrade = async (grade) => {
        const data = await gradeProject(projectId, grade, "Good effort");
        if (data.status === 'success') {
            alert('Project graded! Student will be notified.');
        }
    };

    return (
        <div>
            <button onClick={handleApprove}>Approve</button>
            <button onClick={handleReject}>Reject</button>
            <input type="number" onChange={(e) => handleGrade(e.target.value)} />
        </div>
    );
}
```

---

## 🧪 Testing the Integration

### 1. Test Notifications

1. Like a post → Check if notification appears
2. Comment on a post → Check if author gets notified
3. Click notification → Should mark as read
4. Click "Mark all as read" → All should be marked

### 2. Test Chat

1. Send a message → Should appear in chat
2. Send message with @mention → Mentioned user should get notification
3. Check message validation → Empty messages should be rejected

### 3. Test Profile

1. Visit profile page → Should load all data in one call (ProfileFacade)
2. Check network tab → Should see single `/api/profile/full` call

### 4. Test Project Commands

1. As professor, approve a project → Student should get notification
2. Grade a project → Student should get notification
3. Check database → `project_reviews` table should have new entry

---

## 🎯 Next Steps

1. **Add Notification Bell** to your navbar
2. **Update Profile Page** to use `getFullProfile()`
3. **Update Chat Room** to use `sendMessage()` and `getRoomMessages()`
4. **Add Professor Actions** for project review

All services are ready to use! Just import and call the functions.
