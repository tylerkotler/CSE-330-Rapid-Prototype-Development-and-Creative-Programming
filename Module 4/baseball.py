import sys, os
import re
import operator

if len(sys.argv) < 2:
    sys.exit(f"Usage: {sys.argv[0]} filename")

file_location = str(sys.argv[1])

if not os.path.exists(sys.argv[1]):
    sys.exit(f"Error: File '{sys.argv[1]}' not found")


class Player:
    atbats = 0
    hits = 0
    name = ""
    def __init__(self, name, bat, hit):
        self.name = name
        self.atbats = bat
        self.hits = hit
    def get_name(self):
        return self.name
    def update_atbats(self, num):
        self.atbats = self.atbats+num
    def update_hits(self, num):
        self.hits = self.hits+num
    def get_average(self):
        return self.hits/self.atbats

players = {} 
line_with_player = re.compile(r"^[A-Z]")
with open(file_location) as f:
    for line in f:
        match = line_with_player.match(line)
        if match is not None:
            player_name = re.search(r"^([A-Z][A-Za-z]*\s[A-Z][A-Za-z]*)", line)[0]
            matches = re.findall(r"(\d+)", line)
            player_atbats = matches[0]
            player_hits = matches[1]
            player_atbats = float(player_atbats)
            player_hits = float(player_hits)
            key = player_name
            if key in players:
                players[player_name].update_atbats(player_atbats)
                players[player_name].update_hits(player_hits)
            else:
                newplayer = Player(player_name, player_atbats, player_hits)
                players[player_name] = newplayer

for name, player in sorted(players.items(), key=lambda x:x[1].get_average(), reverse=True):
    print(f"{name}: {player.get_average():.3f}")







