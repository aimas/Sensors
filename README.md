# Sensors
Programs I created on Raspberry PI for handling sensors, mostly written in Python or JAVA, maybe some C at a later point

The env_webinfo.py program is very simple and writes the following values - temperature, atmospheric pressure, atmospheric pressure at sea level and altitude (calculated based on pressure) into a MySQL DB. The php-file env_multiaxis.php reads from the table and displays atmospheric pressure, atmosperic presssure at sea level and altitude in a graph with two y-axis' using the d3s library. The pages refreshes every minute.


