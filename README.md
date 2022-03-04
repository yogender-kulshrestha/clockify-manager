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
open this url in browser for getting all workspaces
``
{domain}/clockify/workspaces
``
###
#### Sync all users
open this url in browser for getting all users
``
{domain}/clockify/users
``
###
#### Sync weekly time entries
open this url in browser for getting last 5 week time entries, or you change week according to needed data 
``
{domain}/clockify/user/times?week=5
``
###
#### Sync weekly time entries
set a cron job run one time in a day for updating current week time entries
``
{domain}/clockify/user/times
``
