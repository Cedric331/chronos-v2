import { ref, watch } from 'vue';

/**
 * Composable pour le debounce
 * @param {Function} fn - Fonction à débouncer
 * @param {Number} delay - Délai en millisecondes
 * @returns {Function} Fonction débouncée
 */
export function useDebounce(fn, delay = 300) {
    let timeoutId = null;
    
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(this, args), delay);
    };
}

/**
 * Composable pour créer une valeur réactive avec debounce
 * @param {*} initialValue - Valeur initiale
 * @param {Number} delay - Délai en millisecondes
 * @returns {Object} Objet avec value et debouncedValue
 */
export function useDebouncedValue(initialValue, delay = 300) {
    const value = ref(initialValue);
    const debouncedValue = ref(initialValue);
    
    watch(value, (newValue) => {
        const timeoutId = setTimeout(() => {
            debouncedValue.value = newValue;
        }, delay);
        
        return () => clearTimeout(timeoutId);
    });
    
    return { value, debouncedValue };
}

