from django.urls import path
from .views import (
    PollListView,
    PollCreateView,
    PollVoteView,
    PollResultsView,
    PollUpdateView,
    PollDeleteView,
    UserPollListView,
    HiddenPollListView,
)
from . import views
from poll import views as poll_views

# links the pages to each other
urlpatterns = [
    path('', PollListView.as_view(), name='home'),
    path('create/', poll_views.PollCreateView, name='create'),
    path('vote/<poll_id>/', poll_views.PollVoteView, name='vote'),
    path('results/<poll_id>/', poll_views.PollResultsView, name='results'),
    path('update/<int:pk>/', PollUpdateView.as_view(), name='update'),
    path('delete/<int:pk>/', PollDeleteView.as_view(success_url="/"), name='delete'),
    path('locked/', HiddenPollListView.as_view(), name='poll-locked'),
    path('user/<str:username>', UserPollListView.as_view(), name='user-poll'),
]
