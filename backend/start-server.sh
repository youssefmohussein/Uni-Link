#!/bin/bash
# Start PHP Built-in Server for Uni-Link Backend
# This script starts the PHP development server on port 8000

echo "========================================"
echo "Starting Uni-Link Backend Server"
echo "========================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP not found"
    echo ""
    echo "Please install PHP or add it to your PATH"
    exit 1
fi

echo "Starting PHP server on http://localhost:8000"
echo "Backend will be accessible at: http://localhost:8000/"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start PHP built-in server
php -S localhost:8000 -t .
