from django.conf.urls.defaults import *

urlpatterns = patterns('littleweb.children.views',	
	url(r'^$', 'index', name='children-index'),
	url(r'^(?P<child_id>\d+)/$', 'child', name='children-child'),
	url(r'^add/$', 'add_child', name='children-add'),
)

