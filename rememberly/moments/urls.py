from django.conf.urls.defaults import *
from django.views.generic.simple import direct_to_template
#from django.contrib.auth.views import login, logout

urlpatterns = patterns('moments.views',
    url(r'^$', 'feed', name='moments_index'),
    url(r'^random/$', 'randompage', name='moments_random'),

    url(r'^ask/$', 'ask', name='moments_ask'),
    url(r'edit/q/(?P<id>\d+)/$', 'edit_question', name='moments_edit_question'),
    url(r'delete/q/(?P<id>\d+)/$', 'delete_question', name='moments_delete_question'),

    url(r'tag_friend/a/(?P<id>\d+)/$', 'tag_friend', name='moments_tag_friend'),
    url(r'respond/q/(?P<id>\d+)/$', 'respond', name='moments_respond'),
    url(r'edit/a/(?P<id>\d+)/$', 'edit_answer', name='moments_edit_answer'),
    url(r'delete/a/(?P<id>\d+)/$', 'delete_answer', name='moments_delete_answer'),

    url(r'a/(?P<id>\d+)/$', 'answer', name='moments_answer'),
	url(r'(?P<id>\d+)/$', 'question', name='moments_question'), #(?P<slug>[-w]+)/

)