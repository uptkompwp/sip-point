<template>
    <MainLayout>
        <Transition name="fade">
            <router-view></router-view>
        </Transition>
    </MainLayout>
</template>
<script lang="ts">
import MainLayout from './components/MainLayout.vue';

export default {
    components: { MainLayout }
}
</script>
<style scoped>
.fade-enter-active {
    transition: all 0.3s ease-out;
}

.fade-leave-active {
    transition: all 0.1s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>