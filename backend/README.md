# Uni-Link Backend - OOP Architecture

## 🎯 Overview

This backend has been refactored to use **Object-Oriented Programming** with **Strategy** and **Mediator** design patterns while maintaining **100% backward compatibility** with the existing React frontend.

## 🏗️ Architecture

### Dual-Mode System

The backend now supports **two routing systems**:

1. **New OOP Routes** - Modern architecture with dependency injection
2. **Legacy Routes** - Existing procedural code for backward compatibility

### Request Flow

```
React Frontend (localhost:5173)
    ↓
Backend (localhost/backend/index.php)
    ↓
Try OOP Routes First
    ↓
Fall Back to Legacy Routes
    ↓
Return JSON Response
```

## 📁 Directory Structure

```
backend/
├── index.php                 # Main entry point (supports both systems)
├── .htaccess                 # URL rewriting
├── .env                      # Database credentials
│
├── app/                      # New OOP Architecture
│   ├── Controllers/          # HTTP request handlers
│   ├── Services/             # Business logic
│   ├── Repositories/         # Data access layer
│   ├── Models/               # Entity models (User, Admin, Student, Professor)
│   ├── Strategies/           # Strategy pattern implementations
│   │   ├── RoleAccess/       # Role-based access control
│   │   └── PostInteraction/  # Post interactions (Like, Love, Save, etc.)
│   ├── Mediators/            # Mediator pattern implementations
│   │   ├── NotificationMediator.php
│   │   └── ProjectRoomMediator.php
│   ├── Middlewares/          # Request middlewares
│   ├── Interfaces/           # Contracts
│   └── Utils/                # Utilities (Database, ResponseHandler, etc.)
│
├── config/                   # Configuration files
│   ├── autoload.php          # PSR-4 autoloader
│   ├── services.php          # DI container configuration
│   ├── routes.php            # OOP routes
│   └── legacy.php            # Legacy controller loader
│
├── controllers/              # Legacy procedural controllers
├── routes/                   # Legacy route files
├── utils/                    # Legacy utilities
└── uploads/                  # File uploads
```

## 🚀 Getting Started

### 1. Database Configuration

Ensure `.env` file exists with:
```env
DB_HOST=localhost
DB_NAME=unilink
DB_USER=root
DB_PASS=
```

### 2. Start PHP Server

```bash
cd backend
php -S localhost:80
```

### 3. Start React Frontend

```bash
cd unilink
npm run dev
```

Frontend will run on `http://localhost:5173`

## 🔌 API Endpoints

All endpoints return JSON responses with this format:

```json
{
  "status": "success|error",
  "message": "...",
  "data": { ... }
}
```

### Authentication

- `POST /api/auth/login` - Login (OOP)
- `POST /api/auth/logout` - Logout (OOP)
- `GET /api/auth/me` - Get current user (OOP)

### Users (Legacy)

- `GET /api/user` - Get all users
- `POST /api/user` - Create user
- `PUT /api/user` - Update user
- `DELETE /api/user` - Delete user

### Posts (Legacy)

- `GET /api/posts` - Get all posts
- `POST /api/posts` - Create post
- `PUT /api/posts` - Update post
- `DELETE /api/posts` - Delete post

### Post Interactions (Legacy)

- `POST /api/post-interactions` - Add interaction
- `DELETE /api/post-interactions` - Remove interaction

### Projects (Legacy)

- `GET /api/projects` - Get all projects
- `POST /api/projects/upload` - Upload project
- `POST /api/projects/grade` - Grade project

### And many more... (see `config/routes.php`)

## 🎨 Design Patterns Implemented

### 1. Strategy Pattern

**Role-Based Access Control:**
- `AdminAccessStrategy` - Full access
- `ProfessorAccessStrategy` - Can grade, manage own content
- `StudentAccessStrategy` - Can manage own content

**Post Interactions:**
- `LikeStrategy`
- `LoveStrategy`
- `SaveStrategy`
- `ShareStrategy`
- `CelebrationStrategy`

**Usage:**
```php
$context = new InteractionContext();
$strategy = InteractionContext::createStrategy('like');
$context->setStrategy($strategy);
$result = $context->executeInteraction($postId, $userId);
```

### 2. Mediator Pattern

**NotificationMediator** - Coordinates notifications across:
- Post comments
- User mentions
- Project reviews
- Room activities

**ProjectRoomMediator** - Coordinates room communication:
- Chat messages
- Member additions
- Room updates

### 3. Repository Pattern

Abstracts database operations:
```php
$userRepo = new UserRepository();
$user = $userRepo->find($id);
$user = $userRepo->findByEmail($email);
```

### 4. Dependency Injection

Controllers receive dependencies via constructor:
```php
class AuthController {
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }
}
```

## 🔄 Migration Strategy

The backend supports **gradual migration**:

1. ✅ **Phase 1**: Core architecture (DONE)
2. ✅ **Phase 2**: User system with OOP (DONE)
3. ⏳ **Phase 3**: Migrate other controllers one by one
4. ⏳ **Phase 4**: Remove legacy code when all migrated

**Current Status**: Both systems work simultaneously!

## 🧪 Testing

### Test Authentication (OOP)

```bash
# Login
curl -X POST http://localhost/backend/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"identifier":"test@example.com","password":"password"}'

# Get current user
curl http://localhost/backend/api/auth/me \
  --cookie-jar cookies.txt
```

### Test Legacy Routes

```bash
# Get all posts
curl http://localhost/backend/api/posts

# Get all users
curl http://localhost/backend/api/user
```

## 📚 Documentation

- **Implementation Plan**: `brain/implementation_plan.md`
- **Walkthrough**: `brain/walkthrough.md`
- **Usage Examples**: `EXAMPLES.php`

## 🔧 Troubleshooting

### CORS Errors

Ensure React runs on `http://localhost:5173` (configured in `index.php`)

### Database Connection

Check `.env` file and ensure MySQL is running

### Route Not Found

Check `config/routes.php` for OOP routes or legacy route files in `routes/`

### Session Issues

Ensure `session_start()` is called and cookies are enabled

## 🎓 Key Benefits

✅ **Backward Compatible** - Existing frontend works without changes
✅ **Modern Architecture** - OOP, SOLID principles, design patterns
✅ **Type Safe** - Type hints throughout
✅ **Testable** - Dependency injection enables mocking
✅ **Extensible** - Easy to add new features via strategies
✅ **Maintainable** - Clear separation of concerns

## 📝 Next Steps

1. Migrate remaining controllers to OOP
2. Add unit tests
3. Implement JWT authentication
4. Add API documentation (Swagger)
5. Remove legacy code when migration complete

## 👥 Contributors

Built with ❤️ using modern PHP best practices and design patterns.
