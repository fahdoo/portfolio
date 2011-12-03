from django import forms
from django.contrib.auth.models import User
from littleweb.children.models import Child, Relationship

class ChildForm(forms.ModelForm):

	class Meta:
		model = Child
		exclude = ('created', 'updated', 'deleted', 'relations', 'data')
		
	def __init__(self, *args, **kwargs):
		super(ChildForm, self).__init__(*args, **kwargs)
		
class RelationshipForm(forms.ModelForm):

	class Meta:
		model = Relationship
		exclude = ('created', 'updated', 'deleted', 'user', 'child', 'access')
		
	def __init__(self, *args, **kwargs):
		super(RelationshipForm, self).__init__(*args, **kwargs)