from django.shortcuts import render, redirect
from django.contrib.auth.mixins import LoginRequiredMixin
from django.contrib.auth.views import(
    LoginView, LogoutView
)
from django.http import HttpResponse

from . forms import LoginForm
