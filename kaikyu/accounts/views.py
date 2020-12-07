from django.shortcuts import render, redirect
from django.contrib.auth.mixins import LoginRequiredMixin
from django.contrib.auth.views import(
    LoginView, LogoutView
)
from django.http import HttpResponse

from . forms import LoginForm


# class Login(LoginView):
#     form_class = LoginForm
#     template_name = 'login.html'


# class Logout(LoginRequiredMixin, LogoutView):
#     template_name = 'login.html'


# def index(request):
#     return redirect(request, '/cms/home/html')
