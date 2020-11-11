from django.shortcuts import render, get_object_or_404, redirect
from django.http import HttpResponse
from django.urls import reverse
from urllib.parse import urlencode

from cms.models import Competition
from cms.forms import CompetitionForm
from cms.models import Match
from cms.forms import MatchForm
from cms.models import Hit
from cms.forms import HitForm
from cms.models import Player

import copy


def home(request):
    competitions = Competition.objects.all().order_by('id')
    # return render(request, 'cms/home.html', {'competitions': competitions})
    return render(request, 'cms/competition_list.html', {'competitions': competitions})


def competition_list(request):
    competitions = Competition.objects.all().order_by('id')
    return render(request, 'cms/competition_list.html', {'competitions': competitions})


def edit_competition(request, competition_id=None):
    # return HttpResponse('aiai')
    if competition_id:
        competition = get_object_or_404(Competition, pk=competition_id)
    else:
        competition = Competition()

    if request.method == 'POST':
        form = CompetitionForm(request.POST, instance=competition)
        if form.is_valid():
            competition = form.save(commit=False)
            competition.save()
            return redirect('cms:competition_list')
    else:
        form = CompetitionForm(instance=competition)

    return render(request, 'cms/edit_competition.html', dict(form=form, competition_id=competition_id))


def delete_competition(request, competition_id):
    competition = get_object_or_404(Competition, pk=competition_id)
    competition.delete()
    return redirect('cms:competition_list')


def match_list(request, competition_id):
    matches = Match.objects.filter(
        competition_id=competition_id).values()

    return render(request, 'cms/match_list.html', {'matches': matches, 'competition_id': competition_id})


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
            name='', competition=Competition.objects.get(id=competition_id))
        form = MatchForm(instance=match, initial=initial_dict)

    return render(request, 'cms/edit_match.html', dict(form=form, competition_id=competition_id, match_id=match_id))


def delete_match(request, competition_id, match_id):
    match = get_object_or_404(Match, pk=match_id)
    match.delete()
    matches = Match.objects.all().order_by('id')
    return render(request, 'cms/match_list.html', dict(matches=matches, competition_id=competition_id))


def edit_hit(request, competition_id, match_id):
    hits = None
    return render(request, 'cms/edit_hit.html', dict(hits=hits, competition_id=competition_id, match_id=match_id, shots=[4, 3, 2, 1], shooting_order=[3, 2, 1, 3, 2, 1]))


def get_players(request, competition_id, match_id):
    players_id = request.POST.getlist('player_id')
    players = []
    for player_id in players_id:
        players.append(get_object_or_404(Player, pk=player_id))

    return render(request, 'cms/edit_hit.html', dict(players=players, competition_id=competition_id, match_id=match_id, shots=[4, 3, 2, 1], shoot_order=[3, 2, 1, 3, 2, 1]))


def save_hit(request, competition_id, match_id):
    matches = Match.objects.filter(
        competition_id=competition_id).values()

    existing_hit_records = Hit.objects.filter(match_id=match_id).values()
    if existing_hit_records.count():
        # 更新処理
        pass
    else:
        # 新規追加
        player_ids = request.POST.getlist('player_ids')
        grounds = request.POST.getlist('grounds'),
        shoot_orders = request.POST.getlist('shoot_orders'),
        hit_records_post = request.POST.getlist('hit_records')

        current_player_hit_record = ['×', '×', '×', '×']
        hit_records = []
        NUM_PLAYER = 6
        NUM_SHOT = 4
        for player in range(NUM_PLAYER):
            for shot in range(NUM_SHOT):
                current_player_hit_record[shot] = hit_records_post[
                    (NUM_SHOT-shot-1) * NUM_PLAYER]

            hit_records.append(copy.deepcopy(current_player_hit_record))

        # 記録の保存
        for player in range(len(player_ids)):
            hit_form_dict = dict(
                competition=Competition.objects.get(id=competition_id),
                match=Match.objects.get(id=match_id),
                player=Player.objects.get(id=player_ids[player]),
                ground=grounds[0][player],
                shoot_order=shoot_orders[0][player],
                hit=hit_records[player])
            hit = Hit()
            form = HitForm(hit_form_dict, instance=hit)
            if form.is_valid():
                hit.save()

    matches = Match.objects.all().order_by('id')
    return render(request, 'cms/match_list.html', {'matches': matches, 'competition_id': competition_id})
