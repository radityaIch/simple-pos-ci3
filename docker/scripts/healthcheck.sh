#!/bin/bash

# Health check script for Simple POS CI3 Docker container

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "🔍 Simple POS CI3 Health Check"
echo "=============================="

# Check if Apache is running
echo -n "Apache Service: "
if pgrep apache2 > /dev/null; then
    echo -e "${GREEN}✓ Running${NC}"
else
    echo -e "${RED}✗ Not Running${NC}"
    exit 1
fi

# Check if application is responding
echo -n "Web Application: "
if curl -s -o /dev/null -w "%{http_code}" http://localhost/ | grep -q "200"; then
    echo -e "${GREEN}✓ Responding${NC}"
else
    echo -e "${RED}✗ Not Responding${NC}"
    exit 1
fi

# Check database connection
echo -n "Database Connection: "
if php -r "
try {
    \$mysqli = new mysqli('$DB_HOST', '$DB_USERNAME', '$DB_PASSWORD', '$DB_DATABASE');
    if (\$mysqli->connect_error) {
        throw new Exception('Connection failed');
    }
    echo 'OK';
    \$mysqli->close();
} catch (Exception \$e) {
    echo 'FAILED';
    exit(1);
}
" | grep -q "OK"; then
    echo -e "${GREEN}✓ Connected${NC}"
else
    echo -e "${RED}✗ Connection Failed${NC}"
    exit 1
fi

# Check file permissions
echo -n "File Permissions: "
if [ -w "/var/www/html/application/cache" ] && [ -w "/var/www/html/application/logs" ]; then
    echo -e "${GREEN}✓ Correct${NC}"
else
    echo -e "${YELLOW}⚠ Warning: Cache/Logs not writable${NC}"
fi

# Check disk space
echo -n "Disk Space: "
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -lt 80 ]; then
    echo -e "${GREEN}✓ ${DISK_USAGE}% used${NC}"
elif [ "$DISK_USAGE" -lt 90 ]; then
    echo -e "${YELLOW}⚠ ${DISK_USAGE}% used${NC}"
else
    echo -e "${RED}✗ ${DISK_USAGE}% used (Critical)${NC}"
fi

echo ""
echo -e "${GREEN}✅ Health check completed successfully!${NC}"
exit 0