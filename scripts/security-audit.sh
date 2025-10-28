#!/bin/bash

# Security Audit Script for Sistem Absensi Wisuda Digital
# This script checks for common security misconfigurations

echo "=========================================="
echo "Security Audit - Sistem Absensi Wisuda"
echo "=========================================="
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check counter
PASSED=0
FAILED=0
WARNINGS=0

# Function to print results
print_pass() {
    echo -e "${GREEN}✓${NC} $1"
    ((PASSED++))
}

print_fail() {
    echo -e "${RED}✗${NC} $1"
    ((FAILED++))
}

print_warn() {
    echo -e "${YELLOW}⚠${NC} $1"
    ((WARNINGS++))
}

echo "1. Checking Environment Configuration..."
echo "----------------------------------------"

# Check if .env exists
if [ -f .env ]; then
    print_pass ".env file exists"
    
    # Check APP_KEY
    if grep -q "APP_KEY=base64:" .env; then
        print_pass "APP_KEY is configured"
    else
        print_fail "APP_KEY is not configured or invalid"
    fi
    
    # Check QR_ENCRYPTION_KEY
    if grep -q "QR_ENCRYPTION_KEY=base64:" .env; then
        print_pass "QR_ENCRYPTION_KEY is configured"
    else
        print_fail "QR_ENCRYPTION_KEY is not configured"
    fi
    
    # Check APP_DEBUG
    if grep -q "APP_DEBUG=false" .env; then
        print_pass "APP_DEBUG is disabled (production ready)"
    else
        print_warn "APP_DEBUG is enabled (should be false in production)"
    fi
    
    # Check SESSION_ENCRYPT
    if grep -q "SESSION_ENCRYPT=true" .env; then
        print_pass "Session encryption is enabled"
    else
        print_fail "Session encryption is disabled"
    fi
    
    # Check SESSION_SECURE_COOKIE
    if grep -q "SESSION_SECURE_COOKIE=true" .env; then
        print_pass "Secure cookies are enabled"
    else
        print_warn "Secure cookies are disabled (required for HTTPS)"
    fi
    
    # Check SESSION_HTTP_ONLY
    if grep -q "SESSION_HTTP_ONLY=true" .env; then
        print_pass "HTTP-only cookies are enabled"
    else
        print_fail "HTTP-only cookies are disabled"
    fi
    
else
    print_fail ".env file not found"
fi

echo ""
echo "2. Checking File Permissions..."
echo "----------------------------------------"

# Check .env permissions
if [ -f .env ]; then
    PERMS=$(stat -f "%A" .env 2>/dev/null || stat -c "%a" .env 2>/dev/null)
    if [ "$PERMS" = "600" ] || [ "$PERMS" = "644" ]; then
        print_pass ".env file permissions are secure ($PERMS)"
    else
        print_warn ".env file permissions could be more restrictive (current: $PERMS, recommended: 600)"
    fi
fi

echo ""
echo "3. Checking Code Security..."
echo "----------------------------------------"

# Check for raw SQL queries
RAW_SQL=$(grep -r "DB::raw\|DB::select\|DB::statement" app/ --include="*.php" | wc -l)
if [ "$RAW_SQL" -eq 0 ]; then
    print_pass "No raw SQL queries found (using Eloquent ORM)"
else
    print_fail "Found $RAW_SQL potential raw SQL queries"
fi

# Check for eval() usage
EVAL_USAGE=$(grep -r "eval(" app/ --include="*.php" | wc -l)
if [ "$EVAL_USAGE" -eq 0 ]; then
    print_pass "No eval() usage found"
else
    print_fail "Found $EVAL_USAGE eval() usage (security risk)"
fi

# Check for unserialize() usage
UNSERIALIZE=$(grep -r "unserialize(" app/ --include="*.php" | wc -l)
if [ "$UNSERIALIZE" -eq 0 ]; then
    print_pass "No unserialize() usage found"
else
    print_warn "Found $UNSERIALIZE unserialize() usage (potential security risk)"
fi

echo ""
echo "4. Checking Dependencies..."
echo "----------------------------------------"

# Check if composer.lock exists
if [ -f composer.lock ]; then
    print_pass "composer.lock exists (dependencies locked)"
else
    print_warn "composer.lock not found (dependencies not locked)"
fi

echo ""
echo "5. Checking Security Files..."
echo "----------------------------------------"

# Check for security documentation
if [ -f SECURITY.md ]; then
    print_pass "Security documentation exists"
else
    print_warn "Security documentation not found"
fi

# Check for security config
if [ -f config/security.php ]; then
    print_pass "Security configuration file exists"
else
    print_warn "Security configuration file not found"
fi

# Check for SecurityHeaders middleware
if [ -f app/Http/Middleware/SecurityHeaders.php ]; then
    print_pass "SecurityHeaders middleware exists"
else
    print_fail "SecurityHeaders middleware not found"
fi

echo ""
echo "6. Checking Rate Limiting..."
echo "----------------------------------------"

# Check if rate limiting is configured
if grep -q "RateLimiter::for" app/Providers/AppServiceProvider.php; then
    print_pass "Rate limiting is configured"
else
    print_warn "Rate limiting configuration not found"
fi

echo ""
echo "=========================================="
echo "Audit Summary"
echo "=========================================="
echo -e "${GREEN}Passed:${NC} $PASSED"
echo -e "${YELLOW}Warnings:${NC} $WARNINGS"
echo -e "${RED}Failed:${NC} $FAILED"
echo ""

if [ "$FAILED" -eq 0 ]; then
    echo -e "${GREEN}Security audit completed successfully!${NC}"
    exit 0
else
    echo -e "${RED}Security audit found issues that need attention.${NC}"
    exit 1
fi
