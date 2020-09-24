from django.db import models
from django.utils import timezone
from django.contrib.auth.models import User
from django.urls import reverse

# Create your models here.
class Post(models.Model):
    title = models.CharField(max_length=100)
    content = models.TextField()
    date = models.DateTimeField(default=timezone.now)
    author = models.ForeignKey(User, on_delete=models.CASCADE)

    def __str__(self):
        return self.title

    #redirect function redirects you to specific route
    #but reverse function returns the full url to that route as a string, we are using that instead
    #we are letting the view handle the url string
    #this is for after you create a new post, it then sends you back to post's detail page
    #to go to home page instead of specific post, set an attribute in createview called success 
    #url, and set that to the home page instead -> the way we have it now is probs better
    def get_absolute_url(self):
        return reverse('post-detail', kwargs={'pk': self.pk}) #post's primary key is the kwargs


    def __unicode__(self):
        return self.question