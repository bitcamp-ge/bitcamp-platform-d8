# Drupal instance for BitCamp

## Installation with development config
1. Make sure `settings.local.php` is enabled in `settings.php`.
1. `cp environments/development/.env ./`
1. `make up`
1. `docker-compose exec php composer install`
1. `cp environments/development/settings.local.php ./web/sites/default/`
1. `cp environments/development/development.services.yml ./web/sites/`
1. `docker-compose exec php drush si --existing-config --account-name="admin"
   --account-pass="1234" --account-mail="admin@bitcamp.ge" -y`
1. `docker-compose exec php drupal site:mode dev`

