<template>
    <div v-if="$page.props.auth.team && $page.props.auth.team.logo_url" class="flex items-center">
        <img :src="$page.props.auth.team.logo_url" alt="Logo" class="rounded-full object-cover w-12 h-12" />
    </div>
    <div v-else class="flex items-center">
        <img src="/images/logo.png" alt="Logo" :class="[$page.props.auth.user ? 'w-12 h-12' : 'w-34']" class="rounded-full object-cover" />
    </div>
</template>
