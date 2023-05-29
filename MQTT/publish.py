import paho.mqtt.publish as publish

publish.single("boton_bool", "1", hostname="152.67.54.115")
publish.single("valor_analog", "357", hostname="152.67.54.115")