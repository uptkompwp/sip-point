
<script  setup>
import icon from '../images/icon.png'

</script>
<template>
    <div class="p-[0.5rem] border-b sticky -top-2 bg-white dark:bg-neutral z-[777]">
        <router-link :to="{ name: 'home' }" class="flex items-center" style="background: none; color: initial;">
            <img :src="icon" alt="LOGO UPT" class="h-12 w-12">
            <h2 class="ml-2 font-semibold text-xl dark:text-white">SIP POINT</h2>
        </router-link>
    </div>
</template>
<script>
export default {

}
</script>
<style lang="">

</style>
<!-- public/images/icon.png -->
