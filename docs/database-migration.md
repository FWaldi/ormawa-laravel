# Database Migration Guide for phpMyAdmin

## Overview
This guide provides step-by-step instructions for migrating the Ormawa UNP database using phpMyAdmin in cPanel.

## Prerequisites
- cPanel access
- phpMyAdmin access
- Database created in cPanel
- SQL migration files from this project

## Step 1: Create Database in cPanel

1. Log into cPanel
2. Navigate to **MySQL Databases**
3. Create a new database:
   - Database name: `ormawa_unp`
   - Click **Create Database**

4. Create a database user:
   - Username: `ormawa_user` (or your preferred name)
   - Password: Generate a strong password
   - Click **Create User**

5. Add user to database:
   - Select the user and database
   - Check **ALL PRIVILEGES**
   - Click **Add**

## Step 2: Import Database Structure

1. Navigate to **phpMyAdmin** in cPanel
2. Select the `ormawa_unp` database
3. Click **Import** tab
4. Choose the file: `database/migrations/phpmyadmin_migration.sql`
5. Leave default settings
6. Click **Go**

## Step 3: Import Initial Data (Optional)

1. With the same database selected
2. Click **Import** tab
3. Choose the file: `database/migrations/phpmyadmin_seeding.sql`
4. Leave default settings
5. Click **Go**

## Step 4: Verify Installation

Run these SQL queries to verify:

```sql
-- Check tables
SHOW TABLES;

-- Verify users table
SELECT COUNT(*) as user_count FROM users;

-- Verify organizations
SELECT COUNT(*) as org_count FROM organizations;

-- Verify activities
SELECT COUNT(*) as activity_count FROM activities;
```

## Default Login Credentials

After seeding, you can login with:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@ormawa-unp.com | admin123 |
| BEM | bem@ormawa-unp.com | admin123 |
| HIMTIF | himtif@ormawa-unp.com | admin123 |
| UKM Olahraga | ukm-olahraga@ormawa-unp.com | admin123 |

**Important:** Change these passwords after first login!

## Migration File Details

### phpmyadmin_migration.sql
- Creates all required tables
- Sets up foreign key constraints
- Configures indexes for performance
- Includes Laravel system tables (cache, sessions, jobs)

### phpmyadmin_seeding.sql
- Inserts default admin user
- Creates sample organizations
- Adds sample activities and announcements
- Includes sample news and files

## Troubleshooting

### Common Issues

1. **Import Timeout**
   - If import times out, split the SQL file into smaller parts
   - Increase PHP memory limit in cPanel if possible

2. **Foreign Key Errors**
   - Ensure tables are created in the correct order
   - Check that all referenced tables exist

3. **Character Encoding Issues**
   - Ensure database uses utf8mb4 charset
   - Check SQL file encoding (should be UTF-8)

4. **Permission Errors**
   - Verify database user has all privileges
   - Check that user is assigned to the correct database

### Error Messages and Solutions

**Error:** `#1044 - Access denied for user`
**Solution:** Check database user permissions in cPanel

**Error:** `#1005 - Can't create table`
**Solution:** Ensure storage engine is InnoDB and charset is utf8mb4

**Error:** `#1215 - Cannot add foreign key constraint`
**Solution:** Verify referenced tables and columns exist

## Post-Migration Steps

1. Update `.env` file with database credentials
2. Test database connection from Laravel
3. Verify all application features work
4. Change default passwords
5. Set up regular database backups

## Backup Strategy

1. **Regular Backups**: Set up automatic daily backups in cPanel
2. **Before Updates**: Always backup before making changes
3. **Off-site Storage**: Download backups periodically

## Performance Optimization

After migration, consider these optimizations:

```sql
-- Add indexes for frequently queried columns
CREATE INDEX idx_activities_status_date ON activities(status, start_date);
CREATE INDEX idx_announcements_org ON announcements(organization_id);
CREATE INDEX idx_news_created_by ON news(created_by);

-- Optimize tables
OPTIMIZE TABLE users, organizations, activities, announcements, news, files;
```