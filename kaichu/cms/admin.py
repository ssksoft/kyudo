from django.contrib import admin
from cms.models import Competition, Match, Player, Hit, User

admin.site.register(Competition)
admin.site.register(Match)
admin.site.register(Player)
admin.site.register(Hit)
admin.site.register(User)


# class CompetitionAdmin(admin.ModelAdmin):
#     list_display = ('id',)
#     list_display_links = ('id',)


# admin.site.register(Competition, CompetitionAdmin)
