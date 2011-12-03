from django.shortcuts import get_object_or_404

from piston.handler import BaseHandler, AnonymousBaseHandler
from piston.utils import rc, require_mime, require_extended, validate

from django.contrib.auth.models import User
from littleweb.stories.models import Story
from littleweb.children.models import Child, Relationship

from littleweb.stories.forms import StoryForm

class UserHandler(BaseHandler):
	allowed_methods = ('GET', 'POST', 'PUT')
	model = User 
	anonymous = 'AnonymousUserHandler'
	exclude = ('password',)
	
class AnonymousUserHandler(UserHandler, AnonymousBaseHandler):
	"""
	Anonymous entrypoint for user.
	"""
	exclude = ('password',)

	def read(self, request, username=None):
		if username is not None:
			return get_object_or_404(User, username=username)	
		else:	
			return User.objects.all()			

class StoryHandler(BaseHandler):
	"""
	Authenticated entrypoint for story.
	"""
	allowed_methods = ('GET', 'POST', 'PUT', 'DELETE')
	model = Story
	#anonymous = 'AnonymousStoryHandler'
	fields = ('data', 'created', 'content_length', ('author', ('username', 'first_name') ), ('children', ()), ('users', ('username', 'first_name')))

	@classmethod
	def content_length(cls, story):
		return len(story.data)
	
	#    @classmethod
	#    def resource_uri(cls, story):
	#        return ('story', [ 'json', ])

	def read(self, request, story_id=None):
		"""
		Returns a story, if `story_id` is given,
		otherwise all the stories.
		
		Parameters:
		 - `story_id`: The id of the story to retrieve.
		"""
		if story_id is not None:
			return Story.objects.filter(pk=story_id, children__in = Child.objects.accessible(request.user))	
		else:	
			return Story.objects.filter(children__in = Child.objects.accessible(request.user)).order_by('-created')[:20]

	#@validate(StoryForm, 'PUT')
	def update(self, request, story_id):	
		super(StoryHandler, self).update(request, story_id)

	#@validate(StoryForm, 'POST')
	def create(self, request):
		"""
		Creates a new story.
		"""
		attrs = self.flatten_dict(request.POST)
		
		if self.exists(**attrs):
			return rc.DUPLICATE_ENTRY
		else:
			s = Story(data=attrs['data'], author=request.user)
			s.save()
			child = Child.objects.get(pk=int(attrs['children']))
			s.children.add(child)
			return s
			
#class AnonymousStoryHandler(StoryHandler, AnonymousBaseHandler):
#    """
#    Anonymous entrypoint for story.
#    """
#    fields = ('id', 'title', 'content', 'created_on')
