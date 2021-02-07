from django.test import TestCase
from django.urls import reverse
from accounts.models import CustomUser, UserGroup
from .models import Competition
from .forms import *
from .views import *
from django.views.generic import TemplateView


class HomeTests(TestCase):
    def test_redirect(self):
        response = self.client.get('/cms/home/')
        self.assertRedirects(
            response, expected_url=reverse('cms:competition_list'), status_code=302, target_status_code=200)


class AddCompetitionTests(TestCase):
    def test_get_without_login(self):
        target_url = '/cms/add_competition/'
        response = self.client.get(target_url)
        expected_url = '/accounts/login/' + '?next=' + target_url
        self.assertRedirects(response, expected_url=expected_url,
                             status_code=302, target_status_code=200)

    def test_get_with_login_user(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))
        target_url = '/cms/add_competition/'

        # GET実行
        response = self.client.get(target_url)

        # テスト結果を確認
        self.assertEqual(404, response.status_code)

    def test_post_with_login_user(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))
        target_url = '/cms/add_competition/'

        # POST実行
        data = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        response = self.client.post(target_url, data)

        # テスト結果を確認
        self.assertEqual(302, response.status_code)


class SaveCompetitionTests(TestCase):
    def test_save_competition(self):
        # データ用意
        competition_obj = Competition()
        post_content = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }

        # テスト対象を実行
        save_competition(post_content, competition_obj)

        # テスト結果を確認
        competition = Competition.objects.all().order_by('id')
        self.assertEqual(
            '<QuerySet [<Competition: test_name>]>', str(competition))

        # TODO：Competitionのデータ保存に失敗した時の動作確認用テストメソッドもほしい


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
        self.assertEqual(
            '<QuerySet [<UserGroup: UserGroup object (1)>]>', str(usergroup)
        )
    # TODO：UserGroupへのデータ保存に失敗した時の動作確認用テストメソッドもほしい


class AddUserAndGroupTests(TestCase):
    def test_add_userandgroup(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        Competition.objects.create(
            name='test_name', competition_type='test_type')

        competition_test = Competition.objects.get(id=1)

        # ダミーデータをUserGroupに追加
        UserGroup.objects.create(competition=competition_test)

        # テスト対象を実行
        usergroup_pk = 1
        user_pk = 1
        ret = add_userandgroup(usergroup_pk, user_pk)
        userandgroup = UserAndGroup.objects.all().order_by('id')

        # テスト結果を確認
        self.assertEqual(1, ret)

        # TODO：UserAndGroupへのデータ保存に失敗した時の動作確認用テストメソッドもほしい


# TODO:GETとPOST両方のテストが必要
class EditCompetitionTests(TestCase):
    def test_get(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        Competition.objects.create(
            name='test_name', competition_type='test_type')

        competition_test = Competition.objects.get(id=1)

        # テスト対象を実行
        data = {
            'competition_id': 1
        }
        target_url = reverse('cms:edit_competition', kwargs=data)
        response = self.client.get(target_url)

        # テスト結果を確認
        self.assertEqual(200, response.status_code)


class DeleteCompaetitionTests(TestCase):
    def test_delete_success(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # テスト対象を実行
        data = {
            'competition_id': 1
        }
        target_url = reverse('cms:delete_competition', kwargs=data)

        # GET実行
        response_target = self.client.get(target_url)

        # テスト結果を確認
        self.assertEqual(302, response_target.status_code)

    def test_delete_without_login(self):
        # テスト対象を実行
        data = {
            'competition_id': 1
        }
        target_url = reverse('cms:delete_competition', kwargs=data)

        # GET実行
        response_target = self.client.get(target_url)

        # テスト結果を確認
        self.assertEqual(302, response_target.status_code)

    def test_delete_with_stranger(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # テスト対象を実行
        data = {
            'competition_id': 1
        }
        target_url = reverse('cms:delete_competition', kwargs=data)

        # GET実行
        response_target = self.client.get(target_url)

        # テスト結果を確認
        self.assertEqual(403, response_target.status_code)


class EditMatchTests(TestCase):
    def test_add_match(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # テスト対象を実行
        data_target = {
            'competition_id': 1,
        }
        url_target = reverse('cms:add_match', kwargs=data_target)
        competition = Competition.objects.get(id=1)
        post_contents = {
            'competition': '1',
            'name': 'test_match_name'
        }
        form = MatchForm(post_contents)
        # self.assertTrue(form.is_valid())
        response_target = self.client.post(
            url_target, post_contents)
        print(response_target)

        # テスト結果を確認
        # self.assertEqual(post_contents, response_target.content)

        self.assertEqual(302, response_target.status_code)

    # def edit_match(self):
    #     # ダミーデータをCompetitionに追加
    #     url_add_competition = reverse('cms:add_competition')
    #     data_competition = {
    #         'name': 'test_name',
    #         'competition_type': 'test_type'
    #     }
    #     self.client.post(
    #         url_add_competition, data_competition)

    #     # ダミーデータをmatchに追加
    #     competition = Competition.objects.get(id=1)
    #     Match.objects.create(
    #         competition=competition, name='test_match_name')

    #     # テスト対象を実行
    #     data_target = {
    #         'competition': str(competition),
    #         'name': 'test_match_name2'
    #     }
    #     url_target = reverse('cms:edit_competition', kwargs=data_target)

    #     # POST実行
    #     response_target = self.client.post(url_target)

    #     # テスト結果を確認
    #     self.assertEqual(1000, response_target.status_code)
