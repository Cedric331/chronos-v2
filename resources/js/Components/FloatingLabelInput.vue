<template>
    <div class="relative">
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            @blur="$emit('blur', $event)"
            @focus="$emit('focus', $event)"
            :class="[
                'peer w-full px-4 pt-6 pb-2 border rounded-lg',
                'focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent',
                'dark:bg-gray-700 dark:border-gray-600 dark:text-white',
                'transition-all duration-200',
                error ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600',
                disabled ? 'opacity-50 cursor-not-allowed' : '',
            ]"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            :aria-invalid="error ? 'true' : 'false'"
            :aria-describedby="error ? `${id}-error` : undefined"
        />
        <label
            :for="id"
            :class="[
                'absolute left-4 text-sm text-gray-500 dark:text-gray-400',
                'peer-placeholder-shown:top-4 peer-placeholder-shown:text-base',
                'peer-focus:top-2 peer-focus:text-sm',
                'top-2',
                'transition-all duration-200',
                'pointer-events-none',
                error ? 'text-red-500' : '',
            ]"
        >
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>
        <div 
            v-if="isValid && !error" 
            class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
        >
            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
        </div>
        <p 
            v-if="error" 
            :id="`${id}-error`"
            class="mt-1 text-sm text-red-600 dark:text-red-400"
            role="alert"
        >
            {{ error }}
        </p>
        <p 
            v-else-if="hint" 
            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
        >
            {{ hint }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    id: { type: String, required: true },
    modelValue: { type: [String, Number], default: '' },
    type: { type: String, default: 'text' },
    label: { type: String, required: true },
    placeholder: { type: String, default: ' ' },
    error: { type: String, default: '' },
    hint: { type: String, default: '' },
    isValid: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
});

defineEmits(['update:modelValue', 'blur', 'focus']);
</script>

