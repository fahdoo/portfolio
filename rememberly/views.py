from annoying.decorators import render_to
    
from django.contrib.auth.decorators import login_required
from django.contrib.comments.models import Comment
from django.shortcuts import get_object_or_404, redirect
from django.conf import settings
from django.contrib import comments

@render_to('landing.html')
def home(request):
    return {}

def magicword(request, key):
	keys = {'nostalgia', 'press', 'hn',}
	if key in keys:
		return redirect('auth_login')
	else:
		return redirect('home')

@login_required
def delete_own_comment(request, answer_id, comment_id):
    comment = get_object_or_404(comments.get_model(), pk=comment_id, site__pk=settings.SITE_ID)
    if comment.user == request.user:
        comment.is_removed = True
        comment.save()
        return redirect("moments_answer", answer_id)
        
