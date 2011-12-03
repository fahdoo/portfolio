from django.contrib import messages
from django.contrib.auth.models import User
from django.shortcuts import get_object_or_404, render_to_response
from django.template import RequestContext
from django.core.urlresolvers import reverse
from django.http import HttpResponseRedirect, HttpResponse, Http404

from fashionista.socialgraph.util import get_people_user_follows, get_people_following_user
from fashionista.socialgraph.util import get_mutual_followers
from fashionista.profile.forms import UserForm

try:
    from django.views.decorators.csrf import csrf_protect
    has_csrf = True
except ImportError:
    has_csrf = False

def detail(request, username=None):
    """
    Renders information about a single user's profile.  This includes
    information about who follows them, who they follow, mutual followers, the
    latest events created, and whether the currently logged in user is a friend
    of the user to render.
    """
    user = get_object_or_404(User, username=username)
    #events_created = list(Event.objects.filter(creator=user, latest=True).order_by('-creation_date')[:10])
    #attended = Attendance.objects.filter(user=user).order_by('-registration_date')[:10]
    #events_attended = list(Event.objects.filter(id__in=[e.event.id for e in attended]).order_by('-creation_date'))
    people_following_user = get_people_following_user(user)
    context = {
        'profile_user': user,
        'people_following_user': people_following_user,
        'people_user_follows': get_people_user_follows(user),
        'mutual_followers': get_mutual_followers(user),
        'friend': request.user in people_following_user,
    }
    return render_to_response(
        'detail.html',
        context,
        context_instance = RequestContext(request)
    )

def edit(request, template='setup.html'):
    """
    Setup view to create a username & set email address after authentication
    """
    try:
    	username = request.user.username
    	email = request.user.email 
    except KeyError:
        return render_to_response(
            template, dict(error=True), context_instance=RequestContext(request))

            
    if not request.method == "POST":
        form = UserForm(request.user, initial={'username': username, 'email': email})
        next = reverse('g-index')
    else:
		form = UserForm(request.user, request.POST, initial={'username': username, 'email': email})
		next = request.POST['next']
        
		if form.is_valid():
			form.save(request=request)
			messages.success(request, 'Profile details updated.')
			return HttpResponseRedirect(next)

    #context.update(dict(form=form))
    context = {
    	'form' : form,
    	'next': next,
    }

    return render_to_response(template, context,
        context_instance=RequestContext(request))
       