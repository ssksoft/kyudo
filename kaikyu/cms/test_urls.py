from django.conf.urls import url
from django.urls import path
from cms.urls import urlpatterns
from cms import tests as cms_tests
from cms import views


urlpatterns += [
    path('test/add_usergroup/<int:competition_id>',
         views.add_usergroup,
         name='test_add_usergroup')
]
