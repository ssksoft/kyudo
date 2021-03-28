from django.db import models


class Competition(models.Model):
    name = models.CharField('大会名', max_length=255)
    competition_type = models.CharField('大会種別', null=True, max_length=255)

    def __str__(self):
        return self.name


class Match(models.Model):
    competition = models.ForeignKey(
        Competition, verbose_name='大会', related_name='match', on_delete=models.CASCADE)
    name = models.CharField('試合名', max_length=255)

    def __str__(self):
        return self.name


class Player(models.Model):

    DAN_CHOICES = [
        ("初段", "初段"),
        ("弐段", "弐段"),
        ("参段", "参段"),
        ("四段", "四段"),
        ("五段", "五段"),
        ("錬士五段", "錬士五段"),
        ("錬士六段", "錬士六段"),
        ("教士六段", "教士六段"),
        ("教士七段", "教士七段"),
        ("教士八段", "教士八段"),
        ("範士八段", "範士八段")
    ]

    competition = models.ForeignKey(
        Competition, verbose_name='大会', on_delete=models.CASCADE)
    name = models.CharField('選手名', max_length=255)
    team_name = models.CharField('団体名', max_length=255)
    dan = models.CharField('称号段位', max_length=255, choices=DAN_CHOICES)
    rank = models.CharField('順位', max_length=255)

    def __str__(self):
        return self.name


class Hit(models.Model):
    competition = models.ForeignKey(
        Competition, verbose_name='大会', on_delete=models.CASCADE)
    match = models.ForeignKey(Match, verbose_name='試合',
                              on_delete=models.CASCADE)
    player = models.ForeignKey(
        Player, verbose_name='選手', on_delete=models.CASCADE)
    ground = models.CharField('射場', max_length=255)
    shoot_order = models.CharField('立ち順', max_length=255)
    hit = models.CharField('的中', max_length=255)
    x_table = models.PositiveSmallIntegerField(
        verbose_name='テーブル上x座標', default=0)

    def __str__(self):
        return self.hit
