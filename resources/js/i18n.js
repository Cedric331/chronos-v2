import { createI18n } from 'vue-i18n';

const messages = {
    en: {
        'titre_1': 'My title',
        'info_team': 'Team information',
    },
    fr: {
        'name': 'Nom',
        'birthday': 'Anniversaire',
        'nav': {
            'dashboard': 'Tableau de bord',
            'management': 'Gestion de la team',
            'profil': 'Mes informations',
            'logout': 'Déconnexion',
        },
        'titre_gestion_team': 'Information sur la team',
        'team_user': {
            'titre': 'Membre de la Team',
            'buttonAdd': 'Ajouter un membre',
            'buttonUpdate': 'Modifier',
            'modalUserUpdate' : {
                'title': 'Modification du conseiller',
                'button': 'Enregistrer'
            }
        },
    }
};

const i18n = createI18n({
    locale: 'fr',
    messages,
});

export default i18n;
