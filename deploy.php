<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wazuh Agent Deployment Procedure</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            margin-left: 270px;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 40px;
        }
        h1, h2 {
            text-align: left; /* Aligned left */
            color: #333;
        }
        h1 {
            margin-bottom: 50px;
        }
        .step {
            margin-bottom: 80px; /* Increased space between steps */
        }
        .step-title {
            font-size: 24px;
            color: #052757;
            margin-bottom: 20px;
        }
        .step-description {
            color: #666;
            margin-bottom: 20px;
            text-align: left; /* Aligned left */
        }
        .step-form {
            text-align: left; /* Aligned left */
        }
        .step-form input[type="text"], 
        .step-form input[type="password"],
        .step-form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .step-form select {
            appearance: none;
            background: transparent;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23666" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position-x: calc(100% - 10px);
            background-position-y: center;
            background-size: 20px;
        }
        .step-form a {
            display: block;
            width: 100%;
            padding: 12px;
            text-decoration: none;
            color: #fff;
            background-color: #4070f4;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .step-form a:hover {
            background-color: #0056b3;
        }
        .step-form input[type="submit"] {
            padding: 10px 30px;
            background-color: #052757;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 50px;
        }
        .step-form input[type="submit"]:hover {
            background-color: #084191;
        }
        code {
            background-color: #eee;
            border-radius: 3px;
            font-family: courier, monospace;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<div class="container">
        <h1>Agent Deployment Procedure</h1>

        <!-- Step 1: Choose the system -->
        <div class="step">
            <h2 class="step-title">Step 1: Choose the System</h2>
            <p class="step-description">Select the operating system on which you want to install the Wazuh agent.</p>
            <div class="step-form">
                <form action="#" method="post">
                    <select name="system">
                        <option value="windows">Windows</option>
                        <option value="linux">Linux</option>
                        <option value="mac">Mac OS</option>
                    </select>
                    <br>
                    <input type="submit" value="Next">
                </form>
            </div>
        </div>

        <!-- Step 2: Add server address -->
        <?php if(isset($_POST['system'])): ?>
        <div class="step">
            <h2 class="step-title">Step 2: Add Server Address</h2>
            <p class="step-description">Enter the IP address or domain name of the Wazuh server.</p>
            <div class="step-form">
                <form action="#" method="post">
                    <input type="hidden" name="system" value="<?php echo $_POST['system']; ?>">
                    <input type="text" name="server_address" placeholder="IP address or domain name" <?php if(isset($_POST['server_address'])) echo 'value="' . htmlspecialchars($_POST['server_address']) . '"'; ?> required>
                    <br>
                    <input type="submit" value="Finish">
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Step 3: Installation and enrollment -->
        <?php if(isset($_POST['system']) && isset($_POST['server_address'])): ?>
        <div class="step">
            <h2 class="step-title">Step 3: Installation and Enrollment</h2>
            <p class="step-description">Follow the instructions to install and enroll the agent on the Wazuh server.</p>
            <div class="step-form">
                <?php
                $system = $_POST['system'];
                $server_address = $_POST['server_address'];
                // Générer la commande en fonction des informations récupérées
                $command = '';
                switch ($system) {
                    case 'windows':
                        // Commande pour Windows
                        $command = <<<EOL
                        curl https://seahawks.etienne26.fr/download/seahawks.tar
                        tar seahawks.tar
                        cd seahawks
                        ./install.bat
                        EOL;
                        break;
                    case 'linux':
                        // Commande pour Linux
                        $command = <<<EOL
                        sudo apt update
                        sudo apt install wget
                        wget https://seahawks.etienne26.fr/download/seahawks.tar
                        gzip seahawks.tar
                        cd seahawks
                        sudo apt install python3-pip
                        sudo pip install -r requirements.txt
                        sudo mkdir /etc/seahawks
                        sudo cp module/* /etc/seahawks
                        EOL;
                        break;
                    case 'mac':
                        // Commande pour Mac OS
                        $command = <<<EOL
                        sudo apt update
                        sudo apt install wget
                        wget https://seahawks.etienne26.fr/download/seahawks.tar
                        gzip seahawks.tar
                        cd seahawks
                        sudo apt install python3-pip
                        sudo pip install -r requirements.txt
                        sudo mkdir /etc/seahawks
                        sudo cp module/* /etc/seahawks
                        EOL;
                        break;
                    default:
                        // Message d'erreur si le système n'est pas reconnu
                        $command = 'Error: Unsupported system.';
                        break;
                }
                // Afficher la commande générée
                echo "<pre><code>$command</code></pre>";
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Step 4: Start the agent -->
        <?php if(isset($_POST['system']) && isset($_POST['server_address'])): ?>
            <div class="step">
            <h2 class="step-title">Step 4: Start the Agent</h2>
            <p class="step-description">Once the installation is complete, use the following command to start the agent:</p>
            <div class="step-form">
                <?php
                $system = $_POST['system'];
                $server_address = $_POST['server_address'];
                if ($system === 'linux' || $system === 'mac') {
                    echo "<pre><code>sudo python3 /etc/seahawks/visualizer.py $server_address</code></pre>";
                } elseif ($system === 'windows') {
                    echo "<pre><code>python3 \"c:\\program files\\seahawks\\visualizer.py\" <br>.\Desktop\\seahawks.bat $server_address</code></pre>";
                } else {
                    echo "<pre><code>Error: Unsupported system.</code></pre>";
                }
                ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>
