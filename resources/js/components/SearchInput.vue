<script lang="ts" setup>
import { debounce } from '../helpers/debounce';
import { ref, watch } from 'vue'
import { useMutation } from '@tanstack/vue-query'
const props = defineProps(['request', 'label', 'value', 'noOpt', 'labelInput', 'model', 'disabled'])
const options = ref([])
const { mutate, isLoading } = useMutation(props.request, {
    onSuccess(data) {
        const changeData = (data as any).data.map((dt) => {
            return { label: dt[props.label], value: dt[props.value] }
        })
        options.value = changeData
    }
})
const onSearch = (search) => {
    debounce(mutate(search), 500)
}

const emit = defineEmits(['onSelected']);
const selected = ref<any | null>(props.model ?? null)

watch(selected, (val) => {
    emit('onSelected', val)
}, { immediate: true })


watch(() => props.model, (val) => {
    if (val === null) {
        selected.value = null
    }
}, { immediate: true })

</script>
<template>
    <div class="space-y-3">
        <label class="text-sm">{{ props.labelInput }}</label>
        <v-select :filterable="false" :options="options" @search="onSearch" v-model="selected"
            :disabled="disabled ?? false">
            <template #spinner>
                <span v-if="isLoading" class="loading loading-spinner loading-xs"></span>
            </template>
            <template #no-options>
                <div class="text-sm">{{ props.noOpt }}</div>
            </template>
            <template #open-indicator>
                <span></span>
            </template>
            <template #option="data">
                <p class="text-sm">{{ data.label }}</p>
            </template>
        </v-select>
    </div>
</template>
<script lang="ts">
export default {

}
</script>
<style lang="">
    
</style>