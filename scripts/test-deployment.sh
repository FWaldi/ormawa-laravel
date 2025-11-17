#!/bin/bash

# Deployment Testing Script
# Simulates cPanel environment and tests deployment readiness

echo "=== Ormawa UNP Deployment Testing ==="
echo "Testing deployment readiness for cPanel..."
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
TESTS_PASSED=0
TESTS_FAILED=0

# Function to run test
run_test() {
    local test_name="$1"
    local test_command="$2"
    
    echo -n "Testing $test_name... "
    
    if eval "$test_command" > /dev/null 2>&1; then
        echo -e "${GREEN}PASS${NC}"
        ((TESTS_PASSED++))
        return 0
    else
        echo -e "${RED}FAIL${NC}"
        ((TESTS_FAILED++))
        return 1
    fi
}

# Function to check file exists
file_exists() {
    [ -f "$1" ]
}

# Function to check directory exists
dir_exists() {
    [ -d "$1" ]
}

# Function to check file permissions
check_permissions() {
    local file="$1"
    local expected="$2"
    
    if [ -e "$file" ]; then
        local actual=$(stat -c "%a" "$file" 2>/dev/null || stat -f "%A" "$file" 2>/dev/null)
        [ "$actual" = "$expected" ]
    else
        return 1
    fi
}

echo "=== Essential Files Test ==="
run_test "Composer JSON" "file_exists 'composer.json'"
run_test "Artisan File" "file_exists 'artisan'"
run_test "Environment Example" "file_exists '.env.example'"
run_test "Vite Config" "file_exists 'vite.config.js'"
run_test "Package JSON" "file_exists 'package.json'"

echo ""
echo "=== Directory Structure Test ==="
run_test "App Directory" "dir_exists 'app'"
run_test "Config Directory" "dir_exists 'config'"
run_test "Database Directory" "dir_exists 'database'"
run_test "Public Directory" "dir_exists 'public'"
run_test "Resources Directory" "dir_exists 'resources'"
run_test "Routes Directory" "dir_exists 'routes'"
run_test "Storage Directory" "dir_exists 'storage'"
run_test "Bootstrap Directory" "dir_exists 'bootstrap'"

echo ""
echo "=== Laravel Configuration Test ==="
run_test "App Config" "file_exists 'config/app.php'"
run_test "Database Config" "file_exists 'config/database.php'"
run_test "Cache Config" "file_exists 'config/cache.php'"
run_test "Session Config" "file_exists 'config/session.php'"
run_test "Mail Config" "file_exists 'config/mail.php'"

echo ""
echo "=== Migration Files Test ==="
run_test "Users Migration" "file_exists 'database/migrations/0001_01_01_000000_create_users_table.php'"
run_test "Organizations Migration" "file_exists 'database/migrations/2025_11_13_000001_create_organizations_table.php'"
run_test "Activities Migration" "file_exists 'database/migrations/2025_11_13_000002_create_activities_table.php'"
run_test "Announcements Migration" "file_exists 'database/migrations/2025_11_13_000003_create_announcements_table.php'"
run_test "News Migration" "file_exists 'database/migrations/2025_11_13_000004_create_news_table.php'"

echo ""
echo "=== Database Migration Scripts Test ==="
run_test "phpMyAdmin Migration SQL" "file_exists 'database/migrations/phpmyadmin_migration.sql'"
run_test "phpMyAdmin Seeding SQL" "file_exists 'database/migrations/phpmyadmin_seeding.sql'"

echo ""
echo "=== Documentation Test ==="
run_test "Deployment Guide" "file_exists 'docs/cpanel-deployment-guide.md'"
run_test "Environment Setup" "file_exists 'docs/environment-setup.md'"
run_test "Database Migration Guide" "file_exists 'docs/database-migration.md'"
run_test "Permissions Guide" "file_exists 'docs/cpanel-permissions.md'"
run_test "Deployment Checklist" "file_exists 'docs/deployment-checklist.md'"

echo ""
echo "=== Scripts Test ==="
run_test "Build Assets Script" "file_exists 'scripts/build-assets.sh'"
run_test "Set Permissions Script" "file_exists 'scripts/set-permissions.sh'"
run_test "Setup Environment Script" "file_exists 'scripts/setup-environment.sh'"
run_test "Create Deployment Package" "file_exists 'scripts/create-deployment-package.sh'"

echo ""
echo "=== Production Optimization Test ==="
run_test "Production Composer Config" "file_exists 'composer.production.json'"
run_test "Production Gitignore" "file_exists '.gitignore.production'"
run_test "Production PHP Config" "file_exists 'config/production.php'"

echo ""
echo "=== Security Configuration Test ==="
run_test "Cache Middleware" "file_exists 'app/Http/Middleware/OptimizeCache.php'"
run_test "Page Cache Middleware" "file_exists 'app/Http/Middleware/PageCache.php'"
run_test "Warm Cache Command" "file_exists 'app/Console/Commands/WarmCache.php'"

echo ""
echo "=== Environment Variables Test ==="
if [ -f ".env.example" ]; then
    echo "Checking .env.example for required variables..."
    
    required_vars=(
        "APP_NAME"
        "APP_ENV"
        "APP_KEY"
        "APP_DEBUG"
        "APP_URL"
        "DB_CONNECTION"
        "DB_HOST"
        "DB_DATABASE"
        "DB_USERNAME"
        "DB_PASSWORD"
        "CACHE_STORE"
        "SESSION_DRIVER"
        "LOG_LEVEL"
    )
    
    for var in "${required_vars[@]}"; do
        if grep -q "^$var=" .env.example; then
            echo -e "  $var: ${GREEN}FOUND${NC}"
            ((TESTS_PASSED++))
        else
            echo -e "  $var: ${RED}MISSING${NC}"
            ((TESTS_FAILED++))
        fi
    done
else
    echo -e ".env.example: ${RED}NOT FOUND${NC}"
    ((TESTS_FAILED++))
fi

echo ""
echo "=== PHP Version Compatibility Test ==="
if command -v php >/dev/null 2>&1; then
    php_version=$(php -r "echo PHP_VERSION;")
    required_version="8.2.0"
    
    if [ "$(printf '%s\n' "$required_version" "$php_version" | sort -V | head -n1)" = "$required_version" ]; then
        echo -e "PHP Version ($php_version): ${GREEN}COMPATIBLE${NC}"
        ((TESTS_PASSED++))
    else
        echo -e "PHP Version ($php_version): ${RED}INCOMPATIBLE (requires 8.2+){NC}"
        ((TESTS_FAILED++))
    fi
else
    echo -e "PHP: ${YELLOW}NOT INSTALLED (check on server){NC}"
fi

echo ""
echo "=== Composer Dependencies Test ==="
if [ -f "composer.json" ]; then
    if command -v composer >/dev/null 2>&1; then
        if composer check-platform-reqs >/dev/null 2>&1; then
            echo -e "Composer Dependencies: ${GREEN}VALID${NC}"
            ((TESTS_PASSED++))
        else
            echo -e "Composer Dependencies: ${YELLOW}WARNINGS (check manually){NC}"
            ((TESTS_PASSED++))
        fi
    else
        echo -e "Composer: ${YELLOW}NOT INSTALLED (check on server){NC}"
    fi
else
    echo -e "composer.json: ${RED}NOT FOUND${NC}"
    ((TESTS_FAILED++))
fi

echo ""
echo "=== File Size Test ==="
if [ -d "database/migrations" ]; then
    sql_size=$(du -sh database/migrations/*.sql 2>/dev/null | awk '{sum+=$1} END {print sum+0}')
    if [ "$sql_size" -gt 0 ]; then
        echo -e "SQL Migration Files: ${GREEN}PRESENT ($sql_size KB)${NC}"
        ((TESTS_PASSED++))
    else
        echo -e "SQL Migration Files: ${RED}EMPTY${NC}"
        ((TESTS_FAILED++))
    fi
fi

echo ""
echo "=== Test Results Summary ==="
echo -e "Tests Passed: ${GREEN}$TESTS_PASSED${NC}"
echo -e "Tests Failed: ${RED}$TESTS_FAILED${NC}"

TOTAL_TESTS=$((TESTS_PASSED + TESTS_FAILED))
if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "Overall Status: ${GREEN}READY FOR DEPLOYMENT${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Run: ./scripts/create-deployment-package.sh"
    echo "2. Upload the generated zip file to cPanel"
    echo "3. Follow docs/cpanel-deployment-guide.md"
    exit 0
else
    echo -e "Overall Status: ${RED}NOT READY FOR DEPLOYMENT${NC}"
    echo ""
    echo "Please fix the failed tests before deploying."
    exit 1
fi