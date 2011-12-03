from django.db import models
from django.contrib.auth.models import User
from datetime import datetime
import dateutil

from littleweb.friends.models import Friendship

class ChildManager(models.Manager):
	def accessible_children(self, user):
		return Child.objects.filter(relationship__user = user).exclude(relationship__access = 'B').order_by('-created')

	def by_access(self, user, access):
		return Child.objects.filter(relationship__user = user, relationship__access = access).order_by('-created')
		
	def accessible_child(self, user, child_id):
		return Child.objects.filter(pk = child_id, relationship__user = user).exclude(relationship__access = 'B')

class Child(models.Model):
	GENDER_CHOICES = (
		(u'M', u'Male'),
		(u'F', u'Female'),
	)
	created = models.DateTimeField(default=datetime.now())
	updated = models.DateTimeField(auto_now=True)
	deleted = models.BooleanField(default=False)
	data = models.TextField(blank=True, null=True)
	first_name = models.CharField(max_length=100)
	last_name = models.CharField(max_length=100)	
	nickname = models.CharField(max_length=100)
	gender = models.CharField(max_length=2, choices=GENDER_CHOICES)
	birth_date = models.DateField()
	relations = models.ManyToManyField(User, through='Relationship')
	objects = ChildManager()

	class Meta:
	    db_table = "children"
	    verbose_name_plural = "children"
	      
	class Admin:
		pass
		
	def __unicode__(self):
		return u'%s' % self.nickname
	
	@property
	def age(self):
		TODAY = datetime.date.today()
		return u'%s' % dateutil.relativedelta(TODAY, self.birth_date).years
		
	@property
	def full_name(self):
		return u'%s %s' % (self.first_name, self.last_name)

#class RelationManager(models.Manager):
	
   
class Relationship(models.Model):
	ACCESS_CHOICES = (
		(u'A', u'Admin'),
		(u'M', u'Member'),
		(u'B', u'Blocked'),
	)
	RELATION_CHOICES = (
		(u'A', u'Aunt'),
		(u'B', u'Brother'),
		(u'C', u'Cousin'),
		(u'F', u'Father'),
		(u'FR', u'Friend'),
		(u'FF', u'Family Friend'),
		(u'GF', u'Grandfather'),
		(u'GM', u'Grandmother'),
		(u'M', u'Mother'),
		(u'R', u'Relative'),
		(u'S', u'Sister'),
		(u'U', u'Uncle'),
	)	
	user = models.ForeignKey(User)
	child = models.ForeignKey(Child)
	relation = models.CharField(max_length=3, choices=RELATION_CHOICES)
	nickname = models.CharField(max_length=128)
	access = models.CharField(max_length=3, choices=ACCESS_CHOICES)
	created = models.DateTimeField(default=datetime.now())
	updated = models.DateTimeField(auto_now=True)
	deleted = models.BooleanField(default=False)
	#objects = RelationManager()
		
	def __unicode__(self):
		return self.nickname
		
	class Admin:
		pass

	def save_relations_for_friend(self, child, friend, access):
		self.user_id = friend.id
		self.child_id = child.id
		self.access = access
		self.save()