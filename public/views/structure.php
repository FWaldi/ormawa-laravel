<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Structure - Ormawa UNP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .nav {
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .nav a {
            color: #333;
            text-decoration: none;
            margin: 0 15px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .nav a:hover {
            background-color: #667eea;
            color: white;
        }
        .tree {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            border-left: 4px solid #667eea;
        }
        .folder { color: #667eea; font-weight: bold; }
        .file { color: #333; }
        .php { color: #8b4513; }
        .blade { color: #ff6b35; }
        h1 { color: #333; }
        h2 { color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .description {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="/simple-index.php">🏠 Home</a>
            <a href="/simple-index.php/project-structure">📁 Project Structure</a>
            <a href="/simple-index.php/models">🗃️ Models</a>
        </div>
        
        <h1>📁 Laravel Project Structure</h1>
        
        <div class="description">
            <p>This is the complete Laravel 11.x project structure for Ormawa UNP (Student Organization Management System).</p>
        </div>
        
        <h2>📂 Directory Structure</h2>
        <div class="tree">
<span class="folder">new/</span><br>
├── <span class="folder">app/</span><br>
│   ├── <span class="folder">Models/</span><br>
│   │   ├── <span class="file php">Activity.php</span><br>
│   │   ├── <span class="file php">Announcement.php</span><br>
│   │   ├── <span class="file php">File.php</span><br>
│   │   ├── <span class="file php">News.php</span><br>
│   │   ├── <span class="file php">Organization.php</span><br>
│   │   └── <span class="file php">User.php</span><br>
│   ├── <span class="folder">Http/</span><br>
│   │   ├── <span class="folder">Controllers/</span><br>
│   │   │   ├── <span class="file php">AuthController.php</span><br>
│   │   │   ├── <span class="file php">ActivityController.php</span><br>
│   │   │   ├── <span class="file php">AnnouncementController.php</span><br>
│   │   │   ├── <span class="file php">CalendarController.php</span><br>
│   │   │   ├── <span class="file php">NewsController.php</span><br>
│   │   │   ├── <span class="file php">OrganizationController.php</span><br>
│   │   │   └── <span class="file php">UploadController.php</span><br>
│   │   └── <span class="folder">Middleware/</span><br>
│   ├── <span class="folder">Services/</span><br>
│   │   ├── <span class="file php">FileUploadService.php</span><br>
│   │   ├── <span class="file php">HtmlSanitizer.php</span><br>
│   │   └── <span class="file php">StorageService.php</span><br>
│   └── <span class="folder">Providers/</span><br>
├── <span class="folder">bootstrap/</span><br>
├── <span class="folder">config/</span><br>
│   ├── <span class="file php">app.php</span><br>
│   ├── <span class="file php">database.php</span><br>
│   ├── <span class="file php">filesystems.php</span><br>
│   └── <span class="file php">services.php</span><br>
├── <span class="folder">database/</span><br>
│   ├── <span class="folder">seeders/</span><br>
│   │   ├── <span class="file php">ActivitySeeder.php</span><br>
│   │   ├── <span class="file php">AnnouncementSeeder.php</span><br>
│   │   ├── <span class="file php">DatabaseSeeder.php</span><br>
│   │   ├── <span class="file php">NewsSeeder.php</span><br>
│   │   ├── <span class="file php">OrganizationSeeder.php</span><br>
│   │   └── <span class="file php">UserSeeder.php</span><br>
│   └── <span class="folder">migrations/</span><br>
├── <span class="folder">public/</span><br>
│   ├── <span class="file php">index.php</span><br>
│   └── <span class="folder">assets/</span><br>
├── <span class="folder">resources/</span><br>
│   ├── <span class="folder">views/</span><br>
│   │   ├── <span class="folder">layouts/</span><br>
│   │   │   └── <span class="file blade">app.blade.php</span><br>
│   │   ├── <span class="folder">auth/</span><br>
│   │   │   ├── <span class="file blade">login.blade.php</span><br>
│   │   │   └── <span class="file blade">register.blade.php</span><br>
│   │   ├── <span class="folder">organizations/</span><br>
│   │   ├── <span class="folder">activities/</span><br>
│   │   ├── <span class="folder">announcements/</span><br>
│   │   ├── <span class="folder">news/</span><br>
│   │   └── <span class="folder">dashboard/</span><br>
│   └── <span class="folder">css/</span><br>
├── <span class="folder">routes/</span><br>
│   ├── <span class="file php">web.php</span><br>
│   └── <span class="file php">console.php</span><br>
├── <span class="folder">storage/</span><br>
│   └── <span class="folder">app/</span><br>
│       └── <span class="folder">uploads/</span><br>
├── <span class="folder">tests/</span><br>
│   ├── <span class="folder">Feature/</span><br>
│   ├── <span class="folder">Unit/</span><br>
│   └── <span class="folder">Browser/</span><br>
├── <span class="file php">artisan</span><br>
├── <span class="file php">composer.json</span><br>
└── <span class="file">.env</span><br>
        </div>
        
        <h2>📋 Key Components</h2>
        
        <div class="description">
            <h3>🗃️ Models</h3>
            <p>Eloquent models for database interaction with proper relationships and validation.</p>
        </div>
        
        <div class="description">
            <h3>🎮 Controllers</h3>
            <p>HTTP request handlers implementing CRUD operations for each entity.</p>
        </div>
        
        <div class="description">
            <h3>🎨 Views</h3>
            <p>Blade templates with responsive design using Tailwind CSS.</p>
        </div>
        
        <div class="description">
            <h3>🛡️ Services</h3>
            <p>Business logic layer for file uploads, HTML sanitization, and storage management.</p>
        </div>
        
        <div class="description">
            <h3>🧪 Tests</h3>
            <p>Comprehensive test suite including Unit, Feature, and Browser tests.</p>
        </div>
        
        <p><a href="/simple-index.php" class="btn">← Back to Home</a></p>
    </div>
</body>
</html>