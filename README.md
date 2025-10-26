# Plugin Log Viewer pour CakePHP

Un visualiseur de logs élégant et complet pour les applications CakePHP, inspiré de Laravel Telescope.

## Fonctionnalités

- 📋 **Liste des fichiers de logs** - Parcourir tous les fichiers de log disponibles dans votre application
- 📊 **Statistiques** - Statistiques visuelles affichant les comptages par niveau de log (Erreur, Avertissement, Info, Debug, Critique)
- 🔍 **Recherche et Filtre** - Recherche textuelle en temps réel et filtre par niveau de log
- 📅 **Filtre par Date** - Filtrer les logs par plage de dates
- 💾 **Export** - Exporter les logs au format CSV ou JSON
- 📥 **Téléchargement** - Télécharger les fichiers de logs directement
- 🗑️ **Vider** - Vider le contenu des fichiers de log
- 🎨 **Interface Magnifique** - Interface moderne avec intégration AdminLTE

## Prérequis

- CakePHP 4.0+
- PHP 7.4+

## Installation

### Via Composer (une fois publié)

```bash
composer require yourname/cakephp-log-viewer
```

### Installation Manuelle

1. Clonez ou téléchargez ce plugin dans `plugins/log-viewer/`
2. Chargez le plugin dans votre `config/bootstrap.php`:

```php
$this->addPlugin('LogViewer');
```

## Configuration

1. Configurez les routes dans `config/routes.php`:

```php
Plugin::load('LogViewer', ['routes' => true]);
```

Ou ajoutez les routes manuellement:

```php
Router::scope('/', function (RouteBuilder $builder) {
    $builder->plugin('LogViewer', function (RouteBuilder $builder) {
        $builder->connect('/logs', ['controller' => 'Logs', 'action' => 'index']);
        $builder->connect('/logs/:action/*', ['controller' => 'Logs']);
    });
});
```

## Utilisation

Accédez au visualiseur de logs à l'adresse `/logs` ou `/log-viewer/logs` selon votre configuration de routage.

### Fonctionnalités

#### Voir les Logs
- Parcourir tous les fichiers de log dans votre répertoire `logs/`
- Cliquez sur n'importe quel fichier de log pour voir son contenu
- Support de la pagination (100 lignes par page)

#### Recherche et Filtre
- Utilisez la boîte de recherche pour trouver du texte spécifique dans les logs
- Filtrez par niveau de log (Erreur, Avertissement, Info, Debug, Critique, Notice)
- Filtrez par plage de dates en utilisant le sélecteur de date

#### Statistiques
- Visualisez le tableau de bord des statistiques affichant les comptages pour chaque niveau de log
- Boîtes colorées pour une identification facile

#### Export
- Exportez les logs filtrés au format CSV ou JSON
- Maintient les filtres de date dans les données exportées

#### Téléchargement et Vider
- Téléchargez les fichiers de logs complets
- Videz le contenu des fichiers de logs (nécessite une requête POST)

## Structure des Fichiers

```
plugins/log-viewer/
├── composer.json
├── README.md
├── LICENSE
├── src/
│   └── Controller/
│       └── LogsController.php
└── templates/
    └── Logs/
        ├── index.php
        └── view.php
```

## Sécurité

⚠️ **IMPORTANT**: Ce plugin fournit un accès direct à vos fichiers de logs. En production:

1. Ajoutez des contrôles d'authentification/autorisation
2. Limitez l'accès aux utilisateurs autorisés uniquement
3. Considérez la mise en liste blanche par IP
4. Désactivez en production ou utilisez le contrôle d'accès basé sur les rôles

Exemple d'autorisation dans le contrôleur:

```php
public function initialize(): void
{
    parent::initialize();
    
    // N'autoriser l'accès qu'aux utilisateurs administrateurs
    $this->Authentication->allowUnauthenticated(['index', 'view', 'download']);
    
    // Ou ajoutez une vérification manuelle
    if (!$this->Authentication->getIdentity()) {
        throw new \Cake\Http\Exception\ForbiddenException();
    }
}
```

## Licence

Licence MIT

## Contribution

Les contributions sont les bienvenues! N'hésitez pas à soumettre une Pull Request.

## Journal des modifications

### 1.0.0
- Version initiale
- Voir les fichiers de logs
- Fonctionnalités de recherche et filtrage
- Tableau de bord des statistiques
- Export CSV/JSON
- Filtrage par plage de dates
