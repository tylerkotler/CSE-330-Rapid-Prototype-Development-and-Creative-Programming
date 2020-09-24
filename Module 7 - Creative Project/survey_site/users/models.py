from django.db import models
from django.contrib.auth.models import User
from PIL import Image


# model for profiles which have a username, a profile picture, a password, and an email
class Profile(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE)
    image = models.ImageField(default='default.jpg', upload_to='profile_pics')
    points = models.IntegerField(default=0)

    def __str__(self):
        return f'{self.user.username} Profile'

    # this method resizes images so that if they are too large, they get reduced to a
    # lower amount of pixels and don't take up so much space on the webstie

    # ** may want to look into deleting old images from database when user changes image
    def save(self, *args, **kwargs):
        super().save(*args, **kwargs)

        img = Image.open(self.image.path)
        if img.height > 300 or img.width > 300:
            output_size = (300, 300)
            img.thumbnail(output_size)
            img.save(self.image.path)
