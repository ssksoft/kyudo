from django.forms import ModelForm
from cms.models import Competition
from cms.models import Match


class CompetitionForm(ModelForm):
    class Meta:
        model = Competition
        fields = ('name', 'competition_type')


class MatchForm(ModelForm):
    class Meta:
        model = Match
        fields = ('name', 'competition')
