<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div id="background" class="min-h-screen flex flex-col sm:justify-center items-center bg-gray-100 dark:bg-gray-900">

        <div class="w-full dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>

<style>
#background {
    background-image: url('/images/background_login.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh;
    width: 100%;
}
</style>
