from django.db import models


class Competition(models.Model):
    name = models.CharField('大会名', max_length=255)
    competition_type = models.CharField('大会種別', null=True, max_length=255)

    def __str__(self):
        return self.name


class Match(models.Model):
    competition = models.ForeignKey(
        Competition, verbose_name='大会', related_name='matches', on_delete=models.CASCADE)
    name = models.CharField('試合名', max_length=255)

    def __str__(self):
        return self.name


class Player(models.Model):
    competition = models.ForeignKey(
        Competition, verbose_name='大会', related_name='players', on_delete=models.CASCADE)
    name = models.CharField('選手名', max_length=255)
    team_name = models.CharField('団体名', max_length=255)
    dan = models.CharField('称号段位', max_length=255)
    rank = models.CharField('順位', max_length=255)

    def __str__(self):
        return self.name


class Hit(models.Model):
    competition = models.ForeignKey(
        Competition, verbose_name='大会', related_name='hits', on_delete=models.CASCADE)
    match = models.ForeignKey(Match, verbose_name='試合',
                              related_name='hits', on_delete=models.CASCADE)
    player = models.ForeignKey(
        Player, verbose_name='選手', related_name='hits', on_delete=models.CASCADE)
    ground = models.CharField('射場', max_length=255)
    shoot_order = models.CharField('立ち位置', max_length=255)
    hit = models.CharField('的中', max_length=255)

    def __str__(self):
        return self.hit


class User(models.Model):
    email = models.CharField('Eメール', max_length=255)
    password = models.CharField('パスワード', max_length=255)

    def __str__(self):
        return self.email
