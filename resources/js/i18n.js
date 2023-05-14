import { createI18n } from 'vue-i18n';

const messages = {
    en: {
        'titre_1': 'My title',
        'info_team': 'Team information',
    },
    fr: {
        'name': 'Nom',
        'birthday': 'Anniversaire',
        'department': 'Département',
        'department_code': 'Code du Département',
        'nav': {
            'dashboard': 'Tableau de bord',
            'management': 'Gestion de la team',
            'profil': 'Mes informations',
            'logout': 'Déconnexion',
        },
        'team_gestion': {
            'titre': 'Information sur la team',
            'buttonUpdate': 'Modifier',
            'modalTeamUpdate': {
                'title': 'Modification de la Team',
                'button': 'Enregistrer'
            }
        },
        'team_rotation': {
            'button_rotation': 'Créer une nouvelle rotation',
            'button_planning': 'Créer un planning',
            'title_planning': 'Création du Planning'
        },
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
