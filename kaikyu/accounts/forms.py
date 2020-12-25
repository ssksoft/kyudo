from django.forms import ModelForm
from django import forms
from .models import UserGroup
from .models import UserAndGroup


class UserGroupForm(ModelForm):
    class Meta:
        model = UserGroup
        fields = ('id', 'competition')


# class UserAndGroup(ModelForm):
#     class Meta:
#         model = UserAndGroup
