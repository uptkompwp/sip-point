<script lang="ts" setup>
import { getCurrentInstance, reactive, onMounted, ref } from 'vue'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ makul: string | null, sks: number | null }>({ makul: null, sks: null })
const validations = ref<any>({})
const storeMakul = async (data) => {
    return await http.post('mata-kuliah', { ...data })
}

const toast = useToast()
const { isLoading, mutate } = useMutation(storeMakul, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil menambah makul", { position: "top" })
        data.makul = null
        data.sks = null
    },
    onError(error: any) {
        switch (error.response.data.context) {
            case "VALIDATIONS":
                validations.value = error.response.data.validations
                break;
            case "INTERNAL_SERVER_ERROR":
                toast.error('Internal server error', { position: 'top' })
                break;
            default:
                toast.error('Internal server error', { position: 'top' })
                break;
        }
    },
})

const handleSubmit = async () => {
    await mutate(data)
}

onMounted(() => {
    toast.clear();
})

</script>
<template>
    <div>
        <Buttonback />
        <div class="p-4 card border lg:max-w-lg w-full rounded-md">
            <h3 class="card-title mb-3">Tambahkan Makul baru</h3>
            <form @submit.prevent="handleSubmit" class="space-y-4">

                <input autofocus type="text" :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.makul)}`"
                    class="input input-bordered w-full max-w-lg" placeholder="Masukan nama mata kuliah" v-model="data.makul"
                    :disabled="isLoading">
                <ValidationFeedback :validations="validations.makul" />

                <input type="number" :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.sks)}`"
                    class="input input-bordered w-full max-w-lg" placeholder="Masukan SKS mata kuliah" v-model="data.sks"
                    :disabled="isLoading">
                <ValidationFeedback :validations="validations.sks" />

                <button class="btn btn-neutral" type="submit" :disabled="isLoading">
                    <span class="loading loading-spinner" v-if="isLoading"></span>
                    Simpan
                </button>
            </form>
        </div>
    </div>
</template>
<script lang="ts">
import { useMutation } from '@tanstack/vue-query';
import Buttonback from '../../../components/Buttonback.vue';
import { useToast } from 'vue-toast-notification';
import { checkValidation } from '../../../helpers/validation_helper';
import ValidationFeedback from '../../../components/ValidationFeedback.vue';

export default {
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>