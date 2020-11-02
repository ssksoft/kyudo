from django.contrib import admin
from cms.models import Competition, Match, Player, Hit, User

admin.site.register(Competition)
admin.site.register(Match)


class PlayerAdmin(admin.ModelAdmin):
    list_display = ('id', 'name')
    list_display_links = ('id', 'name')


admin.site.register(Player, PlayerAdmin)
admin.site.register(Hit)
admin.site.register(User)


# class CompetitionAdmin(admin.ModelAdmin):
#     list_display = ('id',)
#     list_display_links = ('id',)


# admin.site.register(Competition, CompetitionAdmin)
