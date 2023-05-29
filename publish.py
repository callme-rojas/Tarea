import paho.mqtt.publish as publish

publish.single("boton_bool", "1", hostname="localhost")
publish.single("valor_analog", "357", hostname="localhost")