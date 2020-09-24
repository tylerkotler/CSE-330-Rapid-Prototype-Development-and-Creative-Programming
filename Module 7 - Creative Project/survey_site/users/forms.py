from django import forms
from django.contrib.auth.models import User
from django.contrib.auth.forms import UserCreationForm
from .models import Profile

# form for when a user registers
class UserRegisterForm(UserCreationForm):
    email = forms.EmailField() #can put inside the () required=false to make it not required

    #keeps configurations in one place - the model affected is the user model, so form.save saves
    #to user model
    #fields are the fields we want in the register user form
    #this will be inherited in views.py
    class Meta:
        model = User
        fields = ['username', 'email', 'password1', 'password2']

# form for when a user updates their profile
class UserUpdateForm(forms.ModelForm):
    email = forms.EmailField()

    class Meta:
        model = User
        fields = ['username', 'email']

# form for when a user updates their profile picture
class ProfileUpdateForm(forms.ModelForm):
    class Meta:
        model = Profile
        fields = ['image']
