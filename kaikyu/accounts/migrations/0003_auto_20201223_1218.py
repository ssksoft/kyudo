# Generated by Django 3.1.4 on 2020-12-23 03:18

from django.conf import settings
from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('accounts', '0002_auto_20201220_1659'),
    ]

    operations = [
        migrations.RemoveField(
            model_name='usergroup',
            name='user_group',
        ),
        migrations.CreateModel(
            name='UserAndGroup',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('user', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to=settings.AUTH_USER_MODEL, verbose_name='ユーザ')),
                ('user_group', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='accounts.usergroup', verbose_name='ユーザグループ')),
            ],
        ),
    ]
