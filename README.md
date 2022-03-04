# Matthew Clockify

## Change DB config in .env

### Import Database
``
import sql from database/clockify_manager.sql file to database
``

### Project setup
```
composer install
```

### Run the project
```
php artisan serve
```

### Sync Clockify Data

#### Sync all workspaces
open clockify workspces url in browser 
``
{domain}/clockify/workspaces
``
###
#### Sync all users
open clockify users url in browser
``
{domain}/clockify/users
``
###
#### Sync weekly time entries
open clockify time entries url in browser or set a cron job for one time in a day
``
{domain}/clockify/user/times
``
