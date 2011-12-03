from django.db import models
from django.contrib.auth.models import User
from datetime import datetime


class Profile(models.Model):
	GENDER_CHOICES = (
		(u'M', u'Male'),
		(u'F', u'Female'),
	)
	user = models.ForeignKey(User, unique=True)
	invited_by =  models.ForeignKey(User, related_name="profiles")
	created = models.DateTimeField(default=datetime.now())
	updated = models.DateTimeField(auto_now=True)
	deleted = models.BooleanField(default=False)
	data = models.TextField(blank=True, null=True)
	nickname = models.CharField(max_length=100)
	gender = models.CharField(max_length=2, choices=GENDER_CHOICES)
	#connections = models.ManyToManyField('self', through='Connection', symmetrical=False, related_name='connected_to')

	class Meta:
	    db_table = "profiles"
	    verbose_name_plural = "profiles"
        
	class Admin:
		pass
		
	def __unicode__(self):
		return u'%s' % self.nickname
	
User.profile = property(lambda u: Profile.objects.get_or_create(user=u)[0])


