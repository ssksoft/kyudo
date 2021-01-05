from django.db import models
from django.contrib.auth.models import (BaseUserManager, AbstractBaseUser)
from cms.models import Competition


# class CustomUser(AbstractUser):

#     class Meta:
#         verbose_name_plural = 'CustomUser'


# class UserGroup(models.Model):

#     class Meta:
#         verbose_name_plural = 'UserGroup'

#     competition = models.ForeignKey(
#         Competition,
#         default=0,
#         verbose_name='大会',
#         on_delete=models.CASCADE)


# class UserAndGroup(models.Model):
#     class Meta:
#         verbose_name_plural = 'UserAndGroup'

#     user_group = models.ForeignKey(
#         UserGroup, verbose_name='ユーザグループ', on_delete=models.CASCADE)

#     user = models.ForeignKey(
#         CustomUser, verbose_name='ユーザ', on_delete=models.CASCADE)


class UserManager(BaseUserManager):
    def create_user(self, email, date_of_birth, password=None):
        if not email:
            raise ValueError('Users must have an email address')

        user = self.model(
            email=self.normalize_email(email),
            date_of_birth=date_of_birth,
        )

        user.set_password(password)
        user.save(using=self._db)
        return user

    def create_superuser(self, email, date_of_birth, password):
        user = self.create_user(
            email,
            password=password,
            date_of_birth=date_of_birth,
        )
        user.is_admin = True
        user.save(using=self._db)
        return user


class CustomUser(AbstractBaseUser):
    email = models.EmailField(
        verbose_name='email',
        max_length=255,
        unique=True,
    )
    date_of_birth = models.DateField()
    is_active = models.BooleanField(default=True)
    is_admin = models.BooleanField(default=False)

    objects = UserManager()

    USERNAME_FIELD = 'email'
    REQUIRED_FIELDS = ['date_of_birth']

    def __str__(self):
        return self.email

    def has_perm(self, perm, obj=None):
        return True

    def has_module_perms(self, app_label):
        return True

    @property
    def is_staff(self):
        return self.is_admin
