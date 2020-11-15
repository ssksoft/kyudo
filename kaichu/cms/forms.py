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
    def __init__(self, *args, **kwargs):
        super(MatchForm, self).__init__(*args, **kwargs)
        self.fields['competition'].disabled = True

    class Meta:
        model = Match
        fields = ('competition', 'name')


class HitForm(ModelForm):
    class Meta:
        model = Hit
        fields = ('competition', 'match', 'player',
                  'ground', 'shoot_order', 'hit')


class PlayerForm(ModelForm):
    def __init__(self, *args, **kwargs):
        super(PlayerForm, self).__init__(*args, **kwargs)
        self.fields['competition'].disabled = True

    class Meta:
        model = Player
        fields = ('competition', 'name', 'team_name',
                  'dan', 'rank')
