import socket
import json

# Données à envoyer sous forme de dictionnaire
data_to_send = {
    "machine": {
        "ip": "192.168.1.101",
        "nom": "machine_test"
    },
    "performances": {
        "cpu_usage": "50%",
        "ram_usage": "60%",
        "storage_usage": "70%"
    }
}

# Convertir les données en JSON
json_data = json.dumps(data_to_send)

# Adresse IP et port de l'écouteur PHP
server_address = ('127.0.0.1', 5556)

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
