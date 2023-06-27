
<template>
    <div class="relative min-h-screen flex flex-col sm:justify-center items-center">

        <div id="fixed-background" class="absolute inset-0 z-10"></div>

        <div class="w-full overflow-hidden z-20 relative">
            <slot />
        </div>
    </div>
</template>

<style>
#fixed-background {
    background-image: url('/images/background_share.png');
    background-size: cover;
    position: fixed;
    z-index: -1;
}
</style>
