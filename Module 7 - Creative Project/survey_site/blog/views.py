from django.shortcuts import render, get_object_or_404
from django.contrib.auth.mixins import LoginRequiredMixin, UserPassesTestMixin
from django.contrib.auth.models import User
from django.views.generic import (
    ListView, 
    DetailView, 
    CreateView,
    UpdateView,
    DeleteView
)
from .models import Post


def home(request):
    context = {
        'posts': Post.objects.all()
    }
    return render(request, 'blog/home.html', context)

class PostListView(ListView):
    model = Post
    template_name = 'blog/home.html'  # <app>/<model>_<viewtype>.html
    context_object_name = 'posts'
    ordering = ['-date']  #orders posts from newest to oldest
    paginate_by = 6

#show all posts of a specific user
class UserPostListView(ListView):
    model = Post
    template_name = 'blog/user_posts.html'  # <app>/<model>_<viewtype>.html
    context_object_name = 'posts'
    paginate_by = 6

    #overrides query that list view makes
    def get_queryset(self):
        #if username exists, capture in user variable - else, gives 404 -> getting it from url
        user = get_object_or_404(User, username=self.kwargs.get('username')) 
        return Post.objects.filter(author=user).order_by('-date')


class PostDetailView(DetailView):
    model = Post

#has the loginrequiredmixin because you need to be logged in to add new post
#if not logged in, redirected to login page
class PostCreateView(LoginRequiredMixin, CreateView):
    model = Post
    fields = ['title', 'content']  #fields that are part of a new post

    def form_valid(self, form):
        form.instance.author = self.request.user  #setting form instance to the logged in user
        return super().form_valid(form)  #validating, setting author

#has the loginrequiredmixin and userpassestestmixin to make sure there's a logged in user
#and it's the same user as the one who wrote the post - requires an extra function below
class PostUpdateView(LoginRequiredMixin, UserPassesTestMixin, UpdateView):
    model = Post
    fields = ['title', 'content']  #fields that are part of a new post

    def form_valid(self, form):
        form.instance.author = self.request.user  #setting form instance to the logged in user
        return super().form_valid(form)  #validating, setting author

    def test_func(self):
        post = self.get_object()
        #ensures only the creator of post can update it
        if self.request.user == post.author:
            return True
        return False


class PostDeleteView(LoginRequiredMixin, UserPassesTestMixin, DeleteView):
    model = Post

    def test_func(self):
        post = self.get_object()
        success_url = '/'
        #ensures only the creator of post can update it
        if self.request.user == post.author:
            return True
        return False

def about(request):
    return render(request, 'blog/about.html', {'title': 'About'})