# Generated by Django 3.0.2 on 2020-10-25 08:57

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('cms', '0002_auto_20201025_1754'),
    ]

    operations = [
        migrations.AlterField(
            model_name='competition',
            name='competition_type',
            field=models.CharField(max_length=255, null=True, verbose_name='大会種別'),
        ),
    ]