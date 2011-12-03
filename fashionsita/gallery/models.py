from django.db import models
from django.contrib.auth.models import User
from datetime import datetime
from django.template.defaultfilters import slugify
from fashionista.gallery.baseconv import encode_to_key

# Create your models here.
class Photo(models.Model):
	user = models.ForeignKey(User)
	url = models.CharField(max_length=128)
	title = models.CharField(max_length=128)
	description = models.TextField(blank=True)
	created = models.DateTimeField(default=datetime.now())
	updated = models.DateTimeField(null=True)
	slug = models.SlugField(max_length=128, unique=True, null=True)
	short_key = models.CharField(max_length=11, unique=True, null=True)
        	
	def save(self):
		if not self.id:
			models.Model.save(self)
			self.short_key = encode_to_key(self.id)
			self.slug = '%s-%s' % (
		        slugify(self.title), self.short_key
		    )
		else:
			self.updated = datetime.now()
		models.Model.save(self)
	
	@models.permalink
	def get_object_url(self):
		return ('g-photo', [str(self.id)])