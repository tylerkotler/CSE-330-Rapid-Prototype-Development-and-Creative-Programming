from django.db import models
from django.utils import timezone
from django.contrib.auth.models import User
from django.urls import reverse

# model for a poll. Polls have a question, 4 possible responses, 4 counters for keeping track of responses, a date, an author, and whether it is locked or not


class Poll(models.Model):
    question = models.TextField()
    option_one = models.CharField(max_length=100)
    option_two = models.CharField(max_length=100)
    option_three = models.CharField(max_length=100)
    option_four = models.CharField(max_length=100)
    option_one_count = models.IntegerField(default=0)
    option_two_count = models.IntegerField(default=0)
    option_three_count = models.IntegerField(default=0)
    option_four_count = models.IntegerField(default=0)
    date = models.DateTimeField(default=timezone.now)
    author = models.ForeignKey(User, on_delete=models.CASCADE)
    points = models.IntegerField(default=0)
    locked = models.BooleanField(default=False)

    def total(self):
        return self.option_one_count + self.option_two_count + self.option_three_count + self.option_four_count

    def __str__(self):
        return self.question

    def get_absolute_url(self):
        return reverse('home')

#Model with responses to surveys -> used to keep track of which users
#have responded so that a user can not respond more than once
class Respond(models.Model):
    username = models.TextField()
    pollID = models.IntegerField()
