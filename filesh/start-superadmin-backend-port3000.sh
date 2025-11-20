#!/bin/bash

echo "🚀 Starting SuperAdmin Backend on Port 3000"
echo "==========================================="
echo ""

# 1. Find the superadmin-backend directory (where artisan file is located)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$SCRIPT_DIR"

# Try to find artisan file in current directory or parent directories
while [ ! -f "$BACKEND_DIR/artisan" ] && [ "$BACKEND_DIR" != "/" ]; do
    BACKEND_DIR="$(dirname "$BACKEND_DIR")"
done

if [ ! -f "$BACKEND_DIR/artisan" ]; then
    echo "❌ Error: artisan file not found. Please ensure you're in superadmin-backend directory"
    echo "   Current directory: $(pwd)"
    echo "   Script location: $SCRIPT_DIR"
    exit 1
fi

# Change to backend directory
cd "$BACKEND_DIR"
echo "📁 Working directory: $(pwd)"
echo ""

# 2. Check if port 3000 is already in use
if lsof -Pi :3000 -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "⚠️  Port 3000 is already in use!"
    echo "   Killing existing process..."
    lsof -ti:3000 | xargs kill -9 2>/dev/null
    sleep 2
fi

# 3. Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found. Creating from .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        php artisan key:generate
    else
        echo "❌ .env.example not found. Please create .env manually"
        exit 1
    fi
fi

# 4. Clear Laravel cache
echo "🧹 Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo ""

# 5. Check database connection
echo "🔍 Checking database connection..."
php artisan migrate:status > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "⚠️  Database connection issue detected. Please check your .env file"
    echo "   Continuing anyway..."
fi
echo ""

# 6. Start Laravel server on port 3000
echo "🚀 Starting Laravel server on port 3000..."
echo "   Server will be accessible at: http://103.23.198.101:3000"
echo "   API endpoints: http://103.23.198.101:3000/super-admin/api"
echo ""
echo "   Press Ctrl+C to stop the server"
echo ""

# Start server in background and save PID
php artisan serve --host=0.0.0.0 --port=3000 > /tmp/superadmin-backend-3000.log 2>&1 &
SERVER_PID=$!

# Wait a moment for server to start
sleep 3

# Check if server started successfully
if ps -p $SERVER_PID > /dev/null; then
    echo "✅ Server started successfully!"
    echo "   PID: $SERVER_PID"
    echo "   Log file: /tmp/superadmin-backend-3000.log"
    echo ""
    echo "📋 To view logs: tail -f /tmp/superadmin-backend-3000.log"
    echo "📋 To stop server: kill $SERVER_PID"
    echo ""
    
    # Save PID to file for easy management
    echo $SERVER_PID > /tmp/superadmin-backend-3000.pid
    echo "   PID saved to: /tmp/superadmin-backend-3000.pid"
    echo ""
    
    # Test if server is responding
    echo "🧪 Testing server..."
    sleep 2
    if curl -s http://localhost:3000 > /dev/null 2>&1; then
        echo "✅ Server is responding!"
    else
        echo "⚠️  Server started but not responding yet. Please check logs."
    fi
else
    echo "❌ Failed to start server. Check logs: /tmp/superadmin-backend-3000.log"
    exit 1
fi

echo ""
echo "==========================================="
echo "✅ SuperAdmin Backend is running on port 3000"
echo "🌐 Access at: http://103.23.198.101:3000"
echo ""

