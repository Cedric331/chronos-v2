# Solution pour le problème de quota d'emails

Ce document explique les modifications apportées pour résoudre le problème de quota d'emails dépassé.

## Problème identifié

L'application atteignait la limite de 200 emails par heure pour l'adresse `chronos@chronos-hub.fr`, même si vous n'envoyiez pas manuellement beaucoup d'emails.

## Solutions implémentées

### 1. Modification de la planification des tâches

Dans `app/Console/Kernel.php`, nous avons modifié la fréquence d'exécution des commandes :
- `GeneratePlanning` : exécuté une fois par jour à minuit (au lieu de chaque minute)
- `CheckAlertModule` : exécuté une fois par heure (au lieu de chaque minute)

### 2. Journalisation des emails

Nous avons ajouté un système de journalisation des emails pour identifier la source des emails envoyés :
- Création d'un service provider `EmailLogServiceProvider` qui intercepte tous les envois d'emails
- Configuration d'un canal de log spécifique pour les emails dans `config/logging.php`
- Les logs d'emails sont stockés dans `storage/logs/emails.log`

### 3. Limitation du taux d'envoi d'emails

Nous avons ajouté un middleware `EmailRateLimiter` pour limiter le nombre d'emails envoyés par heure :
- Configuration dans `config/mail.php` avec les options `rate_limiting.enabled` et `rate_limiting.per_hour`
- Application du middleware aux routes qui envoient des emails

### 4. Mise en file d'attente des emails

Nous avons modifié tous les envois d'emails pour utiliser la file d'attente :
- Remplacement de `Mail::to()->send()` par `Mail::to()->queue()`
- Cela permet de mieux gérer le taux d'envoi et d'éviter de dépasser les quotas

## Configuration de la file d'attente

Pour que la mise en file d'attente fonctionne correctement, vous devez configurer votre environnement :

1. Modifiez votre fichier `.env` pour utiliser une file d'attente au lieu du mode synchrone :
   ```
   QUEUE_CONNECTION=database
   MAIL_RATE_LIMITING=true
   MAIL_RATE_LIMIT_PER_HOUR=150
   ```

2. Créez la table de file d'attente dans la base de données :
   ```
   php artisan queue:table
   php artisan migrate
   ```

3. Démarrez un worker pour traiter la file d'attente :
   ```
   php artisan queue:work --queue=default,emails
   ```

   Pour la production, configurez un superviseur pour maintenir le worker en fonctionnement.

## Surveillance et diagnostic

Pour surveiller les envois d'emails et identifier les problèmes potentiels, utilisez la commande :

```
php artisan emails:check --days=7
```

Cette commande analysera les logs d'emails des 7 derniers jours et affichera :
- Le nombre total d'emails envoyés
- La distribution des emails par heure
- Les destinataires les plus fréquents
- Les sujets les plus fréquents
- Les sources (fichiers) les plus fréquentes d'envoi d'emails

## Recommandations supplémentaires

1. **Consolidation des notifications** : Envisagez de regrouper plusieurs notifications en un seul email (par exemple, un résumé quotidien).

2. **Nettoyage périodique** : Configurez une tâche pour nettoyer régulièrement les anciennes alertes et notifications.

3. **Surveillance continue** : Vérifiez régulièrement les logs d'emails pour identifier tout problème potentiel.
