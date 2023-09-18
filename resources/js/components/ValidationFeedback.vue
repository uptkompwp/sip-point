<template>
    <p class="text-red-500 text-sm mt-1" v-if="validations">
        {{ validations[0] }}
    </p>
</template>
<script>

export default {
    props: {
        validations: Object
    }
}
</script>
<style lang="">
    
</style>