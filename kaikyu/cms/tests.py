from django.test import TestCase
from django.urls import reverse
from accounts.models import CustomUser
from .models import Competition
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
    def test_add_usergroup(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        Competition.objects.create(
            name='test_name', competition_type='test_type')

        # テスト対象を実行
        add_usergroup(1)
        usergroup = UserGroup.objects.all().order_by('id')

        # テスト結果を確認
        self.assertQuerysetEqual(
            [usergroup],
            ['<QuerySet [<UserGroup: UserGroup object (1)>]>']
        )
    # TODO：UserGroupへのデータ保存に失敗した時の動作確認用テストメソッドもほしい
