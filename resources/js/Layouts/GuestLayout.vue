<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-white via-purple-100 to-blue-100 px-4">
        <div class="mb-6">
            <Link href="/">
                <img class="h-28 w-auto" src="../Assets/arp_logo.png" alt="Logo" />
            </Link>
        </div>

        <div class="w-full sm:max-w-md bg-white p-8 rounded-2xl shadow-xl transition-all duration-300">
            <slot />
        </div>
    </div>
</template>

