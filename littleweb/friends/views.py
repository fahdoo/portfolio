from django.template import RequestContext
from django.http import HttpResponseRedirect, HttpResponse, Http404
from django.contrib.auth.decorators import login_required

from django.shortcuts import render_to_response, get_object_or_404

from django.contrib.auth.models import User
from littleweb.friends.models import Friendship

from littleweb.friends.forms import InviteFriendForm


@login_required
def index(request):
	friends_list = Friendship.objects.friends_for_user(request.user)
	form = InviteFriendForm()
	context = {
		'friends_list': friends_list,
		'form': form
	}

	return render_to_response(
		'friends.html', 
		context,
		context_instance = RequestContext(request)
	)	


@login_required
def friend(request, friend_id):
	f = Friendship.objects.get(pk=friend_id)
	if Friendship.objects.can_access(request.user, f):
		return render_to_response(
			'friend.html',
			{'friend': f},
			context_instance = RequestContext(request)
		)
	else:
		return HttpResponseRedirect('/friends/')

@login_required	
def add_friend(request):
	if request.method == 'POST':
		form = FriendshipForm(request.user, request.POST)
		if form.is_valid():
			friend = form.save(commit=False)
			friend.author = request.user
			friend.save()
			form.save_m2m()
	return HttpResponseRedirect('/friends/')
	