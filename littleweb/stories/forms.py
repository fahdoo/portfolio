from django import forms
#from django.utils.translation import gettext as _
from littleweb.stories.models import Story
from django.contrib.auth.models import User

class StoryForm(forms.ModelForm):
	#story_text = forms.CharField(widget=forms.Textarea, label='A Little Story')
	#children = forms.MultipleChoiceField(required=True, label='Kiddies', choices= User.objects.none())

	class Meta:
		model = Story
		fields = ('data', 'children')
		
	def __init__(self, user, *args, **kwargs):
		super(StoryForm, self).__init__(*args, **kwargs)
		#self.fields['children'].choices = [(c.id, c.nickname) for c in user.child_set.all()]