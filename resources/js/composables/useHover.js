import { ref } from 'vue';

/**
 * Composable pour gérer l'état de survol
 * @returns {Object} État et méthodes pour gérer le hover
 */
export function useHover() {
    const isHovered = ref(false);
    
    const onMouseEnter = () => {
        isHovered.value = true;
    };
    
    const onMouseLeave = () => {
        isHovered.value = false;
    };
    
    return { 
        isHovered, 
        onMouseEnter, 
        onMouseLeave 
    };
}

