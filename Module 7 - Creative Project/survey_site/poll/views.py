from django.shortcuts import render, get_object_or_404, redirect
from django.contrib.auth.mixins import LoginRequiredMixin, UserPassesTestMixin
from django.contrib.auth.models import User
from django.core.paginator import Paginator
from django.contrib.auth.decorators import login_required
from django.views.generic import (
    ListView,
    DetailView,
    CreateView,
    UpdateView,
    DeleteView
)
from .models import Poll, Respond
from users.models import Profile
from django.http import HttpResponse
from .forms import CreatePollForm

# passed to the main page so we can see the open polls


class PollListView(ListView):
    model = Poll
    template_name = 'poll/home.html'  # <app>/<model>_<viewtype>.html
    context_object_name = 'polls'
    ordering = ['-points', '-date']  # orders posts from newest to oldest
    paginate_by = 6
    queryset = Poll.objects.filter(locked='False')

# passed to the locked page so we can see the locked polls


class HiddenPollListView(ListView):
    model = Poll
    template_name = 'poll/locked.html'  # <app>/<model>_<viewtype>.html
    context_object_name = 'polls'
    ordering = ['-points', '-date']  # orders posts from newest to oldest
    paginate_by = 6
    queryset = Poll.objects.filter(locked='True')

# show all posts of a specific user


class UserPollListView(ListView):
    model = Poll
    template_name = 'poll/user_polls.html'  # <app>/<model>_<viewtype>.html
    context_object_name = 'polls'
    ordering = ['-points', '-date']
    paginate_by = 6

    # overrides query that list view makes
    def get_queryset(self):
        # if username exists, capture in user variable - else, gives 404 -> getting it from url
        user = get_object_or_404(User, username=self.kwargs.get('username'))
        return Poll.objects.filter(author=user).order_by('-date')

# passed to the create poll page
@login_required
def PollCreateView(request):
    userPoints = Profile.objects.filter(user=request.user)[0].points
    if request.method == 'POST':
        form = CreatePollForm(request.POST)
        if form.is_valid():
            form.instance.author = request.user
            form.save()
            newPoll = Poll.objects.all().order_by("-id")[0]
            points_to_spend = newPoll.points
            points_available = Profile.objects.filter(user=request.user)[0].points
            if points_to_spend > points_available:
                userProfile = Profile.objects.get(user=request.user)
                userProfile.points = 0
                userProfile.save()
                newPoll.points = points_available
                newPoll.save()
            else:
                points_left = points_available-points_to_spend
                userProfile = Profile.objects.get(user=request.user)
                userProfile.points = points_left
                userProfile.save()
            return redirect('home')
    else:
        form = CreatePollForm()
    
    context = {
        'form': form,
        'userPoints': userPoints
    }
    return render(request, 'poll/create.html', context)
# passed to the voting page


def PollVoteView(request, poll_id):
    poll = Poll.objects.get(pk=poll_id)
    user = request.user
    voted = False
    #checks to see if user already responded to the poll -> if it did, voted is
    #true, and the html file will see that and take away the submit button
    if Respond.objects.filter(username=user, pollID=poll_id).count()==1:
        voted = True
  
    if request.method == 'POST':
        respond_instance = Respond.objects.create(username=user.username, pollID=poll_id)
        respond_instance.save()
        userWithPoint = Profile.objects.get(user=request.user)
        userWithPoint.points += 1
        userWithPoint.save()

        selected_option = request.POST['poll']
        if selected_option == 'option1':
            poll.option_one_count += 1
        elif selected_option == 'option2':
            poll.option_two_count += 1
        elif selected_option == 'option3':
            poll.option_three_count += 1
        elif selected_option == 'option4':
            poll.option_four_count += 1
        else:
            return HttpResponse(400, 'Invalid form')

        poll.save()

        return redirect('home')
        # return redirect('resutls', poll_id)

    context = {
        'poll': poll,
        'voted': voted
    }
    return render(request, 'poll/vote.html', context)

# passed to the results


def PollResultsView(request, poll_id):
    poll = Poll.objects.get(pk=poll_id)
    context = {
        "poll": poll
    }
    return render(request, 'poll/results.html', context)

# passed to the updating poll page so that the fields are filled as they already are


class PollUpdateView(LoginRequiredMixin, UserPassesTestMixin, UpdateView):
    model = Poll
    template_name = 'poll/create.html'
    fields = ['question', 'option_one', 'option_two', 'option_three',
              'option_four', 'locked']  # fields that are part of a new post

    def form_valid(self, form):
        # setting form instance to the logged in user
        form.instance.author = self.request.user
        return super().form_valid(form)  # validating, setting author

    def test_func(self):
        post = self.get_object()
        # ensures only the creator of post can update it
        if self.request.user == post.author:
            return True
        return False

# passed to the delete poll page


class PollDeleteView(LoginRequiredMixin, UserPassesTestMixin, DeleteView):
    model = Poll

    def test_func(self):
        poll = self.get_object()
        success_url = '/'
        # ensures only the creator of post can update it
        if self.request.user == poll.author:
            return True
        return False


def locked(request):
    return render(request, 'poll/locked.html', {'title': 'Locked Polls'})
