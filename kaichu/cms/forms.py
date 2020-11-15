from django.forms import ModelForm
from cms.models import Competition
from cms.models import Match
from cms.models import Hit
from cms.models import Player


class CompetitionForm(ModelForm):
    class Meta:
        model = Competition
        fields = ('name', 'competition_type')


class MatchForm(ModelForm):
    class Meta:
        model = Match
        fields = ('name', 'competition')


class HitForm(ModelForm):
    class Meta:
        model = Hit
        fields = ('competition', 'match', 'player',
                  'ground', 'shoot_order', 'hit')


class PlayerForm(ModelForm):
    class Meta:
        model = Player
        fields = ('competition', 'name', 'team_name',
                  'dan', 'rank')
