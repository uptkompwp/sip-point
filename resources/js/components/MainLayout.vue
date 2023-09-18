<script lang="ts" setup>
import { getCurrentInstance, onMounted } from 'vue'
import { useRoute } from 'vue-router'
const { auth } = getCurrentInstance()?.appContext.app as unknown as { auth: any }
const globalStore = useGlobalStore()

</script>
<template>
    <div class="min-h-full" v-if="!auth.check()">
        <slot></slot>
    </div>
    <div class="min-h-full" v-if="auth.check()">
        <Sidebar />
        <div class="lg:ml-[250px]">
            <NavHeader />
            <div class="justify-center min-h-[80vh] grid place-items-center" v-if="globalStore.getLoading || !auth.ready()">
                <span class="loading loading-dots loading-lg"></span>
            </div>
            <main class="p-3 lg:p-[0.5rem] bg-base-100" v-else>
                <slot></slot>
            </main>
        </div>
    </div>
</template>
<script lang="ts">
import Sidebar from './Sidebar.vue';
import NavHeader from './NavHeader.vue';
import { useGlobalStore } from '../stores/globalStore';

export default {
    components: { Sidebar, NavHeader }
}
</script>
<style lang="">
    
</style>