# Django settings for fashionista project.
import os.path

DEBUG = True
TEMPLATE_DEBUG = DEBUG

DOMAIN = 'http://coco.fahdoo.webfactional.com/'
#FORCE_SCRIPT_NAME = '/coco'

ADMINS = (
    ('Fahdoo', 'fahd828+wf@gmail.com'),
)

MANAGERS = ADMINS

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql_psycopg2', # Add 'postgresql_psycopg2', 'postgresql', 'mysql', 'sqlite3' or 'oracle'.
        'NAME': 'fahdoo_f_dev',                      # Or path to database file if using sqlite3.
        'USER': ' ',                      # Not used with sqlite3.
        'PASSWORD': ' ',                  # Not used with sqlite3.
        'HOST': '',                      # Set to empty string for localhost. Not used with sqlite3.
        'PORT': '',                      # Set to empty string for default. Not used with sqlite3.
    }
}


# Local time zone for this installation. Choices can be found here:
# http://en.wikipedia.org/wiki/List_of_tz_zones_by_name
# although not all choices may be available on all operating systems.
# On Unix systems, a value of None will cause Django to use the same
# timezone as the operating system.
# If running in a Windows environment this must be set to the same as your
# system time zone.
TIME_ZONE = 'America/Chicago'

# Language code for this installation. All choices can be found here:
# http://www.i18nguy.com/unicode/language-identifiers.html
LANGUAGE_CODE = 'en-us'

SITE_ID = 1

# If you set this to False, Django will make some optimizations so as not
# to load the internationalization machinery.
USE_I18N = True

# If you set this to False, Django will not format dates, numbers and
# calendars according to the current locale
USE_L10N = True

# Absolute path to the directory that holds media.
# Example: "/home/media/media.lawrence.com/"
MEDIA_ROOT = 'fahdoo/webapps/static/media/'

# URL that handles the media served from MEDIA_ROOT. Make sure to use a
# trailing slash if there is a path component (optional in other cases).
# Examples: "http://media.lawrence.com", "http://example.com/media/"
MEDIA_URL = DOMAIN + 'static/media/'

# URL prefix for admin media -- CSS, JavaScript and images. Make sure to use a
# trailing slash.
# Examples: "http://foo.com/media/", "/media/".
ADMIN_MEDIA_PREFIX = DOMAIN + 'static/admin/'

# Make this unique, and don't share it with anybody.
SECRET_KEY = '@ye9f&70vja07fky!hmi%$*xbo^6obb*jtvsn^zvh(rv_bp7r2'

#AWS
ACCESS_KEY = ' '
PASS_KEY = ' /jzq4lKk9kBgGIGH4v'
BUCKET = 'fahdoo'
CLOUD_STORAGE_URL = 'http://'+BUCKET+'.s3.amazonaws.com/'

# List of callables that know how to import templates from various sources.
TEMPLATE_LOADERS = (
    'django.template.loaders.filesystem.Loader',
    'django.template.loaders.app_directories.Loader',
	'django.template.loaders.eggs.Loader',
)

TEMPLATE_CONTEXT_PROCESSORS = (
    "django.core.context_processors.auth",
    "django.core.context_processors.debug",
    "django.core.context_processors.i18n",
    "django.core.context_processors.media",
    "django.core.context_processors.request",
    "django.contrib.messages.context_processors.messages",
)

MIDDLEWARE_CLASSES = (
    'django.middleware.common.CommonMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'debug_toolbar.middleware.DebugToolbarMiddleware',
    'fashionista.socialregistration.middleware.FacebookMiddleware',
    'pagination.middleware.PaginationMiddleware',
)

ROOT_URLCONF = 'fashionista.urls'

TEMPLATE_DIRS = (
	os.path.join(os.path.dirname(__file__), 'templates'),
    #'/home/fahdoo/'
    # Don't forget to use absolute paths, not relative paths.
)

INSTALLED_APPS = (
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.sites',
    'django.contrib.messages',
    'django.contrib.admin',
    'django.contrib.admindocs',
    'django.contrib.comments',
    'threadedcomments',
    'debug_toolbar',
    'south',
    'fashionista.socialregistration',
    'pagination',
    'fashionista.gallery',
    'fashionista.socialgraph',
    'fashionista.profile',
)

# CUSTOM SETTINGS

## COMMENTS

#COMMENTS_APP = 'threadedcomments'


## DEBUG TOOLBAR - http://github.com/robhudson/django-debug-toolbar#readme

DEBUG_TOOLBAR_PANELS = (
    'debug_toolbar.panels.version.VersionDebugPanel',
    'debug_toolbar.panels.timer.TimerDebugPanel',
    'debug_toolbar.panels.settings_vars.SettingsVarsDebugPanel',
    'debug_toolbar.panels.headers.HeaderDebugPanel',
    'debug_toolbar.panels.request_vars.RequestVarsDebugPanel',
    'debug_toolbar.panels.template.TemplateDebugPanel',
    'debug_toolbar.panels.sql.SQLDebugPanel',
    'debug_toolbar.panels.signals.SignalDebugPanel',
    'debug_toolbar.panels.logger.LoggingPanel',
)

INTERNAL_IPS = ('127.0.0.1',)

def custom_show_toolbar(request):
    return DEBUG # Always show toolbar, for example purposes only.

DEBUG_TOOLBAR_CONFIG = {
    'INTERCEPT_REDIRECTS': False,
    'SHOW_TOOLBAR_CALLBACK': custom_show_toolbar,
    #'EXTRA_SIGNALS': ['myproject.signals.MySignal'],
    'SHOW_TEMPLATE_CONTEXT': True,
    #'HIDE_DJANGO_SQL': False,
    #'TAG': 'div',
}

#EMAIL_HOST = 'smtp.webfaction.com'
#EMAIL_HOST_USER = 'mailbox_username'
#EMAIL_HOST_PASSWORD = 'mailbox_password'
#DEFAULT_FROM_EMAIL = 'valid_email_address'
#SERVER_EMAIL = 'valid_email_address'

## SOCIAL SERVICES

LOGIN_REDIRECT_URL = '/'
SOCIALREGISTRATION_GENERATE_USERNAME = False

AUTHENTICATION_BACKENDS = (
	'django.contrib.auth.backends.ModelBackend',
	'fashionista.socialregistration.auth.FacebookAuth',
	'fashionista.socialregistration.auth.TwitterAuth',
)

# USEFUL LINKS
"""
Messaging: http://docs.djangoproject.com/en/dev/ref/contrib/messages/
"""