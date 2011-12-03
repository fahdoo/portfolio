from django.conf.urls.defaults import *
from fashionista.socialgraph import views

urlpatterns = patterns('',
    url(
        r'^followers/(?P<username>[a-zA-Z0-9_-]+)/$',
        views.friend_list,
        {'list_type': 'followers'},
        name='sg_followers'
    ),
    url(
        r'^following/(?P<username>[a-zA-Z0-9_-]+)/$',
        views.friend_list,
        {'list_type': 'following'},
        name='sg_following'
    ),
    url(
        r'^mutual/(?P<username>[a-zA-Z0-9_-]+)/$',
        views.friend_list,
        {'list_type': 'mutual'},
        name='sg_mutual'
    ),
    url(
        r'^follow/(?P<username>[a-zA-Z0-9_-]+)/$',
        views.follow,
        name='sg_follow'
    ),
    url(
        r'^unfollow/(?P<username>[a-zA-Z0-9_-]+)/$',
        views.unfollow,
        name='sg_unfollow'
    ),
    url(
        r'^search/$',
        views.find_and_add,
        name='sg_find_add'
    ),
)