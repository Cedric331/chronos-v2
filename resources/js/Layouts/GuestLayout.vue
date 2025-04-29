<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center bg-gray-100 dark:bg-gray-900"
        :style="backgroundStyle"
    >
        <div class="w-full dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    data() {
        const randomIndex = Math.floor(Math.random() * 8);
        return {
            backgroundStyle: {
                backgroundImage: `url('/images/bg-${randomIndex}.jpg')`,
                backgroundSize: "cover",
                backgroundPosition: "center",
                backgroundRepeat: "no-repeat",
                height: "100vh",
                width: "100%"
            }
        };
    }
};
</script>
