from django.db import models
from django.contrib.auth.models import User
from datetime import datetime
from littleweb.children.models import Child

class StoryManager(models.Manager):
	def can_access(self, user, story):
		children = Child.objects.accessible_children(user)
		for child in children:
			for story_child in story.children.all():
				if child == story_child:
					return True
		
		for story_user in story.users.all():
			if user == story_user:
				return True
		
		return False

class Story(models.Model):
	author = models.ForeignKey(User, related_name = 'author')
	created = models.DateTimeField(default=datetime.now())
	updated = models.DateTimeField(auto_now=True)
	deleted = models.BooleanField(default=False)
	data = models.TextField(blank=True,null=True)
	children = models.ManyToManyField(Child)
	users = models.ManyToManyField(User)
	objects = StoryManager()

	class Admin:
		list_display = ('author', 'created', 'data')
		search_fields = ('data')
		list_filter = ('author', 'created')
		
	def __unicode__(self):
		return self.data