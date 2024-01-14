<template>
    <!-- component -->
        <div v-if="show" class="inset-0 bg-gray-800 opacity-80 fixed flex w-full h-full items-center justify-center duration-300 transition-opacity" style="z-index: 6000">
            <div class="flex-col">
                <div class="w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto;" width="220px" height="220px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                        <g>
                            <circle cx="60" cy="50" r="4" fill="#e15b64">
                                <animate attributeName="cx" repeatCount="indefinite" dur="0.5076142131979695s" values="95;35" keyTimes="0;1" begin="-1.3199s"></animate>
                                <animate attributeName="fill-opacity" repeatCount="indefinite" dur="0.5076142131979695s" values="0;1;1" keyTimes="0;0.2;1" begin="-1.3199s"></animate>
                            </circle>
                            <circle cx="60" cy="50" r="4" fill="#e15b64">
                                <animate attributeName="cx" repeatCount="indefinite" dur="0.5076142131979695s" values="95;35" keyTimes="0;1" begin="-0.6501s"></animate>
                                <animate attributeName="fill-opacity" repeatCount="indefinite" dur="0.5076142131979695s" values="0;1;1" keyTimes="0;0.2;1" begin="-0.6501s"></animate>
                            </circle>
                            <circle cx="60" cy="50" r="4" fill="#e15b64">
                                <animate attributeName="cx" repeatCount="indefinite" dur="0.5076142131979695s" values="95;35" keyTimes="0;1" begin="0s"></animate>
                                <animate attributeName="fill-opacity" repeatCount="indefinite" dur="0.5076142131979695s" values="0;1;1" keyTimes="0;0.2;1" begin="0s"></animate>
                            </circle>
                            </g><g transform="translate(-15 0)">
                            <path d="M50 50L20 50A30 30 0 0 0 80 50Z" fill="#ebc14f" transform="rotate(90 50 50)"></path>
                            <path d="M50 50L20 50A30 30 0 0 0 80 50Z" fill="#ebc14f">
                                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.5076142131979695s" values="0 50 50;45 50 50;0 50 50" keyTimes="0;0.5;1"></animateTransform>
                            </path>
                            <path d="M50 50L20 50A30 30 0 0 1 80 50Z" fill="#ebc14f">
                                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.5076142131979695s" values="0 50 50;-45 50 50;0 50 50" keyTimes="0;0.5;1"></animateTransform>
                            </path>
                        </g>
                    </svg>
                    <div class=" text-gray-200 font-mono w-full text-sm sm:text-xs">{{ messageLoading }}</div>

                </div>
            </div>
        </div>
</template>

<script>
export default {
    name: "Loading",
    props: {
        show: Boolean,
        messageLoading: {
            type: String,
            default: "Chargement en cours..."
        }
    }
}
</script>

<style scoped>

</style>
