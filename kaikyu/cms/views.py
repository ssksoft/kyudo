from django.shortcuts import render, get_object_or_404, redirect
from django.http import HttpResponse, HttpResponseRedirect
from django.urls import reverse
from urllib.parse import urlencode

from cms.models import Competition
from cms.forms import CompetitionForm
from cms.models import Match
from cms.forms import MatchForm
from cms.models import Hit
from cms.forms import HitForm
from cms.models import Player
from cms.forms import PlayerForm

from accounts.models import CustomUser

from accounts.models import UserGroup
from accounts.forms import UserGroupForm
from accounts.models import UserAndGroup
from accounts.forms import UserAndGroupForm

import re
from django.contrib.auth import authenticate, login as django_login
from django.contrib.auth.decorators import login_required

import copy

from django.db import transaction

from django.core.exceptions import PermissionDenied
from django.http import Http404


def home(request):
    competitions = Competition.objects.all().order_by('id')
    return redirect('cms:competition_list')


def competition_list(request):
    competitions = Competition.objects.all().order_by('id')
    return render(request, 'cms/competition_list.html', {'competitions': competitions})


@login_required
def add_competition(request):
    if request.method == 'POST':
        with transaction.atomic():
            competition = Competition()
            post_content = request.POST
            competition_id = save_competition(post_content, competition)
            usergroup_id = add_usergroup(competition_id)
            userandgroup_id = add_userandgroup(usergroup_id, request.user.id)

            if(int(userandgroup_id) != -1):
                competitions = Competition.objects.all().order_by('id')
                return redirect('cms:competition_list')
            else:
                raise PermissionDenied
    else:
        raise Http404


def save_competition(post_content, competition):
    form = CompetitionForm(post_content, instance=competition)
    if form.is_valid():
        competition = form.save(commit=True)
        latest_record_pk = Competition.objects.order_by(
            'id').reverse().first().id
    else:
        latest_record_pk = -1

    return latest_record_pk


def add_usergroup(competition_id):
    if(int(competition_id) != -1):
        user_group = UserGroup()
        usergroup_form_dict = {}
        usergroup_form_dict['competition'] = Competition.objects.get(
            id=competition_id)
        form = UserGroupForm(usergroup_form_dict, instance=user_group)

        if form.is_valid():
            form.save()
            latest_record_pk = UserGroup.objects.order_by(
                'id').reverse().first().id
            return latest_record_pk
        else:
            return -1
    else:
        return -1


def add_userandgroup(usergroup_pk, user_pk):
    if(int(usergroup_pk) != -1):
        userandgroup = UserAndGroup()
        userandgroup_form_dict = {}
        userandgroup_form_dict['user_group'] = UserGroup.objects.get(
            id=usergroup_pk)
        userandgroup_form_dict['user'] = CustomUser.objects.get(
            id=user_pk)
        form = UserAndGroupForm(userandgroup_form_dict, instance=userandgroup)

        if form.is_valid():
            form.save()
            latest_record_pk = UserAndGroup.objects.order_by(
                'id').reverse().first().id
            return latest_record_pk
        else:
            return -1
    else:
        return -1


@login_required
def edit_competition(request, competition_id):
    competition = get_object_or_404(Competition, pk=competition_id)
    if request.method == 'POST':
        saved_pk = save_competition(request, competition)
    else:
        pass
    form = CompetitionForm(instance=competition)
    return render(request, 'cms/edit_competition.html', dict(form=form, competition_id=competition_id))


@login_required
def delete_competition(request, competition_id):
    current_login_user_id = request.user.id
    num_current_login_user_in_userandgroup = UserAndGroup.objects.filter(
        user=current_login_user_id)
    if num_current_login_user_in_userandgroup:
        competition = get_object_or_404(Competition, pk=competition_id)
        competition.delete()
    else:
        raise PermissionDenied
    return redirect('cms:competition_list')


def match_list(request, competition_id):
    matches = Match.objects.filter(
        competition_id=competition_id).values()

    return render(request, 'cms/match_list.html', {'matches': matches, 'competition_id': competition_id})


@login_required
def edit_match(request, competition_id, match_id=None):
    if match_id:
        match = get_object_or_404(Match, pk=match_id)
    else:
        match = Match()

    if request.method == 'POST':
        form = MatchForm(request.POST, instance=match)
        if form.is_valid():
            match = form.save(commit=False)
            match.save()
            matches = Match.objects.all().order_by('id')
            return render(request, 'cms/match_list.html', dict(matches=matches, competition_id=competition_id))
    else:
        initial_dict = dict(
            name=match.name,
            competition=Competition.objects.get(id=competition_id))
        form = MatchForm(instance=match, initial=initial_dict)

    return render(request, 'cms/edit_match.html', dict(form=form, competition_id=competition_id, match_id=match_id))


@login_required
def delete_match(request, competition_id, match_id):
    match = get_object_or_404(Match, pk=match_id)
    match.delete()
    matches = Match.objects.all().order_by('id')
    return render(request, 'cms/match_list.html', dict(matches=matches, competition_id=competition_id))


def edit_hit(request, competition_id, match_id):
    NUM_HIT = 4
    NUM_PLAYER = 6

    # 記録の編集
    if Hit.objects.filter(match_id=match_id).exists():
        hits = Hit.objects.filter(match_id=match_id).order_by('id').values()
        player_ids = []
        for current_record in range(len(hits)):
            player_ids.append(hits[current_record]['player_id'])

        players = []
        for player_id in player_ids:
            players.append(get_object_or_404(Player, pk=player_id))

        current_row_hits = ['×'] * NUM_PLAYER
        existing_hits = []

        for current_shot in range(NUM_HIT-1, -1, -1):
            for current_player in range(NUM_PLAYER):
                current_row_hits[current_player] = hits[current_player]['hit'][current_shot]
            existing_hits.append({
                'shot_num': current_shot+1,
                'hit': copy.deepcopy(current_row_hits)
            })

    # 記録の追加
    else:
        player_ids = ['']*NUM_PLAYER
        return render(request, 'cms/input_playerid.html', dict(player_ids=player_ids, competition_id=competition_id, match_id=match_id, shots=[4, 3, 2, 1], shoot_order=[3, 2, 1, 3, 2, 1], columns=[0, 1, 2, 3, 4, 5]))

    return render(request, 'cms/edit_hit.html', dict(players=players, competition_id=competition_id, match_id=match_id, shots=[4, 3, 2, 1], shoot_order=[3, 2, 1, 3, 2, 1], columns=[0, 1, 2, 3, 4, 5], existing_hits=existing_hits))


def get_players(request, competition_id, match_id):
    players_id = request.POST.getlist('player_id')
    players = []
    for player_id in players_id:
        players.append(get_object_or_404(Player, pk=player_id))

    return render(request, 'cms/edit_hit.html', dict(players=players, competition_id=competition_id, match_id=match_id, shots=[4, 3, 2, 1], shoot_order=[3, 2, 1, 3, 2, 1]))


@login_required
def save_hit(request, competition_id, match_id):
    # 記録画面からデータを取得
    player_ids = request.POST.getlist('player_ids')
    grounds = request.POST.getlist('grounds'),
    shoot_orders = request.POST.getlist('shoot_orders'),
    hit_records_post = request.POST.getlist('hit_records')

    # 的中記録の整形
    current_player_hit_record = ['×', '×', '×', '×']
    hit_records = []
    NUM_PLAYER = 6
    NUM_SHOT = 4
    for player in range(NUM_PLAYER):
        for shot in range(NUM_SHOT):
            current_shot = hit_records_post[(
                NUM_SHOT-1-shot) * NUM_PLAYER + player]
            if current_shot == ' ':
                current_player_hit_record[shot] = '-'
            else:
                current_player_hit_record[shot] = current_shot

        hit_records.append(copy.deepcopy(''.join(current_player_hit_record)))

    # 記録の保存
    hit_form_dict = []
    a = 0
    existing_hit_records = Hit.objects.filter(match_id=match_id)
    num_existing_record = existing_hit_records.count()

    for player in range(len(player_ids)):
        # 現在の選手の的中記録を辞書型で整理
        hit_form_dict = dict(
            competition=Competition.objects.get(id=competition_id),
            match=Match.objects.get(id=match_id),
            player=Player.objects.get(id=player_ids[player]),
            ground=grounds[0][player],
            shoot_order=shoot_orders[0][player],
            hit=hit_records[player])

        if num_existing_record > 0:
            # 更新処理
            hits = Hit.objects.filter(
                match_id=match_id).order_by('id').values()
            hit_id = hits[player]['id']
            hit = get_object_or_404(Hit, pk=hit_id)
            form = HitForm(hit_form_dict, instance=hit)

        else:
            # 新規追加
            hit = Hit()
            form = HitForm(hit_form_dict, instance=hit)

        if form.is_valid():
            a = a+1
            hit_save_obj = form.save()
            hit_save_obj.save()

    matches = Match.objects.filter(
        competition_id=competition_id).values()
    return render(request, 'cms/match_list.html', {'matches': matches, 'competition_id': competition_id})


def player_list(request, competition_id):
    players = Player.objects.filter(
        competition_id=competition_id).values()

    return render(request, 'cms/player_list.html', {'players': players, 'competition_id': competition_id})


@login_required
def edit_player(request, competition_id, player_id=None):
    if player_id:
        player = get_object_or_404(Player, pk=player_id)
    else:
        player = Player()

    if request.method == 'POST':
        form = PlayerForm(request.POST, instance=player)
        if form.is_valid():
            player = form.save(commit=False)
            player.save()

            players = Player.objects.filter(
                competition_id=competition_id).values()
            return render(request, 'cms/player_list.html', {'players': players, 'competition_id': competition_id})
    else:
        initial_dict = dict(
            competition=Competition.objects.get(id=competition_id),
            name=player.name,
            team_name=player.team_name,
            dan=player.dan,
            rank=player.rank)
        form = PlayerForm(instance=player, initial=initial_dict)

    return render(request, 'cms/edit_player.html', dict(form=form, competition_id=competition_id, player_id=player_id))


@login_required
def change_player(request, competition_id, match_id):
    player_ids = request.POST.getlist('player_ids')
    return render(request, 'cms/input_playerid.html', dict(player_ids=player_ids, competition_id=competition_id, match_id=match_id, shots=[4, 3, 2, 1], shoot_order=[3, 2, 1, 3, 2, 1], columns=[0, 1, 2, 3, 4, 5]))


@login_required
def delete_player(request, competition_id, player_id):
    player = get_object_or_404(Player, pk=player_id)
    player.delete()
    players = Player.objects.all().order_by('id')
    return render(request, 'cms/player_list.html', dict(players=players, competition_id=competition_id))
