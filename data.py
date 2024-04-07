import socket

# Adresse IP et port de l'écouteur PHP
server_address = ('127.0.0.1', 55000)

# Chemin du fichier JSON
file_path = "data.json"

# Lire les données à partir du fichier JSON
with open(file_path, "r") as json_file:
    json_data = json_file.read()

# Créer un socket TCP/IP
client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

try:
    # Se connecter à l'écouteur PHP
    client_socket.connect(server_address)

    # Envoyer les données
    client_socket.sendall(json_data.encode())

finally:
    # Fermer la connexion
    client_socket.close()
