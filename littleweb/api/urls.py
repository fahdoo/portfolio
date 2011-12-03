from django.conf.urls.defaults import *
from piston.resource import Resource
from piston.authentication import HttpBasicAuthentication
from piston.doc import documentation_view

from littleweb.api.handlers import StoryHandler, UserHandler

#auth = HttpBasicAuthentication(realm='Little Stories')
auth = None
class CsrfExemptResource( Resource ):
	def __init__( self, handler, authentication = auth ):
		super( CsrfExemptResource, self ).__init__( handler, authentication )
		self.csrf_exempt = getattr( self.handler, 'csrf_exempt', True )

stories = CsrfExemptResource( StoryHandler )
users = CsrfExemptResource( UserHandler )

#stories = Resource(handler=StoryHandler, authentication=None)

urlpatterns = patterns('',
	url(r'^stories/$', stories),
	url(r'^stories/(?P<story_id>\d+)$', stories),
	url(r'^stories/(?P<emitter_format>.+)/$', stories),
	url(r'^stories\.(?P<emitter_format>.+)', stories, name='stories'),
	
	url(r'^users/$', users),
	url(r'^users/(?P<username>\w+)/$', users), 
	url(r'^users/(?P<emitter_format>.+)/$', users),
	url(r'^users\.(?P<emitter_format>.+)', users, name='users'),
	
  url(r'^tester/$', 'littleweb.api.views.tester'),
	
	# automated documentation
	url(r'^$', documentation_view),
)