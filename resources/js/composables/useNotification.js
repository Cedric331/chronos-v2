import { notify } from '@kyvg/vue3-notification';

export function useNotification() {
    const showSuccess = (message, title = 'Succès') => {
        notify({
            title,
            text: message,
            type: 'success',
            duration: 3000,
        });
    };
    
    const showError = (message, title = 'Erreur') => {
        notify({
            title,
            text: message,
            type: 'error',
            duration: 5000,
        });
    };
    
    const showWarning = (message, title = 'Attention') => {
        notify({
            title,
            text: message,
            type: 'warn',
            duration: 4000,
        });
    };
    
    const showInfo = (message, title = 'Information') => {
        notify({
            title,
            text: message,
            type: 'info',
            duration: 3000,
        });
    };
    
    return { 
        showSuccess, 
        showError, 
        showWarning, 
        showInfo 
    };
}

