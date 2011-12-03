from django import forms
from django.utils.translation import gettext as _

from django.contrib.auth.models import User

class UserForm(forms.Form):
    username = forms.RegexField(r'^\w+$', max_length=32)
    email = forms.EmailField(required=False)

    def __init__(self, user, *args, **kwargs):
		super(UserForm, self).__init__(*args, **kwargs)
		self.user = user
       

    def clean_username(self):
        username = self.cleaned_data.get('username')
        try:
            user = User.objects.get(username=username)
        except User.DoesNotExist:
            return username
        else:
            if user.username != username:
                raise forms.ValidationError(_('This username is already in use.'))
            else:
            	return username

    def save(self, request=None):
        self.user.username = self.cleaned_data.get('username')
        self.user.email = self.cleaned_data.get('email')
        self.user.save()
        return self.user