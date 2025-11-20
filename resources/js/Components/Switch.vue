<template>
    <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
        <input
            :id="toggleId"
            v-model="checked"
            type="checkbox"
            :name="toggleId"
            class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white appearance-none cursor-pointer"
            @change="onToggleChange"
        />
        <label
            :for="toggleId"
            class="toggle-label block overflow-hidden h-5 rounded-full bg-gray-300 cursor-pointer"
            @click="checked = !checked">
        </label>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: ''
        }
    },
    computed: {
        toggleId() {
            return `toggle-${this._uid || Math.random().toString(36).substr(2, 9)}`;
        }
    },
    watch: {
        modelValue: function (val) {
            this.checked = val;
        }
    },
    data: function () {
        return {
            checked: this.modelValue
        }
    },
    methods: {
        onToggleChange() {
            this.$emit('update:modelValue', this.checked);
        }
    }
}
</script>

<style scoped>
.toggle-checkbox:checked {
    @apply: right-0 border-green-400;
    right: 0;
    border-color: #30336b;
}
.toggle-checkbox:checked + .toggle-label {
    @apply: bg-green-400;
    background-color: #30336b;
}

[type='checkbox'], [type='radio'] {
    color: #9ca3af;
}
</style>
