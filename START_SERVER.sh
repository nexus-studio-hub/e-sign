#!/bin/bash

echo "🚀 Starting E-Sign Application..."
echo ""

# Kill existing servers
pkill -9 -f "php artisan serve" 2>/dev/null
pkill -9 -f "vite" 2>/dev/null

cd "/home/jvchoo/project/nexusstudio project/e-sign"

# Start Laravel server
echo "📦 Starting Laravel server on http://127.0.0.1:8000..."
nohup php artisan serve --host=127.0.0.1 --port=8000 > /tmp/laravel-server.log 2>&1 &

# Start Vite dev server (optional, for hot reload)
echo "⚡ Starting Vite dev server..."
nohup npm run dev > /tmp/vite-dev.log 2>&1 &

sleep 3

echo ""
echo "✅ Servers started successfully!"
echo ""
echo "📍 Access the application at: http://127.0.0.1:8000"
echo ""
echo "👥 Demo Accounts:"
echo "   PIC Development: pic@esign.local / password"
echo "   Supplier: supplier@esign.local / password"
echo "   Group Leader: leader@esign.local / password"
echo "   Manager: manager@esign.local / password"
echo ""
echo "📝 Logs:"
echo "   Laravel: tail -f /tmp/laravel-server.log"
echo "   Vite: tail -f /tmp/vite-dev.log"
echo ""
