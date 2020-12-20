from django.db import models
from django.contrib.auth.models import AbstractUser


class CustomUser(AbstractUser):

    class Meta:
        verbose_name_plural = 'CustomUser'


class UserGroup(models.Model):

    class Meta:
        verbose_name_plural = 'UserGroup'

    user_group = models.ManyToManyField(CustomUser)
