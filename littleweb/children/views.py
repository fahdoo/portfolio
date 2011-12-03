from django.template import RequestContext
from django.http import HttpResponseRedirect, HttpResponse, Http404
from django.shortcuts import render_to_response, get_object_or_404

from littleweb.children.models import Child, Relationship
from littleweb.children.forms import ChildForm, RelationshipForm

from littleweb.friends.models import Friendship


def index(request):
	children_list = Child.objects.accessible_children(request.user)
	child_form = ChildForm(prefix='c')
	relationship_form = RelationshipForm(prefix='r')
	context = {
		'children_list': children_list,
		'child_form': child_form,
		'relationship_form' : relationship_form
	}
	return render_to_response(
		'children.html', 
		context,
		context_instance = RequestContext(request)
	)
    
def child(request, child_id):
	c = Child.objects.accessible_child(request.user, child_id)
	if c:
		return render_to_response(
			'child.html',
			{'child': c},
			context_instance = RequestContext(request)
		)
	else:
		return HttpResponseRedirect('/children/')		
	
def add_child(request):
	if request.method == 'POST':
		child_form = ChildForm(request.POST, prefix='c')
		relationship_form = RelationshipForm(request.POST, prefix='r')
		if child_form.is_valid() and relationship_form.is_valid():
			child = child_form.save(commit=False)
			child.data = u'%s %s' % (child.first_name, child.last_name)
			child.save();
			relationship = relationship_form.save(commit=False)
			relationship.user_id = request.user.id
			relationship.child_id = child.id
			relationship.access = 'A'
			relationship.save()

			for friend in Friendship.objects.friends_for_user(request.user):
				Relationship.save_relations_for_friend(child, friend, 'M')			
			
			return HttpResponseRedirect('/children/')
		else:
			context = {
				'child_form': child_form,
				'relationship_form' : relationship_form
			}
			return render_to_response(
				'add_child.html', 
				context,
				context_instance = RequestContext(request)
			)		
		