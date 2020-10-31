from django.urls import path
from cms import views

app_name = 'cms'
urlpatterns = [
    path('home/', views.home, name='home'),
    path('competition_list/', views.competition_list, name='competition_list'),
    path('add_competition/',
         views.edit_competition, name='add_competition'),
    path('edit_competition/<int:competition_id>',
         views.edit_competition, name='edit_competition'),
]
