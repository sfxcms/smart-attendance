#!/bin/bash

set -e

echo ""
echo "╔═══════════════════════════════════════════════════════════════╗"
echo "║         Smart Attendance System - Auto Installer              ║"
echo "║         Sistem Absensi Hybrid (Online + Offline)             ║"
echo "╚═══════════════════════════════════════════════════════════════╝"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

check_command() {
    if command -v "$1" &> /dev/null; then
        echo -e "${GREEN}✅${NC} $2 found"
        return 0
    else
        echo -e "${RED}❌${NC} $2 not found"
        return 1
    fi
}

echo "🔍 Checking requirements..."
ERRORS=0

check_command php "PHP" || ERRORS=$((ERRORS + 1))
check_command composer "Composer" || ERRORS=$((ERRORS + 1))
check_command node "Node.js" || ERRORS=$((ERRORS + 1))
check_command npm "npm" || ERRORS=$((ERRORS + 1))
check_command git "Git" || ERRORS=$((ERRORS + 1))
check_command psql "PostgreSQL" || ERRORS=$((ERRORS + 1))

if [ $ERRORS -gt 0 ]; then
    echo ""
    echo -e "${RED}❌ Please install missing requirements and try again.${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ All requirements satisfied!${NC}"
echo ""

# Clone if not already in directory
if [ ! -f "artisan" ]; then
    echo "📥 Cloning repository..."
    git clone https://github.com/sfxcms/smart-attendance.git
    cd smart-attendance
fi

# Run PHP installer
echo ""
echo "🚀 Running installer..."
php install.php
