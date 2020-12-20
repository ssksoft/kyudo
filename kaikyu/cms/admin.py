from django.contrib import admin
from accounts.models import UserGroup
from cms.models import Competition, Match, Player, Hit


class PlayerAdmin(admin.ModelAdmin):
    list_display = ('id', 'name')
    list_display_links = ('id', 'name')


class HitAdmin(admin.ModelAdmin):
    list_display = ('competition', 'match', 'player', 'hit')
    list_display_links = ('competition', 'match', 'player', 'hit')


admin.site.register(UserGroup)
admin.site.register(Competition)
admin.site.register(Match)
admin.site.register(Player, PlayerAdmin)
admin.site.register(Hit, HitAdmin)
