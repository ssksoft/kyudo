from django.shortcuts import render, get_object_or_404, redirect
from django.http import HttpResponse

from cms.models import Competition
from cms.forms import CompetitionForm


def home(request):
    competitions = Competition.objects.all().order_by('id')
    return render(request, 'cms/home.html', {'competitions': competitions})


def edit_competition(request, competition_id=None):
    return HttpResponse('aiai')
    # if competition_id:
    #     competition = get_object_or_404(Competition, pk=competition_id)
    # else:
    #     competition = Competition()

    # if request.method == 'POST':
    #     form = CompetitionForm(request.POST, instance=competition)
    #     if form.is_valid():
    #         book = form.save(commit=False)
    #         book.save()
    #         return redirect('cms:competition_list')
    # else:
    #     form = CompetitionForm(instance=competition)

    # return render(request, 'cms/edit_compatition.html', dict(form=form, competition_id=competition_id))


# def delete_competition(request, competition_id):
#     competition = get_object_or_404(Competition, pk=competition_id)
#     competition.delete()
#     return redirect('cms:home')
