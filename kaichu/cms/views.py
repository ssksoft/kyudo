from django.shortcuts import render, get_object_or_404, redirect
from django.http import HttpResponse
from django.urls import reverse
from urllib.parse import urlencode

from cms.models import Competition
from cms.forms import CompetitionForm
from cms.models import Match
from cms.forms import MatchForm


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
    matches = Match.objects.all().order_by('id')
    # return render(request, 'cms/match_list/2', dict(matches=matches, competition_id=competition_id))
    return render(request, 'cms/match_list.html', {'matches': matches, 'competition_id': competition_id})


def edit_match(request, competition_id, match_id=None):
    competition_id = competition_id
    matches = Match.objects.all().order_by('id')
    if match_id:
        match = get_object_or_404(Match, pk=match_id)
    else:
        match = Match()

    if request.method == 'POST':
        form = MatchForm(request.POST, instance=match)
        if form.is_valid():
            match = form.save(commit=False)
            match.save()
            return render(request, 'cms/match_list/2', dict(matches=matches, competition_id=competition_id))
    else:
        form = MatchForm(instance=match)

    return render(request, 'cms/edit_match.html', dict(form=form, match_id=match_id))
    # return HttpResponse('Hi')
