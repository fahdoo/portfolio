from django.conf.urls.defaults import patterns, include, url
from django.contrib.staticfiles.urls import staticfiles_urlpatterns

from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('rememberly',
    (r'^admin/doc/', include('django.contrib.admindocs.urls')),
    (r'^admin/', include(admin.site.urls)),

    
    # AUTH
    (r'^accounts/', include('registration.urls')),
    (r'^facebook/', include('django_facebook.urls')),	

	# EXTERNAL APPS
	(r'^tinymce/', include('tinymce.urls')),
    (r'^comments/', include('django.contrib.comments.urls')),
    
	# INTERNAL APPS
	(r'^moments/', include('moments.urls')),   	
    (r'^profiles/', include('profiles.urls')),   

    url(r'^magicword/(?P<key>\w+)/$', 'views.magicword', name='magicword'),	  	
    url(r'delete/c/(?P<answer_id>\d+)/(?P<comment_id>\d+)/$', 'views.delete_own_comment', name='delete_own_comment'),
    url(r'^$', 'views.home', name='home'),

    url(r'^(?P<username>\w+)/$', 'profiles.views.profile_detail', name='profile_detail'),	  	
)

urlpatterns += staticfiles_urlpatterns()
