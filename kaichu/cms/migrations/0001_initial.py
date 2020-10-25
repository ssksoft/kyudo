# Generated by Django 3.0.2 on 2020-10-25 08:51

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    initial = True

    dependencies = [
    ]

    operations = [
        migrations.CreateModel(
            name='Competition',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('name', models.CharField(max_length=255, verbose_name='大会名')),
                ('competition_type', models.CharField(max_length=255, verbose_name='大会種別')),
            ],
        ),
        migrations.CreateModel(
            name='User',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('email', models.CharField(max_length=255, verbose_name='Eメール')),
                ('password', models.CharField(max_length=255, verbose_name='パスワード')),
            ],
        ),
        migrations.CreateModel(
            name='Player',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('name', models.CharField(max_length=255, verbose_name='選手名')),
                ('team_name', models.CharField(max_length=255, verbose_name='団体名')),
                ('dan', models.CharField(max_length=255, verbose_name='段位')),
                ('rank', models.CharField(max_length=255, verbose_name='順位')),
                ('competition', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, related_name='players', to='cms.Competition', verbose_name='大会')),
            ],
        ),
        migrations.CreateModel(
            name='Match',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('name', models.CharField(max_length=255, verbose_name='試合名')),
                ('competition', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, related_name='matches', to='cms.Competition', verbose_name='大会')),
            ],
        ),
        migrations.CreateModel(
            name='Hit',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('ground', models.CharField(max_length=255, verbose_name='射場')),
                ('shoot_order', models.CharField(max_length=255, verbose_name='立ち位置')),
                ('hit', models.CharField(max_length=255, verbose_name='的中')),
                ('competition', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, related_name='hits', to='cms.Competition', verbose_name='大会')),
                ('match', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, related_name='hits', to='cms.Match', verbose_name='試合')),
                ('player', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, related_name='hits', to='cms.Player', verbose_name='選手')),
            ],
        ),
    ]
