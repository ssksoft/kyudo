from django.urls import path
from cms import views

app_name = 'cms'
urlpatterns = [
    path('home/', views.home, name='home'),
    path('competition_list/', views.competition_list,
         name='competition_list'),
    path('add_competition/',
         views.edit_competition, name='add_competition'),
    path('edit_competition/<int:competition_id>',
         views.edit_competition, name='edit_competition'),
    path('delete_competition/<int:competition_id>',
         views.delete_competition, name='delete_competition'),
    path('match_list/<int:competition_id>',
         views.match_list, name='match_list'),
    path('add_match/<int:competition_id>',
         views.edit_match, name='add_match'),
    path('edit_match/<int:competition_id>/<int:match_id>',
         views.edit_match, name='edit_match'),
    path('delete_match/<int:competition_id>/<int:match_id>',
         views.delete_match, name='delete_match'),
    path('edit_hit/<int:competition_id>/<int:match_id>',
         views.edit_hit, name='edit_hit'),
    path('get_players/<int:competition_id>/<int:match_id>',
         views.get_players, name='get_players'),
    path('save_hit/<int:competition_id>/<int:match_id>',
         views.save_hit, name='save_hit'),
    path('player_list/<int:competition_id>',
         views.player_list, name='player_list'),
    path('add_player/<int:competition_id>',
         views.edit_player, name='add_player'),
    path('edit_player/<int:competition_id>/<int:player_id>',
         views.edit_player, name='edit_player'),
    path('change_player/<int:competition_id>/<int:match_id>',
         views.change_player, name='change_player'),
    path('login/', views.login_user, name='login'),
    path("registration/", views.registration_user, name='registration')
]