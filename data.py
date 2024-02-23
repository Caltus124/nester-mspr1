import socket
import json
import datetime

# Fonction pour obtenir le temps UTC en secondes
def get_utc_time():
    date = datetime.datetime.utcnow()
    return int(date.timestamp())

# Données à envoyer sous forme de dictionnaire
data_to_send = {
    "system_info": [
        {
            "cpu_usage": 24.9,
            "ram_info": {
                "total": 8589934592,
                "used": 1165732864,
                "free": 87490560
            },
            "storage_info": {
                "total": 474404847616,
                "used": 10122203136,
                "free": 19832930304
            },
            "machine_name": "pc-enzo",
            "os_info": "Windows",
            "ip_address": "192.168.1.249"
        }
    ],
    "ping_result": [
        {
            "timestamp": 1708675417.612751,
            "host": "1.1.1.1",
            "result": 0.0056362152099609375
        }
    ],
    "network_host": [
        {
            "timestamp": 1708675417.3973482,
            "hosts": [
                "192.168.1.1",
                "192.168.1.10",
                "192.168.1.100",
                "192.168.1.11",
                "192.168.1.117",
                "192.168.1.137",
                "192.168.1.17",
                "192.168.1.18",
                "192.168.1.21",
                "192.168.1.53",
                "192.168.1.54",
                "192.168.1.90"
            ]
        }
    ],
    "wan_latency": [
        {
            "timestamp": 1708675417.6059031,
            "latency": 20
        }
    ]
}

# Convertir les données en JSON
json_data = json.dumps(data_to_send)

# Adresse IP et port de l'écouteur PHP
server_address = ('127.0.0.1', 55000)

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
