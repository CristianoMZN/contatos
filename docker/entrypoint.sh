#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting Contatos Application...${NC}"

# Set default values for environment variables if not set
export PHP_FPM_PM=${PHP_FPM_PM:-dynamic}
export PHP_FPM_MAX_CHILDREN=${PHP_FPM_MAX_CHILDREN:-50}
export PHP_FPM_START_SERVERS=${PHP_FPM_START_SERVERS:-5}
export PHP_FPM_MIN_SPARE_SERVERS=${PHP_FPM_MIN_SPARE_SERVERS:-5}
export PHP_FPM_MAX_SPARE_SERVERS=${PHP_FPM_MAX_SPARE_SERVERS:-35}
export PHP_FPM_MAX_REQUESTS=${PHP_FPM_MAX_REQUESTS:-1000}

echo -e "${YELLOW}PHP-FPM Configuration:${NC}"
echo "  PM Mode: $PHP_FPM_PM"
echo "  Max Children: $PHP_FPM_MAX_CHILDREN"
echo "  Start Servers: $PHP_FPM_START_SERVERS"
echo "  Min Spare: $PHP_FPM_MIN_SPARE_SERVERS"
echo "  Max Spare: $PHP_FPM_MAX_SPARE_SERVERS"
echo "  Max Requests: $PHP_FPM_MAX_REQUESTS"

# Generate PHP-FPM pool configuration from template
if [ -f "/etc/php-fpm.d/www.conf.template" ]; then
    echo -e "${GREEN}Generating PHP-FPM pool configuration...${NC}"
    envsubst < /etc/php-fpm.d/www.conf.template > /etc/php-fpm.d/www.conf
    echo -e "${GREEN}PHP-FPM pool configuration generated${NC}"
else
    echo -e "${RED}Warning: PHP-FPM pool template not found${NC}"
fi

# Clear cache if needed
if [ "$CLEAR_CACHE" = "true" ]; then
    echo -e "${YELLOW}Clearing cache...${NC}"
    rm -rf storage/cache/*
    echo -e "${GREEN}Cache cleared${NC}"
fi

echo -e "${GREEN}Starting services...${NC}"

# Execute the CMD
exec "$@"
