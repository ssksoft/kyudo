from django.db import models
from django.contrib.auth.models import AbstractUser
from cms.models import Competition


class CustomUser(AbstractUser):

    class Meta:
        verbose_name_plural = 'CustomUser'


class UserGroup(models.Model):

    class Meta:
        verbose_name_plural = 'UserGroup'

    competition = models.ForeignKey(
        Competition, verbose_name='大会', on_delete=models.CASCADE)


class UserAndGroup(models.Model):
    class Meta:
        verbose_name_plural = 'UserAndGroup'

    user_group = models.ForeignKey(
        UserGroup, verbose_name='ユーザグループ', on_delete=models.CASCADE)

    user = models.ForeignKey(
        CustomUser, verbose_name='ユーザ', on_delete=models.CASCADE)
