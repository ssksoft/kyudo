from django.forms import ModelForm
from cms.models import Competition


class CompetitionForm(ModelForm):
    class Meta:
        model = Competition
        fields = ('name', 'competition_type')
