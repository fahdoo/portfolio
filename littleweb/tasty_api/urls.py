from django.conf.urls.defaults import *
from tastypie.api import Api
from littleweb.api.resources import StoryResource, UserResource

v1_api = Api(api_name='v1')
v1_api.register(StoryResource(), canonical=True)
v1_api.register(UserResource(), canonical=True)

#urlpatterns = v1_api.urls
urlpatterns = patterns('',
	(r'^/$', include(v1_api.urls)),
)
