<template>
    <div v-if="$page.props.auth.team && $page.props.auth.team.logo_url">
        <img :src="$page.props.auth.team.logo_url" alt="Logo" class="rounded-full w-10 h-10" />
    </div>
    <div v-else>
        <img src="/images/logo.png" alt="Logo" :class="[$page.props.auth.user ? 'w-10 h-10' : 'w-34']" class="rounded-full" />
    </div>
</template>
