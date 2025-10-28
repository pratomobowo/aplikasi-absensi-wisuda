#!/bin/bash

# Fix File Permissions Script
# Run this if you encounter permission issues

echo "🔐 Fixing file permissions..."
echo ""

# Get current user
CURRENT_USER=$(whoami)
echo "Current user: $CURRENT_USER"
echo ""

# Fix node_modules ownership and permissions
if [ -d "node_modules" ]; then
    echo "Step 1: Fixing node_modules..."
    
    # Check if we need sudo
    if [ "$CURRENT_USER" != "root" ]; then
        echo "Fixing ownership (requires sudo)..."
        sudo chown -R $CURRENT_USER:$CURRENT_USER node_modules
    fi
    
    # Fix permissions
    chmod -R 755 node_modules/.bin 2>/dev/null || true
    echo "✓ node_modules fixed"
else
    echo "⚠️  node_modules not found"
fi
echo ""

# Fix storage permissions
if [ -d "storage" ]; then
    echo "Step 2: Fixing storage..."
    
    # Get web server user (www-data, www, nginx, etc.)
    WEB_USER="www"
    if id "www-data" &>/dev/null; then
        WEB_USER="www-data"
    elif id "nginx" &>/dev/null; then
        WEB_USER="nginx"
    fi
    
    echo "Web server user: $WEB_USER"
    
    if [ "$CURRENT_USER" != "root" ]; then
        sudo chown -R $WEB_USER:$WEB_USER storage
        sudo chmod -R 755 storage
    else
        chown -R $WEB_USER:$WEB_USER storage
        chmod -R 755 storage
    fi
    
    echo "✓ storage fixed"
else
    echo "⚠️  storage not found"
fi
echo ""

# Fix bootstrap/cache permissions
if [ -d "bootstrap/cache" ]; then
    echo "Step 3: Fixing bootstrap/cache..."
    
    WEB_USER="www"
    if id "www-data" &>/dev/null; then
        WEB_USER="www-data"
    elif id "nginx" &>/dev/null; then
        WEB_USER="nginx"
    fi
    
    if [ "$CURRENT_USER" != "root" ]; then
        sudo chown -R $WEB_USER:$WEB_USER bootstrap/cache
        sudo chmod -R 755 bootstrap/cache
    else
        chown -R $WEB_USER:$WEB_USER bootstrap/cache
        chmod -R 755 bootstrap/cache
    fi
    
    echo "✓ bootstrap/cache fixed"
else
    echo "⚠️  bootstrap/cache not found"
fi
echo ""

# Fix vendor permissions (if needed)
if [ -d "vendor" ]; then
    echo "Step 4: Fixing vendor..."
    chmod -R 755 vendor/bin 2>/dev/null || true
    echo "✓ vendor fixed"
fi
echo ""

echo "✅ Permissions fixed!"
echo ""
echo "Now you can run:"
echo "  npm run build"
echo "  php artisan optimize"
echo ""
