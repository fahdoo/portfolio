from django.conf.urls.defaults import *
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User

# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
	(r'^admin/doc/', include('django.contrib.admindocs.urls')),
	(r'^admin/', include(admin.site.urls)),
	(r'^api/1.0/', include('littleweb.api.urls')),
	(r'^stories/', include('littleweb.stories.urls')),
	(r'^profiles/', include('littleweb.profiles.urls')),
  (r'^friends/', include('littleweb.friends.urls')),
	(r'^children/', include('littleweb.children.urls')),
	(r'^accounts/', include('littleweb.registration.backends.default.urls')),
	url(r'^about/$', 'django.views.generic.simple.direct_to_template', {'template': 'misc/about.html'}, name='about'),
)

#urlpatterns += patterns('',
#        (r'^media/(?P<path>.*)$', 'django.views.static.serve', {'document_root': settings.MEDIA_ROOT, 'show_indexes':True}),
#)
