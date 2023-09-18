<script lang="ts" setup>
const { text, count } = defineProps(['text', 'count'])
</script>
<template>
    <div class="bg-neutral text-white border h-40 rounded-box px-6 py-3 flex flex-col">
        <div class="flex justify-between items-center flex-1">
            <div class="space-y-1">
                <p class="font-semibold text-lg">{{ text }}</p>
                <h3 class="font-bold text-2xl">{{ count }}</h3>
            </div>
            <div>
                <slot name="icon"></slot>
            </div>
        </div>
    </div>
</template>
<script lang="ts">

export default {
}
</script>
<style lang="">
    
</style>