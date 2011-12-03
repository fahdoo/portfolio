from django.conf.urls.defaults import *
from fashionista.gallery import views

urlpatterns = patterns('',	
	url(r'^$', views.index, name='g-index'),
	url(r'^p/(?P<slug>[a-zA-Z0-9_-]+)/$', views.photo, name='g-photo'),
)

