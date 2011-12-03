# Create your views here.
import os
import mimetypes
import logging
from fashionista.gallery.baseconv import generate_key

from django.shortcuts import render_to_response, get_object_or_404
from django import forms
from django.conf import settings
from boto.s3.connection import S3Connection
from boto.s3.key import Key
from django.template import RequestContext
from django.http import HttpResponseRedirect, HttpResponse, Http404

from models import Photo

#For future see: http://docs.djangoproject.com/en/dev/topics/forms/modelforms/#topics-forms-modelforms
class UploadForm(forms.Form):
    title = forms.CharField(max_length = 128)
    description = forms.CharField(required=False, widget=forms.Textarea)
    file = forms.ImageField(label='Select photo to upload')
   
def index(request):
	def store_in_s3(filename, content):
		conn = S3Connection(settings.ACCESS_KEY, settings.PASS_KEY)
		b = conn.create_bucket(settings.BUCKET)
		mime = mimetypes.guess_type(filename)[0]
		k = Key(b)
		k.key = filename
		k.set_metadata("Content-Type", mime)
		k.set_contents_from_string(content)
		k.set_acl("public-read")
			
	if request.method == "POST":
		next = request.POST.get('next', '')
		form = UploadForm(request.POST, request.FILES)
	    
		if form.is_valid():
			file = request.FILES["file"]
			content = file.read()
			ext = os.path.splitext(file.name)[1]
			encoded_key, uuid_key = generate_key()
			filename = uuid_key + ext
			store_in_s3(filename, content)			
			p = Photo(
				user = request.user, 
				url= settings.CLOUD_STORAGE_URL + filename, 
				title = form.cleaned_data['title'], 
				description = form.cleaned_data['description']
			)
			p.save()
			#request.user.message_set.create(message=_('bunny found a carrot') )
	else:
		form = UploadForm()
	
	photos = Photo.objects.all().order_by("-created")
	
	context = {
		'form':form, 
		'photos':photos, 
	}
	return render_to_response(
		'index.html', 
		context,
		context_instance = RequestContext(request)
	)

def photo(request, slug):
    """
    Render a single photo.
    """
    photo = get_object_or_404(Photo, slug=slug)
    return render_to_response(
        'photo.html',
        {'photo': photo},
        context_instance = RequestContext(request)
    )