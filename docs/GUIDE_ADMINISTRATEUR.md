# Guide utilisateur — Back-office administrateur THILOR DESIGN

Le back-office est accessible à l'adresse **`/admin`** de votre site
(ex. `https://www.thilor-design.com/admin`). Connectez-vous avec un compte
disposant du rôle **Administrateur** ou **Gestionnaire**.

## Rôles

- **Administrateur** : accès complet, y compris paramètres boutique,
  gestion des comptes administrateurs et journal d'activité.
- **Gestionnaire** : accès au catalogue, aux commandes, aux clients, aux
  avis et au contenu (pages/bannières) — sans accès aux paramètres
  sensibles ni à la gestion des autres administrateurs.
- **Client** : n'a jamais accès au back-office (uniquement à son espace
  "Mon compte" sur le site public).

## Tableau de bord

En arrivant sur `/admin`, vous voyez :
- **Commandes ce mois** et **chiffre d'affaires ce mois** (uniquement les
  commandes en préparation, expédiées ou livrées comptent dans le CA — une
  commande "en attente" non encore confirmée n'est pas comptabilisée).
- **Nombre de clients** inscrits.
- **Produits vendus** (cumul de toutes les commandes confirmées).
- **Produits en rupture** (variantes à 0 en stock).
- Un tableau des **variantes en stock faible ou nul** (≤ 5), pour anticiper
  les réassorts.

## Gérer le catalogue

### Produits (menu *Catalogue > Produits*)
1. **Ajouter un produit** : renseignez nom, catégorie, prix. Le slug (URL)
   se génère automatiquement à partir du nom — vous pouvez le modifier.
2. **Prix barré / promotion** : renseignez "Prix barré / promo" avec un
   montant **supérieur** au prix actuel pour afficher une réduction sur le
   site (badge "-X%" calculé automatiquement).
3. **Images** : ajoutez une ou plusieurs images dans la section *Images*.
   Cochez "Image principale" pour celle qui apparaît en premier sur le
   catalogue. Les images sont automatiquement redimensionnées et
   converties en WebP pour la performance — inutile de les optimiser
   vous-même avant l'envoi.
4. **Variantes (tailles/couleurs/stock)** : une fois le produit créé,
   ouvrez l'onglet *Variantes (tailles / couleurs / stock)* en bas de la
   fiche pour ajouter chaque combinaison taille/couleur avec son stock.
   **Le produit n'est achetable que si au moins une variante a du stock.**
5. **Suppression** : un produit déjà commandé ne peut pas être supprimé
   définitivement (protection des données de commande) — il est
   désactivé ("archivé") à la place ; décochez simplement "Actif" pour le
   retirer du site sans perdre l'historique.

### Catégories et Collections
- **Catégories** (*Catalogue > Catégories*) : Femme, Homme, Enfant, Haute
  couture, Accessoires — utilisées pour la navigation principale et les
  filtres du catalogue.
- **Collections** (*Catalogue > Collections*) : mises en avant marketing
  transverses (ex. "Prêt-à-porter", "Édition Mariage") affichées sur la
  page *Collections* du site.

### Avis clients (*Catalogue > Avis*)
Les avis déposés par les clients sont **masqués sur le site tant qu'ils ne
sont pas approuvés**. Cliquez sur *Approuver* pour les publier, ou
supprimez ceux qui ne respectent pas vos règles.

## Gérer les commandes (*Ventes > Commandes*)

- Chaque commande affiche son **statut de traitement** (En attente, En
  préparation, Expédiée, Livrée, Annulée) et son **statut de paiement**
  (En attente, Payé, Échoué, Annulé, Remboursé) — ce sont deux informations
  distinctes : une commande peut être "En préparation" alors que le
  paiement à la livraison est encore "En attente" (il ne devient "Payé"
  qu'à l'encaissement réel).
- Utilisez le bouton **"Marquer payée"** une fois le paiement à la
  livraison effectivement encaissé.
- Ouvrez une commande pour voir le détail des articles (avec le prix
  exact au moment de l'achat, même si le prix du produit a changé depuis)
  et l'historique des paiements associés.
- **Modes de livraison** (*Ventes > Modes de livraison*) : ajoutez,
  modifiez ou désactivez les options proposées au client au moment du
  paiement (coût, délai affiché).

## Marketing

- **Coupons** (*Marketing > Coupons*) : créez un code (ex. `BIENVENUE10`),
  choisissez pourcentage ou montant fixe, une date de validité et un
  montant minimum de commande si besoin.
- **Newsletter** (*Marketing > Newsletter*) : liste des inscrits via le
  formulaire du site (accueil et pied de page).

## Contenu

- **Bannières** (*Marketing > Bannières*) : image + texte du grand
  bandeau d'accueil ("bannière principale") et du bandeau promotionnel
  secondaire.
- **Pages & Blog** (*Contenu > Pages & Blog*) : pages statiques (À propos,
  CGV, FAQ...) et articles de blog. Le type "Article de blog" apparaît
  sur `/blog`, le type "Page statique" est accessible via son URL directe
  (utilisée par les liens du pied de page).
- **Messages de contact** (*Contenu > Messages de contact*) : messages
  envoyés depuis le formulaire `/contact`. Un badge sur le menu indique le
  nombre de messages non lus.

## Paramètres boutique (*Administration > Paramètres boutique*, admin
uniquement)

Coordonnées affichées sur le site (téléphone, email, adresse), liens
réseaux sociaux du pied de page, et titre/description SEO par défaut
utilisés sur les pages qui n'ont pas de meta personnalisée.

## Administrateurs (*Administration > Administrateurs*, admin uniquement)

Créez des comptes pour votre équipe avec le rôle **Gestionnaire**
(catalogue/commandes/contenu, sans accès aux paramètres sensibles) ou
**Administrateur** (accès complet). Un compte ne peut pas se supprimer
lui-même.

## Journal d'activité (*Administration > Journal d'activité*, admin
uniquement)

Trace les créations, modifications et suppressions de produits, commandes
et coupons réalisées par les administrateurs/gestionnaires (qui, quand,
quoi) — à consulter en cas de question sur un changement de prix ou de
statut de commande.

## Bonnes pratiques

- Changez le mot de passe du compte administrateur de démonstration
  (`admin@thilor-design.com`) dès la mise en production, ou supprimez-le
  et créez votre propre compte.
- Désactivez (plutôt que supprimer) un produit en rupture prolongée pour
  le retirer du site sans perdre son historique de vente et ses avis.
- Vérifiez régulièrement le tableau des **stocks faibles** sur le tableau
  de bord pour anticiper les réassorts.
