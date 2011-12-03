from django.conf.urls.defaults import *
from fashionista.profile import views

urlpatterns = patterns('',
    url(r'^edit/$', views.edit, name='profile_edit'),
    url(r'^(?P<username>[a-zA-Z0-9_-]+)/$', views.detail, name='profile_detail'),
)