from django import forms
from models import *
from tinymce.widgets import TinyMCE

class QuestionForm(forms.ModelForm):
    def __init__(self, user = None, *args, **kwargs):
        self.user = user
        super(QuestionForm, self).__init__(*args, **kwargs)
    
    def save(self):
    	if (self.user):
	        question = Question(user = self.user, title =self.cleaned_data['title'].strip())
    	else:
    		question = self.instance
    		question.title = self.cleaned_data['title'].strip()
    		question.description = self.cleaned_data['description'].strip()
        question.save()
        return question
        
    class Meta:
        model = Question
        fields = ('title', 'description')
        widgets = {
        	'title': forms.TextInput(attrs={'class': 'span12', 'title': 'Remember?', 'placeholder': 'e.g. Remember when you helped a stranger?'}),
			'description': forms.Textarea(attrs={'rows': 5, 'class' : 'span12', 'placeholder': 'Description...'}),
        }
             
class AnswerForm(forms.ModelForm):
	def __init__(self, user = None, question = None, *args, **kwargs):
		self.user = user
		self.question = question
		super(AnswerForm, self).__init__(*args, **kwargs)

	def save(self):
		if (self.user and self.question):
			answer = Answer(text = self.cleaned_data['text'].strip())
			answer.user = self.user
			answer.question = self.question
		else:
			answer = self.instance
			answer.text = self.cleaned_data['text'].strip()			
		answer.save()
		return answer

	class Meta:
		model = Answer
		fields = ('text',)  
		widgets = {
			'text': TinyMCE(attrs={'class':'span8', 'rows': 5, 'placeholder': 'Write your story...'}),
			#forms.Textarea(attrs={'rows': 5, 'class' : 'span8', 'placeholder': 'Write your story...'}),
		}

class AnswerFriendForm(forms.ModelForm):
	def __init__(self, facebook_id = None, answer_id = None, *args, **kwargs):
		super(AnswerFriendForm, self).__init__(*args, **kwargs)


	class Meta:
		model = AnswerFriend
