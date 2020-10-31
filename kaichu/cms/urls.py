from django.urls import path
from cms import views

app_name = 'cms'
urlpatterns = [
    path('home/', views.home, name='home'),
    path('edit_competition/<int:competition_id>',
         views.edit_competition, name='edit_competition'),
]
