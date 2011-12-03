from django.template import RequestContext
from django.http import HttpResponseRedirect, HttpResponse, Http404
from django.contrib.auth.decorators import login_required

from django.shortcuts import render_to_response, get_object_or_404

from littleweb.stories.models import Story
from littleweb.stories.forms import StoryForm

from littleweb.children.models import Child

@login_required
def index(request):
	latest_stories_list = Story.objects.filter(children__in = Child.objects.accessible_children(request.user)).order_by('-created')[:20]
	form = StoryForm(request.user)
	context = {
		'latest_stories_list': latest_stories_list,
		'form': form
	}

	return render_to_response(
		'stories.html', 
		context,
		context_instance = RequestContext(request)
	)	

@login_required
def story(request, story_id):
	s = Story.objects.get(pk=story_id)
	if Story.objects.can_access(request.user, s):
		return render_to_response(
			'story.html',
			{'story': s},
			context_instance = RequestContext(request)
		)
	else:
		return HttpResponseRedirect('/stories/')

@login_required	
def add_story(request):
	if request.method == 'POST':
		form = StoryForm(request.user, request.POST)
		if form.is_valid():
			story = form.save(commit=False)
			story.author = request.user
			story.save()
			form.save_m2m()
	return HttpResponseRedirect('/stories/')
	