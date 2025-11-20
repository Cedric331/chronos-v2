<template>
    <i 
        :class="[
            iconClass,
            sizeClasses,
            colorClasses,
            'inline-block',
            spin ? 'fa-spin' : '',
        ]"
        :aria-hidden="ariaHidden"
        :aria-label="ariaLabel"
    ></i>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
        validator: (value) => {
            // Vérifier que le nom commence par 'fa' ou 'fas' ou 'far' ou 'fal' ou 'fab'
            return /^(fa[srb]?|fa)\s/.test(value) || value.startsWith('fa-');
        },
    },
    size: {
        type: String,
        default: 'md',
        validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'].includes(v),
    },
    color: {
        type: String,
        default: 'current',
    },
    spin: {
        type: Boolean,
        default: false,
    },
    ariaHidden: {
        type: Boolean,
        default: true,
    },
    ariaLabel: {
        type: String,
        default: '',
    },
});

// Normaliser le nom de l'icône
const iconClass = computed(() => {
    // Si le nom commence déjà par 'fa', l'utiliser tel quel
    if (props.name.startsWith('fa')) {
        return props.name;
    }
    // Sinon, ajouter 'fas fa-' par défaut
    return `fas fa-${props.name}`;
});

const sizeClasses = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
    xl: 'text-xl',
    '2xl': 'text-2xl',
}[props.size];

const colorClasses = computed(() => {
    if (props.color === 'current') {
        return 'text-current';
    }
    // Support des couleurs Tailwind
    return `text-${props.color}`;
});
</script>

