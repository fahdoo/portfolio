from django.contrib import admin
from fashionista.socialregistration.models import (FacebookProfile, TwitterProfile,
    OpenIDProfile, OpenIDStore, OpenIDNonce)

admin.site.register([FacebookProfile, TwitterProfile, OpenIDProfile, OpenIDStore, OpenIDNonce])


