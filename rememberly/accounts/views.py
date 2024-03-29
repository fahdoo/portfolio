from django.http import HttpResponseRedirect
from django.contrib.auth import logout as auth_logout
from django.contrib.auth.decorators import login_required
from django.template import RequestContext
from django.shortcuts import render_to_response
from django.contrib.messages.api import get_messages

from social_auth import __version__ as version

def login(request):
    """Login view, displays login mechanism"""
    if request.user.is_authenticated():
        return HttpResponseRedirect('/done')
    else:
        return render_to_response('accounts/login.html', {'version': version}, RequestContext(request))

@login_required
def done(request):
    """Login complete view, displays user data"""
    ctx = {'version': version,
           'last_login': request.session.get('social_auth_last_login_backend')}
    return render_to_response('accounts/done.html', ctx, RequestContext(request))

def error(request):
    """Error view"""
    messages = get_messages(request)
    return render_to_response('accounts/error.html', {'messages': messages}, RequestContext(request))

def logout(request):
    """Logs out user"""
    auth_logout(request)
    return HttpResponseRedirect('/')
    