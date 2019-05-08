ubitransport
================

API Rest Symfony qui gère les élèves et les notes.

Environnement:
- PhpStorm / Sublime Text
- Wamp Server (php 7.2.14, MySql 5.7.24, Apache 2.4.37)
- Symfony 3.2
- Composer
- Git
- PostMan
- Invite de commandes (Cmder)

N.B: Une documentation des URLs est générée sous PATH/doc

Exemple d'ajout d'élève avec les notes (POST) : PATH/eleves
{
	"nom": "Rollet",
	"prenom": "Julien",
	"date_de_naissance": "2000-12-01",
	"note": [
		{
			"matiere": "chimie",
			"evaluation": "15"
		},
		{
			"matiere": "maths",
			"evaluation": "19"

		}]
}