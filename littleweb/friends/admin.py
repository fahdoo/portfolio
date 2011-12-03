from django.contrib import admin

from littleweb.friends.models import Friendship, FriendshipInvitation, FriendshipInvitationHistory
from littleweb.friends.models import JoinInvitation



class FriendshipAdmin(admin.ModelAdmin):
    list_display = ('id', 'from_user', 'to_user', 'added',)


class JoinInvitationAdmin(admin.ModelAdmin):
    list_display = ('id', 'from_user', 'profile', 'status')


class FriendshipInvitationAdmin(admin.ModelAdmin):
    list_display = ('id', 'from_user', 'to_user', 'sent', 'status',)


class FriendshipInvitationHistoryAdmin(admin.ModelAdmin):
    list_display = ('id', 'from_user', 'to_user', 'sent', 'status',)


admin.site.register(Friendship, FriendshipAdmin)
admin.site.register(JoinInvitation, JoinInvitationAdmin)
admin.site.register(FriendshipInvitation, FriendshipInvitationAdmin)
admin.site.register(FriendshipInvitationHistory, FriendshipInvitationHistoryAdmin)
