<script lang="ts" setup>
import { getCurrentInstance, reactive, onMounted, ref } from 'vue'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ makul: string | null, sks: number | null }>({ makul: null, sks: null })
const validations = ref<any>({})
const route = useRoute()
const router = useRouter()
const updateMakul = async (data) => {
    return await http.patch(`mata-kuliah/${route.params.id}`, { ...data })
}

const toast = useToast()
const { isLoading, mutate } = useMutation(updateMakul, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil update makul", { position: "top" })
    },
    onError(error: any) {
        switch (error.response.data.context) {
            case "VALIDATIONS":
                validations.value = error.response.data.validations
                break;
            case "NOT_FOUND":
                router.push({ name: 'makul' })
            default:
                toast.error('Internal server error', { position: 'top' })
                break;
        }
    },
})

const handleSubmit = async () => {
    await mutate(data)
}


const getMakul = async () => {
    return await http.get(`/mata-kuliah/${route.params.id}/edit`);
}

const { mutate: get_edit, status } = useMutation(getMakul, {
    onSuccess(makul) {
        data.makul = makul.data.makul
        data.sks = makul.data.sks
    },

    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('Mata kuliah tidak di temukan', { position: 'top' })
                router.back()
                break;
            default:
                toast.error('Internal server error', { position: 'top' })
                router.back()
                break;
        }
    },
})



onMounted(async () => {
    await get_edit()
    toast.clear();
})

</script>
<template>
    <div>
        <div v-if="status === 'success'">
            <Buttonback />
            <div class="p-4 card border lg:max-w-lg w-full rounded-md">
                <h3 class="card-title mb-3">Edit Makul</h3>
                <form @submit.prevent="handleSubmit" class="space-y-4">

                    <input type="text" :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.makul)}`"
                        class="input input-bordered w-full max-w-lg" placeholder="Masukan nama mata kuliah"
                        v-model="data.makul" :disabled="isLoading">
                    <ValidationFeedback :validations="validations.makul" />

                    <input type="number" :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.sks)}`"
                        class="input input-bordered w-full max-w-lg" placeholder="Masukan SKS mata kuliah"
                        v-model="data.sks" :disabled="isLoading">
                    <ValidationFeedback :validations="validations.sks" />

                    <button class="btn btn-neutral" type="submit" :disabled="isLoading">
                        <span class="loading loading-spinner" v-if="isLoading"></span>
                        Update
                    </button>
                </form>
            </div>
        </div>
        <Loading v-if="status === 'loading'" />

    </div>
</template>
<script lang="ts">
import { useMutation } from '@tanstack/vue-query';
import Buttonback from '../../../components/Buttonback.vue';
import { useToast } from 'vue-toast-notification';
import { checkValidation } from '../../../helpers/validation_helper';
import ValidationFeedback from '../../../components/ValidationFeedback.vue';
import { useRoute, useRouter } from 'vue-router';
import Loading from '../../../components/Loading.vue';

export default {
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>