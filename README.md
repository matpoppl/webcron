Learning design patterns from Zend Framework, Laminas, Symfony by implementing CRON with custom framework  

# Setup

## Manual

- Run in 'html/app' 
   - `composer intall`
   - `composer setup`
- Point webserver document root to 'html/'
- Manually import to sqlite database 'html/app/modules/sql/*.sql'

## Docker

- Run `docker compose -f docker-compose.yaml up`
