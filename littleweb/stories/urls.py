from django.conf.urls.defaults import *

urlpatterns = patterns('littleweb.stories.views',	
	url(r'^$', 'index', name='stories-index'),
	url(r'^(?P<story_id>\d+)/$', 'story', name='stories-story'),
	url(r'^add/$', 'add_story', name='stories-add'),
)

