# Generated by Django 3.0.2 on 2020-10-25 08:54

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('cms', '0001_initial'),
    ]

    operations = [
        migrations.AlterField(
            model_name='competition',
            name='competition_type',
            field=models.CharField(blank=True, max_length=255, verbose_name='大会種別'),
        ),
    ]
