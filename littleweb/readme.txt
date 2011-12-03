# South Migrations

Step 1
- For a new app
python manage.py schemamigration appname --initial
- For an existing app
python manage.py schemamigration appname --auto

Step 2
python manage.py migrate southtut