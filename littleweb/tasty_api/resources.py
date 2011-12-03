from django.contrib.auth.models import User

from tastypie.resources import ModelResource
from tastypie import fields
from tastypie.authorization import Authorization

from littleweb.stories.models import Story


class UserResource(ModelResource):
	class Meta:
		resource_name = 'users'
    excludes = ['email', 'password', 'is_active', 'is_staff', 'is_superuser']
		queryset = User.objects.all()
		allowed_methods = ['get', 'put', 'post']
		authorization = Authorization()


class StoryResource(ModelResource):
	author = fields.ForeignKey(UserResource, 'user')
	class Meta:
		queryset = Story.objects.all()
		authorization = Authorization()
		allowed_methods = ['get', 'put', 'post', 'delete']
