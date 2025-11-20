<script setup>
import { computed, onMounted, onUnmounted, ref, nextTick } from 'vue';

const props = defineProps({
    align: {
        default: 'right',
    },
    width: {
        default: '48',
    },
    contentClasses: {
        default: () => ['py-1'],
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    window.removeEventListener('resize', updateDropdownPosition);
    window.removeEventListener('scroll', updateDropdownPosition);
});

const contentClasses = props.contentClasses.toString();

const widthClass = computed(() => {
    return {
        48: 'w-48',
        56: 'w-56',
        64: 'w-64',
        72: 'w-72',
        96: 'w-96',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0';
    } else if (props.align === 'right') {
        return 'origin-top-right right-0';
    } else {
        return 'origin-top';
    }
});

const open = ref(false);
const triggerRef = ref(null);
const dropdownPosition = ref({ top: 0, left: 0, width: 0 });

const updateDropdownPosition = () => {
    if (!triggerRef.value) return;

    const rect = triggerRef.value.getBoundingClientRect();
    const width = props.width === '48' ? 192 : (props.width === '56' ? 224 : (props.width === '64' ? 256 : (props.width === '72' ? 288 : 384)));
    
    // Utiliser getBoundingClientRect() qui donne la position relative à la viewport
    // Avec fixed, on n'ajoute pas scrollY/scrollX car fixed est relatif à la viewport
    dropdownPosition.value = {
        top: rect.bottom + 4, // 4px de marge, position relative à la viewport
        left: props.align === 'left' ? rect.left : rect.right - width,
        width: width
    };
};

const toggleDropdown = () => {
    open.value = !open.value;
    if (open.value) {
        nextTick(() => {
            updateDropdownPosition();
            window.addEventListener('resize', updateDropdownPosition);
            window.addEventListener('scroll', updateDropdownPosition);
        });
    } else {
        window.removeEventListener('resize', updateDropdownPosition);
        window.removeEventListener('scroll', updateDropdownPosition);
    }
};
</script>

<template>
    <div class="relative">
        <div ref="triggerRef" @click="toggleDropdown">
            <slot name="trigger" />
        </div>

        <teleport to="body">
            <!-- Full Screen Dropdown Overlay -->
            <div v-if="open" class="fixed inset-0 z-90" @click="open = false"></div>

            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <div
                    v-if="open"
                    class="fixed z-90 mt-2 rounded-md shadow-lg"
                    :style="{
                        top: `${dropdownPosition.top}px`,
                        left: `${dropdownPosition.left}px`,
                        width: `${dropdownPosition.width}px`,
                        maxHeight: '80vh',
                        overflowY: 'auto'
                    }"
                >
                    <div class="rounded-md ring-1 p-1 ring-black ring-opacity-5 bg-white dark:bg-gray-700" :class="contentClasses">
                        <slot name="content" />
                    </div>
                </div>
            </transition>
        </teleport>
    </div>
</template>
