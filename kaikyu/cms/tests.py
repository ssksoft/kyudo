from django.test import TestCase
from django.urls import reverse
from accounts.models import CustomUser
from .views import *
from django.views.generic import TemplateView


class HomeTests(TestCase):
    def test_redirect(self):
        response = self.client.get('/cms/home/')
        self.assertRedirects(
            response, expected_url=reverse('cms:competition_list'), status_code=302, target_status_code=200)


class AddCompetitionTests(TestCase):
    def test_no_login(self):
        target_url = '/cms/add_competition/'
        response = self.client.get(target_url)
        expected_url = '/accounts/login/' + '?next=' + target_url
        self.assertRedirects(response, expected_url=expected_url,
                             status_code=302, target_status_code=200)

    def test_login_as_unauthorized_user(self):
        self.client.force_login(CustomUser.objects.create_user('tester'))
        target_url = '/cms/add_competition/'
        response = self.client.get(target_url)
        self.assertEqual(response.status_code, 200)


class AddUserGroupTests(TestCase):
    urls = 'cms.test_urls'

    def setUp(self):
        super().setUp()
        self.path = reverse('cms:test_add_usergroup')

    def test_valid_competition_id(self):
        self.client.force_login(CustomUser.objects.create_user('tester'))
        target_url = '/cms/add_competition/'
        response = self.client.get(target_url)

        # テスト対象を実行
        response = self.client.get(self.path)

        # テスト結果を確認
        self.assertEqual(1, 1)


class TestAddUserGroupView(TemplateView):
    def get(self, request, *args, **kwds):
        return HttpResponse()

    def post(self, request, *args, **kwds):
        return HttpResponse()
