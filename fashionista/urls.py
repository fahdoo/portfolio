from django.conf.urls.defaults import *
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.views.generic.list_detail import object_list

# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
    (r'^admin/doc/', include('django.contrib.admindocs.urls')),
    (r'^admin/', include(admin.site.urls)),
    # Example:
    # (r'^fashionista/', include('fashionista.foo.urls')),
    (r'^', include('fashionista.gallery.urls')),
	(r'^services/', include('fashionista.socialregistration.urls')),
    (r'^cocos/', include('fashionista.socialgraph.urls')),
    (r'^profile/', include('fashionista.profile.urls')),
    (r'^comments/', include('django.contrib.comments.urls')),
    url(r'^settings/$', 'fashionista.profile.views.edit', name='settings'),
    url(r'^cocos/latest/$',
        login_required(object_list),
        {'queryset': User.objects.order_by('-date_joined'),
        'paginate_by': 50, 'allow_empty': True},
        name='user_list'),
    url(r'^about/$', 'django.views.generic.simple.direct_to_template', 
        {'template': 'misc/about.html'}, name='about'),
)
