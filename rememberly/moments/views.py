from django.http import HttpResponse, HttpResponseRedirect
from django.shortcuts import render_to_response
from django.template import RequestContext
from django.contrib.auth.decorators import login_required

import random, logging

from moments.models import *
from django.db.models import F
from moments import aforms
from django_facebook.models import FacebookUser
from profiles.models import UserProfile

from itertools import chain
from operator import attrgetter

def feed(request):
    qform = aforms.QuestionForm()
    payload = {'qform' : qform}
    return render(request, 'moments/feed.html', payload)
    
@login_required
def ask(request):
    """Ask a question"""
    if request.method == 'POST':
        qform = aforms.QuestionForm(request.user, request.POST)
        if qform.is_valid():
            question = qform.save()
            return HttpResponseRedirect(question.get_absolute_url())
    elif request.method == 'GET':
        qform = aforms.QuestionForm()
    payload = {'qform':qform}
    return render_to_response('moments/ask.html', payload, RequestContext(request))


@login_required
def edit_question(request, id):
	q = Question.objects.get(pk = id)
	if request.method == 'POST' and q.user == request.user:
		qform = aforms.QuestionForm(data=request.POST, instance=q) 
		if qform.is_valid():
			if(qform.save()):
				return HttpResponseRedirect(q.get_absolute_url())
	else:
		qform = aforms.QuestionForm(instance=q) 
	payload = {'question':q, 'qform':qform, }
	return render_to_response('moments/edit/question.html', payload, RequestContext(request))
	
@login_required
def delete_question(request, id):
	q = Question.objects.get(pk = id)
	if q.user == request.user:
		q.delete()
	return HttpResponseRedirect(reverse('moments_index'))

@login_required
def question(request, id):
    """Question Page with Answers to question with the given id"""
    q = Question.objects.get(pk = id)
    a = q.answer_set.filter(deleted = False).order_by('-created')[:20]
    if request.method == 'GET':
        aform = aforms.AnswerForm()
    payload = {'question':q, 'answers':a, 'aform':aform, }
    return render_to_response('moments/question.html', payload, RequestContext(request))
    	
@login_required
def respond(request, id):
	q = Question.objects.get(pk = id)
	if request.method == 'POST':
		aform = aforms.AnswerForm(user = request.user, question = q, data = request.POST)
		if aform.is_valid():
			if(aform.save()):
				q.answer_count = F('answer_count') + 1
				q.save()
	return HttpResponseRedirect(q.get_absolute_url())

@login_required
def edit_answer(request, id):
	a = Answer.objects.get(pk = id)
	q = a.question
	if request.method == 'POST' and a.user == request.user:
		aform = aforms.AnswerForm(data=request.POST, instance=a) 
		if aform.is_valid():
			if(aform.save()):
				return HttpResponseRedirect(a.get_absolute_url())
	else:
		aform = aforms.AnswerForm(instance=a) 
	payload = {'question':q, 'answers':a, 'aform':aform, }
	return render_to_response('moments/edit/answer.html', payload, RequestContext(request))

@login_required
def delete_answer(request, id):
	a = Answer.objects.get(pk = id)
	if a.user == request.user:
		a.delete()
	return HttpResponseRedirect(reverse('moments_index'))
		
@login_required
def answer(request, id):
    """Answer Page"""
    a = Answer.objects.get(pk = id)
    q = a.question
    if( False and a.user == request.user ):
        fb_friends = random_fb_friends(request.user.id)
    else:
        fb_friends = None
    payload = {'question':q, 'answer':a, 'fb_friends': fb_friends }
    return render_to_response('moments/answer.html', payload, RequestContext(request))
    
@login_required
def tag_friend(request, id):
	if request.method == 'POST':
		af_form = aforms.AnswerFriendForm(request.POST)
		af = af_form.save(commit=False)
		af.answer = Answer.objects.get(pk = request.POST['answer_id'])
		af.question = af.answer.question
		af.facebook_id = request.POST['facebook_id']
		try:
			af.friend = UserProfile.objects.get(facebook_id = af.facebook_id).user
		except UserProfile.DoesNotExist:
			af.friend = None	
		
		af.save()
	return HttpResponseRedirect(af.answer.get_absolute_url())

def randompage(request):
    for i in xrange(5):
        i = Question.objects.filter(deleted = False).count()
        randcount = random.randint(1, i - 1)
        question = Question.objects.get(id = randcount)
        if question.is_open:
            return HttpResponseRedirect(question.get_absolute_url())
    return HttpResponseRedirect(question.get_absolute_url())
    
def render(request, template, payload):
    open_questions = Question.objects.filter(is_open = True, deleted = False)[:20]
    recently_answered = Answer.objects.filter(deleted = False).order_by('-created')[:20]
   
    items = sorted(chain(open_questions, recently_answered),key=attrgetter('created'), reverse=True)
    
    payload.update({'items': items,})
    return render_to_response(template, payload, RequestContext(request))

def random_fb_friends(id):
	count = FacebookUser.objects.filter(user_id = id).count()
	slice = random.random() * (count - 10)
	return FacebookUser.objects.filter(user_id = id)[slice: slice+10]
   
    
