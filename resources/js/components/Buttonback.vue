<script setup lang="ts">
import { useRouter } from 'vue-router'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'
const router = useRouter()

</script>
<template>
    <button @click="router.back" class="btn btn-neutral btn-sm my-4">
        <ChevronLeftIcon class="h-5 w-5" /> Kembali
    </button>
</template>
<script lang="ts">
export default {

}
</script>
<style lang="">
    
</style>