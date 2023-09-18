<script lang="ts" setup>
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ kelas: string | null }>({ kelas: null })
const validations = ref<any>({})
const updateKelas = async (data) => {
    return await http.patch(`/kelas/${route.params.id}`, { ...data })
}

const toast = useToast()
const { isLoading, mutate } = useMutation(updateKelas, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil update kelas", { position: "top" })
    },
    onError(error: any) {
        switch (error.response.data.context) {
            case "VALIDATIONS":
                validations.value = error.response.data.validations
                break;
            case "NOT_FOUND":
                router.push({ name: 'kelas' })
                break;
            default:
                toast.error("Terjadi kesalahan")
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
const route = useRoute()
const router = useRouter()
const getKelas = async () => {
    return await http.get(`/kelas/${route.params.id}/edit`);
}

const { mutate: get_edit, status } = useMutation(getKelas, {
    onSuccess(kelas) {
        data.kelas = kelas.data.kelas
    },

    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('Kelas tidak di temukan', { position: 'top' })
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
    toast.clear();
    await get_edit()
})

</script>
<template>
    <div>
        <div v-if="status === 'success'">
            <Buttonback />
            <div class="p-4 card border lg:max-w-lg w-full rounded-md">
                <h3 class="card-title mb-3">Edit Kelas</h3>
                <form @submit.prevent="handleSubmit" class="space-y-4">

                    <input type="text" :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.kelas)}`"
                        class="input input-bordered w-full max-w-lg" placeholder="Masukan nama kelas" v-model="data.kelas"
                        :disabled="isLoading">
                    <ValidationFeedback :validations="validations.kelas" />

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
import { useToast } from 'vue-toast-notification';
import Buttonback from '../../../components/Buttonback.vue';
import ValidationFeedback from '../../../components/ValidationFeedback.vue';
import { checkValidation } from '../../../helpers/validation_helper';
import Loading from '../../../components/Loading.vue';

export default {
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>