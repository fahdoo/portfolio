from django.conf.urls.defaults import *

urlpatterns = patterns('littleweb.friends.views',	
	url(r'^$', 'index', name='friends-index'),
	url(r'^(?P<friend_id>\d+)/$', 'friend', name='friends-friend'),
	url(r'^add/$', 'add_friend', name='friends-add'),
	url(r'^invite/$', 'invite_friend', name='friends-invite'),
)

