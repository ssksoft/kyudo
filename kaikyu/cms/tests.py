from django.test import TestCase
from django.urls import reverse
from accounts.models import CustomUser, UserGroup
from .models import Competition, Player
from .forms import PlayerForm
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
        self.assertEqual(200, response.status_code)

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
        save_competition(post_content)

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


class EditCompetitionTests(TestCase):
    def test_get(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        post_contents_add_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        response = self.client.post(
            url_add_competition, post_contents_add_competition)

        # テスト対象を実行
        args_url = {
            'competition_id': 1
        }
        url_edit_competition = reverse('cms:edit_competition', kwargs=args_url)
        response_edit_competition = self.client.get(url_edit_competition)

        # テスト結果を確認
        print(response_edit_competition.status_code)
        self.assertEqual(200, response_edit_competition.status_code)

    def test_get_without_login(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        Competition.objects.create(
            name='test_name', competition_type='test_type')

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        args_url = {
            'competition_id': 1
        }
        url_edit_competition = reverse('cms:edit_competition', kwargs=args_url)
        response = self.client.get(url_edit_competition)

        # リダイレクト先が期待通りであることを確認
        expected_url = expected_url = settings.LOGIN_URL + '?next=' + url_edit_competition
        self.assertRedirects(response, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_get_with_unauthorized_user(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        Competition.objects.create(
            name='test_name', competition_type='test_type')

        competition_test = Competition.objects.get(id=1)

        # ログアウト
        self.client.logout()

        # 非認証ユーザでログイン
        self.client.force_login(
            CustomUser.objects.create_user('unauthorized_user'))

        # テスト対象を実行
        args_url = {
            'competition_id': 1
        }
        url_edit_competition = reverse('cms:edit_competition', kwargs=args_url)
        response = self.client.get(url_edit_competition)

        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:notice_unauthorized_user')
        self.assertRedirects(response, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_post(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        post_contents_add_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        response = self.client.post(
            url_add_competition, post_contents_add_competition)

        # テスト対象を実行
        args_url = {
            'competition_id': 1
        }
        url_edit_competition = reverse('cms:edit_competition', kwargs=args_url)
        post_contents_edit_competition = {
            'name': 'edited_name',
            'competition_type': 'test_type'
        }

        response_edit_competition = self.client.post(
            url_edit_competition, post_contents_edit_competition)

        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:competition_list')
        self.assertRedirects(response, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_post_without_login(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        post_contents_add_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        response_add_competition = self.client.post(
            url_add_competition, post_contents_add_competition)

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        args_url = {
            'competition_id': 1
        }
        url_edit_competition = reverse('cms:edit_competition', kwargs=args_url)
        post_contents_edit_competition = {
            'name': 'edited_name',
            'competition_type': 'test_type'
        }

        response_edit_competition = self.client.post(
            url_edit_competition, post_contents_edit_competition)

        # リダイレクト先が期待通りであることを確認
        expected_url = expected_url = settings.LOGIN_URL + '?next=' + url_edit_competition
        self.assertRedirects(response_edit_competition, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)


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


class AddMatchTests(TestCase):
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
        # リダイレクト先が期待通りであることを確認
        expected_url = settings.LOGIN_URL + '?next=' + url_target

        self.assertRedirects(response_target, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

        # レコードが追加されていないことを確認
        num_record_match = Match.objects.all().count()
        self.assertEqual(0, num_record_match)

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
            CustomUser.objects.create_user('unauthorized_tester'))

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


class EditMatchTests(TestCase):
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

        # リダイレクト先が期待通りであることを確認
        url_match_list = reverse('cms:match_list', args=[competition.id])
        expected_url = url_match_list
        self.assertRedirects(response_edit, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

        # レコードが保存されていることを確認
        self.assertEqual(post_contents_edit['name'], match_after_edit.name)
        num_record_match = Match.objects.all().count()
        self.assertEqual(1, num_record_match)

    def test_edit_match_without_login(self):
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
        competition = Competition.objects.get(id=1)

        competition_id_dict = {
            'competition_id': competition.id
        }
        url_add_match = reverse('cms:add_match', kwargs=competition_id_dict)
        post_contents_add = {
            'competition': competition.id,
            'name': 'added_match_name'
        }
        response_add = self.client.post(url_add_match, post_contents_add)

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        match_before_edit = Match.objects.get(id=1)
        arguments_edit = {
            'competition_id': competition.id,
            'match_id': match_before_edit.id
        }
        url_edit_match = reverse('cms:edit_match', kwargs=arguments_edit)

        # POST実行
        post_contents_edit = {
            'competition': competition.id,
            'name': 'edited_match_name'
        }
        response_target = self.client.post(url_edit_match, post_contents_edit)

        # テスト結果を確認(TODO:matchオブジェクトのcompetitionとnameの値も期待通りか確認したい)
        # リダイレクト先が期待通りであることを確認
        expected_url = settings.LOGIN_URL + '?next=' + url_edit_match

        self.assertRedirects(response_target, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

        # レコードの数に変化がないことを確認
        num_record_match = Match.objects.all().count()
        self.assertEqual(1, num_record_match)

        # レコードが編集されていないことを確認
        match_after_edit = Match.objects.get(id=1)
        self.assertEqual(match_before_edit.name, match_after_edit.name)

    def test_edit_match_with_unauthorized_user(self):
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
        competition = Competition.objects.get(id=1)

        competition_id_dict = {
            'competition_id': competition.id
        }
        url_add_match = reverse('cms:add_match', kwargs=competition_id_dict)
        post_contents_add = {
            'competition': competition.id,
            'name': 'added_match_name'
        }
        response_add = self.client.post(url_add_match, post_contents_add)

        # ログアウト
        self.client.logout()

        # 権限のないユーザでログイン
        self.client.force_login(
            CustomUser.objects.create_user('not_authorized_tester'))

        # テスト対象を実行
        match_before_edit = Match.objects.get(id=1)
        arguments_edit = {
            'competition_id': competition.id,
            'match_id': match_before_edit.id
        }
        url_edit_match = reverse('cms:edit_match', kwargs=arguments_edit)

        # POST実行
        post_contents_edit = {
            'competition': competition.id,
            'name': 'edited_match_name'
        }
        response_target = self.client.post(url_edit_match, post_contents_edit)

        # テスト結果を確認(TODO:matchオブジェクトのcompetitionとnameの値も期待通りか確認したい)
        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:notice_unauthorized_user')

        self.assertRedirects(response_target, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

        # レコードの数に変化がないことを確認
        num_record_match = Match.objects.all().count()
        self.assertEqual(1, num_record_match)

        # レコードが編集されていないことを確認
        match_after_edit = Match.objects.get(id=1)
        self.assertEqual(match_before_edit.name, match_after_edit.name)


class NoticeUnauthorizedUserTests(TestCase):
    def test_render(self):
        target_url = reverse('cms:notice_unauthorized_user')

        # テスト対象を実行
        response_target = self.client.get(target_url)
        self.assertEqual(200, response_target.status_code)


class IsAuthorizedUserTests(TestCase):
    def test_authorized_user(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        request = self.client.post(
            url_add_competition, data_competition)

        # テスト対象を実行
        competition_id = 1
        current_login_user = CustomUser.objects.get(id=1)
        self.assertTrue(is_authorized_user(competition_id, current_login_user))

    def test_unauthorized_user(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        request = self.client.post(
            url_add_competition, data_competition)

        # ログアウト
        self.client.logout()

        # 権限のないユーザでログイン
        self.client.force_login(
            CustomUser.objects.create_user('unauthorized_tester'))

        # テスト対象を実行
        competition_id = 1
        current_login_user = CustomUser.objects.get(id=2)
        self.assertFalse(is_authorized_user(
            competition_id, current_login_user))


class DeleteMatchTests(TestCase):
    def test_delete(self):
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
        self.client.post(url_add_match, post_contents_add)
        num_record_match_before_delete = Match.objects.all().count()

        # テスト対象を実行
        args_delete_match = {
            'competition_id': 1,
            'match_id': 1
        }
        url_delete_match = reverse(
            'cms:delete_match', kwargs=args_delete_match)
        self.client.post(
            url_delete_match, data_competition)

        num_record_match_after_delete = Match.objects.all().count()
        self.assertEqual(1, num_record_match_before_delete)
        self.assertEqual(0, num_record_match_after_delete)

    def test_delete_without_login(self):
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
        self.client.post(url_add_match, post_contents_add)
        num_record_match_before_delete = Match.objects.all().count()

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        args_delete_match = {
            'competition_id': 1,
            'match_id': 1
        }
        url_delete_match = reverse(
            'cms:delete_match', kwargs=args_delete_match)
        self.client.post(
            url_delete_match, data_competition)

        num_record_match_after_delete = Match.objects.all().count()
        self.assertEqual(1, num_record_match_before_delete)
        self.assertEqual(1, num_record_match_after_delete)

    def test_delete_with_unauthorized_user(self):
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
        self.client.post(url_add_match, post_contents_add)
        num_record_match_before_delete = Match.objects.all().count()

        # ログアウト
        self.client.logout()

        # 非認証ユーザでログイン
        self.client.force_login(
            CustomUser.objects.create_user('unauthorized_user'))

        # テスト対象を実行
        args_delete_match = {
            'competition_id': 1,
            'match_id': 1
        }
        url_delete_match = reverse(
            'cms:delete_match', kwargs=args_delete_match)
        response_delete = self.client.post(
            url_delete_match, data_competition)

        # 試合が削除されていないことを確認
        num_record_match_after_delete = Match.objects.all().count()
        self.assertEqual(1, num_record_match_before_delete)
        self.assertEqual(1, num_record_match_after_delete)

        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:notice_unauthorized_user')
        self.assertRedirects(response_delete, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)


class AddPlayerTests(TestCase):
    def test_add_player_get(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        self.client.post(url_add_competition, data_competition)

        # テスト対象を実行
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        response_add_player = self.client.get(url_add_player)
        self.assertEqual(200, response_add_player.status_code)

    def test_add_player_post(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # テスト対象を実行
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        player = Player
        competition = Competition.objects.get(id=1)

        post_contents = {
            'competition': competition.id,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        response_add_player = self.client.post(url_add_player, post_contents)

        # リダイレクト先が期待通りであることを確認
        args_player_list = {
            'competition_id': 1,
        }
        expected_url = reverse(
            'cms:player_list', kwargs=args_player_list)
        self.assertRedirects(response_add_player, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_add_player_post_invalid(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # テスト対象を実行
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        player = Player
        competition = Competition.objects.get(id=1)

        post_contents = {
            'competition': 2,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        response_add_player = self.client.post(url_add_player, post_contents)

        # 表示内容が期待通りであることを確認
        args_player_list = {
            'competition_id': 1,
        }
        expected_url = reverse(
            'cms:player_list', kwargs=args_player_list)
        self.assertEqual(404, response_add_player.status_code)

    def test_add_player_get_without_login(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        self.client.post(url_add_competition, data_competition)

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        args_add_player = {
            'competition_id': 1
        }

        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        response_add_player = self.client.get(url_add_player)

        # リダイレクト先が期待通りであることを確認
        expected_url = settings.LOGIN_URL + '?next=' + url_add_player
        self.assertRedirects(response_add_player, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_add_player_get_with_unauthorized_user(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_name',
            'competition_type': 'test_type'
        }
        self.client.post(url_add_competition, data_competition)

        # ログアウト
        self.client.logout()

        # ログイン
        self.client.force_login(
            CustomUser.objects.create_user('unauthorized_user'))

        # テスト対象を実行
        args_add_player = {
            'competition_id': 1
        }

        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        response_add_player = self.client.get(url_add_player)

        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:notice_unauthorized_user')
        self.assertRedirects(response_add_player, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_add_player_post_without_login(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        player = Player
        competition = Competition.objects.get(id=1)

        post_contents = {
            'competition': 2,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        response_add_player = self.client.post(url_add_player, post_contents)

        # 表示内容が期待通りであることを確認
        args_player_list = {
            'competition_id': 1,
        }

        # リダイレクト先が期待通りであることを確認
        expected_url = settings.LOGIN_URL + '?next=' + url_add_player
        self.assertRedirects(response_add_player, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_add_player_post_with_unauthorized_user(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ログアウト
        self.client.logout()

        # ログイン
        self.client.force_login(
            CustomUser.objects.create_user('unauthorized_user'))

        # テスト対象を実行
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        player = Player
        competition = Competition.objects.get(id=1)

        post_contents = {
            'competition': 2,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        response_add_player = self.client.post(url_add_player, post_contents)

        # 表示内容が期待通りであることを確認
        args_player_list = {
            'competition_id': 1,
        }

        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:notice_unauthorized_user')
        self.assertRedirects(response_add_player, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)


class EditPlayerTests(TestCase):
    def test_edit_player_get(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ダミーデータをPlayerに追加
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        add_player_post_contents = {
            'competition': 1,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        self.client.post(url_add_player, add_player_post_contents)

        # テスト対象を実行
        args_edit_player = {
            'competition_id': 1,
            'player_id': 1
        }
        url_edit_player = reverse('cms:edit_player', kwargs=args_edit_player)
        response_edit_player = self.client.get(url_edit_player)
        self.assertEqual(200, response_edit_player.status_code)

    def test_edit_player_post(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ダミーデータをPlayerに追加
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        post_contents_add_player = {
            'competition': 1,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        self.client.post(url_add_player, post_contents_add_player)

        # テスト対象を実行
        get_args_edit_player = {
            'competition_id': 1,
            'player_id': 1
        }

        post_contents_edit_player = {
            'competition': 1,
            'name': 'test_player_name22',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        url_edit_player = reverse(
            'cms:edit_player', kwargs=get_args_edit_player)
        response_edit_player = self.client.post(
            url_edit_player, post_contents_edit_player)
        self.assertEqual(302, response_edit_player.status_code)

        player = Player.objects.get(id=1)
        self.assertEqual(post_contents_edit_player['name'], player.name)

    def test_edit_player_get_without_login(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ダミーデータをPlayerに追加
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        add_player_post_contents = {
            'competition': 1,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        self.client.post(url_add_player, add_player_post_contents)

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        args_edit_player = {
            'competition_id': 1,
            'player_id': 1
        }
        url_edit_player = reverse('cms:edit_player', kwargs=args_edit_player)
        response_edit_player = self.client.get(url_edit_player)

        url_edit_player = reverse('cms:edit_player', kwargs=args_edit_player)
        response_edit_player = self.client.get(url_edit_player)
        self.assertEqual(200, response_edit_player.status_code)

    def test_edit_player_get_with_unauthorized_user(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ダミーデータをPlayerに追加
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        add_player_post_contents = {
            'competition': 1,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        self.client.post(url_add_player, add_player_post_contents)

        # ログアウト
        self.client.logout()

        # 非認証ユーザでログイン
        self.client.force_login(
            CustomUser.objects.create_user('unauthorized_user'))

        # テスト対象を実行
        args_edit_player = {
            'competition_id': 1,
            'player_id': 1
        }
        url_edit_player = reverse('cms:edit_player', kwargs=args_edit_player)
        response_edit_player = self.client.get(url_edit_player)

        response_edit_player = self.client.get(url_edit_player)
        self.assertEqual(200, response_edit_player.status_code)

    def test_edit_player_post_without_login(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ダミーデータをPlayerに追加
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        post_contents_add_player = {
            'competition': 1,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        self.client.post(url_add_player, post_contents_add_player)

        # ログアウト
        self.client.logout()

        # テスト対象を実行
        get_args_edit_player = {
            'competition_id': 1,
            'player_id': 1
        }

        post_contents_edit_player = {
            'competition': 1,
            'name': 'test_player_name22',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        url_edit_player = reverse(
            'cms:edit_player', kwargs=get_args_edit_player)
        response_edit_player = self.client.post(
            url_edit_player, post_contents_edit_player)

        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:notice_unauthorized_user')
        self.assertRedirects(response_edit_player, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

        # TODO：notice_unauthorized_user画面にログインへのリンクを表示させたい

    def test_edit_player_post_without_login(self):
        # ログイン
        self.client.force_login(CustomUser.objects.create_user('tester'))

        # ダミーデータをCompetitionに追加
        url_add_competition = reverse('cms:add_competition')
        data_competition = {
            'name': 'test_competition',
            'competition_type': 'test_type'
        }
        self.client.post(
            url_add_competition, data_competition)

        # ダミーデータをPlayerに追加
        args_add_player = {
            'competition_id': 1
        }
        url_add_player = reverse('cms:add_player', kwargs=args_add_player)
        post_contents_add_player = {
            'competition': 1,
            'name': 'test_player_name',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        self.client.post(url_add_player, post_contents_add_player)

        # ログアウト
        self.client.logout()

        # 非認証ユーザでログイン
        self.client.force_login(
            CustomUser.objects.create_user('unauthorized_user'))

        # テスト対象を実行
        get_args_edit_player = {
            'competition_id': 1,
            'player_id': 1
        }

        post_contents_edit_player = {
            'competition': 1,
            'name': 'test_player_name22',
            'team_name': 'test_team',
            'dan': '初段',
            'rank': '-'
        }
        url_edit_player = reverse(
            'cms:edit_player', kwargs=get_args_edit_player)
        response_edit_player = self.client.post(
            url_edit_player, post_contents_edit_player)

        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:notice_unauthorized_user')
        self.assertRedirects(response_edit_player, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)


class EditHitTests(TestCase):
    def test_input_playerid_for_hit(self):
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
        self.client.post(url_add_match, post_contents_add)

        # テスト対象を実行
        args_add_hit = {
            'competition_id': 1,
            'match_id': 1
        }
        url_edit_hit = reverse('cms:edit_hit', kwargs=args_add_hit)
        response_add_hit = self.client.get(url_edit_hit)

        # リダイレクト先が期待通りであることを確認
        args_input_playerid = {
            'competition_id': 1,
            'match_id': 1,
            'NUM_PLAYER': 6
        }
        expected_url = reverse(
            'cms:input_playerid_for_hit', kwargs=args_input_playerid)
        self.assertRedirects(response_add_hit, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_input_playerid_for_hit_without_login(self):
        args_add_hit = {
            'competition_id': 1,
            'match_id': 1
        }
        url_edit_hit = reverse('cms:edit_hit', kwargs=args_add_hit)
        response_add_hit = self.client.get(url_edit_hit)

        # リダイレクト先が期待通りであることを確認
        expected_url = settings.LOGIN_URL + '?next=' + url_edit_hit
        self.assertRedirects(response_add_hit, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)

    def test_input_playerid_for_hit_with_unauthorized_user(self):
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
        self.client.post(url_add_match, post_contents_add)

        # ログアウト
        self.client.logout()

        # 権限のないユーザでログイン
        self.client.force_login(
            CustomUser.objects.create_user('unauthorized_tester'))

        # テスト対象を実行
        args_edit_hit = {
            'competition_id': 1,
            'match_id': 1
        }
        url_edit_hit = reverse('cms:edit_hit', kwargs=args_edit_hit)
        response_edit_hit = self.client.get(url_edit_hit)

        # リダイレクト先が期待通りであることを確認
        expected_url = reverse('cms:notice_unauthorized_user')
        self.assertRedirects(response_edit_hit, expected_url,
                             status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)


class AddHitTests(TestCase):
    def test_add_hit_succsess(self):
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
        self.client.post(url_add_match, post_contents_add)

        # ダミーの選手登録

        # テスト対象を実行
        args_add_hit = {
            'competition_id': 1,
            'match_id': 1
        }
        # TODO：選手登録を事前に実施する必要あり
        post_contents_add_hit = {
            'player_id': [1, 2, 3, 4, 5, 6],
        }
        url_add_hit = reverse('cms:add_hit', kwargs=args_add_hit)
        response_add_hit = self.client.post(url_add_hit, post_contents_add_hit)
        # print(response_add_hit.content)
        # self.assertEqual(200, response_add_hit.status_code)

        # # リダイレクト先が期待通りであることを確認
        # args_render_edit_hit = {
        #     'competition_id': 1,
        #     'match_id': 1,
        #     'players': [0, 1, 2, 3, 4, 5],
        # }
        # expected_url = reverse('cms:render_edit_hit',
        #                        kwargs=args_render_edit_hit)
        # self.assertRedirects(response_add_hit, expected_url,
        #                      status_code=302, target_status_code=200, msg_prefix='', fetch_redirect_response=True)
    # def add_hit_without_playerid:
    # def add_hit_with_invalid_playerid:
