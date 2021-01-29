from django.conf.urls import url
from cms.urls import urlpatterns
from cms import tests as cms_tests


urlpatterns += [url(r'^test/cms/add_usergroup/$',
                    cms_tests.TestAddUserGroupView.as_view(),
                    name='test_add_usergroup')]
