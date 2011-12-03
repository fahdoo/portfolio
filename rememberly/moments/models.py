from django.db import models
from django.contrib.auth.models import User
from datetime import datetime
from django.template.defaultfilters import slugify
from django.core.urlresolvers import reverse

class Question(models.Model):
	user = models.ForeignKey(User)
	title = models.CharField(max_length = 300)
	slug = models.SlugField()
	description = models.TextField(blank=True,null=True)
	created = models.DateTimeField(auto_now_add=True)
	updated = models.DateTimeField(auto_now=True)
	deleted = models.BooleanField(default=False)
	is_open = models.BooleanField(default = True)
	answer_count = models.PositiveIntegerField(default = 0)

	@models.permalink
	def get_absolute_url(self):
		return ('moments_question', [str(self.id)]) #, self.slug

	def save(self):
		self.slug = slugify(self.title)[:50]
		super(Question, self).save()

	def delete(self):
		self.deleted = True
		self.save()
				
	def __str__(self):
		return self.title 
	
	class Admin:
		pass

class Answer(models.Model):
	user = models.ForeignKey(User)
	question = models.ForeignKey(Question)
	text = models.TextField()
 	created = models.DateTimeField(auto_now_add=True)
	updated = models.DateTimeField(auto_now=True)
	deleted = models.BooleanField(default=False)
	friends = models.ManyToManyField(User, through='AnswerFriend', related_name = 'friends')

	@models.permalink
	def get_absolute_url(self):
		return ('moments_answer', [self.id])
		
	def save(self):
		self.slug = slugify(self.text)[:50]
		super(Answer, self).save()
	
	def delete(self):
		self.deleted = True
		self.save()
			
	def __str__(self):
		return self.text    

	class Admin:
		pass

class AnswerFriend(models.Model):
	friend = models.ForeignKey(User, related_name = 'friend', blank=True, null=True, default=None)
	facebook_id = models.BigIntegerField(blank=True, null=True, default=None)
	answer = models.ForeignKey(Answer)	
	question = models.ForeignKey(Question)
 	created = models.DateTimeField(auto_now_add=True)
	updated = models.DateTimeField(auto_now=True)
	deleted = models.BooleanField(default=False)

	class Admin:
		pass

	def __str__(self):
		return str(self.facebook_id)