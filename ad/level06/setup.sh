# Nivel 6 — GPP en SYSVOL.
# En SYSVOL quedó un Groups.xml de Group Policy Preferences con una contraseña
# "cifrada" (cpassword). La descifras con la clave pública de MS14-025 y lees el
# Flag con la cuenta que aparece ahí.
starter_user
user_add respaldo 'corazon' --description='Cuenta de respaldo'
gpp_groups 'A1B2C3D4-1111-2222-3333-444455556666' 'Cuenta de respaldo' respaldo 'corazon'
flag_share respaldo
