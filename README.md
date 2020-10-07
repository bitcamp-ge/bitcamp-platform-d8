# Drupal instance for BitCamp

## Installation
1. `make up`
1. `docker-compose exec php composer install`
1. `docker-compose exec php drush si --existing-config --account-name="oto"
   --account-pass="1234" --account-mail="admin@bitcamp.ge" -y`
