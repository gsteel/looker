# Minimum version of PHP supported by this project
PHP_VERSION ?= 8.4
PHP_EXTENSIONS ?= intl bcmath mbstring json xdebug
DOCKER_IMAGE_NAME = gsteel/looker

# Includes _everything_ supplied by the internal tooling lib
include vendor/laminas/internal-tooling/Makefile
