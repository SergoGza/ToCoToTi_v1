<!-- resources/js/Components/LogoToCoToTi.vue -->
<template>
    <div class="flex items-center">
        <div class="logo-container mr-2">
            <!-- Aquí iría el SVG del logo con las flechas circulares -->
            <!-- Por ahora usaré un placeholder simple -->
            <svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                <circle cx="20" cy="20" r="18" fill="white" stroke="#00913F" stroke-width="2" />
                <path d="M20 8c-6.627 0-12 5.373-12 12s5.373 12 12 12c2.81 0 5.392-.968 7.432-2.582" stroke="#00913F" stroke-width="2" stroke-linecap="round" />
                <path d="M20 32c6.627 0 12-5.373 12-12s-5.373-12-12-12c-2.81 0-5.392.968-7.432 2.582" stroke="#825028" stroke-width="2" stroke-linecap="round" />
                <path d="M10 16l-2 4 4-2" fill="none" stroke="#825028" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M30 24l2-4-4 2" fill="none" stroke="#00913F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <div class="logo-text font-bold text-xl">
            <span class="text-primary">To</span><span class="text-secondary">Co</span><span class="text-primary">To</span><span class="text-secondary">Ti</span>
        </div>
    </div>
</template>
