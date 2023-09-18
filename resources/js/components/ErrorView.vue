<template>
    <div class="grid place-items-center min-h-[45vh]">
        <div class="text-center space-y-5">
            <h1 class="font-bold text-7xl">{{ code }}</h1>
            <p class="text-sm font-semibold">{{ text }}</p>
            <Buttonback />
        </div>
    </div>
</template>
<script>
import Buttonback from './Buttonback.vue';

export default {
    props: ['code', 'text'],
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>