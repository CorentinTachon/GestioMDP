GestioMDP - Gestionnaire de mots de passe personnel (BTS SIO SLAM)

Description

GestioMDP est une application web simple permettant de stocker et gérer vos mots de passe de manière chiffrée localement. Elle propose une alerte automatique si un mot de passe n'a pas été changé depuis plus de 30 jours.

Fonctionnalités

CRUD sur mots de passe (Ajout, Lecture, Modification, Suppression)

Données chiffrées avec un algorithme personnalisé

Base SQLite portable

Popup de rappel de sécurité (30 jours)

UI responsive avec TailwindCSS

Stack technique

PHP 8.x (procédural)

SQLite3

HTML5 + TailwindCSS

JavaScript

Framework Slim

Installation

Cloner le dépôt ou copier les fichiers dans votre htdocs

Lancer un serveur local (XAMPP, WAMP)

Ouvrir http://localhost/GestioMDP/public/index.php

Structure

GestioMDP/
├── config/
│   └── database.php
├── app/
│   └── controllers/
│       ├── add.php
│       ├── delete.php
│       ├── update.php
│       ├── encrypt.php
│       └── decrypt.php
├── public/
│   └── index.php


Rendu avec la popup^d'alerte dans les fichiers joints 



