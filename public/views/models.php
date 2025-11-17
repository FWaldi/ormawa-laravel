<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Models - Ormawa UNP</title>
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
        .model-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .model-name {
            color: #667eea;
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .model-description {
            color: #666;
            margin-bottom: 15px;
        }
        .model-fields {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .field {
            margin: 5px 0;
        }
        .field-type {
            color: #28a745;
            font-weight: bold;
        }
        .field-name {
            color: #333;
        }
        h1 { color: #333; }
        h2 { color: #667eea; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
        }
        .btn:hover {
            background-color: #5a6fd8;
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
        
        <h1>🗃️ Laravel Models</h1>
        
        <div class="model-card">
            <div class="model-name">User</div>
            <div class="model-description">Manages user authentication and profile information for students, administrators, and organization members.</div>
            <div class="model-fields">
                <div class="field"><span class="field-type">string</span> <span class="field-name">name</span> - User full name</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">email</span> - Unique email address</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">password</span> - Encrypted password</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">role</span> - User role (admin, student, organization)</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">nim</span> - Student ID number</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">phone</span> - Phone number</div>
                <div class="field"><span class="field-type">text</span> <span class="field-name">address</span> - User address</div>
            </div>
        </div>
        
        <div class="model-card">
            <div class="model-name">Organization</div>
            <div class="model-description">Represents student organizations with their details, leadership, and membership information.</div>
            <div class="model-fields">
                <div class="field"><span class="field-type">string</span> <span class="field-name">name</span> - Organization name</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">abbreviation</span> - Short name/acronym</div>
                <div class="field"><span class="field-type">text</span> <span class="field-name">description</span> - Organization description</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">category</span> - Type of organization</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">faculty</span> - Associated faculty</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">advisor</span> - Faculty advisor name</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">email</span> - Organization email</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">phone</span> - Contact phone</div>
            </div>
        </div>
        
        <div class="model-card">
            <div class="model-name">Activity</div>
            <div class="model-description">Tracks organization activities, events, and programs with scheduling and participation details.</div>
            <div class="model-fields">
                <div class="field"><span class="field-type">string</span> <span class="field-name">title</span> - Activity title</div>
                <div class="field"><span class="field-type">text</span> <span class="field-name">description</span> - Activity description</div>
                <div class="field"><span class="field-type">date</span> <span class="field-name">start_date</span> - Activity start date</div>
                <div class="field"><span class="field-type">date</span> <span class="field-name">end_date</span> - Activity end date</div>
                <div class="field"><span class="field-type">time</span> <span class="field-name">start_time</span> - Start time</div>
                <div class="field"><span class="field-type">time</span> <span class="field-name">end_time</span> - End time</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">location</span> - Activity location</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">type</span> - Activity type</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">status</span> - Activity status</div>
            </div>
        </div>
        
        <div class="model-card">
            <div class="model-name">Announcement</div>
            <div class="model-description">Manages official announcements and notifications for organizations and the system.</div>
            <div class="model-fields">
                <div class="field"><span class="field-type">string</span> <span class="field-name">title</span> - Announcement title</div>
                <div class="field"><span class="field-type">text</span> <span class="field-name">content</span> - Announcement content</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">type</span> - Announcement type</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">priority</span> - Priority level</div>
                <div class="field"><span class="field-type">datetime</span> <span class="field-name">published_at</span> - Publication date</div>
                <div class="field"><span class="field-type">datetime</span> <span class="field-name">expires_at</span> - Expiration date</div>
            </div>
        </div>
        
        <div class="model-card">
            <div class="model-name">News</div>
            <div class="model-description">Handles news articles and blog posts for organizations and general announcements.</div>
            <div class="model-fields">
                <div class="field"><span class="field-type">string</span> <span class="field-name">title</span> - News title</div>
                <div class="field"><span class="field-type">text</span> <span class="field-name">content</span> - News content</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">slug</span> - URL-friendly slug</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">category</span> - News category</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">author</span> - News author</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">image</span> - Featured image</div>
                <div class="field"><span class="field-type">boolean</span> <span class="field-name">published</span> - Publication status</div>
            </div>
        </div>
        
        <div class="model-card">
            <div class="model-name">File</div>
            <div class="model-description">Manages file uploads and attachments for activities, announcements, and documents.</div>
            <div class="model-fields">
                <div class="field"><span class="field-type">string</span> <span class="field-name">filename</span> - Original filename</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">path</span> - File storage path</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">mime_type</span> - File MIME type</div>
                <div class="field"><span class="field-type">integer</span> <span class="field-name">size</span> - File size in bytes</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">category</span> - File category</div>
                <div class="field"><span class="field-type">string</span> <span class="field-name">uploaded_by</span> - Uploader ID</div>
            </div>
        </div>
        
        <h2>🔗 Model Relationships</h2>
        <div class="model-card">
            <div class="model-description">
                <strong>Key Relationships:</strong><br>
                • User hasMany Organization (as leader)<br>
                • User belongsTo Organization (as member)<br>
                • Organization hasMany Activity<br>
                • Organization hasMany Announcement<br>
                • Organization hasMany News<br>
                • Activity hasMany File<br>
                • Announcement hasMany File<br>
                • News hasMany File
            </div>
        </div>
        
        <p><a href="/simple-index.php" class="btn">← Back to Home</a></p>
    </div>
</body>
</html>