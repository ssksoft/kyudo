from django.test import TestCase
from django.urls import reverse
from accounts.models import CustomUser, UserGroup
from .models import Competition
from .forms import *
from .views import *
from django.views.generic import TemplateView
import requests
from kaikyu import settings


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
        data = {
            'competition_id': 1
        }
        url_target = reverse('cms:add_match', kwargs=data)

        # POST実行
        competition = Competition.objects.get(id=1)
        post_contents = {
            'competition': competition.id,
            'name': 'test_match_name'
        }
        response_target = self.client.post(url_target, post_contents)

        expected_url = reverse('cms:match_list', kwargs=data)
        self.assertRedirects(response_target, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

        match_after_add = Match.objects.get(id=1)
        self.assertEqual(post_contents['name'], match_after_add.name)

    def test_edit_match(self):
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

        # ダミーデータをMatchに追加
        competition_id = {
            'competition_id': 1
        }
        url_add_match = reverse('cms:add_match', kwargs=competition_id)
        competition = Competition.objects.get(id=1)
        post_contents_add = {
            'competition': competition.id,
            'name': 'added_match_name'
        }
        response_add = self.client.post(url_add_match, post_contents_add)

        # edit_match用URL用意
        match = Match.objects.get(id=1)
        arguments_edit = {
            'competition_id': competition.id,
            'match_id': match.id
        }
        url_edit_match = reverse('cms:edit_match', kwargs=arguments_edit)
        post_contents_edit = {
            'competition': competition.id,
            'name': 'edited_match_name'
        }

        # テスト対象を実行
        response_edit = self.client.post(url_edit_match,
                                         post_contents_edit)

        # テスト結果を確認
        match_after_edit = Match.objects.get(id=1)
        self.assertEqual(302, response_edit.status_code)
        self.assertEqual(post_contents_edit['name'], match_after_edit.name)

    def test_add_match_without_login(self):
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

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        data = {
            'competition_id': 1
        }
        url_target = reverse('cms:add_match', kwargs=data)

        # POST実行
        competition = Competition.objects.get(id=1)
        post_contents = {
            'competition': competition.id,
            'name': 'test_match_name'
        }
        response_target = self.client.post(url_target, post_contents)

        # テスト結果を確認(TODO:matchオブジェクトのcompetitionとnameの値も期待通りか確認したい)
        self.assertEqual(302, response_target.status_code)
        expected_url = settings.LOGIN_URL + '?next=' + url_target

        self.assertRedirects(response_target, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_add_match_with_unauthorized_user(self):
        # ログイン
        self.client.force_login(
            CustomUser.objects.create_user('authorized_tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ログアウト
        self.client.logout()

        # 権限のないユーザでログイン
        self.client.force_login(
            CustomUser.objects.create_user('not_authorized_tester'))

        # テスト対象を実行
        data = {
            'competition_id': 1
        }
        url_target = reverse('cms:add_match', kwargs=data)

        # POST実行
        competition = Competition.objects.get(id=1)
        post_contents = {
            'competition': competition.id,
            'name': 'test_match_name'
        }
        response_target = self.client.post(url_target, post_contents)

        # テスト結果を確認(TODO:matchオブジェクトのcompetitionとnameの値も期待通りか確認したい)
        # 遷移先の確認
        expected_url = reverse('cms:notice_unauthorized_user')
        self.assertRedirects(response_target, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

        # レコードが追加されていないことの確認
        num_record_match = Match.objects.all().count()
        self.assertEqual(0, num_record_match)
