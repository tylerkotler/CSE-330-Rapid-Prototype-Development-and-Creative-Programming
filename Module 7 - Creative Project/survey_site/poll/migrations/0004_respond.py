# Generated by Django 3.0.5 on 2020-04-21 22:36

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('poll', '0003_poll_locked'),
    ]

    operations = [
        migrations.CreateModel(
            name='Respond',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('username', models.TextField()),
                ('pollID', models.IntegerField()),
            ],
        ),
    ]