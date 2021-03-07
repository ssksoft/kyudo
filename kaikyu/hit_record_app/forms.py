from django.forms import ModelForm
from hit_record_app.models import Competition
from hit_record_app.models import Match
from hit_record_app.models import Hit
from hit_record_app.models import Player
from django import forms


class CompetitionForm(ModelForm):
    class Meta:
        model = Competition
        fields = ('name', 'competition_type')


class MatchForm(ModelForm):
    def __init__(self, *args, **kwargs):
        super(MatchForm, self).__init__(*args, **kwargs)
        self.fields['competition'].widget.attrs['readonly'] = 'readonly'

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
        self.fields['competition'].widget.attrs['readonly'] = 'readonly'

    class Meta:
        model = Player
        fields = ('competition', 'name', 'team_name',
                  'dan', 'rank')
