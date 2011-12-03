from django.contrib import admin

from moments.models import Question, Answer, AnswerFriend

admin.site.register(Question)
admin.site.register(Answer)
admin.site.register(AnswerFriend)
