from django.http import HttpResponse, HttpResponseRedirect
from django.contrib.auth.decorators import login_required
from django.shortcuts import render_to_response, get_object_or_404
from django.template import RequestContext
    
def tester(request):
    return render_to_response('tester.html', {}, RequestContext(request))
