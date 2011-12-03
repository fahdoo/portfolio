DEBUG = True
TEMPLATE_DEBUG = DEBUG

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql_psycopg2', # Add 'postgresql_psycopg2', 'postgresql', 'mysql', 'sqlite3' or 'oracle'.
        'NAME': 'r_test',                      # Or path to database file if using sqlite3.
        'USER': 'radmin',                      # Not used with sqlite3.
        'PASSWORD': '',                  # Not used with sqlite3.
        'HOST': '',                      # Set to empty string for localhost. Not used with sqlite3.
        'PORT': '',                      # Set to empty string for default. Not used with sqlite3.
    }
}


STATIC_ROOT = ''
STATIC_URL = '/static/'
STATICFILES_STORAGE = ''


FACEBOOK_APP_ID = '163201843772430'
FACEBOOK_APP_SECRET = 'bf40f845bb4e32b322db6db918fa47f7'

EMAIL_BACKEND = 'django.core.mail.backends.dummy.EmailBackend'

# DEBUG TOOLBAR
DEBUG_TOOLBAR_CONFIG = { 'INTERCEPT_REDIRECTS': False, }
INTERNAL_IPS = ('0.0.0.0:5000','127.0.0.1',)