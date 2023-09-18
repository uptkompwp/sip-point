<script lang="ts" setup>
import { getCurrentInstance, reactive, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ kuis: string | null, type: string }>({ kuis: null, type: "PRAKTEK" })
const validations = ref<any>({})
const route = useRoute()
const storeKuis = async (data) => {
    return await http.put(`/kuis/${route.params.id}`, { ...data })
}

const toast = useToast()
const { isLoading, mutate } = useMutation(storeKuis, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil mengupdate kuis", { position: "top" })

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

const getSesi = async () => {
    return await http.get(`/sesi/${route.params.sesi_id}/edit`);
}
const getKuis = async () => {
    return await http.get(`/kuis/${route.params.id}/edit`);
}

const router = useRouter()
const { mutate: get_edit, status } = useMutation(getSesi, {
    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('Sesi tidak di temukan', { position: 'top' })
                router.back()
                break;
            default:
                toast.error('Internal server error', { position: 'top' })
                router.back()
                break;
        }
    },
})
const { mutate: get_edit_kuis, status: status_kuis } = useMutation(getKuis, {
    onSuccess(kuis) {
        data.kuis = kuis.data.kuis
        data.type = kuis.data.tipe_kuis
    },
    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('kuis tidak di temukan', { position: 'top' })
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
    await get_edit_kuis()
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
        <div v-if="status === 'success' && status_kuis === 'success'">
            <Buttonback />
            <div class="p-4 card border lg:max-w-lg w-full rounded-md">
                <h3 class="card-title mb-3">Edit Kuis</h3>
                <form @submit.prevent="handleSubmit" class="space-y-4">

                    <input autofocus type="text"
                        :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.kelas)}`"
                        class="input input-bordered w-full max-w-lg" placeholder="Masukan nama kuis" v-model="data.kuis"
                        :disabled="isLoading">
                    <ValidationFeedback :validations="validations.kuis" />
                    <div class="form-control">
                        <label for="tipe_kuis" class="mb-2">Tipe Kuis</label>
                        <select class="select select-bordered" v-model="data.type">
                            <option value="PRAKTEK">Praktek</option>
                            <option value="TEORI">Teori</option>
                        </select>
                        <ValidationFeedback :validations="validations.type" />
                    </div>

                    <button class="btn btn-neutral" type="submit" :disabled="isLoading">
                        <span class="loading loading-spinner" v-if="isLoading"></span>
                        Update
                    </button>
                </form>
            </div>
        </div>
        <Loading v-if="status === 'loading' && status_kuis === 'loading'" />
    </div>
</template>
<script lang="ts">
import { useMutation } from '@tanstack/vue-query';
import Buttonback from '../../../components/Buttonback.vue';
import { useToast } from 'vue-toast-notification';
import { checkValidation } from '../../../helpers/validation_helper';
import ValidationFeedback from '../../../components/ValidationFeedback.vue';
import Loading from '../../../components/Loading.vue'
export default {
    components: { Buttonback, Loading }
}
</script>
<style lang="">
    
</style>